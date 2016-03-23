# Everon Factory Library
Library to handle dependency injection and instantiation. Allows to produce code that is easy to test.

## Works with
* Php 5.6+
* Php 7
* Hhvm

## Features
* One line, lazy loaded dependency injection (via setters or constructors)
* Trait based, simple to use and implement, no hassle with config files, pure PHP
* Factory/FactoryWorker gives full control and overview, on how each and every object is created
* FactoryWorker allows for custom implementation of Factory methods
* Full control when a dependency should be reused (via Dependency Container) or created from scratch (eg. Logger vs Value Object)
* Minimized file/memory access/usage due to callbacks and lazy load
* Intuitive Interface: clear, small and simple API
* Convention over configuration
* Clean code

## How it works
Every instantiation should happen only inside of the ```FactoryWorker``` class.
Its role is similar of ```AbstractFactory```. It sits between *Everon Factory Component* and the client code that uses it, so there is no direct coupling.
It's easy to implement and manage, because injection is cheap and dependency setup happens in one place.

This makes testing much easier, as everything can be easily mocked/stubbed/faked.
It's ok to use ```new``` operator outside of Factory methods for simple value object like classes.  For example ```new Collection()```.

For a particular module or application, the ```registerBeforeWork``` method  of ```FactoryWorker``` is one of a places where you could setup your dependency tree.
Or you could use [Root Composition pattern](http://blog.ploeh.dk/2011/07/28/CompositionRoot/) and handle your whole dependency graph outside of your application.

### Easy Dependency Injection
To use dependency injection, register it with ```Dependency Container``` and use one line trait to inject it.

For example, this will auto inject a predefined ```Logger``` instance via setter injection into ```Foo``` class.
```php
class Foo
{
    use Dependency\Setter\Logger;
}
```

### Register with Dependency Container
Use ```register``` method to register the dependency under ```Logger``` name.

```php
$Container->register('Logger', function () use ($FactoryWorker) {
    return $FactoryWorker->buildLogger();
});
```

### Define the traits and interface
Example of ```Logger``` dependency trait, which is reused between all of the classes that use ```Dependency\Setter\Logger``` trait.
The only thing to remember is that, the name of the trait should be the same,
as the name under which the dependency was registered with the ```Dependency Container```.


```php
trait Logger
{
    /**
     * @var LoggerInterface
     */
    protected $Logger;

    /**
     * @inheritdoc
     */
    public function getLogger()
    {
        return $this->Logger;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(LoggerInterface $Logger)
    {
        $this->Logger = $Logger;
    }
}
```

Bonus: You can also define and assign the ```LoggerAwareInterface``` too all classes that are being injected with ```Logger``` instance.
```php
interface LoggerAwareInterface
{
    /**
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * @param Logger LoggerInterface
     */
    public function setLogger(LoggerInterface $Logger);
}
```

Define the setter injection trait.
The only requirement is that the name ends with ```Dependency\Setter\<dependency name>```.
You can reuse already defined ```Dependency\Logger``` trait, in every class that implements LoggerAwareInterface.


```php
namespace Application\Modules\Logger\Dependency\Setter;

use Application\Modules\Logger\Dependency;

trait Logger
{
    use Dependency\Logger;
}
```

### Register with Factory
Use ```registerWorkerCallback``` to register callback which will return instance of required FactoryWorker.
```php
$Factory->registerWorkerCallback('ApplicationFactoryWorker', function() use ($Factory) {
    return $Factory->buildWorker(Application::class);
});
```

### Build with FactoryWorker
To build your dependencies use the ```FactoryWorker``` classes.

```php
class ApplicationFactoryWorker extends AbstractWorker implements FactoryWorkerInterface
{
    /**
     * @inheritdoc
     */
    protected function registerBeforeWork()
    {
        $this->getFactory()->registerWorkerCallback('ApplicationFactoryWorker', function () {
            return $this->getFactory()->buildWorker(self::class);
        });
    }

    /**
     * @return Logger
     */
    public function buildLogger()
    {
        $Logger = new Logger();
        $this->getFactory()->injectDependencies(Logger::class, $Logger);
        return $Logger;
    }

    /**
     * @param LoggerInterface $Logger
     * @param string $anotherArgument
     * @param array $data
     *
     * @return ApplicationInterface
     */
    public function buildApplication(LoggerInterface $Logger)
    {
        $Application = new Application($Logger);
        $this->getFactory()->injectDependencies(Application::class, $Application);
        return $Application;
    }

    /**
     * @param LoggerInterface $Logger
     *
     * @return UserManagerInterface
     */
    public function buildUserManager(LoggerInterface $Logger)
    {
        $UserManager = new UserManager($Logger);
        $this->getFactory()->injectDependencies(UserManager::class, $UserManager);
        return $UserManager;
    }
}
```

### Resolve with Dependency Container
Use ```resolve``` to receive dependency defined earlier with ```register``` or ```propose```.
So you can pass the same instance to another class via constructor injection.


```php
$Container->register('Logger', function () use ($FactoryWorker) {
    return $FactoryWorker->buildLogger();
});

$Container->register('UserManager', function () use ($FactoryWorker, $Container) {
    $Logger = $Container->resolve('Logger');
    return $FactoryWorker->buildUserManager($Logger);
});

$Container->register('Application', function () use ($FactoryWorker, $Container) {
    $Logger = $Container->resolve('Logger');
    return $FactoryWorker->buildApplication($UserManager, $Logger);
});
```

Now ```Application``` and ```UserManager``` will share the same instance of ```Logger``` class.

```php
$Application->getLogger()->log('It works');
$UserManager->getLogger()->log('It works, too');
```

If you don't do any work in constructors, and you shouldn't, and only require the ```Logger``` functionality later, it would be easier
to just use the ```Logger``` as the infrastructure type dependency and just inject it via setter injection with one line.
The end result is the same.

**Every required class will be injected with the same ```Logger``` instance,
that was registered with the ```Dependency Container``` and assembled by ```FactoryWorker``` in ```Factory```.**


### Ensures Tests Ready Code (TM)
Writing tests of classes that use ```Everon Factory``` for the dependency injection
and instantiation removes the hassle of dealing with dependency problems since everything is so easy to mock.

### Dependency Container, Factory and FactoryWorker
Instantiate new ```Dependency Container``` and assign it to ```Factory```.
Use ```Factory``` to get instance of your specific ```FactoryWorker```.

The best thing is, that the classes which are being instantiated with the ```FactoryWorker``` are not aware
about the ```Dependency Container``` at all.

It could be in separate files, obviously, split by the application type and the dependencies it needs.

An example, of using the same instance of ```Logger```, in every class, through out whole application,
which required ```Logger``` dependency.

```php
$Container = new Dependency\Container();
$Factory = new Factory($Container);
$Factory->registerWorkerCallback('ApplicationFactoryWorker', function() use ($Factory) {
    return $Factory->buildWorker(Application::class);
});

$FactoryWorker = $Factory->getWorkerByName('ApplicationFactoryWorker');

$Container->register('Application', function () use ($FactoryWorker, $Container) {
    $UserManager = $Container->resolve('UserManager');
    $Logger = $Container->resolve('Logger');
    return $FactoryWorker->buildApplication($UserManager, $Logger);
});

$Container->register('UserManager', function () use ($FactoryWorker) {
    $Logger = $FactoryWorker->getFactory()->getDependencyContainer()->resolve('Logger');
    return $FactoryWorker->buildUserManager($UserRepository, $Logger);
});

$Container->register('Logger', function () use ($FactoryWorker) {
    return $FactoryWorker->buildLogger();
});

//..
//.. Instantiate your application, and proceed as usual
//..
$Application = $Container->resolve('Application');
$Application
    ->bootstrap()
    ->run();
```

### What's the best way to inject dependencies?
Use constructor for dependencies that are part of what the class is doing, and use setters/getters for infrastructure type dependencies.
In general, a ```Logger``` or ```FactoryWorker``` could be good examples of infrastructure type dependencies.

## Test Driven
See [tests](https://github.com/oliwierptak/everon-factory/blob/development/tests/unit/)
for [more examples with trait dependencies](https://github.com/oliwierptak/everon-factory/tree/development/tests/unit/doubles/).

## Example
Check [Everon Criteria Builder](https://github.com/oliwierptak/everon-criteria-builder) to see how to use Everon Factory by example.

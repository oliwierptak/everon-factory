<?php declare(strict_types = 1);
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Dependency;

use Everon\Component\Collection\Collection;
use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\Exception\DependencyServiceAlreadyRegisteredException;
use Everon\Component\Factory\Exception\InstanceIsNotObjectException;
use Everon\Component\Factory\Exception\UndefinedContainerDependencyException;
use Everon\Component\Factory\Exception\UndefinedDependencySetterException;
use Everon\Component\Utils\Text\EndsWith;
use Everon\Component\Utils\Text\LastTokenToName;

class Container implements ContainerInterface
{

    use EndsWith;
    use LastTokenToName;

    const DEPENDENCY_SETTER_FACTORY = 'Dependency\Setter\Factory';
    const TYPE_SETTER_INJECTION = 'Dependency\Setter';

    /**
     * @var CollectionInterface
     */
    protected $ServiceDefinitionCollection;

    /**
     * @var CollectionInterface
     */
    protected $ServiceCollection;

    /**
     * @var CollectionInterface
     */
    protected $ClassDependencyCollection;

    /**
     * @var CollectionInterface
     */
    protected $RequireFactoryCollection;

    /**
     * @var CollectionInterface
     */
    protected $InjectedCollection;

    /**
     * @param string $dependencyName
     * @param mixed $Receiver
     *
     * @throws UndefinedContainerDependencyException
     * @throws UndefinedDependencySetterException
     */
    protected function injectSetterDependency($dependencyName, $Receiver)
    {
        $receiverClassName = get_class($Receiver);
        $method = 'set' . $dependencyName; //eg. setConfigManager
        if (method_exists($Receiver, $method) === false) {
            throw new UndefinedDependencySetterException([
                $method,
                $dependencyName,
                $receiverClassName,
            ]);
        }

        $Dependency = $this->resolve($dependencyName);
        $Receiver->$method($Dependency);
    }

    /**
     * @param string $className
     * @param bool $autoload
     *
     * @return array
     */
    protected function getClassSetterDependencies($className, $autoload = true)
    {
        if ($this->getClassDependencyCollection()->has($className)) {
            return $this->getClassDependencyCollection()->get($className);
        }

        $traits = class_uses($className, $autoload);
        $parents = class_parents($className, $autoload);

        foreach ($parents as $parent) {
            $traits = array_merge(
                class_uses($parent, $autoload),
                $traits
            );
        }

        $dependencies = array_keys($traits);
        $dependencies = array_filter($dependencies, function ($dependencyName) {
            return $this->isSetterInjection($dependencyName);
        });

        $this->getClassDependencyCollection()->set($className, $dependencies);

        return $this->getClassDependencyCollection()->get($className);
    }

    /**
     * @param string $dependencyName
     *
     * @return bool
     */
    protected function isSetterInjection($dependencyName)
    {
        $requiredDependency = $this->textLastTokenToName($dependencyName);
        $setterDependency = sprintf('%s\%s', static::TYPE_SETTER_INJECTION, $requiredDependency);

        return $this->textEndsWith($dependencyName, $setterDependency);
    }

    /**
     * @param string $dependencyName
     *
     * @return bool
     */
    protected function isFactoryInjection($dependencyName)
    {
        return $this->textEndsWith($dependencyName, static::DEPENDENCY_SETTER_FACTORY);
    }

    /**
     * @inheritdoc
     */
    public function inject($receiverClassName, $ReceiverInstance)
    {
        if (is_object($ReceiverInstance) === false) {
            throw new InstanceIsNotObjectException();
        }

        $dependencies = $this->getClassSetterDependencies($receiverClassName);
        foreach ($dependencies as $dependencyName) {
            if ($this->isFactoryInjection($dependencyName)) {
                $this->getRequireFactoryCollection()->set($receiverClassName, true);
                continue;
            }

            $name = $this->textLastTokenToName($dependencyName);
            $this->injectSetterDependency($name, $ReceiverInstance);
        }

        $this->getInjectedCollection()->set($receiverClassName, true);
    }

    /**
     * @inheritdoc
     */
    public function injectOnce($receiverClassName, $ReceiverInstance)
    {
        if ($this->isInjected($receiverClassName)) {
            return;
        }

        $this->inject($receiverClassName, $ReceiverInstance);
    }

    /**
     * @inheritdoc
     */
    public function register($name, \Closure $ServiceClosure)
    {
        if ($this->isRegistered($name)) {
            throw new DependencyServiceAlreadyRegisteredException($name);
        }

        $this->getServiceDefinitionCollection()->set($name, $ServiceClosure);

        $this->getServiceCollection()->remove($name);
    }

    /**
     * @inheritdoc
     */
    public function propose($name, \Closure $ServiceClosure)
    {
        if ($this->isRegistered($name)) {
            return;
        }

        $this->register($name, $ServiceClosure);
    }

    /**
     * @inheritdoc
     */
    public function resolve($name)
    {
        if ($this->getServiceDefinitionCollection()->has($name) === false) {
            throw new UndefinedContainerDependencyException($name);
        }

        if ($this->getServiceCollection()->has($name)) {
            return $this->getServiceCollection()->get($name);
        }

        /** @var \Closure $Service */
        $Service = $this->getServiceDefinitionCollection()->get($name);
        if (is_callable($Service)) {
            $this->getServiceCollection()->set($name, $Service());
        }

        return $this->getServiceCollection()->get($name);
    }

    /**
     * @inheritdoc
     */
    public function isFactoryRequired($className)
    {
        return $this->getRequireFactoryCollection()->has($className);
    }

    /**
     * @inheritdoc
     */
    public function isInjected($className)
    {
        return $this->getInjectedCollection()->has($className);
    }

    /**
     * @inheritdoc
     */
    public function isRegistered($name)
    {
        return ($this->getServiceDefinitionCollection()->has($name) || $this->getServiceCollection()->has($name));
    }

    /**
     * @inheritdoc
     */
    public function getServiceDefinitionCollection()
    {
        if ($this->ServiceDefinitionCollection === null) {
            $this->ServiceDefinitionCollection = new Collection([]);
        }

        return $this->ServiceDefinitionCollection;
    }

    /**
     * @inheritdoc
     */
    public function getClassDependencyCollection()
    {
        if ($this->ClassDependencyCollection === null) {
            $this->ClassDependencyCollection = new Collection([]);
        }

        return $this->ClassDependencyCollection;
    }

    /**
     * @inheritdoc
     */
    public function getServiceCollection()
    {
        if ($this->ServiceCollection === null) {
            $this->ServiceCollection = new Collection([]);
        }

        return $this->ServiceCollection;
    }

    /**
     * @inheritdoc
     */
    public function getRequireFactoryCollection()
    {
        if ($this->RequireFactoryCollection === null) {
            $this->RequireFactoryCollection = new Collection([]);
        }

        return $this->RequireFactoryCollection;
    }

    /**
     * @inheritdoc
     */
    public function getInjectedCollection()
    {
        if ($this->InjectedCollection === null) {
            $this->InjectedCollection = new Collection([]);
        }

        return $this->InjectedCollection;
    }

}

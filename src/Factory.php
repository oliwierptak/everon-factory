<?php declare(strict_types = 1);
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory;

use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Dependency\FactoryAwareInterface;
use Everon\Component\Factory\Exception\MissingFactoryAwareInterfaceException;
use Everon\Component\Factory\Exception\FailedToInjectDependenciesException;
use Everon\Component\Factory\Exception\UndefinedClassException;
use Everon\Component\Factory\Exception\UndefinedFactoryWorkerException;

class Factory implements FactoryInterface
{

    /**
     * @var ContainerInterface
     */
    protected static $DependencyContainer;

    /**
     * @param ContainerInterface $Container
     */
    public function __construct(ContainerInterface $Container)
    {
        static::$DependencyContainer = $Container;
    }

    /**
     * @inheritdoc
     */
    public function injectDependencies($className, $Instance)
    {
        try {
            $this->getDependencyContainer()->inject($className, $Instance);
            $this->injectFactoryWhenRequired($className, $Instance);
        } catch (\Exception $e) {
            throw new FailedToInjectDependenciesException($className, null, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function injectDependenciesOnce($className, $Instance)
    {
        try {
            $this->getDependencyContainer()->injectOnce($className, $Instance);
            $this->injectFactoryWhenRequired($className, $Instance);
        } catch (\Exception $e) {
            throw new FailedToInjectDependenciesException($className, null, $e);
        }
    }

    /**
     * @param string $className
     * @param object $Instance
     *
     * @throws MissingFactoryAwareInterfaceException
     *
     * @return void
     */
    protected function injectFactoryWhenRequired($className, $Instance)
    {
        if ($this->getDependencyContainer()->isFactoryRequired($className)) {
            if (($Instance instanceof FactoryAwareInterface) === false) {
                throw new MissingFactoryAwareInterfaceException($className);
            }
            /* @var FactoryAwareInterface $Instance */
            $Instance->setFactory($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function getDependencyContainer()
    {
        return static::$DependencyContainer;
    }

    /**
     * @inheritdoc
     */
    public function getFullClassName($namespace, $className)
    {
        if ($className[0] === '\\') { //used for when laading classmap from cache
            return $className; //absolute name
        }

        return $namespace . '\\' . $className;
    }

    /**
     * @inheritdoc
     */
    public function classExists($class)
    {
        if (class_exists($class, true) === false) {
            throw new UndefinedClassException($class);
        }
    }

    /**
     * @inheritdoc
     */
    public function buildWorker($className)
    {
        if ($this->classExists($className) === false) {
            throw new UndefinedClassException();
        }
        /** @var FactoryWorkerInterface $Worker */
        $Worker = new $className($this);
        $this->injectDependencies($className, $Worker);

        return $Worker;
    }

    /**
     * @inheritdoc
     */
    public function registerWorkerCallback($name, \Closure $Worker)
    {
        $this->getDependencyContainer()->propose($name, $Worker);
    }

    /**
     * @inheritdoc
     */
    public function getWorkerByName($name)
    {
        $Worker = $this->getDependencyContainer()->resolve($name);

        if ($Worker === null || ($Worker instanceof FactoryWorkerInterface) === false) {
            throw new UndefinedFactoryWorkerException($name);
        }

        return $Worker;
    }

}

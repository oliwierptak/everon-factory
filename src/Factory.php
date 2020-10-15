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
     * @var \Everon\Component\Factory\Dependency\ContainerInterface
     */
    protected static $DependencyContainer;

    public function __construct(ContainerInterface $Container)
    {
        static::$DependencyContainer = $Container;
    }

    public function injectDependencies(string $className, $Instance): void
    {
        try {
            $this->getDependencyContainer()->inject($className, $Instance);
            $this->injectFactoryWhenRequired($className, $Instance);
        }
        catch (\Exception $e) {
            throw new FailedToInjectDependenciesException($className, null, $e);
        }
    }

    public function injectDependenciesOnce(string $className, $Instance): void
    {
        try {
            $this->getDependencyContainer()->injectOnce($className, $Instance);
            $this->injectFactoryWhenRequired($className, $Instance);
        }
        catch (\Exception $e) {
            throw new FailedToInjectDependenciesException($className, null, $e);
        }
    }

    /**
     * @param string $className
     * @param object $Instance
     *
     * @return void
     * @throws \Everon\Component\Factory\Exception\MissingFactoryAwareInterfaceException
     *
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

    public function getDependencyContainer(): ContainerInterface
    {
        return static::$DependencyContainer;
    }

    public function getFullClassName(string $namespace, string $className): string
    {
        if ($className[0] === '\\') { //used for when laading classmap from cache
            return $className; //absolute name
        }

        return $namespace . '\\' . $className;
    }

    public function classExists(string $class): void
    {
        if (class_exists($class, true) === false) {
            throw new UndefinedClassException($class);
        }
    }

    public function buildWorker(string $className): FactoryWorkerInterface
    {
        if ($this->classExists($className) === false) {
            throw new UndefinedClassException();
        }
        /** @var FactoryWorkerInterface $Worker */
        $Worker = new $className($this);
        $this->injectDependencies($className, $Worker);

        return $Worker;
    }

    public function registerWorkerCallback(string $name, \Closure $Worker): void
    {
        $this->getDependencyContainer()->propose($name, $Worker);
    }

    /**
     * @param string $name
     *
     * @return \Everon\Component\Factory\FactoryWorkerInterface
     * @throws \Everon\Component\Factory\Exception\UndefinedFactoryWorkerException
     * @throws \Everon\Component\Factory\Exception\UndefinedContainerDependencyException
     */
    public function getWorkerByName(string $name): FactoryWorkerInterface
    {
        $Worker = $this->getDependencyContainer()->resolve($name);

        if ($Worker === null || ($Worker instanceof FactoryWorkerInterface) === false) {
            throw new UndefinedFactoryWorkerException($name);
        }

        return $Worker;
    }
}

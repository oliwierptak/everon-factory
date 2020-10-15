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

use Everon\Component\Factory\Exception\FailedToInjectDependenciesException;
use Everon\Component\Factory\Exception\UndefinedClassException;
use Everon\Component\Factory\Exception\UndefinedFactoryWorkerException;

interface FactoryInterface
{

    /**
     * @return \Everon\Component\Factory\Dependency\ContainerInterface
     */
    public function getDependencyContainer();

    /**
     * @param string $className
     * @param mixed $Instance
     *
     * @throws FailedToInjectDependenciesException
     *
     * @return void
     */
    public function injectDependencies($className, $Instance);

    /**
     * @param string $className
     * @param mixed $Instance
     *
     * @throws FailedToInjectDependenciesException
     * @return void
     */
    public function injectDependenciesOnce($className, $Instance);

    /**
     * @param string $namespace
     * @param string $className
     *
     * @return string
     */
    public function getFullClassName($namespace, $className);

    /**
     * @param $class
     *
     * @throws UndefinedClassException
     *
     * @return void
     */
    public function classExists($class);

    /**
     * @param string $className
     *
     * @throws UndefinedClassException
     *
     * @return FactoryWorkerInterface
     */
    public function buildWorker($className);

    /**
     * @param string $name
     * @param \Closure $Worker
     *
     * @return void
     */
    public function registerWorkerCallback($name, \Closure $Worker);

    /**
     * @param string $name
     *
     * @throws UndefinedFactoryWorkerException
     *
     * @return FactoryWorkerInterface
     */
    public function getWorkerByName($name);

}

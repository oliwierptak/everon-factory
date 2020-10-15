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

interface FactoryInterface
{
    public function getDependencyContainer(): ContainerInterface;

    /**
     * @param string $className
     * @param mixed $Instance
     *
     * @return void
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function injectDependencies(string $className, $Instance): void;

    /**
     * @param string $className
     * @param mixed $Instance
     *
     * @return void
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function injectDependenciesOnce(string $className, $Instance): void;

    public function getFullClassName(string $namespace, string $className): string;

    /**
     * @param string $class
     *
     * @return void
     * @throws \Everon\Component\Factory\Exception\UndefinedClassException
     */
    public function classExists(string $class): void;

    /**
     * @param string $className
     *
     * @return \Everon\Component\Factory\FactoryWorkerInterface
     * @throws \Everon\Component\Factory\Exception\UndefinedClassException
     */
    public function buildWorker(string $className): FactoryWorkerInterface;

    public function registerWorkerCallback(string $name, \Closure $Worker): void;

    /**
     * @param string $name
     *
     * @return \Everon\Component\Factory\FactoryWorkerInterface
     * @throws \Everon\Component\Factory\Exception\UndefinedFactoryWorkerException
     * @throws \Everon\Component\Factory\Exception\UndefinedContainerDependencyException
     */
    public function getWorkerByName(string $name): FactoryWorkerInterface;
}

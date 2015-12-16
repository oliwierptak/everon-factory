<?php
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory;

use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Exception\InstanceIsAbstractClassException;
use Everon\Component\Factory\Exception\MissingFactoryAwareInterfaceException;
use Everon\Component\Factory\Exception\UnableToInstantiateException;
use Everon\Component\Factory\Exception\UndefinedClassException;

interface FactoryInterface
{

    /**
     * @param string $className
     * @param $Instance
     *
     * @return void
     */
    public function injectDependencies(string $className, $Instance);

    /**
     * @param string $className
     * @param $Instance
     *
     * @return void
     */
    public function injectDependenciesOnce(string $className, $Instance);

    /**
     * @param string $className
     * @param string $namespace
     *
     * @throws MissingFactoryAwareInterfaceException
     * @throws UndefinedClassException
     *
     * @return object
     */
    public function buildWithEmptyConstructor(string $className, string $namespace);

    /**
     * @param string $className
     * @param string $namespace
     * @param CollectionInterface $parameterCollection
     *
     * @throws MissingFactoryAwareInterfaceException
     * @throws UndefinedClassException
     *
     * @return object
     */
    public function buildWithConstructorParameters(string $className, string $namespace, CollectionInterface $parameterCollection);

    /**
     * @return ContainerInterface
     */
    public function getDependencyContainer(): ContainerInterface;

    /**
     * @param ContainerInterface $Container
     *
     * @return void
     */
    public function setDependencyContainer(ContainerInterface $Container);

    /**
     * @param string $namespace
     * @param string $className
     *
     * @return string
     */
    public function getFullClassName(string $namespace, string $className): string;

    /**
     * @param string $class
     *
     * @throws UndefinedClassException
     *
     * @return void
     */
    public function classExists(string $class);

    /**
     * @param array $parameters
     *
     * @return CollectionInterface
     */
    public function buildParameterCollection(array $parameters): CollectionInterface;

    /**
     * @param string $name
     * @param string $namespace
     *
     * @throws InstanceIsAbstractClassException
     * @throws UnableToInstantiateException
     *
     * @return FactoryWorkerInterface
     */
    public function getWorkerByName(string $name, string $namespace='Everon\Component\Factory'): FactoryWorkerInterface;

}

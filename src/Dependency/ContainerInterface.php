<?php
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Dependency;

use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\Exception\DependencyServiceAlreadyRegisteredException;
use Everon\Component\Factory\Exception\UndefinedContainerDependencyException;

interface ContainerInterface
{

    /**
     * @param string $receiverClassName
     * @param object $ReceiverInstance
     *
     * @return void
     */
    public function inject(string $receiverClassName, $ReceiverInstance);

    /**
     * @param string $receiverClassName
     * @param object $ReceiverInstance
     *
     * @return void
     */
    public function injectOnce(string $receiverClassName, $ReceiverInstance);

    /**
     * @param string $className
     *
     * @return bool
     */
    public function isFactoryRequired(string $className): bool;

    /**
     * @param string $name
     * @param \Closure $ServiceClosure
     *
     * @throws DependencyServiceAlreadyRegisteredException
     *
     * @return void
     */
    public function register(string $name, \Closure $ServiceClosure);

    /**
     * @param string $name
     * @param \Closure $ServiceClosure
     *
     * @return void
     */
    public function propose(string $name, \Closure $ServiceClosure);

    /**
     * @param string $name
     *
     * @throws UndefinedContainerDependencyException
     *
     * @return mixed
     */
    public function resolve(string $name);

    /**
     * @param string $className
     *
     * @return bool
     */
    public function isInjected(string $className): bool;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isRegistered(string $name): bool;

    /**
     * @return CollectionInterface
     */
    public function getServiceDefinitionCollection(): CollectionInterface;

    /**
     * @return CollectionInterface
     */
    public function getClassDependencyCollection(): CollectionInterface;

    /**
     * @return CollectionInterface
     */
    public function getServiceCollection(): CollectionInterface;

    /**
     * @return CollectionInterface
     */
    public function getRequireFactoryCollection(): CollectionInterface;

    /**
     * @return CollectionInterface
     */
    public function getInjectedCollection(): CollectionInterface;

}

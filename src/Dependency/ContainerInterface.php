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

use Everon\Component\Collection\CollectionInterface;

interface ContainerInterface
{
    /**
     * @param string $receiverClassName
     * @param object $ReceiverInstance
     *
     * @return void
     * @throws \Everon\Component\Factory\Exception\UndefinedContainerDependencyException
     * @throws \Everon\Component\Factory\Exception\UndefinedDependencySetterException
     * @throws \Everon\Component\Factory\Exception\InstanceIsNotObjectException
     */
    public function inject(string $receiverClassName, object $ReceiverInstance): void;

    public function injectOnce(string $receiverClassName, object $ReceiverInstance): void;

    public function isFactoryRequired(string $className): bool;

    /**
     * @param string $name
     * @param \Closure $ServiceClosure
     *
     * @return void
     * @throws \Everon\Component\Factory\Exception\DependencyServiceAlreadyRegisteredException
     */
    public function register(string $name, \Closure $ServiceClosure): void;

    public function propose(string $name, \Closure $ServiceClosure): void;

    /**
     * @param string $name
     *
     * @return mixed
     * @throws \Everon\Component\Factory\Exception\UndefinedContainerDependencyException
     */
    public function resolve(string $name);

    public function isInjected(string $className): bool;

    public function isRegistered(string $name): bool;

    public function getServiceDefinitionCollection(): CollectionInterface;

    public function getClassDependencyCollection(): CollectionInterface;

    public function getServiceCollection(): CollectionInterface;

    public function getRequireFactoryCollection(): CollectionInterface;

    public function getInjectedCollection(): CollectionInterface;
}

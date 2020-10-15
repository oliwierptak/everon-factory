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
     * @var \Everon\Component\Collection\CollectionInterface
     */
    protected $ServiceDefinitionCollection;

    /**
     * @var \Everon\Component\Collection\CollectionInterface
     */
    protected $ServiceCollection;

    /**
     * @var \Everon\Component\Collection\CollectionInterface
     */
    protected $ClassDependencyCollection;

    /**
     * @var \Everon\Component\Collection\CollectionInterface
     */
    protected $RequireFactoryCollection;

    /**
     * @var \Everon\Component\Collection\CollectionInterface
     */
    protected $InjectedCollection;

    /**
     * @param string $dependencyName
     * @param mixed $Receiver
     *
     * @throws \Everon\Component\Factory\Exception\UndefinedContainerDependencyException
     * @throws \Everon\Component\Factory\Exception\UndefinedDependencySetterException
     */
    protected function injectSetterDependency(string $dependencyName, $Receiver): void
    {
        $receiverClassName = get_class($Receiver);
        $method = 'set' . $dependencyName; //eg. setConfigManager
        if (method_exists($Receiver, $method) === false) {
            throw new UndefinedDependencySetterException(
                [
                    $method,
                    $dependencyName,
                    $receiverClassName,
                ]
            );
        }

        $Dependency = $this->resolve($dependencyName);
        $Receiver->$method($Dependency);
    }

    protected function getClassSetterDependencies(string $className, bool $autoload = true): array
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
        $dependencies = array_filter(
            $dependencies,
            function ($dependencyName) {
                return $this->isSetterInjection($dependencyName);
            }
        );

        $this->getClassDependencyCollection()->set($className, $dependencies);

        return $this->getClassDependencyCollection()->get($className);
    }

    protected function isSetterInjection(string $dependencyName): bool
    {
        $requiredDependency = $this->textLastTokenToName($dependencyName);
        $setterDependency = sprintf('%s\%s', static::TYPE_SETTER_INJECTION, $requiredDependency);

        return $this->textEndsWith($dependencyName, $setterDependency);
    }

    protected function isFactoryInjection(string $dependencyName): bool
    {
        return $this->textEndsWith($dependencyName, static::DEPENDENCY_SETTER_FACTORY);
    }

    /**
     * @param string $receiverClassName
     * @param object $ReceiverInstance
     *
     * @return void
     * @throws \Everon\Component\Factory\Exception\UndefinedContainerDependencyException
     * @throws \Everon\Component\Factory\Exception\UndefinedDependencySetterException
     * @throws \Everon\Component\Factory\Exception\InstanceIsNotObjectException
     */
    public function inject(string $receiverClassName, object $ReceiverInstance): void
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
     * @param string $receiverClassName
     * @param object $ReceiverInstance
     *
     * @return void
     * @throws \Everon\Component\Factory\Exception\UndefinedContainerDependencyException
     * @throws \Everon\Component\Factory\Exception\UndefinedDependencySetterException
     * @throws \Everon\Component\Factory\Exception\InstanceIsNotObjectException
     */
    public function injectOnce(string $receiverClassName, object $ReceiverInstance): void
    {
        if ($this->isInjected($receiverClassName)) {
            return;
        }

        $this->inject($receiverClassName, $ReceiverInstance);
    }

    public function register(string $name, \Closure $ServiceClosure): void
    {
        if ($this->isRegistered($name)) {
            throw new DependencyServiceAlreadyRegisteredException($name);
        }

        $this->getServiceDefinitionCollection()->set($name, $ServiceClosure);

        $this->getServiceCollection()->remove($name);
    }

    /**
     * @param string $name
     * @param \Closure $ServiceClosure
     *
     * @return void
     * @throws \Everon\Component\Factory\Exception\DependencyServiceAlreadyRegisteredException
     */
    public function propose(string $name, \Closure $ServiceClosure): void
    {
        if ($this->isRegistered($name)) {
            return;
        }

        $this->register($name, $ServiceClosure);
    }

    public function resolve(string $name)
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

    public function isFactoryRequired(string $className): bool
    {
        return $this->getRequireFactoryCollection()->has($className);
    }

    public function isInjected(string $className): bool
    {
        return $this->getInjectedCollection()->has($className);
    }

    public function isRegistered(string $name): bool
    {
        return ($this->getServiceDefinitionCollection()->has($name) || $this->getServiceCollection()->has($name));
    }

    public function getServiceDefinitionCollection(): CollectionInterface
    {
        if ($this->ServiceDefinitionCollection === null) {
            $this->ServiceDefinitionCollection = new Collection([]);
        }

        return $this->ServiceDefinitionCollection;
    }

    public function getClassDependencyCollection(): CollectionInterface
    {
        if ($this->ClassDependencyCollection === null) {
            $this->ClassDependencyCollection = new Collection([]);
        }

        return $this->ClassDependencyCollection;
    }

    public function getServiceCollection(): CollectionInterface
    {
        if ($this->ServiceCollection === null) {
            $this->ServiceCollection = new Collection([]);
        }

        return $this->ServiceCollection;
    }

    public function getRequireFactoryCollection(): CollectionInterface
    {
        if ($this->RequireFactoryCollection === null) {
            $this->RequireFactoryCollection = new Collection([]);
        }

        return $this->RequireFactoryCollection;
    }

    public function getInjectedCollection(): CollectionInterface
    {
        if ($this->InjectedCollection === null) {
            $this->InjectedCollection = new Collection([]);
        }

        return $this->InjectedCollection;
    }
}

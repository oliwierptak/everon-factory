<?php declare(strict_types = 1);
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit\Doubles;

use Everon\Component\Factory\AbstractWorker;

class StubFactoryWorker extends AbstractWorker
{
    /**
     * @return \Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function buildFuzz(): FuzzStub
    {
        $FooStub = $this->buildFoo();

        $FuzzStub = new FuzzStub($FooStub);
        $this->getFactory()->injectDependencies(FuzzStub::class, $FuzzStub);

        return $FuzzStub;
    }

    /**
     * @return \Everon\Component\Factory\Tests\Unit\Doubles\FooStub
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function buildFoo(): FooStub
    {
        $FooStub = new FooStub();
        $this->getFactory()->injectDependencies(FooStub::class, $FooStub);

        return $FooStub;
    }

    /**
     * @param mixed $anotherArgument
     * @param array $data
     *
     * @return \Everon\Component\Factory\Tests\Unit\Doubles\BarStub
     * @throws \Everon\Component\Factory\Exception\UndefinedContainerDependencyException
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function buildBar($anotherArgument, array $data): BarStub
    {
        //$LoggerStub = $this->getFactory()->buildWithEmptyConstructor('LoggerStub', $namespace);
        $LoggerStub = $this->getFactory()->getDependencyContainer()->resolve('Logger');

        $BarStub = new BarStub($LoggerStub, $anotherArgument, $data);
        $this->getFactory()->injectDependencies(BarStub::class, $BarStub);

        return $BarStub;
    }

    /**
     * @return \Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function buildLogger(): LoggerStub
    {
        $LoggerStub = new LoggerStub();
        $this->getFactory()->injectDependencies(LoggerStub::class, $LoggerStub);

        return $LoggerStub;
    }

}

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

use Everon\Component\Factory\Factory;

class FactoryStub extends Factory
{
    /**
     * @param \Everon\Component\Factory\Tests\Unit\Doubles\FooStub $FooStub
     *
     * @return \Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function buildFuzz(FooStub $FooStub): FuzzStub
    {
        $FuzzStub = new FuzzStub($FooStub);
        $this->injectDependencies(FuzzStub::class, $FuzzStub);

        return $FuzzStub;
    }

    /**
     * @return \Everon\Component\Factory\Tests\Unit\Doubles\FooStub
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function buildFoo(): FooStub
    {
        $FooStub = new FooStub();
        $this->injectDependencies(FooStub::class, $FooStub);

        return $FooStub;
    }

    /**
     * @param \Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub $LoggerStub
     * @param $anotherArgument
     * @param array $data
     *
     * @return \Everon\Component\Factory\Tests\Unit\Doubles\BarStub
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function buildBar(LoggerStub $LoggerStub, $anotherArgument, array $data): BarStub
    {
        $BarStub = new BarStub($LoggerStub, $anotherArgument, $data);
        $this->injectDependencies(BarStub::class, $BarStub);

        return $BarStub;
    }

    /**
     * @return \Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub
     * @throws \Everon\Component\Factory\Exception\FailedToInjectDependenciesException
     */
    public function buildLogger(): LoggerStub
    {
        $LoggerStub = new LoggerStub();
        $this->injectDependencies(LoggerStub::class, $LoggerStub);

        return $LoggerStub;
    }

}

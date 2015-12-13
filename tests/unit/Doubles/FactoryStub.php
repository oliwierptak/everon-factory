<?php
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
     * @param string $namespace
     *
     * @return FuzzStub
     */
    public function buildFuzz(FooStub $FooStub, $namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->buildWithConstructorParameters('FuzzStub', $namespace, $this->buildParameterCollection([
            $FooStub,
        ]));
    }

    /**
     * @param string $namespace
     *
     * @return FooStub
     */
    public function buildFoo($namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->buildWithEmptyConstructor('FooStub', $namespace);
    }

    /**
     * @param string $namespace
     *
     * @return BarStub
     */
    public function buildBar(LoggerStub $LoggerStub, $anotherArgument, array $data, $namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->buildWithConstructorParameters('BarStub', $namespace, $this->buildParameterCollection([
            $LoggerStub,
            $anotherArgument,
            $data,
        ]));
    }

    public function buildLogger($namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->buildWithEmptyConstructor('LoggerStub', $namespace);
    }

}
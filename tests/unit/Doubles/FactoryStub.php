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
     * @return FuzzStub
     */
    public function buildFuzz(FooStub $FooStub)
    {
        $FuzzStub = new FuzzStub($FooStub);
        $this->injectDependencies(FuzzStub::class, $FuzzStub);

        return $FuzzStub;
    }

    /**
     * @return FooStub
     */
    public function buildFoo()
    {
        $FooStub = new FooStub();
        $this->injectDependencies(FooStub::class, $FooStub);

        return $FooStub;
    }

    /**
     * @return BarStub
     */
    public function buildBar(LoggerStub $LoggerStub, $anotherArgument, array $data)
    {
        $BarStub = new BarStub($LoggerStub, $anotherArgument, $data);
        $this->injectDependencies(BarStub::class, $BarStub);

        return $BarStub;
    }

    /**
     * @return LoggerStub
     */
    public function buildLogger()
    {
        $LoggerStub = new LoggerStub();
        $this->injectDependencies(LoggerStub::class, $LoggerStub);

        return $LoggerStub;
    }

}

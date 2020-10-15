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
     * @return FuzzStub
     */
    public function buildFuzz()
    {
        $FooStub = $this->buildFoo();

        $FuzzStub = new FuzzStub($FooStub);
        $this->getFactory()->injectDependencies(FuzzStub::class, $FuzzStub);

        return $FuzzStub;
    }

    /**
     * @return FooStub
     */
    public function buildFoo()
    {
        $FooStub = new FooStub();
        $this->getFactory()->injectDependencies(FooStub::class, $FooStub);

        return $FooStub;
    }

    /**
     * @return BarStub
     */
    public function buildBar($anotherArgument, array $data)
    {
        //$LoggerStub = $this->getFactory()->buildWithEmptyConstructor('LoggerStub', $namespace);
        $LoggerStub = $this->getFactory()->getDependencyContainer()->resolve('Logger');

        $BarStub = new BarStub($LoggerStub, $anotherArgument, $data);
        $this->getFactory()->injectDependencies(BarStub::class, $BarStub);

        return $BarStub;
    }

    /**
     * @return LoggerStub
     */
    public function buildLogger()
    {
        $LoggerStub = new LoggerStub();
        $this->getFactory()->injectDependencies(LoggerStub::class, $LoggerStub);

        return $LoggerStub;
    }

}

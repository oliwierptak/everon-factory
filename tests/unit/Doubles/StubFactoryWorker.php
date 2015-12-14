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

use Everon\Component\Factory\AbstractWorker;

class StubFactoryWorker extends AbstractWorker
{

    /**
     * @inheritdoc
     */
    protected function registerBeforeWork()
    {
        $this->registerWorker('StubFactoryWorker', function () {
            return $this->getFactory()->getWorkerByName('Stub', 'Everon\Component\Factory\Tests\Unit\Doubles');
        });
    }

    /**
     * @param string $namespace
     *
     * @return FuzzStub
     */
    public function buildFuzz($namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        $FooStub = $this->buildFoo($namespace);

        return $this->getFactory()->buildWithConstructorParameters('FuzzStub', $namespace, $this->getFactory()->buildParameterCollection([
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
        return $this->getFactory()->buildWithEmptyConstructor('FooStub', $namespace);
    }

    /**
     * @param string $namespace
     *
     * @return BarStub
     */
    public function buildBar($anotherArgument, array $data, $namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        //$LoggerStub = $this->getFactory()->buildWithEmptyConstructor('LoggerStub', $namespace);
        $LoggerStub = $this->getFactory()->getDependencyContainer()->resolve('Logger');

        return $this->getFactory()->buildWithConstructorParameters('BarStub', $namespace,
            $this->getFactory()->buildParameterCollection([
                $LoggerStub,
                $anotherArgument,
                $data,
            ])
        );
    }

    /**
     * @param string $namespace
     *
     * @return LoggerStub
     */
    public function buildLogger($namespace = 'Everon\Component\Factory\Tests\Unit\Doubles')
    {
        return $this->getFactory()->buildWithEmptyConstructor('LoggerStub', $namespace);
    }

}

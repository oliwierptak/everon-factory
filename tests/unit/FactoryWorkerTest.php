<?php
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit;

use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\FactoryInterface;
use Everon\Component\Factory\Tests\Unit\Doubles\StubFactoryWorker;
use Everon\Component\Utils\TestCase\MockeryTest;
use Mockery;
use Mockery\MockInterface;

class FactoryWorkerTest extends MockeryTest
{

    /**
     * @var StubFactoryWorker
     */
    protected $FactoryWorker;

    protected function setUp()
    {
        $Factory = Mockery::mock('Everon\Component\Factory\FactoryInterface');

        /* @var FactoryInterface $Factory */
        $this->FactoryWorker = new StubFactoryWorker($Factory);
    }

    public function test_build_with_empty_constructor()
    {
        $LoggerMock = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub');

        $Factory = $this->FactoryWorker->getFactory();
        /* @var MockInterface $Factory */
        $Factory->shouldReceive('buildWithEmptyConstructor')->times(1)
            ->with('LoggerStub', 'Everon\Component\Factory\Tests\Unit\Doubles')
            ->andReturn($LoggerMock);

        $LoggerStub = $this->FactoryWorker->buildLogger();

        $this->assertInstanceOf(get_class($LoggerStub), $LoggerMock);
    }

    public function test_build_with_same_instance_of_logger()
    {
        $BarStubMock = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\BarStub');
        $LoggerStub = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub');
        $CollectionParameters = Mockery::mock('Everon\Component\Collection\CollectionInterface');

        $parameters = [
            $LoggerStub,
            'anotherArgument', [
                'some' => 'data',
            ],
        ];

        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');
        $Container->shouldReceive('resolve')->times(1)
            ->with('Logger')
            ->andReturn($LoggerStub);

        $Factory = $this->FactoryWorker->getFactory();
        /* @var MockInterface $Factory */
        $Factory->shouldReceive('buildParameterCollection')->times(1)
            ->with($parameters)
            ->andReturn($CollectionParameters);

        $Factory->shouldReceive('buildWithEmptyConstructor')
            ->times(0) //this should never be called, we use same instance of Logger
            ->with('LoggerStub', 'Everon\Component\Factory\Tests\Unit\Doubles')
            ->andReturn($LoggerStub);

        $Factory->shouldReceive('buildWithConstructorParameters')->times(1)
            ->with('BarStub', 'Everon\Component\Factory\Tests\Unit\Doubles', $CollectionParameters)
            ->andReturn($BarStubMock);

        $Factory->shouldReceive('getDependencyContainer')->times(1)
            ->andReturn($Container);

        /* @var CollectionInterface $CollectionParameters */
        $BarStub = $this->FactoryWorker->buildBar('anotherArgument', [
            'some' => 'data',
        ], 'Everon\Component\Factory\Tests\Unit\Doubles');

        $this->assertInstanceOf(get_class($BarStub), $BarStubMock);
    }

    public function test_do_work()
    {
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');

        $Factory = $this->FactoryWorker->getFactory();
        /* @var MockInterface $Factory */
        $Factory->shouldReceive('getDependencyContainer')->times(1)
            ->andReturn($Container);
        $Container->shouldReceive('propose')->times(1)
            ->andReturn($this->FactoryWorker);

        $result = $this->FactoryWorker->doWork();

        $this->assertNull($result);
    }

}

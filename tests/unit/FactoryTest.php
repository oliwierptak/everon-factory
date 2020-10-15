<?php declare(strict_types = 1);
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit;

use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Factory;
use Everon\Component\Factory\Tests\Unit\Doubles\BarStub;
use Everon\Component\Factory\Tests\Unit\Doubles\StubFactoryWorker;
use Everon\Component\Utils\TestCase\MockeryTest;
use Mockery;
use Mockery\MockInterface;

class FactoryTest extends MockeryTest
{

    public function test_inject_dependencies_and_require_factory(): void
    {
        /* @var ContainerInterface $Container */
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');
        $Factory = new Factory($Container);

        $Fuzz = Mockery::mock(
            'Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub, Everon\Component\Factory\Dependency\FactoryAwareInterface'
        );

        $Fuzz->shouldReceive('setFactory')->times(1)->with($Factory);

        /* @var MockInterface $Container */
        $Container->shouldReceive('inject')->times(1);

        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub')
            ->andReturn(true);

        $Factory->injectDependencies('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub', $Fuzz);
    }

    public function test_inject_dependencies_without_factory(): void
    {
        /* @var ContainerInterface $Container */
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');
        $Factory = new Factory($Container);

        $Fuzz = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub');

        /* @var MockInterface $Container */
        $Container->shouldReceive('inject')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub')
            ->andReturn(false);

        $Factory->injectDependencies('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub', $Fuzz);
    }

    public function test_inject_dependency_once(): void
    {
        /* @var ContainerInterface $Container */
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');
        $Factory = new Factory($Container);

        $LoggerStub = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub');

        /* @var MockInterface $Container */
        $Container->shouldReceive('injectOnce')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with(BarStub::class)
            ->andReturn(false);

        $Factory->injectDependenciesOnce(BarStub::class, $LoggerStub);
    }

    public function test_getWorkerByName(): void
    {
        /* @var ContainerInterface $Container */
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');
        $Factory = new Factory($Container);

        $FactoryWorker = Mockery::mock('Everon\Component\Factory\FactoryWorkerInterface');

        /* @var MockInterface $Container */
        $Container->shouldReceive('resolve')->times(1)->with('StubFactoryWorker')->andReturn($FactoryWorker);

        $Worker = $Factory->getWorkerByName('StubFactoryWorker');

        $this->assertInstanceOf('Everon\Component\Factory\FactoryWorkerInterface', $Worker);
    }

    public function test_getWorkerByName_should_throw_exception_when_wrong_worker_name(): void
    {
        $this->expectException(\Everon\Component\Factory\Exception\UndefinedFactoryWorkerException::class);
        $this->expectExceptionMessage('Undefined Factory Worker "StubFactoryWorker"');

        /* @var ContainerInterface $Container */
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');
        $Factory = new Factory($Container);

        /* @var MockInterface $Container */
        $Container->shouldReceive('resolve')->times(1)->with('StubFactoryWorker')->andReturn(null);

        $Worker = $Factory->getWorkerByName('StubFactoryWorker');

        $this->assertInstanceOf('Everon\Component\Factory\FactoryWorkerInterface', $Worker);
    }

    public function test_buildWorker(): void
    {
        /* @var ContainerInterface $Container */
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');
        $Factory = new Factory($Container);

        /* @var MockInterface $Container */
        $Container->shouldReceive('inject')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with(StubFactoryWorker::class)
            ->andReturn(false);

        $Worker = $Factory->buildWorker(StubFactoryWorker::class);

        $this->assertInstanceOf('Everon\Component\Factory\FactoryWorkerInterface', $Worker);
    }

    public function test_class_exists_should_throw_exception(): void
    {
        $this->expectException(\Everon\Component\Factory\Exception\UndefinedClassException::class);
        $this->expectExceptionMessage('Undefined class "Foo32847822ffs"');

        /* @var ContainerInterface $Container */
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');
        $Factory = new Factory($Container);

        $Factory->classExists('Foo32847822ffs');
    }

}

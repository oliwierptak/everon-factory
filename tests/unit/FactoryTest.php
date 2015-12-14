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

use Everon\Component\Collection\Collection;
use Everon\Component\Collection\CollectionInterface;
use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Factory;
use Everon\Component\Utils\TestCase\MockeryTest;
use Mockery;
use Mockery\MockInterface;
use Everon\Component\Factory\Tests\Unit\Doubles\FactoryStub;

class FactoryTest extends MockeryTest
{

    /**
     * @var FactoryStub
     */
    protected $Factory;

    protected function setUp()
    {
        $Container = Mockery::mock('Everon\Component\Factory\Dependency\ContainerInterface');

        /* @var ContainerInterface $Container */
        $this->Factory = new Factory($Container);
    }

    public function test_inject_dependencies_and_require_factory()
    {
        $Fuzz = Mockery::mock(
            'Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub, Everon\Component\Factory\Dependency\FactoryAwareInterface'
        );

        $Fuzz->shouldReceive('setFactory')->times(1)->with($this->Factory);

        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1);

        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub')
            ->andReturn(true);

        $this->Factory->injectDependencies('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub', $Fuzz);
    }

    public function test_inject_dependencies_without_factory()
    {
        $Fuzz = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub');

        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub')
            ->andReturn(false);

        $this->Factory->injectDependencies('Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub', $Fuzz);
    }

    public function test_build_with_empty_constructor()
    {
        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub')
            ->andReturn(false);

        $LoggerStub = $this->Factory->buildWithEmptyConstructor('LoggerStub', 'Everon\Component\Factory\Tests\Unit\Doubles');

        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub', $LoggerStub);
    }

    public function test_build_with_constructor_parameters()
    {
        $LoggerStub = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub');

        $CollectionParameters = Mockery::mock('Everon\Component\Collection\CollectionInterface');
        $CollectionParameters->shouldReceive('toArray')->times(1)->andReturn([
            $LoggerStub,
            'argument', [
                'some' => 'data',
            ],
        ]);

        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\BarStub')
            ->andReturn(false);

        /* @var CollectionInterface $CollectionParameters */
        $BarStub = $this->Factory->buildWithConstructorParameters('BarStub',
            'Everon\Component\Factory\Tests\Unit\Doubles',
            $CollectionParameters
        );

        $this->assertInstanceOf('Everon\Component\Factory\Tests\Unit\Doubles\BarStub', $BarStub);
    }

    public function test_inject_dependency_once()
    {
        $LoggerStub = Mockery::mock('Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub');

        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('injectOnce')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\BarStub')
            ->andReturn(false);

        $this->Factory->injectDependenciesOnce('Everon\Component\Factory\Tests\Unit\Doubles\BarStub', $LoggerStub);
    }

    public function test_getWorkerByName()
    {
        $FactoryWorker = Mockery::mock('Everon\Component\Factory\FactoryWorkerInterface');

        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\StubFactoryWorker')
            ->andReturn(false);

        $Container->shouldReceive('propose')->times(1)
            ->andReturn($FactoryWorker);

        $Worker = $this->Factory->getWorkerByName('Stub', 'Everon\Component\Factory\Tests\Unit\Doubles');

        $this->assertInstanceOf('Everon\Component\Factory\FactoryWorkerInterface', $Worker);
    }

    public function test_getWorkerByName_should_use_cache()
    {
        $FactoryWorker = Mockery::mock('Everon\Component\Factory\FactoryWorkerInterface');

        /** @var MockInterface $Container */
        $Container = $this->Factory->getDependencyContainer();
        $Container->shouldReceive('inject')->times(1);
        $Container->shouldReceive('isFactoryRequired')
            ->times(1)
            ->with('Everon\Component\Factory\Tests\Unit\Doubles\StubFactoryWorker')
            ->andReturn(false);

        $Container->shouldReceive('propose')->times(1)
            ->andReturn($FactoryWorker);

        $Worker = $this->Factory->getWorkerByName('Stub', 'Everon\Component\Factory\Tests\Unit\Doubles');
        $Worker = $this->Factory->getWorkerByName('Stub', 'Everon\Component\Factory\Tests\Unit\Doubles');

        $this->assertInstanceOf('Everon\Component\Factory\FactoryWorkerInterface', $Worker);
    }

    /**
     * @expectedException \Everon\Component\Factory\Exception\UndefinedClassException
     * @expectedExceptionMessage Undefined class "Foo32847822ffs"
     */
    public function test_class_exists_should_throw_exception()
    {
        $this->Factory->classExists('Foo32847822ffs');
    }

    /**
     * @expectedException \Everon\Component\Factory\Exception\InstanceIsAbstractClassException
     * @expectedExceptionMessage Cannot instantiate abstract class "Everon\Component\Factory\Tests\Unit\Doubles\AbstractStub"
     */
    public function test_buildWithEmptyConstructor_instantiate_abstract_class_should_throw_exception()
    {
        $AbstractStub = $this->Factory->buildWithEmptyConstructor('AbstractStub', 'Everon\Component\Factory\Tests\Unit\Doubles');
    }

    /**
     * @expectedException \Everon\Component\Factory\Exception\InstanceIsAbstractClassException
     * @expectedExceptionMessage Cannot instantiate abstract class "Everon\Component\Factory\Tests\Unit\Doubles\AbstractStub"
     */
    public function test_buildWithConstructorParameters_instantiate_abstract_class_should_throw_exception()
    {
        $AbstractStub = $this->Factory->buildWithConstructorParameters('AbstractStub',
            'Everon\Component\Factory\Tests\Unit\Doubles',
            new Collection([])
        );
    }

}

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

use Everon\Component\Factory\Dependency\Container;
use Everon\Component\Factory\Dependency\ContainerInterface;
use Everon\Component\Factory\Tests\Unit\Doubles\BarStub;
use Everon\Component\Factory\Tests\Unit\Doubles\FactoryStub;
use Everon\Component\Factory\Tests\Unit\Doubles\FooStub;
use Everon\Component\Factory\Tests\Unit\Doubles\FuzzStub;
use Everon\Component\Factory\Tests\Unit\Doubles\LoggerStub;
use Everon\Component\Utils\TestCase\MockeryTest;

class DependencyContainerTest extends MockeryTest
{
    /**
     * @var ContainerInterface
     */
    protected $Container;

    /**
     * @var FactoryStub
     */
    protected $Factory;

    protected function setUp(): void
    {
        $this->Container = new Container();
        $this->Factory = new FactoryStub($this->Container);
        $Factory = $this->Factory;
        $Container = $this->Container;

        /* Everything used with resolve() must be registered with register() or propose() */

        $this->Container->register(
            'Logger',
            function () use ($Factory) {
                return $Factory->buildLogger();
            }
        );

        $this->Container->register(
            'Fuzz',
            function () use ($Factory) {
                /* FuzzStub requires constructor injection of Foo
                    creates always new instance of Foo */

                $FooStub = $Factory->buildFoo();

                return $Factory->buildFuzz($FooStub);
            }
        );

        $this->Container->register(
            'Foo',
            function () use ($Factory) {
                /* FooStub requires setter injection of Bar
                    Bar requires constructor injection of Logger */

                return $Factory->buildFoo();
            }
        );

        $this->Container->register(
            'Bar',
            function () use ($Factory, $Container) {
                /* BarStub requires constructor injection of $Logger
                    uses same Logger instance that would be injected via setter injection */

                $Logger = $Container->resolve('Logger');

                return $Factory->buildBar(
                    $Logger,
                    'argument',
                    [
                        'some' => 'data',
                    ]
                );
            }
        );
    }

    public function test_setter_dependency_injection_one_logger_instance(): void
    {
        $Foo = new FooStub();

        $this->Container->inject(get_class($Foo), $Foo);

        $this->assertInstanceOf(BarStub::class, $Foo->getBar());
        $this->assertInstanceOf(LoggerStub::class, $Foo->getBar()->getLogger());

        $this->assertEquals($Foo->getLogger(), $Foo->getBar()->getLogger());
    }

    public function test_setter_dependency_should_be_injected_many_times(): void
    {
        $FooStub = new FooStub();
        $Fuzz = new FuzzStub($FooStub);
        $FooStubDupe = new FooStub();
        $FuzzDupe = new FuzzStub($FooStubDupe);

        $this->Container->inject(get_class($Fuzz), $Fuzz);
        $this->assertEquals($FooStub, $Fuzz->getFoo());

        $this->Container->inject(get_class($Fuzz), $FuzzDupe);
        $this->assertNotEquals($FooStub, $FuzzDupe->getFoo());
    }

    public function test_setter_dependency_should_only_be_injected_once(): void
    {
        $FooStub = new FooStub();
        $FooStubDupe = new FooStub();
        $Fuzz = new FuzzStub($FooStub);

        $this->Container->injectOnce(get_class($Fuzz), $Fuzz);
        $this->assertEquals($FooStub, $Fuzz->getFoo());

        $this->Container->injectOnce(get_class($FooStub), $FooStub);
        $this->Container->injectOnce(get_class($FooStub), $FooStubDupe);

        $this->assertEquals($FooStub, $Fuzz->getFoo());
    }

    public function test_service_only_registers_once(): void
    {
        $this->expectException(\Everon\Component\Factory\Exception\DependencyServiceAlreadyRegisteredException::class);
        $this->expectExceptionMessage('Dependency service "Fuzz" is already registered');

        $Factory = $this->Factory;

        $this->Container->register(
            'Fuzz',
            function () use ($Factory) {
                $FooStub = $Factory->buildFoo();

                return $Factory->buildFuzz($FooStub);
            }
        );
    }

    public function test_propose_should_register_when_not_yet_registered(): void
    {
        $Factory = $this->Factory;

        $this->Container->propose(
            'Foo34343',
            function () use ($Factory) {
                return $Factory->buildFoo();
            }
        );

        $this->assertInstanceOf(
            FooStub::class,
            $this->Container->resolve('Foo34343')
        );
    }

    public function test_propose_should_not_throw_exception_when_service_already_registered(): void
    {
        $Factory = $this->Factory;

        $this->Container->propose(
            'Foo',
            function () use ($Factory) {
                return $Factory->buildFoo();
            }
        );

        $this->Container->propose(
            'Foo',
            function () {
            }
        );

        $this->assertInstanceOf(
            FooStub::class,
            $this->Container->resolve('Foo')
        );
    }

    public function test_inject_should_throw_exception_when_not_object(): void
    {
        $this->expectException(\Everon\Component\Factory\Exception\InstanceIsNotObjectException::class);
        $this->expectExceptionMessage('Instance is not object');

        $this->Container->inject('Foo2344db', 'foobar');
    }

    public function test_resolve_should_throw_exception(): void
    {
        $this->expectException(\Everon\Component\Factory\Exception\UndefinedContainerDependencyException::class);
        $this->expectExceptionMessage('Undefined container dependency "Foo2344db"');

        $this->Container->resolve('Foo2344db');
    }

    public function test_resolve_should_use_cache(): void
    {
        $Logger = $this->Container->resolve('Logger');

        $this->assertTrue($this->Container->getServiceCollection()->has('Logger'));
    }

    public function test_require_factory(): void
    {
        $Fuzz = new FuzzStub(new FooStub());
        $this->Container->inject(FuzzStub::class, $Fuzz);

        $this->assertTrue($this->Container->isFactoryRequired(FuzzStub::class));
    }
}

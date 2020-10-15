<?php declare(strict_types = 1);
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit\Doubles\Dependency;

use Everon\Component\Factory\Tests\Unit\Doubles\FooStub;

trait Foo
{

    /**
     * @var FooStub
     */
    protected $Foo;

    public function getFoo(): FooStub
    {
        return $this->Foo;
    }

    public function setFoo(FooStub $Foo)
    {
        $this->Foo = $Foo;
    }

}

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

use Everon\Component\Factory\Tests\Unit\Doubles\BarStub;

trait Bar
{

    /**
     * @var BarStub
     */
    protected $Bar;

    /**
     * @return BarStub
     */
    public function getBar()
    {
        return $this->Bar;
    }

    /**
     * @param BarStub $Bar
     */
    public function setBar(BarStub $Bar)
    {
        $this->Bar = $Bar;
    }

}

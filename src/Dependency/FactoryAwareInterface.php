<?php
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Dependency;

use Everon\Component\Factory\FactoryInterface;

interface FactoryAwareInterface
{

    /**
     * @return FactoryInterface
     */
    public function getFactory(): FactoryInterface;

    /**
     * @param FactoryInterface $Factory
     */
    public function setFactory(FactoryInterface $Factory);

}

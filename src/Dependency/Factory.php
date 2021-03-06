<?php declare(strict_types = 1);
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

trait Factory
{

    /**
     * @var \Everon\Component\Factory\FactoryInterface
     */
    protected $Factory;

    public function getFactory(): FactoryInterface
    {
        return $this->Factory;
    }

    public function setFactory(FactoryInterface $Factory): void
    {
        $this->Factory = $Factory;
    }

}

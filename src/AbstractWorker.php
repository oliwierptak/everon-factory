<?php declare(strict_types = 1);
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory;

abstract class AbstractWorker implements FactoryWorkerInterface
{

    use Dependency\Factory;

    /**
     * @param FactoryInterface $Factory
     */
    public function __construct(FactoryInterface $Factory)
    {
        $this->Factory = $Factory;
    }

}

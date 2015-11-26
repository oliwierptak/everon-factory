<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory;


abstract class AbstractWorker implements FactoryWorkerInterface
{
    use Dependency\Factory;
    
    public function __construct(FactoryInterface $Factory)
    {
        $this->Factory = $Factory;
    }

}

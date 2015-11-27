<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Dependency;

use Everon\Component\Factory\FactoryWorkerInterface;

interface FactoryWorkerDependencyInterface
{
    /**
     * @return FactoryWorkerInterface
     */
    public function getFactoryWorker();

    /**
     * @param FactoryWorkerInterface $FactoryWorker
     */
    public function setFactory(FactoryWorkerInterface $FactoryWorker);

}
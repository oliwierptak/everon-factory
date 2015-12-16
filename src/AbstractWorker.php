<?php
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
     * @return void
     */
    abstract protected function registerBeforeWork();

    /**
     * @param FactoryInterface $Factory
     */
    public function __construct(FactoryInterface $Factory)
    {
        $this->Factory = $Factory;
    }

    /**
     * <code>
     *   $this->registerWorker('StubFactoryWorker', function () {
     *       return $this->getFactory()->getWorkerByName('Stub', 'Everon\Component\Factory\Tests\Unit\Doubles');
     *   });
     * </code>
     *
     *
     * @param string $name
     * @param \Closure $callback FactoryWorkerInterface should be returned
     *
     * @return void
     */
    protected function registerWorker(string $name, \Closure $callback)
    {
        $this->getFactory()->getDependencyContainer()->propose($name, $callback);
    }

    /**
     * @inheritdoc
     */
    public function doWork()
    {
        $this->registerBeforeWork();
    }

}

<?php declare(strict_types = 1);
/**
 * This file is part of the Everon components.
 *
 * (c) Oliwier Ptak <everonphp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Tests\Unit\Doubles;

class BarStub
{

    use Dependency\Logger;

    /**
     * @var string
     */
    protected $anotherArgument;

    /**
     * @var array
     */
    protected $data = [];

    public function __construct(LoggerStub $LoggerStub, $anotherArgument = 'anotherArgument', array $data=[])
    {
        $this->Logger = $LoggerStub;
        $this->anotherArgument = $anotherArgument;
        $this->data = $data;
    }

    public function getAnotherArgument(): string
    {
        return $this->anotherArgument;
    }

    public function setAnotherArgument(string $anotherArgument)
    {
        $this->anotherArgument = $anotherArgument;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

}

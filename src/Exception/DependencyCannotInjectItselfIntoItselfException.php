<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <EveronFramework@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Component\Factory\Exception;

use Everon\Component\Utils\Exception\AbstractException;

class DependencyCannotInjectItselfIntoItselfException extends AbstractException
{
    protected $message = 'Dependency "%s" cannot inject itself into itself';
}
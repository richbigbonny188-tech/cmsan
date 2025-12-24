<?php
/* --------------------------------------------------------------
 LeagueInflector.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\DependencyInjection\Abstraction;

use Gambio\Core\Application\DependencyInjection\Inflector;
use League\Container\Inflector\InflectorInterface;

/**
 * Class LeagueInflector
 *
 * @package Gambio\Core\Application\DependencyInjection\Abstraction
 */
class LeagueInflector implements Inflector
{
    /**
     * @var InflectorInterface
     */
    private $internal;
    
    
    /**
     * LeagueInflector constructor.
     *
     * @param InflectorInterface $internal
     */
    public function __construct(InflectorInterface $internal)
    {
        $this->internal = $internal;
    }
    
    
    /**
     * @inheritDoc
     */
    public function invokeMethod(string $name, array $args): void
    {
        $this->internal = $this->internal->invokeMethod($name, $args);
    }
}

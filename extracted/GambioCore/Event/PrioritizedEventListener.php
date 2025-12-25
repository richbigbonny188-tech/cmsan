<?php
/* --------------------------------------------------------------
   PrioritizedEventListener.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Event;

/**
 * Interface PrioritizedEventListener
 *
 * @package Gambio\Core\Event
 */
interface PrioritizedEventListener
{
    public const PRIORITY_VERY_HIGH = 1000000;
    public const PRIORITY_HIGH      = 100000;
    public const PRIORITY_NORMAL    = 10000;
    public const PRIORITY_LOW       = 1000;
    public const PRIORITY_VERY_LOW  = 100;
    
    
    /**
     * Returns the priority of this event listener. The higher the returned value, the earlier it will be executed.
     *
     * @return int
     */
    public function priority(): int;
}
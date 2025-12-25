<?php
/* --------------------------------------------------------------
 GroupCheckUpdateEvent.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Model\Events;

/**
 * Class GroupCheckUpdateEvent
 * @package Gambio\Core\Configuration\Model\Events
 *
 * @codeCoverageIgnore
 */
class GroupCheckUpdated
{
    /**
     * @var string
     */
    private $newValue;
    
    
    /**
     * GroupCheckUpdateEvent constructor.
     *
     * @param string $newValue
     */
    public function __construct(string $newValue)
    {
        $this->newValue = $newValue;
    }
    
    
    /**
     * @return string
     */
    public function newValue(): string
    {
        return $this->newValue;
    }
}
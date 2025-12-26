<?php
/* --------------------------------------------------------------
 PreferredPeriod.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

use Gambio\Admin\Modules\DashboardStatistics\Model\Exceptions\InvalidPreferredPeriodException;
use JsonSerializable;

class PreferredPeriod implements JsonSerializable
{
    /**
     * @var string
     */
    private $period;
    
    /**
     * @var string[]
     */
    private $periods = [
        'today',
        'thisweek',
        'last7days',
        'thismonth',
        'last28days',
        'thisyear',
        'last12months'
    ];
    
    
    /**
     * Constructor.
     */
    public function __construct(string $period)
    {
        if (!in_array($period, $this->periods)) {
            throw InvalidPreferredPeriodException::forPeriod($period);
        }
        
        $this->period = $period;
    }
    
    
    /**
     * Return value.
     */
    public function value(): string
    {
        return $this->period;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): string
    {
        return $this->value();
    }
}
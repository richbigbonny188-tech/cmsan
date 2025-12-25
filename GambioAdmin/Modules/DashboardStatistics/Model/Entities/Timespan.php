<?php
/*--------------------------------------------------------------
  Timespan.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Entities;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

class Timespan
{
    /**
     * @var DateTimeImmutable
     */
    protected $startDate;
    
    /**
     * @var DateTimeImmutable
     */
    protected $endDate;
    
    /**
     * @var DateTimeImmutable
     */
    protected $comparisonStartDate;
    
    /**
     * @var DateTimeImmutable
     */
    protected $comparisonEndDate;
    
    /**
     * @var DateTimeImmutable
     */
    protected $totalsStartDate;
    
    /**
     * @var DateTimeImmutable
     */
    protected $totalsEndDate;
    
    /**
     * @var DateTimeImmutable
     */
    protected $comparisonTotalsStartDate;
    
    /**
     * @var DateTimeImmutable
     */
    protected $comparisonTotalsEndDate;
    
    /**
     * @var string
     */
    private $name;
    
    
    /**
     * Constructor.
     */
    public function __construct(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $name = '',
        ?DateTimeImmutable $totalsStartDate = null,
        ?DateTimeImmutable $totalsEndDate = null
    ) {
        if ($startDate > $endDate) {
            throw new InvalidArgumentException('The start date cannot be greater than the end date');
        }
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        
        switch ($name) {
            case 'thisweek':
            case 'last7days':
                $interval       = '7 days';
                $totalsInterval = '7 days';
                break;
            case 'last28days':
                $interval       = '28 days';
                $totalsInterval = '28 days';
                break;
            case 'thismonth':
                $interval       = '1 month';
                $totalsInterval = '1 month';
                break;
            case 'thisyear':
            case 'last12months':
                $interval       = '1 year';
                $totalsInterval = '1 year';
                break;
            case 'today':
            default:
                $interval       = '7 days';
                $totalsInterval = '1 day';
                break;
        }
        
        $this->comparisonStartDate       = $startDate->sub(DateInterval::createFromDateString($interval));
        $this->comparisonEndDate         = $startDate->sub(DateInterval::createFromDateString('1 usec'));
        $this->name                      = $name;
        $this->totalsStartDate           = $totalsStartDate ?? $startDate;
        $this->totalsEndDate             = $totalsEndDate ?? $endDate;
        $this->comparisonTotalsStartDate = $this->totalsStartDate->sub(DateInterval::createFromDateString($totalsInterval));
        $this->comparisonTotalsEndDate   = $this->totalsStartDate->sub(DateInterval::createFromDateString('1 usec'));
    }
    
    
    /**
     * Return time span start.
     */
    public function startDate(): DateTimeImmutable
    {
        return $this->startDate;
    }
    
    
    /**
     * Return time span end.
     */
    public function endDate(): DateTimeImmutable
    {
        return $this->endDate;
    }
    
    
    /**
     * Return comparison start date.
     */
    public function comparisonStartDate(): DateTimeImmutable
    {
        return $this->comparisonStartDate;
    }
    
    
    /**
     * Return comparison end date.
     */
    public function comparisonEndDate(): DateTimeImmutable
    {
        return $this->comparisonEndDate;
    }
    
    
    /**
     * Return time span name.
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * Return totals start date.
     */
    public function totalsStartDate(): DateTimeImmutable
    {
        return $this->totalsStartDate;
    }
    
    
    /**
     * Return totals end date.
     */
    public function totalsEndDate(): DateTimeImmutable
    {
        return $this->totalsEndDate;
    }
    
    
    /**
     * Return comparison totals end date.
     */
    public function comparisonTotalsEndDate(): DateTimeImmutable
    {
        return $this->comparisonTotalsEndDate;
    }
    
    
    /**
     * Return comparison totals start date.
     */
    public function comparisonTotalsStartDate(): DateTimeImmutable
    {
        return $this->comparisonTotalsStartDate;
    }
}

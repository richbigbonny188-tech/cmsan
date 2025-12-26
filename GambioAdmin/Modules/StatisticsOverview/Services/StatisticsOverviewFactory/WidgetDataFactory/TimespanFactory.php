<?php
/* --------------------------------------------------------------
   TimespanFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory;

use DateTimeImmutable;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\Timespan;
use Webmozart\Assert\Assert;

/**
 * Class representing a time span factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory
 */
class TimespanFactory
{
    /**
     * Valid terms.
     */
    private const VALID_TERMS = ["today", "week", "month", "year", "all"];
    
    /**
     * Earliest date.
     */
    private const EARLIEST_TIME = "1970-01-01";
    
    
    /**
     * Return time span.
     *
     * @param DateTimeImmutable $start Start date.
     * @param DateTimeImmutable $end   End date.
     *
     * @return Timespan Time span.
     */
    public function createTimespan(DateTimeImmutable $start, DateTimeImmutable $end): Timespan
    {
        return Timespan::create($start, $end);
    }
    
    
    /**
     * Return time span for provided term.
     *
     * @param string $term Term.
     *
     * @return Timespan Time span.
     */
    public function createFromTerm(string $term): Timespan
    {
        Assert::inArray($term, self::VALID_TERMS);
        
        if ($term === "today") {
            return $this->createForToday();
        }
        
        if ($term === "week") {
            return $this->createForThisWeek();
        }
        
        if ($term === "month") {
            return $this->createForThisMonth();
        }
        
        if ($term === "year") {
            return $this->createForThisYear();
        }
        
        return $this->createForAllTime();
    }
    
    
    /**
     * Return timespan for current day.
     *
     * @return Timespan Time span.
     */
    public function createForToday(): Timespan
    {
        return $this->createTimespan((new DateTimeImmutable())->setTime(0, 0),
                                     (new DateTimeImmutable())->setTime(23, 59, 59));
    }
    
    
    /**
     * Return timespan for current week.
     *
     * @return Timespan Time span.
     */
    public function createForThisWeek(): Timespan
    {
        $monday = new DateTimeImmutable();
        $sunday = new DateTimeImmutable();
        
        // @codeCoverageIgnoreStart
        if ((int)$monday->format('N') !== 1) {
            $monday = $monday->modify('last monday');
        }
        // @codeCoverageIgnoreEnd
        
        $monday = $monday->setTime(0, 0);
        
        // @codeCoverageIgnoreStart
        if ((int)$sunday->format('N') !== 0) {
            $sunday = $sunday->modify('next sunday');
        }
        // @codeCoverageIgnoreEnd
        
        $sunday = $sunday->setTime(23, 59, 59);
        
        return $this->createTimespan($monday, $sunday);
    }
    
    
    /**
     * Return timespan for current month.
     *
     * @return Timespan Time span.
     */
    public function createForThisMonth(): Timespan
    {
        return $this->createTimespan((new DateTimeImmutable())->modify('first day of this month')->setTime(0, 0),
                                     (new DateTimeImmutable())->modify('last day of this month')->setTime(23, 59, 59));
    }
    
    
    /**
     * Return timespan for current year.
     *
     * @return Timespan Time span.
     */
    public function createForThisYear(): Timespan
    {
        return $this->createTimespan((new DateTimeImmutable())->modify('first day of January this year')->setTime(0, 0),
                                     (new DateTimeImmutable())->modify('last day of December this year')
                                         ->setTime(23, 59, 59));
    }
    
    
    /**
     * Return timespan for all time.
     *
     * @return Timespan Time span.
     */
    public function createForAllTime(): Timespan
    {
        return $this->createTimespan(new DateTimeImmutable(self::EARLIEST_TIME),
                                     (new DateTimeImmutable())->setTime(23, 59, 59));
    }
}
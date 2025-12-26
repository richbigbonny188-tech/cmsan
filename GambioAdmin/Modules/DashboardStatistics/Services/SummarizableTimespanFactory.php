<?php
/*--------------------------------------------------------------
  SummarizableTimespanFactory.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Services;

use DateTimeImmutable;
use Gambio\Admin\Modules\DashboardStatistics\App\Summarizer\MonthSummarizer;
use Gambio\Admin\Modules\DashboardStatistics\App\Summarizer\WeekSummarizer;
use Gambio\Admin\Modules\DashboardStatistics\App\Summarizer\YearSummarizer;
use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\SummarizableTimespans;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\SummarizableTimespan;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\Timespan;
use Gambio\Admin\Modules\DashboardStatistics\Model\Exceptions\CouldNotCreateTimespanException;
use Gambio\Core\TextManager\Services\TextManager;

class SummarizableTimespanFactory
{
    /**
     * @var string[]
     */
    private $datetimeMap = [
        'today'        => [
            'partialsTimespan' => [
                'from' => 'this week',
                'to'   => 'this week +6 days',
            ],
            'summerizer'       => WeekSummarizer::class,
            'totalsTimespan'   => [
                'from' => 'today',
                'to'   => 'today',
            ]
        ],
        'thisweek'     => [
            'partialsTimespan' => [
                'from' => 'this week',
                'to'   => 'this week +6 days',
            ],
            'summerizer'       => WeekSummarizer::class,
            'totalsTimespan'   => [
                'from' => 'this week',
                'to'   => 'this week +6 days',
            ]
        ],
        'last7days'    => [
            'partialsTimespan' => [
                'from' => 'today -7 days',
                'to'   => 'yesterday',
            ],
            'summerizer'       => WeekSummarizer::class,
            'totalsTimespan'   => [
                'from' => 'today -7 days',
                'to'   => 'yesterday',
            ]
        ],
        'thismonth'    => [
            'partialsTimespan' => [
                'from' => 'first day of this month',
                'to'   => 'last day of this month',
            ],
            'summerizer'       => MonthSummarizer::class,
            'totalsTimespan'   => [
                'from' => 'first day of this month',
                'to'   => 'last day of this month',
            ]
        ],
        'last28days'   => [
            'partialsTimespan' => [
                'from' => 'today -28 days',
                'to'   => 'yesterday',
            ],
            'summerizer'       => MonthSummarizer::class,
            'totalsTimespan'   => [
                'from' => 'today -28 days',
                'to'   => 'yesterday',
            ]
        ],
        'thisyear'     => [
            'partialsTimespan' => [
                'from' => 'first day of january this year',
                'to'   => 'last day of december this year',
            ],
            'summerizer'       => YearSummarizer::class,
            'totalsTimespan'   => [
                'from' => 'first day of january this year',
                'to'   => 'last day of december this year',
            ]
        ],
        'last12months' => [
            'partialsTimespan' => [
                'from' => 'first day of this month -12 months',
                'to'   => 'last day of last month',
            ],
            'summerizer'       => YearSummarizer::class,
            'totalsTimespan'   => [
                'from' => 'first day of this month -12 months',
                'to'   => 'last day of last month',
            ]
        ],
    ];
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * Constructor.
     */
    public function __construct(TextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * Return whole period.
     */
    public function createForTheWholePeriod(): Timespan
    {
        $startDate = new DateTimeImmutable('first day of january this year -2 year');
        $startDate = $startDate->setTime(0, 0);
        $endDate   = new DateTimeImmutable('last day of december this year');
        $endDate   = $endDate->setTime(23, 59, 59, 999999);
        
        return new Timespan($startDate, $endDate);
    }
    
    
    /**
     * Return all time spans.
     */
    public function createAll(): SummarizableTimespans
    {
        $result = new SummarizableTimespans();
        
        foreach (array_keys($this->datetimeMap) as $key) {
            $result[] = $this->createFromString($key);
        }
        
        return $result;
    }
    
    
    /**
     * Return time span by provided name.
     */
    public function createFromString(string $timespanName): SummarizableTimespan
    {
        if (array_key_exists($timespanName, $this->datetimeMap) === false) {
            throw CouldNotCreateTimespanException::invalidDatetimeString($timespanName,
                                                                         array_keys($this->datetimeMap));
        }
        
        $startDateString       = $this->datetimeMap[$timespanName]['partialsTimespan']['from'];
        $endDateString         = $this->datetimeMap[$timespanName]['partialsTimespan']['to'];
        $summarizerClassName   = $this->datetimeMap[$timespanName]['summerizer'];
        $totalsStartDateString = $this->datetimeMap[$timespanName]['totalsTimespan']['from'];
        $totalsEndDateString   = $this->datetimeMap[$timespanName]['totalsTimespan']['to'];
        
        $startDate = new DateTimeImmutable($startDateString);
        $startDate = $startDate->setTime(0, 0);
        
        $endDate = new DateTimeImmutable($endDateString);
        $endDate = $endDate->setTime(23, 59, 59, 999999);
        
        $totalsStartDate = new DateTimeImmutable($totalsStartDateString);
        $totalsStartDate = $totalsStartDate->setTime(0, 0);
        
        $totalsEndDate = new DateTimeImmutable($totalsEndDateString);
        $totalsEndDate = $totalsEndDate->setTime(23, 59, 59, 999999);
        
        $summarizer = new $summarizerClassName($this->textManager);
        
        return new SummarizableTimespan($startDate,
                                        $endDate,
                                        $summarizer,
                                        $timespanName,
                                        $totalsStartDate,
                                        $totalsEndDate);
    }
}
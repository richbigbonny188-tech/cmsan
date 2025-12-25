<?php
/*--------------------------------------------------------------
   Timespan.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects;

use DateTimeImmutable;
use Webmozart\Assert\Assert;

/**
 * Class representing a time span.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects
 */
class Timespan
{
    /**
     * Start date.
     *
     * @var DateTimeImmutable $start
     */
    private $start;
    
    /**
     * End date.
     *
     * @var DateTimeImmutable $end
     */
    private $end;
    
    
    /**
     * Constructor.
     *
     * @param DateTimeImmutable $start Start date.
     * @param DateTimeImmutable $end   End date.
     */
    private function __construct(DateTimeImmutable $start, DateTimeImmutable $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }
    
    
    /**
     * Create instance.
     *
     * @param DateTimeImmutable $start Start date.
     * @param DateTimeImmutable $end   End date.
     *
     * @return Timespan Instance.
     */
    public static function create(DateTimeImmutable $start, DateTimeImmutable $end): self
    {
        Assert::lessThan($start, $end);
        
        return new self($start, $end);
    }
    
    
    /**
     * Return start date.
     *
     * @return DateTimeImmutable Start date.
     */
    public function startDate(): DateTimeImmutable
    {
        return $this->start;
    }
    
    
    /**
     * Return end date.
     *
     * @return DateTimeImmutable End date.
     */
    public function endDate(): DateTimeImmutable
    {
        return $this->end;
    }
    
}
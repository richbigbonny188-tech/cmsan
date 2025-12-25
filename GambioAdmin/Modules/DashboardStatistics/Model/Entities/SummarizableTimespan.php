<?php
/*------------------------------------------------------------------------------
 SummarizableTimespan.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\Entities;

use DateTimeImmutable;
use Gambio\Admin\Modules\DashboardStatistics\App\Summarizer\AbstractSummarizer;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummarizedData;

class SummarizableTimespan extends Timespan
{
    /**
     * @var AbstractSummarizer
     */
    private $summarizer;
    
    
    /**
     * @inheritDoc
     */
    public function __construct(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        AbstractSummarizer $summarizer,
        string $name,
        ?DateTimeImmutable $totalsStartDate = null,
        ?DateTimeImmutable $totalsEndDate = null
    ) {
        parent::__construct($startDate, $endDate, $name, $totalsStartDate, $totalsEndDate);
        $this->summarizer = $summarizer;
    }
    
    
    /**
     * Summarize values.
     */
    public function summarize(DataProviderResult ...$values): SummarizedData
    {
        return $this->summarizer->summarize($this, ...$values);
    }
    
}
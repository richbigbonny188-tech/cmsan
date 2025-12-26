<?php
/*------------------------------------------------------------------------------
 MonthSumarizer.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\App\Summarizer;

use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\DataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\Timespan;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummarizedData;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummaryHint;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummaryName;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\SummaryTitle;

class MonthSummarizer extends AbstractSummarizer
{
    private const LANGUAGE_SECTION = 'dashboard_statistics';
    
    
    /**
     * @inheritDoc
     */
    public function summarize(
        Timespan $timespan,
        DataProviderResult ...$values
    ): SummarizedData {
        $this->values = $values;
        $struct       = $this->summarizeAsArray('d', $timespan);
        $map          = [];
        $i            = 0;
        while ($i++ < 31) {
            $map[$i] = $i < 10 ? '0%s.' : '%s.';
        }
        $title = new SummaryTitle($this->textManager->getPhraseText($timespan->name(), static::LANGUAGE_SECTION));
        $name  = new SummaryName($timespan->name());
        $hint  = new SummaryHint($this->textManager->getPhraseText($timespan->name() . '_hint',
                                                                   static::LANGUAGE_SECTION));
        
        return $this->parseArray($struct,
                                 $title,
                                 $hint,
                                 $name,
                                 $map);
    }
}
<?php
/*------------------------------------------------------------------------------
  YearSummarizer.php 2021-09-20
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

class YearSummarizer extends AbstractSummarizer
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
        $struct       = $this->summarizeAsArray('n', $timespan);
        
        $title = new SummaryTitle($this->textManager->getPhraseText($timespan->name(),
                                                                    static::LANGUAGE_SECTION));
        $hint  = new SummaryHint($this->textManager->getPhraseText($timespan->name() . '_hint',
                                                                   static::LANGUAGE_SECTION));
        
        return $this->parseArray($struct,
                                 $title,
                                 $hint,
                                 new SummaryName($timespan->name()),
                                 [
                                     1  => $this->textManager->getPhraseText('january', static::LANGUAGE_SECTION),
                                     2  => $this->textManager->getPhraseText('february', static::LANGUAGE_SECTION),
                                     3  => $this->textManager->getPhraseText('march', static::LANGUAGE_SECTION),
                                     4  => $this->textManager->getPhraseText('april', static::LANGUAGE_SECTION),
                                     5  => $this->textManager->getPhraseText('may', static::LANGUAGE_SECTION),
                                     6  => $this->textManager->getPhraseText('june', static::LANGUAGE_SECTION),
                                     7  => $this->textManager->getPhraseText('july', static::LANGUAGE_SECTION),
                                     8  => $this->textManager->getPhraseText('august', static::LANGUAGE_SECTION),
                                     9  => $this->textManager->getPhraseText('september', static::LANGUAGE_SECTION),
                                     10 => $this->textManager->getPhraseText('october', static::LANGUAGE_SECTION),
                                     11 => $this->textManager->getPhraseText('november', static::LANGUAGE_SECTION),
                                     12 => $this->textManager->getPhraseText('december', static::LANGUAGE_SECTION),
                                 ]);
    }
}
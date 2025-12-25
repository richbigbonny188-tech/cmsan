<?php
/* --------------------------------------------------------------
  VisitorsDataProviderResultFactory.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Services;

use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\Values;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\VisitorsDataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\MinimumFractionDigits;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Style;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Title;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Unit;
use Gambio\Core\TextManager\Services\TextManager;

class VisitorsDataProviderResultFactory
{
    private const PHRASE = 'visitors_title';
    
    private const LANGUAGE_SECTION = 'dashboard_statistics';
    
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
     * Create dataset.
     */
    public function create(Values $collection): VisitorsDataProviderResult
    {
        return new VisitorsDataProviderResult(new Title($this->textManager->getPhraseText(static::PHRASE,
                                                                                          static::LANGUAGE_SECTION)),
                                              new Unit(''),
                                              new Style(''),
                                              new MinimumFractionDigits(0),
                                              $collection);
    }
}
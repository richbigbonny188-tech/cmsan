<?php
/*--------------------------------------------------------------
  SalesDataProviderResultFactory.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Services;

use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\Values;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\SalesDataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\MinimumFractionDigits;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Style;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Title;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Unit;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TextManager\Services\TextManager;

class SalesDataProviderResultFactory
{
    private const PHRASE = 'sales_title';
    
    private const LANGUAGE_SECTION = 'dashboard_statistics';
    
    /**
     * @var TextManager
     */
    private $languageTextManager;
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    
    /**
     * Constructor.
     */
    public function __construct(TextManager $textManager, ConfigurationService $configurationService)
    {
        $this->languageTextManager  = $textManager;
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * Create dataset.
     */
    public function create(Values $collection): SalesDataProviderResult
    {
        $unit = $this->configurationService->find('configuration/DEFAULT_CURRENCY');
        
        return new SalesDataProviderResult(new Title($this->languageTextManager->getPhraseText(static::PHRASE,
                                                                                               static::LANGUAGE_SECTION)),
                                           new Unit($unit ? $unit->value() : 'EUR'),
                                           new Style('currency'),
                                           new MinimumFractionDigits(0),
                                           $collection);
    }
}
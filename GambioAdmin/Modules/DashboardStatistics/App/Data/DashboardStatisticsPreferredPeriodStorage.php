<?php
/* --------------------------------------------------------------
 DashboardStatisticsPreferredPeriodStorage.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\App\Data;

use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PreferredPeriod;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

class DashboardStatisticsPreferredPeriodStorage
{
    private const NAMESPACE = 'modules/gambio/statistics';
    
    private const KEY = 'PREFERRED_PERIOD';
    
    /**
     * @var string
     */
    private $periodFallbackValue;
    
    /**
     * @var ConfigurationStorageRepository
     */
    private $repository;
    
    
    /**
     * Constructor.
     */
    public function __construct(ConfigurationStorageRepositoryBuilder $repositoryBuilder)
    {
        $this->repository          = $repositoryBuilder->build(static::NAMESPACE);
        $this->periodFallbackValue = 'thisweek';
    }
    
    
    /**
     * Set preferred period for current user.
     */
    public function setPreferredPeriod(PreferredPeriod $period): void
    {
        $this->repository->set(static::KEY, $period->value());
    }
    
    
    /**
     * Return preferred period for current user.
     */
    public function getPreferredPeriod(): PreferredPeriod
    {
        $value = $this->repository->get(static::KEY);
        $value = $value === '' ? $this->periodFallbackValue : $value;
        
        return new PreferredPeriod($value);
    }
}
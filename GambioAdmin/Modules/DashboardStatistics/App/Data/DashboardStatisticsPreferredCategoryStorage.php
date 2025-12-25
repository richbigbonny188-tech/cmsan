<?php
/* --------------------------------------------------------------
 DashboardStatisticsPreferredCategoryStorage.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\App\Data;

use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PreferredCategory;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

class DashboardStatisticsPreferredCategoryStorage
{
    private const NAMESPACE = 'modules/gambio/statistics';
    
    private const KEY = 'PREFERRED_CATEGORY';
    
    /**
     * @var string
     */
    private $categoryFallbackValue;
    
    /**
     * @var ConfigurationStorageRepository
     */
    private $repository;
    
    
    /**
     * Constructor.
     */
    public function __construct(ConfigurationStorageRepositoryBuilder $repositoryBuilder)
    {
        $this->repository            = $repositoryBuilder->build(static::NAMESPACE);
        $this->categoryFallbackValue = 'sales';
    }
    
    
    /**
     * Set preferred category for current user.
     */
    public function setPreferredCategory(PreferredCategory $category): void
    {
        $this->repository->set(static::KEY, $category->value());
    }
    
    
    /**
     * Return preferred category for current user.
     */
    public function getPreferredCategory(): PreferredCategory
    {
        $value = $this->repository->get(static::KEY);
        $value = $value === '' ? $this->categoryFallbackValue : $value;
        
        return new PreferredCategory($value);
    }
}
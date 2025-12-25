<?php
/* --------------------------------------------------------------
   CustomerAddonValueFilterFactory.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\Filter\CustomerAddonValueFilters;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\Filter\CustomerAddonValueSorting;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Filter\CustomerAddonValueFilters as CustomerAddonValueFiltersInterface;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Filter\CustomerAddonValueSorting as CustomerAddonValueSortingInterface;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class CustomerAddonValueFilterFactory
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App\Data\Filter
 * @codeCoverageIgnore
 */
class CustomerAddonValueFilterFactory
{
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return SqlPagination
     */
    public function createPagination(int $limit, int $offset): SqlPagination
    {
        return SqlPagination::createWithLimitAndOffset($limit, $offset);
    }
    
    
    /**
     * @param array $filters
     *
     * @return CustomerAddonValueFiltersInterface
     */
    public function createFilters(array $filters): CustomerAddonValueFiltersInterface
    {
        return CustomerAddonValueFilters::createFromMap($filters);
    }
    
    
    /**
     * @param string|null $sorting
     *
     * @return CustomerAddonValueSortingInterface
     */
    public function createSorting(?string $sorting): CustomerAddonValueSortingInterface
    {
        return CustomerAddonValueSorting::create($sorting);
    }
}
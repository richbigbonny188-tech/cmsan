<?php
/*--------------------------------------------------------------
   CurrencyFilterFactory.php 2022-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\Services;

use Gambio\Admin\Modules\Currency\Model\Filter\CurrencyFilters;
use Gambio\Admin\Modules\Currency\Model\Filter\CurrencySorting;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class CurrencyFilterFactory
 *
 * @package Gambio\Admin\Modules\Currency\Services
 * @codeCoverageIgnore
 */
class CurrencyFilterFactory
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
     * @param string|null $sorting
     *
     * @return CurrencySorting
     */
    public function createSorting(?string $sorting): CurrencySorting
    {
        return CurrencySorting::create($sorting);
    }
    
    
    /**
     * @param array $filters
     *
     * @return CurrencyFilters
     */
    public function createFilters(array $filters): CurrencyFilters
    {
        return CurrencyFilters::createFromMap($filters);
    }
}
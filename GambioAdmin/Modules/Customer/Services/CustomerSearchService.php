<?php
/*--------------------------------------------------------------
   CustomerSearchService.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services;

use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerSearch;

/**
 * Interface CustomerSearchService
 *
 * @package Gambio\Admin\Modules\Customer\Services
 */
interface CustomerSearchService
{
    /**
     * Returns a paginated collection of customers based on the given search term and sorting arguments.
     * The sorting must be a comma-separated list of attributes. A `-` can be used to change the order to descending.
     *
     * @param string      $searchTerm
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return Customers
     */
    public function searchCustomers(
        string  $searchTerm,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): Customers;
    
    /**
     * Returns total count of customers based on the given search object.
     *
     * @param string $searchTerm
     *
     * @return int
     */
    public function getSearchedCustomerTotalCount(string $searchTerm): int;
}
<?php
/* --------------------------------------------------------------
   AdminAccessUserRepository.php 2023-11-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Customer\Model\Customer;

/**
 * Class AdminAccessUserRepository
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class AdminAccessUserRepository
{
    /**
     * @param Connection $connection
     */
    public function __construct(private Connection $connection)
    {
    }
    
    
    /**
     * Deletes the admin access by the given customer ID
     *
     * @param Customer ...$customers
     *
     * @return void
     */
    public function deleteAdminAccessByCustomers(Customer ...$customers): void
    {
        $customersId = array_map(static fn($customer) => $customer->id(), $customers);
        
        $this->connection->createQueryBuilder()
            ->delete('admin_access_users')
            ->where('customer_id IN (:customersId)')
            ->setParameter('customersId', $customersId, ArrayParameterType::INTEGER)
            ->executeQuery();
    }
}
<?php
/*--------------------------------------------------------------
   CustomerSuperAdministratorRepository.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Class CustomerSuperAdministratorRepository
 *
 * @package Gambio\Admin\Modules\Customer\App
 * @codeCoverageIgnore
 */
class CustomerAdministratorPermissionRepository
{
    private const SUPER_ADMIN_ACCESS_ROLE_ID = '1';
    private const CUSTOMER_ACCESS_GROUP_ID   = '6';
    private Connection $connection;
    
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @param int $customerId
     *
     * @return array
     * @throws Exception
     */
    public function getPermissions(int $customerId): array
    {
        $isSuperAdmin = $this->customerIsSuperAdministrator($customerId);
        
        $permissions = [
            'super'    => $isSuperAdmin,
            'customer' => [
                'read'   => $isSuperAdmin,
                'write'  => $isSuperAdmin,
                'delete' => $isSuperAdmin,
            ],
        ];
        
        if ($isSuperAdmin) {
            return $permissions;
        }
        
        $result = $this->connection->createQueryBuilder()
            ->select('admin_access_role_id')
            ->from('admin_access_users')
            ->where('customer_id = :customer_id')
            ->andWhere('admin_access_role_id != :super_admin_id')
            ->setParameter('customer_id', $customerId)
            ->setParameter('super_admin_id', static::SUPER_ADMIN_ACCESS_ROLE_ID)
            ->executeQuery();
        
        if ($result->rowCount() !== 0) {
            while ($row = $result->fetchAssociative()) {
                [$read, $write, $delete] = $this->getCustomerPermissionsForGroup((int)$row['admin_access_role_id']);
                
                $permissions['customer']['read']   = $permissions['customer']['read'] || $read;
                $permissions['customer']['write']  = $permissions['customer']['write'] || $write;
                $permissions['customer']['delete'] = $permissions['customer']['delete'] || $delete;
            }
        }
        
        return $permissions;
    }
    
    
    /**
     * @param int $customerId
     *
     * @return bool
     * @throws Exception
     */
    private function customerIsSuperAdministrator(int $customerId): bool
    {
        $result = $this->connection->createQueryBuilder()
            ->select('admin_access_role_id')
            ->from('admin_access_users')
            ->where('customer_id = :customer_id')
            ->setParameter('customer_id', $customerId)
            ->executeQuery();
        
        if ($result->rowCount() !== 0) {
            while ($row = $result->fetchAssociative()) {
                if ($row['admin_access_role_id'] === static::SUPER_ADMIN_ACCESS_ROLE_ID) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    
    /**
     * @param int $roleId
     *
     * @return array
     * @throws Exception
     */
    private function getCustomerPermissionsForGroup(int $roleId): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('reading_granted, writing_granted, deleting_granted')
            ->from('admin_access_permissions')
            ->where('admin_access_role_id = :role_id')
            ->andWhere('admin_access_group_id = :access_group_id')
            ->setParameter('role_id', $roleId)
            ->setParameter('access_group_id', static::CUSTOMER_ACCESS_GROUP_ID)
            ->executeQuery();
        
        if ($result->rowCount() === 0) {
            return [false, false, false];
        }
        
        $row = $result->fetchAssociative();
        
        return [
            $row['reading_granted'] === '1',
            $row['writing_granted'] === '1',
            $row['deleting_granted'] === '1',
        ];
    }
}
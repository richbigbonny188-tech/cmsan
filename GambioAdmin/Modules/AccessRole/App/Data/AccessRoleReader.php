<?php
/* --------------------------------------------------------------
   AccessRoleReader.php 2023-11-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AdminId;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\AccessRoleDoesNotExistException;

/**
 * Class AccessRoleReader
 *
 * @package Gambio\Admin\Modules\AccessRole\App\Data
 */
class AccessRoleReader
{
    /**
     * Defines the admin customer status
     */
    private const CUSTOMER_GROUP_ADMIN = 0;
    
    /**
     * AccessRoleReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(private Connection $db)
    {
    }
    
    
    /**
     * @param AccessRoleId $id
     *
     * @return array<string, string|array|bool|int>
     *
     * @throws AccessRoleDoesNotExistException
     * @throws Exception
     */
    public function getAccessRoleDataById(AccessRoleId $id): array
    {
        $roleData = $this->db->createQueryBuilder()
            ->select('admin_access_role_id, sort_order, protected')
            ->from('admin_access_roles')
            ->where('admin_access_role_id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery()
            ->fetchAssociative();
        
        if ($roleData === false) {
            throw AccessRoleDoesNotExistException::forId($id->value());
        }
        
        $roleDetails     = $this->getDetails($id->value());
        $rolePermissions = $this->getPermissions($id->value());
        
        return [
            'id'           => (int)$roleData['admin_access_role_id'],
            'names'        => $roleDetails['names'],
            'descriptions' => $roleDetails['descriptions'],
            'permissions'  => $rolePermissions,
            'sortOrder'    => (int)$roleData['sort_order'],
            'isProtected'  => $roleData['protected'] === '1',
        ];
    }
    
    
    /**
     * @return array<array<string, string|array|bool|int>>
     * @throws Exception
     */
    public function getAccessRolesData(): array
    {
        $roles     = [];
        $rolesData = $this->db->createQueryBuilder()
            ->select('admin_access_role_id, sort_order, protected')
            ->from('admin_access_roles')
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($rolesData as $roleData) {
            $roleDetails     = $this->getDetails((int)$roleData['admin_access_role_id']);
            $rolePermissions = $this->getPermissions((int)$roleData['admin_access_role_id']);
            
            $roles[] = [
                'id'           => (int)$roleData['admin_access_role_id'],
                'names'        => $roleDetails['names'],
                'descriptions' => $roleDetails['descriptions'],
                'permissions'  => $rolePermissions,
                'sortOrder'    => (int)$roleData['sort_order'],
                'isProtected'  => $roleData['protected'] === '1',
            ];
        }
        
        return $roles;
    }
    
    
    /**
     * @param AdminId $admin
     *
     * @return array<array<string, string|array|bool|int>>
     * @throws Exception
     */
    public function getAccessRolesDataByAdmin(AdminId $admin): array
    {
        $roles     = [];
        $rolesData = $this->db->createQueryBuilder()
            ->select('aar.admin_access_role_id, aar.sort_order, aar.protected')
            ->from('admin_access_roles', 'aar')
            ->join('aar', 'admin_access_users', 'aau', 'aar.admin_access_role_id = aau.admin_access_role_id')
            ->join('aau', 'customers', 'c', 'aau.customer_id = c.customers_id')
            ->where('aau.customer_id = :adminId')
            ->andWhere('c.customers_status = :customerGroupAdmin')
            ->setParameter('adminId', $admin->value())
            ->setParameter('customerGroupAdmin', self::CUSTOMER_GROUP_ADMIN)
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($rolesData as $roleData) {
            $roleDetails     = $this->getDetails((int)$roleData['admin_access_role_id']);
            $rolePermissions = $this->getPermissions((int)$roleData['admin_access_role_id']);
            
            $roles[] = [
                'id'           => (int)$roleData['admin_access_role_id'],
                'names'        => $roleDetails['names'],
                'descriptions' => $roleDetails['descriptions'],
                'permissions'  => $rolePermissions,
                'sortOrder'    => (int)$roleData['sort_order'],
                'isProtected'  => $roleData['protected'] === '1',
            ];
        }
        
        return $roles;
    }
    
    
    /**
     * @param int $id
     *
     * @return array<string, array<string, string>>
     * @throws Exception
     */
    private function getDetails(int $id): array
    {
        $roleDetails = $this->db->createQueryBuilder()
            ->select('languages.code AS language_code, aard.name, aard.description')
            ->from('admin_access_role_descriptions', 'aard')
            ->join('aard', 'languages', 'languages', 'aard.language_id = languages.languages_id')
            ->where('aard.admin_access_role_id = :roleId')
            ->setParameter('roleId', $id)
            ->executeQuery()
            ->fetchAllAssociative();
        
        $names        = [];
        $descriptions = [];
        foreach ($roleDetails as $roleDetail) {
            $names[$roleDetail['language_code']]        = $roleDetail['name'];
            $descriptions[$roleDetail['language_code']] = $roleDetail['description'];
        }
        
        return [
            'names'        => $names,
            'descriptions' => $descriptions,
        ];
    }
    
    
    /**
     * @param int $id
     *
     * @return array
     * @throws Exception
     */
    private function getPermissions(int $id): array
    {
        $permissions = $this->db->createQueryBuilder()
            ->select('admin_access_group_id, reading_granted, writing_granted, deleting_granted')
            ->from('admin_access_permissions')
            ->where('admin_access_role_id = :roleId')
            ->setParameter('roleId', $id)
            ->executeQuery()
            ->fetchAllAssociative();
        
        return array_map(static function (array $permission): array {
            return [
                'groupId'         => (int)$permission['admin_access_group_id'],
                'readingGranted'  => $permission['reading_granted'] === '1',
                'writingGranted'  => $permission['writing_granted'] === '1',
                'deletingGranted' => $permission['deleting_granted'] === '1',
            ];
        },
            $permissions);
    }
}
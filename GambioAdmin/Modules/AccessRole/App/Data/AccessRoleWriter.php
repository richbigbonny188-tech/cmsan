<?php
/* --------------------------------------------------------------
   AccessRoleWriter.php 2023-06-09
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
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\AccessRole\Model\AccessRole;
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoleIds;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleDescriptions;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleNames;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\DeletionOfAccessRolesFailedException;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\StorageOfAccessRolesFailedException;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Core\Language\Services\LanguageService;

/**
 * Class AccessRoleWriter
 *
 * @package Gambio\Admin\Modules\AccessRole\App\Data
 */
class AccessRoleWriter
{
    /**
     * @var Connection
     */
    private $db;
    
    /**
     * @var LanguageService
     */
    private $languageService;
    
    
    /**
     * AccessRoleWriter constructor.
     *
     * @param Connection      $db
     * @param LanguageService $languageService
     */
    public function __construct(Connection $db, LanguageService $languageService)
    {
        $this->db              = $db;
        $this->languageService = $languageService;
    }
    
    
    /**
     * @param AccessRole ...$roles
     *
     * @return int[]
     *
     * @throws StorageOfAccessRolesFailedException
     * @throws Exception
     */
    public function storeAccessRoles(AccessRole ...$roles): array
    {
        $ids = [];
        $this->db->beginTransaction();
        
        try {
            foreach ($roles as $role) {
                $this->updateAccessRole($role);
                $ids[] = $role->id();
            }
            
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            
            throw StorageOfAccessRolesFailedException::becauseOfException($exception);
        }
        
        return $ids;
    }
    
    
    /**
     * @param AccessRoleNames        $names
     * @param AccessRoleDescriptions $descriptions
     * @param int                    $sortOrder
     * @param bool                   $isProtected
     *
     * @return int
     * @throws Exception
     */
    public function createAccessRole(
        AccessRoleNames        $names,
        AccessRoleDescriptions $descriptions,
        int                    $sortOrder,
        bool                   $isProtected
    ): int {
        $this->db->createQueryBuilder()
            ->insert('admin_access_roles')
            ->setValue('sort_order', ':sortOrder')
            ->setValue('protected', ':isProtected')
            ->setParameter('sortOrder', $sortOrder)
            ->setParameter('isProtected', $isProtected ? '1' : '0')
            ->executeQuery();
        
        $roleId = (int)$this->db->lastInsertId();
        
        /** @var Language $language */
        foreach ($this->languageService->getAvailableLanguages() as $language) {
            $this->db->createQueryBuilder()
                ->insert('admin_access_role_descriptions')
                ->setValue('admin_access_role_id', ':roleId')
                ->setValue('language_id', ':languageId')
                ->setValue('name', ':name')
                ->setValue('description', ':description')
                ->setParameter('roleId', $roleId)
                ->setParameter('languageId', $language->id())
                ->setParameter('name', $names->getName($language->code()))
                ->setParameter('description', $descriptions->getDescription($language->code()))
                ->executeQuery();
        }
        
        return $roleId;
    }
    
    
    /**
     * @param AccessRole $role
     *
     * @throws Exception
     */
    private function updateAccessRole(AccessRole $role): void
    {
        $this->db->createQueryBuilder()
            ->update('admin_access_roles')
            ->set('sort_order', ':sortOrder')
            ->set('protected', ':isProtected')
            ->where('admin_access_role_id', ':roleId')
            ->setParameter('sortOrder', $role->sortOrder())
            ->setParameter('isProtected', $role->isProtected() ? '1' : '0')
            ->setParameter('roleId', $role->id())
            ->executeQuery();
        
        $this->addPermissions($role->id(), $role);
        $this->addDetails($role->id(), $role);
    }
    
    
    /**
     * @param int        $roleId
     * @param AccessRole $role
     *
     * @throws Exception
     */
    private function addPermissions(int $roleId, AccessRole $role): void
    {
        $this->db->createQueryBuilder()
            ->delete('admin_access_permissions')
            ->where('admin_access_role_id = :roleId')
            ->setParameter('roleId', $roleId)
            ->executeQuery();
        
        foreach ($role->permissions() as $permission) {
            $this->db->createQueryBuilder()
                ->insert('admin_access_permissions')
                ->setValue('admin_access_role_id', ':roleId')
                ->setValue('admin_access_group_id', ':groupId')
                ->setValue('reading_granted', ':readingGranted')
                ->setValue('writing_granted', ':writingGranted')
                ->setValue('deleting_granted', ':deletingGranted')
                ->setParameter('roleId', $roleId)
                ->setParameter('groupId', $permission->groupId())
                ->setParameter('readingGranted', $permission->readingGranted() ? '1' : '0')
                ->setParameter('writingGranted', $permission->writingGranted() ? '1' : '0')
                ->setParameter('deletingGranted', $permission->deletingGranted() ? '1' : '0')
                ->executeQuery();
        }
    }
    
    
    /**
     * @param int        $roleId
     * @param AccessRole $role
     *
     * @throws Exception
     */
    private function addDetails(int $roleId, AccessRole $role): void
    {
        $this->db->createQueryBuilder()
            ->delete('admin_access_role_descriptions')
            ->where('admin_access_role_id = :roleId')
            ->setParameter('roleId', $roleId)
            ->executeQuery();
        
        /** @var Language $language */
        foreach ($this->languageService->getAvailableLanguages() as $language) {
            $this->db->createQueryBuilder()
                ->insert('admin_access_role_descriptions')
                ->setValue('admin_access_role_id', ':roleId')
                ->setValue('language_id', ':languageId')
                ->setValue('name', ':name')
                ->setValue('description', ':description')
                ->setParameter('roleId', $roleId)
                ->setParameter('languageId', $language->id())
                ->setParameter('name', $role->name($language->code()))
                ->setParameter('description', $role->description($language->code()))
                ->executeQuery();
        }
    }
    
    
    /**
     * @param AccessRoleIds $roleIds
     *
     * @throws ConnectionException
     *
     * @throws DeletionOfAccessRolesFailedException
     * @throws Exception
     */
    public function deleteAccessRoles(AccessRoleIds $roleIds): void
    {
        $this->db->beginTransaction();
        try {
            foreach ($roleIds as $roleId) {
                $this->deleteAccessRole($roleId);
            }
            
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            
            throw DeletionOfAccessRolesFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param AccessRoleId $id
     *
     * @throws Exception
     */
    private function deleteAccessRole(AccessRoleId $id): void
    {
        $this->db->createQueryBuilder()
            ->delete('admin_access_roles')
            ->where('admin_access_role_id = :roleId')
            ->setParameter('roleId', $id->value())
            ->executeQuery();
        
        $this->db->createQueryBuilder()
            ->delete('admin_access_users')
            ->where('admin_access_role_id = :roleId')
            ->setParameter('roleId', $id->value())
            ->executeQuery();
        
        $this->db->createQueryBuilder()
            ->delete('admin_access_permissions')
            ->where('admin_access_role_id = :roleId')
            ->setParameter('roleId', $id->value())
            ->executeQuery();
        
        $this->db->createQueryBuilder()
            ->delete('admin_access_role_descriptions')
            ->where('admin_access_role_id = :roleId')
            ->setParameter('roleId', $id->value())
            ->executeQuery();
    }
}
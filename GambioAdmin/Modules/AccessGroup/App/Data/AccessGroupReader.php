<?php
/* --------------------------------------------------------------
   AccessGroupReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupId;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessGroup\Services\Exceptions\AccessGroupDoesNotExistException;

/**
 * Class AccessGroupReader
 *
 * @package Gambio\Admin\Modules\AccessGroup\App\Data
 */
class AccessGroupReader
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * AccessGroupReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * @param AccessGroupId $id
     *
     * @return array<string, string|array|bool|int>
     *
     * @throws AccessGroupDoesNotExistException
     * @throws Exception
     */
    public function getAccessGroupDataById(AccessGroupId $id): array
    {
        $groupData = $this->db->createQueryBuilder()
            ->select('admin_access_group_id, parent_id, sort_order, protected')
            ->from('admin_access_groups')
            ->where('admin_access_group_id = :id')
            ->orderBy('sort_order')
            ->setParameter('id', $id->value())
            ->executeQuery()
            ->fetchAssociative();
        
        if ($groupData === false) {
            throw AccessGroupDoesNotExistException::forId($id->value());
        }
        
        $groupDetails = $this->getDetails($id->value());
        $groupItems   = $this->getItems($id->value());
        
        return [
            'id'            => (int)$groupData['admin_access_group_id'],
            'parentGroupId' => ((int)$groupData['parent_id'] > 0) ? (int)$groupData['parent_id'] : null,
            'names'         => $groupDetails['names'],
            'descriptions'  => $groupDetails['descriptions'],
            'items'         => $groupItems,
            'sortOrder'     => (int)$groupData['sort_order'],
            'isProtected'   => $groupData['protected'] === '1',
        ];
    }
    
    
    /**
     * @param AccessGroupItem $groupItem
     *
     * @return array<string, string|array|bool|int>
     *
     * @throws AccessGroupDoesNotExistException
     * @throws Exception
     */
    public function getAccessGroupDataByItem(AccessGroupItem $groupItem): array
    {
        $groupData = $this->db->createQueryBuilder()
            ->select('aag.admin_access_group_id, aag.parent_id, aag.sort_order, aag.protected')
            ->from('admin_access_groups', 'aag')
            ->join('aag', 'admin_access_group_items', 'aagi', 'aag.admin_access_group_id = aagi.admin_access_group_id')
            ->where('aagi.identifier = :descriptor')
            ->andWhere('aagi.type = :type')
            ->orderBy('aag.sort_order')
            ->setParameter('descriptor', $groupItem->descriptor())
            ->setParameter('type', $groupItem->type())
            ->executeQuery()
            ->fetchAssociative();
        
        if ($groupData === false) {
            throw AccessGroupDoesNotExistException::forDescriptorAndType($groupItem->descriptor(), $groupItem->type());
        }
        
        $groupDetails = $this->getDetails((int)$groupData['admin_access_group_id']);
        $groupItems   = $this->getItems((int)$groupData['admin_access_group_id']);
        
        return [
            'id'            => (int)$groupData['admin_access_group_id'],
            'parentGroupId' => ((int)$groupData['parent_id'] > 0) ? (int)$groupData['parent_id'] : null,
            'names'         => $groupDetails['names'],
            'descriptions'  => $groupDetails['descriptions'],
            'items'         => $groupItems,
            'sortOrder'     => (int)$groupData['sort_order'],
            'isProtected'   => $groupData['protected'] === '1',
        ];
    }
    
    
    /**
     * @return array<array<string, string|array|bool|int>>
     * @throws Exception
     */
    public function getAccessGroupsData(): array
    {
        $groups     = [];
        $groupsData = $this->db->createQueryBuilder()
            ->select('admin_access_group_id, parent_id, sort_order, protected')
            ->from('admin_access_groups')
            ->orderBy('sort_order')
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($groupsData as $groupData) {
            $groupDetails = $this->getDetails((int)$groupData['admin_access_group_id']);
            $groupItems   = $this->getItems((int)$groupData['admin_access_group_id']);
            
            $groups[] = [
                'id'            => (int)$groupData['admin_access_group_id'],
                'parentGroupId' => ((int)$groupData['parent_id'] > 0) ? (int)$groupData['parent_id'] : null,
                'names'         => $groupDetails['names'],
                'descriptions'  => $groupDetails['descriptions'],
                'items'         => $groupItems,
                'sortOrder'     => (int)$groupData['sort_order'],
                'isProtected'   => $groupData['protected'] === '1',
            ];
        }
        
        return $groups;
    }
    
    
    /**
     * @param int $id
     *
     * @return array<string, array<int, string>>
     * @throws Exception
     */
    private function getDetails(int $id): array
    {
        $groupDetails = $this->db->createQueryBuilder()
            ->select('languages.code AS language_code, aagd.name, aagd.description')
            ->from('admin_access_group_descriptions', 'aagd')
            ->join('aagd', 'languages', 'languages', 'aagd.language_id = languages.languages_id')
            ->where('aagd.admin_access_group_id = :groupId')
            ->setParameter('groupId', $id)
            ->executeQuery()
            ->fetchAllAssociative();
        
        $names        = [];
        $descriptions = [];
        foreach ($groupDetails as $groupDetail) {
            $names[$groupDetail['language_code']]        = $groupDetail['name'];
            $descriptions[$groupDetail['language_code']] = $groupDetail['description'];
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
    private function getItems(int $id): array
    {
        return $this->db->createQueryBuilder()
            ->select('type, identifier as descriptor')
            ->from('admin_access_group_items')
            ->where('admin_access_group_id = :groupId')
            ->setParameter('groupId', $id)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
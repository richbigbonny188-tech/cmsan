<?php

/* --------------------------------------------------------------
   AdminAccessGroupWriter.inc.php 2018-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessGroupWriter
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Writers
 */
class AdminAccessGroupWriter implements AdminAccessGroupWriterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    /**
     * @var string
     */
    protected $groupsTable;
    
    /**
     * @var string
     */
    protected $groupItemsTable;
    
    /**
     * @var string
     */
    protected $groupDescriptionsTable;
    
    
    /**
     * AdminAccessGroupWriter constructor.
     *
     * @param CI_DB_query_builder       $db               Query builder.
     * @param LanguageProviderInterface $languageProvider Language provider.
     */
    public function __construct(CI_DB_query_builder $db, LanguageProviderInterface $languageProvider)
    {
        $this->db               = $db;
        $this->languageProvider = $languageProvider;
        
        $this->groupsTable            = 'admin_access_groups';
        $this->groupItemsTable        = 'admin_access_group_items';
        $this->groupDescriptionsTable = 'admin_access_group_descriptions';
    }
    
    
    /**
     * Stores a role into the database.
     *
     * @param AdminAccessGroup $group Group object.
     *
     * @return int ID of stored group.
     */
    public function insert(AdminAccessGroup $group)
    {
        try {
            $parentGroupId = $group->getParentGroup()->getId();
        } catch (GroupNotFoundException $e) {
            $parentGroupId = 0;
        }
        
        $groupData = [
            'parent_id'  => $parentGroupId,
            'sort_order' => $group->getSortOrder(),
        ];
        
        // Start transaction so we won't lose data if something goes wrong.
        $this->db->trans_start();
        
        $this->db->insert($this->groupsTable, $groupData);
        $groupId = $this->db->insert_id();
        
        foreach ($this->languageProvider->getCodes() as $langCode) {
            $langId           = $this->languageProvider->getIdByCode($langCode);
            $groupDescription = [
                'admin_access_group_id' => $groupId,
                'language_id'           => $langId,
                'name'                  => $group->getName()->getValue((string)$langCode),
                'description'           => $group->getDescription()->getValue((string)$langCode),
            ];
            $this->db->insert($this->groupDescriptionsTable, $groupDescription);
        }
        
        $this->db->trans_complete();
        
        return $groupId;
    }
    
    
    /**
     * Updates a role from the database.
     *
     * @param AdminAccessGroup $group Group object.
     *
     * @return AdminAccessGroupWriterInterface Returns same instance for chained method calls.
     */
    public function update(AdminAccessGroup $group)
    {
        try {
            $parentGroupId = $group->getParentGroup()->getId();
        } catch (GroupNotFoundException $e) {
            $parentGroupId = 0;
        }
        
        $groupData = [
            'parent_id'  => $parentGroupId,
            'sort_order' => $group->getSortOrder(),
        ];
        
        $this->db->where('admin_access_group_id', $group->getId());
        
        // Start transaction so we won't lose data if something goes wrong.
        $this->db->trans_start();
        $this->db->update($this->groupsTable, $groupData);
        
        foreach ($this->languageProvider->getCodes() as $langCode) {
            $langId           = $this->languageProvider->getIdByCode($langCode);
            $groupDescription = [
                'name'        => $group->getName()->getValue((string)$langCode),
                'description' => $group->getDescription()->getValue((string)$langCode),
            ];
            $this->db->where('admin_access_group_id', $group->getId());
            $this->db->where('language_id', $langId);
            $this->db->update($this->groupDescriptionsTable, $groupDescription);
        }
        
        $this->db->delete($this->groupItemsTable, ['admin_access_group_id' => $group->getId()]);
        if ($group->getItems()->count() > 0) {
            /** @var \AdminAccessGroupItem $item */
            foreach ($group->getItems() as $item) {
                $itemData = [
                    'admin_access_group_id' => $item->getGroupId(),
                    'identifier'            => $item->getIdentifier(),
                    'type'                  => $item->getType(),
                ];
                $this->db->replace($this->groupItemsTable, $itemData);
            }
        }
        
        $this->db->trans_complete();
        
        return $this;
    }
}
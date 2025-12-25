<?php

/* --------------------------------------------------------------
   AdminAccessGroupReader.inc.php 2018-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessGroupReader
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Readers
 */
class AdminAccessGroupReader implements AdminAccessGroupReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var AdminAccessGroupFactoryInterface
     */
    protected $factory;
    
    /**
     * @var string
     */
    protected $controllerType;
    
    /**
     * @var string
     */
    protected $pageType;
    
    /**
     * @var string
     */
    protected $ajaxHandlerType;
    
    /**
     * @var string
     */
    protected $adminAccessGroupsTable;
    
    /**
     * @var string
     */
    protected $adminAccessGroupItemsTable;
    
    /**
     * @var string
     */
    protected $adminAccessGroupsDescriptionsTable;
    
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    
    /**
     * AdminAccessGroupReader constructor.
     *
     * @param CI_DB_query_builder              $queryBuilder       Query builder.
     * @param AdminAccessGroupFactoryInterface $accessGroupFactory Access group factory.
     * @param LanguageProviderInterface        $languageProvider   Language provider.
     */
    public function __construct(
        CI_DB_query_builder $queryBuilder,
        AdminAccessGroupFactoryInterface $accessGroupFactory,
        LanguageProviderInterface $languageProvider
    ) {
        $this->queryBuilder     = $queryBuilder;
        $this->factory          = $accessGroupFactory;
        $this->languageProvider = $languageProvider;
        
        $this->controllerType  = 'CONTROLLER';
        $this->pageType        = 'PAGE';
        $this->ajaxHandlerType = 'AJAX_HANDLER';
        
        $this->adminAccessGroupsTable             = 'admin_access_groups';
        $this->adminAccessGroupItemsTable         = 'admin_access_group_items';
        $this->adminAccessGroupsDescriptionsTable = 'admin_access_group_descriptions';
    }
    
    
    /**
     * Returns an AdminAccessGroup instance by the given page identifier.
     *
     * @param NonEmptyStringType $pageIdentifier Page identifier.
     *
     * @return AdminAccessGroupInterface Group object.
     * @throws GroupNotFoundException If group not found.
     *
     */
    public function getByPage(NonEmptyStringType $pageIdentifier)
    {
        $groupData = $this->queryBuilder->select()
            ->from($this->adminAccessGroupsTable)
            ->join($this->adminAccessGroupItemsTable,
                   'admin_access_group_id')
            ->where([
                        'type'       => $this->pageType,
                        'identifier' => $pageIdentifier->asString()
                    ])
            ->order_by('sort_order')
            ->get()
            ->row_array();
        
        if (empty($groupData)) {
            throw new GroupNotFoundException($pageIdentifier);
        }
        
        $groupData['metaData'] = $this->_getGroupMetaData($groupData['admin_access_group_id']);
        $groupData['items']    = $this->_getGroupItemsData($groupData['admin_access_group_id']);
        
        return $this->_createAdminAccessGroupByArray($groupData);
    }
    
    
    /**
     * Returns an AdminAccessGroup instance by the given ajax handler identifier.
     *
     * @param NonEmptyStringType $identifier Identifier.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException If group not found.
     */
    public function getByAjaxHandler(NonEmptyStringType $identifier)
    {
        $groupData = $this->queryBuilder->select()
            ->from($this->adminAccessGroupsTable)
            ->join($this->adminAccessGroupItemsTable,
                   'admin_access_group_id')
            ->where([
                        'type'       => $this->ajaxHandlerType,
                        'identifier' => $identifier->asString()
                    ])
            ->order_by('sort_order')
            ->get()
            ->row_array();
        
        if (empty($groupData)) {
            throw new GroupNotFoundException($identifier);
        }
        
        $groupData['metaData'] = $this->_getGroupMetaData($groupData['admin_access_group_id']);
        $groupData['items']    = $this->_getGroupItemsData($groupData['admin_access_group_id']);
        
        return $this->_createAdminAccessGroupByArray($groupData);
    }
    
    
    /**
     * Returns an AdminAccessGroup instance by the given AccessGroup ID.
     *
     * @param IdType $groupId Group ID.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException
     */
    public function getById(IdType $groupId)
    {
        $groupData = $this->queryBuilder->select()
            ->from($this->adminAccessGroupsTable)
            ->where('admin_access_group_id',
                    $groupId->asInt())
            ->get()
            ->row_array();
        
        if (empty($groupData)) {
            throw new GroupNotFoundException(new NonEmptyStringType('ID ' . $groupId->asInt()));
        }
        
        $groupData['metaData'] = $this->_getGroupMetaData($groupData['admin_access_group_id']);
        $groupData['items']    = $this->_getGroupItemsData($groupData['admin_access_group_id']);
        
        return $this->_createAdminAccessGroupByArray($groupData);
    }
    
    
    /**
     * Returns an AdminAccessGroup instance by the given group identifier.
     *
     * @param NonEmptyStringType $controllerIdentifier Controller identifier.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException
     */
    public function getByController(NonEmptyStringType $controllerIdentifier)
    {
        $groupData = $this->queryBuilder->select()
            ->from($this->adminAccessGroupsTable)
            ->join($this->adminAccessGroupItemsTable,
                   'admin_access_group_id')
            ->where([
                        'type'       => $this->controllerType,
                        'identifier' => $controllerIdentifier->asString()
                    ])
            ->order_by('sort_order')
            ->get()
            ->row_array();
        
        if (empty($groupData) && strpos($controllerIdentifier->asString(), '/') === false) {
            throw new GroupNotFoundException($controllerIdentifier);
        } elseif (empty($groupData) && strpos($controllerIdentifier->asString(), '/') !== false) {
            return $this->getByController(new NonEmptyStringType(strtok($controllerIdentifier->asString(), '/')));
        }
        
        $groupData['metaData'] = $this->_getGroupMetaData($groupData['admin_access_group_id']);
        $groupData['items']    = $this->_getGroupItemsData($groupData['admin_access_group_id']);
        
        return $this->_createAdminAccessGroupByArray($groupData);
    }
    
    
    /**
     * Returns an AdminAccessGroupCollection with all existing AdminAccessGroup objects.
     *
     * @return AdminAccessGroupCollection Group collection with all available groups.
     */
    public function getAll()
    {
        /**
         * @var CI_DB_mysqli_result $result
         */
        $result = $this->queryBuilder->select()->from($this->adminAccessGroupsTable)->order_by('sort_order')->get();
        
        $resultArray = $result->result_array();
        foreach ($resultArray as $key => $value) {
            $resultArray[$key]['metaData'] = $this->_getGroupMetaData($value['admin_access_group_id']);
            $resultArray[$key]['items']    = $this->_getGroupItemsData($value['admin_access_group_id']);
        }
        
        $groupsArray = [];
        foreach ($resultArray as $groupData) {
            $groupsArray[] = $this->_createAdminAccessGroupByArray($groupData);
        }
        
        $adminAccessGroupCollection = $this->factory->createAdminAccessGroupCollection($groupsArray);
        
        return $adminAccessGroupCollection;
    }
    
    
    /**
     * Returns an AdminAccessGroupCollection instance with all child groups for the given group ID.
     *
     * @param IdType $groupId Group ID.
     *
     * @return AdminAccessGroupCollection Group collection.
     */
    public function getChildren(IdType $groupId)
    {
        $resultArray = $this->queryBuilder->select()
            ->from($this->adminAccessGroupsTable)
            ->where('parent_id',
                    $groupId->asInt())
            ->order_by('sort_order')
            ->get()
            ->result_array();
        
        foreach ($resultArray as $key => $value) {
            $resultArray[$key]['metaData'] = $this->_getGroupMetaData($value['admin_access_group_id']);
            $resultArray[$key]['items']    = $this->_getGroupItemsData($value['admin_access_group_id']);
        }
        
        $groupsArray = [];
        foreach ($resultArray as $groupData) {
            $groupsArray[] = $this->_createAdminAccessGroupByArray($groupData);
        }
        
        $adminAccessGroupCollection = $this->factory->createAdminAccessGroupCollection($groupsArray);
        
        return $adminAccessGroupCollection;
    }
    
    
    /**
     * Returns the group descriptions that were queried from the database.
     *
     * @param int $groupId Group ID.
     *
     * @return array Array with group meta data.
     */
    protected function _getGroupMetaData($groupId)
    {
        $groupMetaData = $this->queryBuilder->select()
            ->from($this->adminAccessGroupsDescriptionsTable)
            ->where('admin_access_group_id',
                    $groupId)
            ->get()
            ->result_array();
        
        return $groupMetaData;
    }
    
    
    /**
     * Returns the group descriptions that were queried from the database.
     *
     * @param int $groupId Group ID.
     *
     * @return array Array with group meta data.
     */
    protected function _getGroupItemsData($groupId)
    {
        $groupItemData = $this->queryBuilder->select()
            ->from($this->adminAccessGroupItemsTable)
            ->where('admin_access_group_id',
                    $groupId)
            ->get()
            ->result_array();
        
        return $groupItemData;
    }
    
    
    /**
     * Returns a AdminAccessGroup object, created from an array with the group data.
     *
     * @param array $groupData Group data.
     *
     * @return AdminAccessGroupInterface Group object.
     */
    protected function _createAdminAccessGroupByArray(array $groupData)
    {
        $groupMetaData = $groupData['metaData'];
        $accessGroup   = $this->factory->createAdminAccessGroup();
        
        $accessGroup->setId(new IdType($groupData['admin_access_group_id']));
        $accessGroup->setSortOrder(new IntType($groupData['sort_order']));
        $accessGroup->setProtected(new BoolType($groupData['protected']));
        
        $name        = [];
        $description = [];
        $items       = [];
        
        foreach ($groupMetaData as $meta) {
            $languageCode               = $this->languageProvider->getCodeById(new IdType($meta['language_id']))
                ->asString();
            $name[$languageCode]        = $meta['name'];
            $description[$languageCode] = $meta['description'];
        }
        
        if (count($groupData['items']) > 0) {
            foreach ($groupData['items'] as $item) {
                $items[] = $this->factory->createAdminAccessGroupItem(new IdType($item['admin_access_group_id']),
                                                                      new NonEmptyStringType($item['identifier']),
                                                                      new NonEmptyStringType($item['type']));
            }
        }
        
        $accessGroup->setName(new KeyValueCollection($name));
        $accessGroup->setDescription(new KeyValueCollection($description));
        $accessGroup->setItems($this->factory->createAdminAccessGroupItemCollection($items));
        
        try {
            $parentGroup = $this->getById(new IdType($groupData['parent_id']));
            $accessGroup->setParentGroup($parentGroup);
        } catch (GroupNotFoundException $e) {
        }
        
        return $accessGroup;
    }
}

<?php

/* --------------------------------------------------------------
   AdminAccessGroupFactory.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessGroupFactory
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
class AdminAccessGroupFactory implements AdminAccessGroupFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var AdminAccessGroupReaderInterface
     */
    protected $groupReader;
    
    /**
     * @var AdminAccessGroupWriterInterface
     */
    protected $groupWriter;
    
    /**
     * @var AdminAccessGroupDeleterInterface
     */
    protected $groupDeleter;
    
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    
    /**
     * AdminAccessPermissionFactory constructor.
     *
     * @param CI_DB_query_builder $queryBuilder Query builder.
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Creates an admin access group.
     *
     * @return AdminAccessGroupInterface Group object.
     */
    public function createAdminAccessGroup()
    {
        return MainFactory::create(AdminAccessGroup::class,
                                   $this->_createAdminAccessGroupReader(),
                                   $this->_createAdminAccessGroupWriter(),
                                   $this->_createAdminAccessGroupDeleter(),
                                   $this->createAdminAccessGroupItemCollection([]));
    }
    
    
    /**
     * Creates an admin access group collection.
     *
     * @param array $groupsArray Array of AdminAccessGroupInterface objects.
     *
     * @return AdminAccessGroupCollection Group collection.
     */
    public function createAdminAccessGroupCollection(array $groupsArray)
    {
        return MainFactory::create(AdminAccessGroupCollection::class, $groupsArray);
    }
    
    
    /**
     * Creates an admin access group item.
     *
     * @return AdminAccessGroupItemInterface Group item object.
     */
    public function createAdminAccessGroupItem(
        IntType $groupId,
        NonEmptyStringType $identifier,
        NonEmptyStringType $type
    ) {
        return MainFactory::create(AdminAccessGroupItem::class, $groupId, $identifier, $type);
    }
    
    
    /**
     * Creates an admin access group item collection.
     *
     * @param array $groupItemsArray Array of AdminAccessGroupItemInterface objects.
     *
     * @return AdminAccessGroupItemCollection Group item collection.
     */
    public function createAdminAccessGroupItemCollection(array $groupItemsArray)
    {
        return MainFactory::create(AdminAccessGroupItemCollection::class, $groupItemsArray);
    }
    
    
    /**
     * Creates an admin access group reader.
     *
     * @return AdminAccessGroupReaderInterface Group reader object.
     */
    protected function _createAdminAccessGroupReader()
    {
        if (null === $this->groupReader) {
            $this->groupReader = MainFactory::create(AdminAccessGroupReader::class,
                                                     $this->queryBuilder,
                                                     $this,
                                                     $this->_createLanguageProvider());
        }
        
        return $this->groupReader;
    }
    
    
    /**
     * Creates an admin access group reader.
     *
     * @return AdminAccessGroupWriterInterface Group reader object.
     */
    protected function _createAdminAccessGroupWriter()
    {
        if (null === $this->groupWriter) {
            $this->groupWriter = MainFactory::create(AdminAccessGroupWriter::class,
                                                     $this->queryBuilder,
                                                     $this->_createLanguageProvider());
        }
        
        return $this->groupWriter;
    }
    
    
    /**
     * Creates an admin access group reader.
     *
     * @return AdminAccessGroupDeleterInterface Group reader object.
     */
    protected function _createAdminAccessGroupDeleter()
    {
        if (null === $this->groupDeleter) {
            $this->groupDeleter = MainFactory::create(AdminAccessGroupDeleter::class, $this->queryBuilder);
        }
        
        return $this->groupDeleter;
    }
    
    
    /**
     * Creates a language provider.
     *
     * @return LanguageProviderInterface Language provider object.
     */
    protected function _createLanguageProvider()
    {
        if (null === $this->languageProvider) {
            $this->languageProvider = MainFactory::create(LanguageProvider::class, $this->queryBuilder);
        }
        
        return $this->languageProvider;
    }
}

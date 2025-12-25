<?php

/* --------------------------------------------------------------
   AdminAccessPermissionFactory.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessPermissionFactory
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
class AdminAccessPermissionFactory implements AdminAccessPermissionFactoryInterface
{
    /**
     * @var AdminAccessGroupReaderInterface
     */
    protected $groupReader;
    
    /**
     * @var AdminAccessPermissionWriterInterface
     */
    protected $permissionWriter;
    
    /**
     * @var AdminAccessPermissionDeleterInterface
     */
    protected $permissionDeleter;
    
    /**
     * @var AdminAccessPermissionReaderInterface
     */
    protected $permissionReader;
    
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * AdminAccessPermissionFactory constructor.
     *
     * @param CI_DB_query_builder             $queryBuilder Query builder.
     * @param AdminAccessGroupReaderInterface $groupReader  Admin access group reader.
     */
    public function __construct(
        CI_DB_query_builder $queryBuilder,
        AdminAccessGroupReaderInterface $groupReader
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->groupReader  = $groupReader;
    }
    
    
    /**
     * Creates an admin access permission.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Permission object.
     */
    public function createAdminAccessPermission()
    {
        return MainFactory::create(AdminAccessPermission::class,
                                   $this->_createAdminAccessPermissionDeleter(),
                                   $this->_createAdminAccessPermissionWriter(),
                                   $this->groupReader,
                                   $this->_createAdminAccessPermissionReader());
    }
    
    
    /**
     * Creates an admin access permission collection.
     *
     * @param array $permissionObjectsArray Array of permission objects.
     *
     * @return AdminAccessPermissionCollection Permission collection.
     */
    public function createAdminAccessPermissionCollection(array $permissionObjectsArray = [])
    {
        return MainFactory::create(AdminAccessPermissionCollection::class, $permissionObjectsArray);
    }
    
    
    /**
     * Creates an admin access permission writer.
     *
     * @return AdminAccessPermissionWriterInterface Permission writer object.
     */
    protected function _createAdminAccessPermissionWriter()
    {
        if (null === $this->permissionWriter) {
            $this->permissionWriter = MainFactory::create(AdminAccessPermissionWriter::class, $this->queryBuilder);
        }
        
        return $this->permissionWriter;
    }
    
    
    /**
     * Creates an admin access permission deleter.
     *
     * @return AdminAccessPermissionDeleter Permission deleter object.
     */
    protected function _createAdminAccessPermissionDeleter()
    {
        if (null === $this->permissionDeleter) {
            $this->permissionDeleter = MainFactory::create(AdminAccessPermissionDeleter::class, $this->queryBuilder);
        }
        
        return $this->permissionDeleter;
    }
    
    
    /**
     * Creates an admin access permission reader.
     *
     * @return AdminAccessPermissionReaderInterface Permission reader object.
     */
    protected function _createAdminAccessPermissionReader()
    {
        if (null === $this->permissionReader) {
            $this->permissionReader = MainFactory::create(AdminAccessPermissionReader::class,
                                                          $this->queryBuilder,
                                                          $this);
        }
        
        return $this->permissionReader;
    }
}

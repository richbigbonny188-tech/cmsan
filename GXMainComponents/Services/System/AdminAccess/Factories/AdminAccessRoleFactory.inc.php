<?php

/* --------------------------------------------------------------
   AdminAccessRoleFactory.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessRoleFactory
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
class AdminAccessRoleFactory implements AdminAccessRoleFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    
    /**
     * AdminAccessRoleFactory constructor.
     *
     * @param CI_DB_query_builder       $queryBuilder     Query builder.
     * @param LanguageProviderInterface $languageProvider Language provider.
     */
    public function __construct(CI_DB_query_builder $queryBuilder, LanguageProviderInterface $languageProvider)
    {
        $this->queryBuilder     = $queryBuilder;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Creates an admin access role.
     *
     * @return AdminAccessRoleInterface Role object.
     */
    public function createAdminAccessRole()
    {
        return MainFactory::create(AdminAccessRole::class,
                                   $this->_createAdminAccessRoleDeleter(),
                                   $this->_createAdminAccessRoleWriter(),
                                   $this->_createAdminAccessPermissionReader());
    }
    
    
    /**
     * Creates an admin access role collection.
     *
     * @return AdminAccessRoleCollection Role collection.
     */
    public function createAdminAccessRoleCollection()
    {
        return MainFactory::create(AdminAccessRoleCollection::class);
    }
    
    
    /**
     * Creates an admin access role deleter.
     *
     * @return AdminAccessRoleDeleterInterface Role deleter object.
     */
    protected function _createAdminAccessRoleDeleter()
    {
        return MainFactory::create(AdminAccessRoleDeleter::class, $this->queryBuilder);
    }
    
    
    /**
     * Creates an admin access role writer.
     *
     * @return AdminAccessRoleWriterInterface Role writer object.
     */
    protected function _createAdminAccessRoleWriter()
    {
        return MainFactory::create(AdminAccessRoleWriter::class, $this->queryBuilder, $this->languageProvider);
    }
    
    
    /**
     * Creates an admin access permission reader.
     *
     * @return AdminAccessPermissionReaderInterface Permission reader object.
     */
    protected function _createAdminAccessPermissionReader()
    {
        return MainFactory::create(AdminAccessPermissionReader::class,
                                   $this->queryBuilder,
                                   $this->_createAdminAccessPermissionFactory());
    }
    
    
    /**
     * Creates an admin access permission factory.
     *
     * @return AdminAccessPermissionFactoryInterface Permission factory object.
     */
    protected function _createAdminAccessPermissionFactory()
    {
        return MainFactory::create(AdminAccessPermissionFactory::class,
                                   $this->queryBuilder,
                                   $this->_createAdminAccessGroupReader());
    }
    
    
    /**
     * Creates an admin access group reader.
     *
     * @return AdminAccessGroupReader Group reader object.
     */
    protected function _createAdminAccessGroupReader()
    {
        return MainFactory::create(AdminAccessGroupReader::class,
                                   $this->queryBuilder,
                                   $this->_createAdminAccessGroupFactory(),
                                   $this->languageProvider);
    }
    
    
    /**
     * Creates an admin access group factory.
     *
     * @return AdminAccessGroupFactoryInterface Group factory object.
     */
    protected function _createAdminAccessGroupFactory()
    {
        return MainFactory::create(AdminAccessGroupFactory::class, $this->queryBuilder);
    }
}

<?php

/* --------------------------------------------------------------
 AdminAccessServiceFactory.inc.php 2018-01-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class AdminAccessServiceFactory
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
class AdminAccessServiceFactory implements AdminAccessServiceFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var AdminAccessPermissionManagerInterface
     */
    protected $permissionManager;
    
    /**
     * @var AdminAccessRoleManagerInterface
     */
    protected $roleManager;
    
    
    /**
     * @var AdminAccessUserManagerInterface
     */
    protected $userManager;
    
    
    /**
     * @var AdminAccessGroupManagerInterface
     */
    protected $groupManager;
    
    
    /**
     * @var AdminAccessGroupReaderInterface
     */
    protected $groupReader;
    
    
    /**
     * @var AdminAccessRoleFactoryInterface
     */
    protected $roleFactory;
    
    
    /**
     * @var AdminAccessUserReaderInterface
     */
    protected $userReader;
    
    
    /**
     * @var AdminAccessRoleReaderInterface
     */
    protected $roleReader;
    
    
    /**
     * @var AdminAccessGroupFactory
     */
    protected $groupFactory;
    
    
    /**
     * @var AdminAccessUserFactory
     */
    protected $userFactory;
    
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    /**
     * @var AdminAccessPermissionFactoryInterface
     */
    protected $permissionFactory;
    
    /**
     * @var AdminAccessPermissionReaderInterface
     */
    protected $permissionReader;
    
    /**
     * @var AdminAccessSettingsInterface
     */
    protected $adminAccessSettings;
    
    
    /**
     * AdminAccessServiceFactory constructor.
     *
     * @param CI_DB_query_builder $queryBuilder Query builder.
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Creates an admin access service.
     *
     * @return AdminAccessServiceInterface Service object.
     */
    public function createAdminAccessService()
    {
        return MainFactory::create(AdminAccessService::class,
                                   $this->_createAdminAccessSettings(),
                                   $this->_createAdminAccessPermissionManager(),
                                   $this->_createAdminAccessRoleManager(),
                                   $this->_createAdminAccessUserManager(),
                                   $this->_createAdminAccessGroupManager());
    }
    
    
    /**
     * Creates an admin access permission manager.
     *
     * @return AdminAccessPermissionManagerInterface Permission manager object.
     */
    protected function _createAdminAccessPermissionManager()
    {
        if (null === $this->permissionManager) {
            $this->permissionManager = MainFactory::create(AdminAccessPermissionManager::class,
                                                           $this->_createAdminAccessGroupReader(),
                                                           $this->_createAdminAccessRoleFactory(),
                                                           $this->_createAdminAccessUserReader(),
                                                           $this->_createAdminAccessRoleReader());
        }
        
        return $this->permissionManager;
    }
    
    
    /**
     * Creates an admin access role manager.
     *
     * @return AdminAccessRoleManagerInterface Role manager object.
     */
    protected function _createAdminAccessRoleManager()
    {
        if (null === $this->roleManager) {
            $this->roleManager = MainFactory::create(AdminAccessRoleManager::class,
                                                     $this->_createAdminAccessRoleFactory(),
                                                     $this->_createAdminAccessRoleReader());
        }
        
        return $this->roleManager;
    }
    
    
    /**
     * Creates an admin access user manager.
     *
     * @return AdminAccessUserManagerInterface User manager object.
     */
    protected function _createAdminAccessUserManager()
    {
        if (null === $this->userManager) {
            $this->userManager = MainFactory::create(AdminAccessUserManager::class,
                                                     $this->_createAdminAccessUserReader(),
                                                     $this->_createAdminAccessRoleReader());
        }
        
        return $this->userManager;
    }
    
    
    /**
     * Creates an admin access group manager.
     *
     * @return AdminAccessGroupManagerInterface Group manager object.
     */
    protected function _createAdminAccessGroupManager()
    {
        if (null === $this->groupManager) {
            $this->groupManager = MainFactory::create(AdminAccessGroupManager::class,
                                                      $this->_createAdminAccessGroupFactory(),
                                                      $this->_createAdminAccessGroupReader());
        }
        
        return $this->groupManager;
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
                                                     $this->_createAdminAccessGroupFactory(),
                                                     $this->_createLanguageProvider());
        }
        
        return $this->groupReader;
    }
    
    
    /**
     * Creates an admin access role factory.
     *
     * @return AdminAccessRoleFactoryInterface Role factory object.
     */
    protected function _createAdminAccessRoleFactory()
    {
        if (null === $this->roleFactory) {
            $this->roleFactory = MainFactory::create(AdminAccessRoleFactory::class,
                                                     $this->queryBuilder,
                                                     $this->languageProvider);
        }
        
        return $this->roleFactory;
    }
    
    
    /**
     * Creates an admin access user reader.
     *
     * @return AdminAccessUserReaderInterface User reader object.
     */
    protected function _createAdminAccessUserReader()
    {
        if (null === $this->userReader) {
            $this->userReader = MainFactory::create(AdminAccessUserReader::class,
                                                    $this->queryBuilder,
                                                    $this->_createAdminAccessUserFactory(),
                                                    $this->_createAdminAccessRoleReader());
        }
        
        return $this->userReader;
    }
    
    
    /**
     * Creates an admin access role reader.
     *
     * @return AdminAccessRoleReaderInterface Role reader object.
     */
    protected function _createAdminAccessRoleReader()
    {
        if (null === $this->roleReader) {
            $this->roleReader = MainFactory::create(AdminAccessRoleReader::class,
                                                    $this->queryBuilder,
                                                    $this->_createAdminAccessRoleFactory(),
                                                    $this->languageProvider);
        }
        
        return $this->roleReader;
    }
    
    
    /**
     * Creates an admin access group factory.
     *
     * @return AdminAccessGroupFactoryInterface Group factory object.
     */
    protected function _createAdminAccessGroupFactory()
    {
        if (null === $this->groupFactory) {
            $this->groupFactory = MainFactory::create(AdminAccessGroupFactory::class, $this->queryBuilder);
        }
        
        return $this->groupFactory;
    }
    
    
    /**
     * Creates an admin access user factory.
     *
     * @return AdminAccessUserFactoryInterface User factory object.
     */
    protected function _createAdminAccessUserFactory()
    {
        if (null === $this->userFactory) {
            $this->userFactory = MainFactory::create(AdminAccessUserFactory::class,
                                                     $this->queryBuilder,
                                                     $this->_createAdminAccessRoleFactory());
        }
        
        return $this->userFactory;
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
    
    
    /**
     * Creates an admin access settings object.
     *
     * @return AdminAccessSettingsInterface Admin access settings.
     */
    protected function _createAdminAccessSettings()
    {
        if (null === $this->adminAccessSettings) {
            $this->adminAccessSettings = MainFactory::create(AdminAccessSettings::class);
        }
        
        return $this->adminAccessSettings;
    }
}

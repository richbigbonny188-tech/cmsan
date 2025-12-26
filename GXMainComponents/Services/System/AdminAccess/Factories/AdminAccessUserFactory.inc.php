<?php

/* --------------------------------------------------------------
   AdminAccessUserFactory.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessUserFactory
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Factories
 */
class AdminAccessUserFactory implements AdminAccessUserFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var AdminAccessRoleFactory
     */
    protected $roleFactory;
    
    
    /**
     * AdminAccessUserFactory constructor.
     *
     * @param CI_DB_query_builder    $queryBuilder Query builder.
     * @param AdminAccessRoleFactory $roleFactory  Role factory.
     */
    public function __construct(CI_DB_query_builder $queryBuilder, AdminAccessRoleFactory $roleFactory)
    {
        $this->queryBuilder = $queryBuilder;
        $this->roleFactory  = $roleFactory;
    }
    
    
    /**
     * Creates an admin access user.
     *
     * @param IdType $customerId User id.
     *
     * @return AdminAccessUserInterface User object.
     */
    public function createAdminAccessUser(IdType $customerId)
    {
        return MainFactory::create(AdminAccessUser::class,
                                   $this->_createAdminAccessUserWriter(),
                                   $this->_createAdminAccessUserDeleter(),
                                   $customerId,
                                   $this->roleFactory->createAdminAccessRoleCollection());
    }
    
    
    /**
     * Creates an admin access user writer.
     *
     * @return AdminAccessUserWriterInterface User writer object.
     */
    protected function _createAdminAccessUserWriter()
    {
        return MainFactory::create(AdminAccessUserWriter::class, $this->queryBuilder);
    }
    
    
    /**
     * Creates an admin access user deleter.
     *
     * @return AdminAccessUserDeleterInterface User deleter object.
     */
    protected function _createAdminAccessUserDeleter()
    {
        return MainFactory::create(AdminAccessUserDeleter::class, $this->queryBuilder);
    }
    
}

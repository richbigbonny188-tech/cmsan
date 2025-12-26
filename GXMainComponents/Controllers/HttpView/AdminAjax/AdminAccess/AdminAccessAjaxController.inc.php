<?php

/* --------------------------------------------------------------
   AdminAccessAjaxController.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessAjaxController
 *
 * Ajax controller for the admin access ajax requests.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class AdminAccessAjaxController extends AdminHttpViewController
{
    /**
     * @var \AdminAccessService
     */
    protected $adminAccessService;
    
    /**
     * Database connection.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var \LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * Init
     */
    public function init()
    {
        $this->validateCurrentAdminStatus();
        
        $this->adminAccessService  = StaticGXCoreLoader::getService('AdminAccess');
        $this->db                  = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->languageTextManager = MainFactory::create('LanguageTextManager',
                                                         'admin_access',
                                                         $_SESSION['languages_id']);
        $this->languageProvider    = MainFactory::create('LanguageProvider', $this->db);
    }
    
    
    /**
     * Returns all roles information for a given role id.
     *
     * @param int roleId [GET] Id of the role that should be deleted.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionGetRoleData()
    {
        $roleId = (int)$this->_getQueryParameter('roleId');
        if ($roleId <= 0) {
            return MainFactory::create('JsonHttpControllerResponse',
                                       [
                                           'success' => false,
                                           'error'   => $this->languageTextManager->get_text('error_invalid_id')
                                       ]);
        }
        
        $role = $this->adminAccessService->getRoleById(new IdType($roleId));
        
        return MainFactory::create('JsonHttpControllerResponse',
                                   [
                                       'success' => true,
                                       'data'    => [
                                           'id'                     => $role->getId(),
                                           'sortOrder'              => $role->getSortOrder(),
                                           'unknownReadingGranted'  => $role->checkReadingPermissionForUnknownGroup(),
                                           'unknownWritingGranted'  => $role->checkWritingPermissionForUnknownGroup(),
                                           'unknownDeletingGranted' => $role->checkDeletingPermissionForUnknownGroup(),
                                           'names'                  => $role->getName()->getArray(),
                                           'descriptions'           => $role->getDescription()->getArray(),
                                       ]
                                   ]);
    }
    
    
    /**
     * Deletes a role by a given role id.
     *
     * @param int roleId [POST] Id of the role that should be deleted.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionDeleteRole()
    {
        $roleId = (int)$this->_getPostData('roleId');
        if ($roleId <= 0) {
            return MainFactory::create('JsonHttpControllerResponse',
                                       [
                                           'success' => false,
                                           'error'   => $this->languageTextManager->get_text('error_invalid_id')
                                       ]);
        }
        
        $this->adminAccessService->deleteRoleById(new IdType($roleId));
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
    }
    
    
    /**
     * Creates or updates a role by the given parameters.
     *
     * @param int id             [POST] Id of the role that should be updated. If null or empty a new role will be
     *                           created.
     * @param int sortOrder      [POST] Value of the sort order.
     * @param array names        [POST] Array that contains all language depended names. Index must be the language id.
     * @param array descriptions [POST] Array that contains all language depended descriptions. Index must be the
     *                           language id.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionSaveRoleData()
    {
        $roleId                 = (int)$this->_getPostData('id');
        $unknownReadingGranted  = true;
        $unknownWritingGranted  = true;
        $unknownDeletingGranted = true;
        $sortOrder              = (int)$this->_getPostData('sortOrder');
        $names                  = $this->_getPostData('names');
        $descriptions           = $this->_getPostData('descriptions');
        
        if ($roleId <= 0) {
            $roleId = $this->adminAccessService->createNewRole(new KeyValueCollection($names),
                                                               new KeyValueCollection($descriptions),
                                                               new IntType($sortOrder),
                                                               new BoolType($unknownReadingGranted),
                                                               new BoolType($unknownWritingGranted),
                                                               new BoolType($unknownDeletingGranted))->getId();
        } else {
            $this->adminAccessService->updateRoleById(new IdType($roleId),
                                                      new KeyValueCollection($names),
                                                      new KeyValueCollection($descriptions),
                                                      new IntType($sortOrder),
                                                      new BoolType($unknownReadingGranted),
                                                      new BoolType($unknownWritingGranted),
                                                      new BoolType($unknownDeletingGranted));
        }
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true, 'roleId' => $roleId]);
    }
    
    
    /**
     * Updates the sort orders of all roles.
     *
     * @param array sorting [POST] Contains new sort orders. The index must be the role id.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionSaveRoleSorting()
    {
        $sorting = array_flip($this->_getPostData('sorting'));
        
        if (count($sorting) === 0) {
            return MainFactory::create('JsonHttpControllerResponse',
                                       [
                                           'success' => false,
                                           'error'   => 'sorting: '
                                                        . $this->languageTextManager->get_text('error_invalid_value')
                                       ]);
        }
        
        $roles = $this->adminAccessService->getAllRoles();
        
        /** @var \AdminAccessRoleInterface $role */
        foreach ($roles->getArray() as $role) {
            $newSortOrder = isset($sorting[$role->getId()]) ? (int)$sorting[$role->getId()] + 1 : 0;
            $this->adminAccessService->updateRoleById(new IdType($role->getId()),
                                                      $role->getName(),
                                                      $role->getDescription(),
                                                      new IntType($newSortOrder),
                                                      new BoolType($role->checkReadingPermissionForUnknownGroup()),
                                                      new BoolType($role->checkWritingPermissionForUnknownGroup()),
                                                      new BoolType($role->checkDeletingPermissionForUnknownGroup()));
        }
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
    }
}
<?php
/* --------------------------------------------------------------
   CustomerLoginController.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/inc/gm_string_filter.inc.php');
require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');

MainFactory::load_class('AdminHttpViewController');

/**
 * Class CustomerLoginController
 *
 * Controller to login as a certain customer
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class CustomerLoginController extends AdminHttpViewController
{
    public function actionDefault()
    {
        $_SESSION['coo_page_token']->is_valid($this->_getQueryParameter('pageToken'));
        
        $customerId = (int)$this->_getQueryParameter('customerId');
        $db         = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $query = 'SELECT * 
					FROM customers 
					WHERE 
						customers_id = ' . $customerId . ' AND 
						customers_status != 0';
        
        if ($db->query($query)->num_rows()) {
            if (!function_exists('xtc_create_random_value')) {
                require_once DIR_FS_INC . 'xtc_create_random_value.inc.php';
            }
            
            $logoffControl = MainFactory::create('LogoffContentControl');
            $loginControl  = MainFactory::create('LoginContentControl');
            
            $logoffControl->reset_user_session();
            $loginControl->loginAfterSuccessfulAuthorization($customerId, true);
            
            return MainFactory::create('RedirectHttpControllerResponse', HTTP_SERVER . DIR_WS_CATALOG . 'account.php');
        }
        
        return MainFactory::create('RedirectHttpControllerResponse', 'customers.php');
    }
}
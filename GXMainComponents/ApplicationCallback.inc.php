<?php
/* --------------------------------------------------------------
   ApplicationCallback.inc.php 2022-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003	 nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: application_top_callback.php 149 2007-01-24 09:46:13Z mzanier $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon
   
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

namespace Gambio\GX;

require_once __DIR__ . '/Application.inc.php';

use Doctrine\DBAL\Exception\DriverException;

/**
 * Class ApplicationCallback
 * @package Gambio
 */
class ApplicationCallback extends Application
{
    public function run()
    {
        $this->registerComposerAutoloader();
        $this->runGProtector();
        
        self::loadConfig();
        
        $this->checkRequestUriForCorrectProtocolAndDomain();
        $this->setUpEnvironment();
    }
    
    
    protected function setUpEnvironment()
    {
        $this->setMissingServerVariables();
        $this->defineInitialConstants();
        $this->setMemoryLimit();
        $this->includeWrapperFunctions();
        $this->initGXEngine();
        $this->setTimezone();
        $this->registerAutoloader();
        try {
            $this->registerErrorHandler();
        } catch (DriverException $exception) {
            self::handleDbConnectionError($exception);
        }
        $this->initializeGlobalDebuggerObject();
        $this->initializeGlobalPhpSelfVariable();
        $this->includeFunctions();
        $this->connectToDatabase();
        $this->defineConstantsFromDbConfigurationTable();
        $this->updateTimezone(DATE_TIMEZONE);
        $this->startSession();
        $this->setXSRFPageToken();
    }
    
    
    protected function defineConstantsFromDbConfigurationTable()
    {
        $configuration_query = xtc_db_query('SELECT
                                                    `key`,
                                                    `value`
                                                FROM gx_configurations WHERE `key` LIKE "configuration/%";');
        
        while ($configuration = xtc_db_fetch_array($configuration_query)) {
            $key = str_replace('configuration/', '', $configuration['key']);
            if (!defined($key)) {
                define($key, $configuration['value']);
            }
        }
        
        if (gm_get_conf('SUPPRESS_INDEX_IN_URL') === 'true') {
            define('FILENAME_DEFAULT', '');
        } else {
            define('FILENAME_DEFAULT', 'index.php');
        }
    }
    
    
    protected function defineInitialConstants()
    {
        if (!defined('APPLICATION_RUN_MODE')) {
            define('APPLICATION_RUN_MODE', 'frontend');
        }
        
        define('PROJECT_VERSION', 'xt:Commerce v3.0.4 SP2.1');
        define('PAGE_PARSE_START_TIME', microtime(true));
        
        // include the list of project filenames
        require_once DIR_WS_INCLUDES . 'filenames.php';
        
        // include the list of project database tables
        require_once DIR_WS_INCLUDES . 'database_tables.php';
        
        define('STORE_DB_TRANSACTIONS', 'false');
    }
    
    
    protected function includeFunctions()
    {
        // Database
        require_once DIR_FS_INC . 'xtc_db_connect.inc.php';
        require_once DIR_FS_INC . 'xtc_db_close.inc.php';
        require_once DIR_FS_INC . 'xtc_db_perform.inc.php';
        require_once DIR_FS_INC . 'xtc_db_query.inc.php';
        require_once DIR_FS_INC . 'xtc_db_fetch_array.inc.php';
        require_once DIR_FS_INC . 'xtc_db_num_rows.inc.php';
        require_once DIR_FS_INC . 'xtc_db_insert_id.inc.php';
        require_once DIR_FS_INC . 'xtc_db_free_result.inc.php';
        require_once DIR_FS_INC . 'xtc_db_input.inc.php';
        require_once DIR_FS_INC . 'xtc_db_prepare_input.inc.php';
        
        // include needed functions
        require_once DIR_FS_INC . 'xtc_draw_form.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_input_field.inc.php'; // TODO delete
        
        // html basics
        require_once DIR_FS_INC . 'xtc_href_link.inc.php';
        require_once DIR_FS_INC . 'xtc_draw_separator.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_php_mail.inc.php';
        
        require_once DIR_FS_INC . 'xtc_product_link.inc.php';
        require_once DIR_FS_INC . 'xtc_category_link.inc.php';
        
        // html functions
        require_once DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_form.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_hidden_field.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_input_field.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_password_field.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_radio_field.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_selection_field.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_separator.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_draw_textarea_field.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_image_button.inc.php'; // TODO delete
        
        require_once DIR_FS_INC . 'xtc_not_null.inc.php';
        require_once DIR_FS_INC . 'xtc_parse_category_path.inc.php';
        require_once DIR_FS_INC . 'xtc_get_product_path.inc.php';
        require_once DIR_FS_INC . 'xtc_get_parent_categories.inc.php';
        require_once DIR_FS_INC . 'xtc_redirect.inc.php';
        require_once DIR_FS_INC . 'xtc_get_uprid.inc.php';
        require_once DIR_FS_INC . 'xtc_get_all_get_params.inc.php';
        require_once DIR_FS_INC . 'xtc_has_product_attributes.inc.php';
        require_once DIR_FS_INC . 'xtc_image.inc.php'; // TODO delete
        require_once DIR_FS_INC . 'xtc_check_stock_attributes.inc.php';
        require_once DIR_FS_INC . 'xtc_currency_exists.inc.php';
        require_once DIR_FS_INC . 'xtc_remove_non_numeric.inc.php';
        require_once DIR_FS_INC . 'xtc_get_ip_address.inc.php';
        require_once DIR_FS_INC . 'xtc_count_cart.inc.php';
        require_once DIR_FS_INC . 'xtc_get_qty.inc.php';
        require_once DIR_FS_INC . 'xtc_get_tax_rate.inc.php';
        require_once DIR_FS_INC . 'xtc_add_tax.inc.php';
        require_once DIR_FS_INC . 'xtc_cleanName.inc.php';
        require_once DIR_FS_INC . 'xtc_calculate_tax.inc.php';
        require_once DIR_FS_INC . 'xtc_input_validation.inc.php';
        require_once DIR_FS_INC . 'fetch_email_template.inc.php';
        
        require_once DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php';
    }
}
<?php
/* --------------------------------------------------------------
   ApplicationExport.inc.php 2020-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003	 nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: application_top_export.php 1323 2005-10-27 17:58:08Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon
   
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

namespace Gambio\GX;

require_once __DIR__ . '/Application.inc.php';

/**
 * Class ApplicationExport
 * @package Gambio
 */
class ApplicationExport extends Application
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
	    if (file_exists(DIR_FS_CATALOG . '.dev-environment')) {
		    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_CORE_ERROR & ~E_CORE_WARNING);
	    } else {
		    error_reporting(0);
		    ini_set('display_errors', '0');
	    }
        
        $this->setMissingServerVariables();
        $this->defineInitialConstants();
        $this->setMemoryLimit();
        $this->includeWrapperFunctions();
        $this->initGXEngine();
        $this->setTimezone();
        $this->registerAutoloader();
        $this->initializeGlobalDebuggerObject();
        $this->initializeGlobalPhpSelfVariable();
        $this->includeFunctions();
        $this->connectToDatabase();
        $this->defineConstantsFromDbConfigurationTable();
        $this->updateTimezone(DATE_TIMEZONE);
        
        // Include Template Engine
        require DIR_FS_CATALOG . 'vendor/smarty/smarty/libs/Smarty.class.php';
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
        require_once DIR_FS_INC . 'fetch_email_template.inc.php';
        
        // include used functions
        require_once DIR_FS_INC . 'xtc_db_connect.inc.php';
        require_once DIR_FS_INC . 'xtc_db_close.inc.php';
        require_once DIR_FS_INC . 'xtc_db_error.inc.php';
        require_once DIR_FS_INC . 'xtc_db_perform.inc.php';
        require_once DIR_FS_INC . 'xtc_db_query.inc.php';
        require_once DIR_FS_INC . 'xtc_db_fetch_array.inc.php';
        require_once DIR_FS_INC . 'xtc_db_num_rows.inc.php';
        require_once DIR_FS_INC . 'xtc_db_data_seek.inc.php';
        require_once DIR_FS_INC . 'xtc_db_insert_id.inc.php';
        require_once DIR_FS_INC . 'xtc_db_free_result.inc.php';
        require_once DIR_FS_INC . 'xtc_db_fetch_fields.inc.php';
        require_once DIR_FS_INC . 'xtc_db_output.inc.php';
        require_once DIR_FS_INC . 'xtc_db_input.inc.php';
        require_once DIR_FS_INC . 'xtc_db_prepare_input.inc.php';
        require_once DIR_FS_INC . 'clean_numeric_input.inc.php';
        require_once DIR_FS_INC . 'country_eu_status_by_country_id.inc.php';
        require_once DIR_FS_INC . 'update_customer_b2b_status.inc.php';
        
        require_once DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php';
    }
}
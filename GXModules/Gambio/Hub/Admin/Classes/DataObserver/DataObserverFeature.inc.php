<?php
/* --------------------------------------------------------------
   DataObserverFeature.inc.php 2022-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DataObserverFeature
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class DataObserverFeature
{
	/**
	 * @var string
	 */
	const GLOBAL_CONFIGURATION_KEY = 'MODULE_PAYMENT_GAMBIO_HUB_DATA_OBSERVER';

	/**
	 * @var string
	 */
	const MODULE_CONFIGURATION_KEY = '%GAMBIO_HUB_REMOTE_CONFIG_%_ERPINTEGRATION%';


	/**
	 * Checks whether the data observer feature is active.
	 *
	 * The data observer is a rather resource consuming feature adding extra overhead to each request and should
	 * therefore only be executed, when it is really necessary. This method will check all the required cases that
	 * need to be met, before considering the feature as active.
	 *
	 * @return bool
	 */
	public static function isActive()
	{
		// Check if the global configuration value is true.
		if(!defined(self::GLOBAL_CONFIGURATION_KEY))
		{
			return false; // Do not process data changes if the data observer configuration entry is missing.
		}

		if(defined(self::GLOBAL_CONFIGURATION_KEY)
		   && !filter_var(@constant(self::GLOBAL_CONFIGURATION_KEY), FILTER_VALIDATE_BOOLEAN))
		{
			return false; // Do not process data changes if the data observer feature is inactive.
		}
        
        set_error_handler('DataObserverFeature::silent_error_handler');

		// Check if there is at least one Hub module that has the ERP integration feature on.
		$queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        if ($queryBuilder->conn_id === false
            || (@$queryBuilder->conn_id instanceof mysqli
                && @$queryBuilder->conn_id->ping() !== true)
            || !is_object($GLOBALS['db_link'])
            || (@$GLOBALS['db_link'] instanceof mysqli
                && @$GLOBALS['db_link']->ping() !== true)) {
            restore_error_handler();
            
            return false;
        }

		$isLegacyConfiguration = $queryBuilder->table_exists('gx_configurations');

		$moduleConfigurationKey = $queryBuilder->escape($isLegacyConfiguration
            ? self::MODULE_CONFIGURATION_KEY
            : 'gm_configuration/' . self::MODULE_CONFIGURATION_KEY);

        if ($isLegacyConfiguration) {
            $erpIntegrationCount = $queryBuilder->where('`key` LIKE ' . $moduleConfigurationKey)
                                                ->where('`value` = "True"')
                                                ->get('gx_configurations')
                                                ->num_rows();
        } else {
            $erpIntegrationCount = $queryBuilder->where('gm_key LIKE ' . $moduleConfigurationKey)
                                                ->where('gm_value = "True"')
                                                ->get('gm_configuration')
                                                ->num_rows();
        }
        
        restore_error_handler();

        if($erpIntegrationCount === 0)
		{
			return false; // Do not process data changes if no Hub module is using the ERP integration feature.
		}

		return true;
	}
    
    
    public static function silent_error_handler($errno, $errstr)
    {
        return true;
    }
}

<?php

/* --------------------------------------------------------------
   HubShopKeyConfiguration.inc.php 2020-02-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class HubShopKeyConfiguration
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
class HubShopKeyConfiguration implements HubShopKeyConfigurationInterface
{
	/**
	 * Configuration key.
	 *
	 * @var string
	 */
	protected $configurationKey = 'GAMBIO_SHOP_KEY';


	/**
	 * Returns the shop key from the database.
	 *
	 * @return string Returns the shop key.
	 *
	 * @throws RuntimeException If no shop key exists.
	 */
    public function get()
    {
        $isLegacyConfiguration = version_compare(gm_get_conf('INSTALLED_VERSION'), '4.1') < 0;
        if ($isLegacyConfiguration) {
            $sql = 'SELECT `configuration_value` AS `value` FROM configuration WHERE configuration_key = "'
                   . $this->configurationKey . '"';
        } else {
            $sql = 'SELECT * FROM `gx_configurations` WHERE `key` = "configuration/' . $this->configurationKey . '"';
        }
        $query  = xtc_db_query($sql, 'db_link', false);
        $result = xtc_db_fetch_array($query);

        if (!$result || !array_key_exists('value', $result)) {
            throw new RuntimeException('Gambio shop key was not found!');
        }

        return $result['value'];
    }
}

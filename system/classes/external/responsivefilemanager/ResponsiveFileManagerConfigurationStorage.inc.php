<?php

/* --------------------------------------------------------------
	ResponsiveFileManagerConfigurationStorage.inc.php 2017-09-29
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ResponsiveFileManagerConfigurationStorage.
 *
 * Class representing the configuration storage for the responsive file manager.
 */
class ResponsiveFileManagerConfigurationStorage extends ConfigurationStorage
{
    /**
     * Configuration storage namespace.
     */
    const CONFIGURATION_STORAGE_NAMESPACE = 'modules/admin/responsivefilemanager';

    /**
     * Default configuration values.
     */
    protected $defaultConfigurationValues = [];

    /**
     * ResponsiveFileManagerConfigurationStorage constructor.
     */
    public function __construct()
    {
        parent::__construct(self::CONFIGURATION_STORAGE_NAMESPACE);
        $this->setDefaultConfigurationValues();
    }

    /**
     * Returns the configuration value of a specific key.
     *
     * @param string $key Configuration key (without namespace prefix).
     * @returns string Configuration value.
     * @throws InvalidArgumentException Missing or invalid configuration key.
     */
    public function get($key)
    {
        $this->validateKey($key);

        return parent::get($key) === false ? $this->defaultConfigurationValues[$key] : parent::get($key);
    }

    /**
     * Sets a configuration value.
     *
     * @param string $key Configuration key (without namespace prefix).
     * @param string $value Configuration value.
     * @throws InvalidArgumentException Missing or invalid configuration key or value.
     */
    public function set($key, $value)
    {
        $this->validateKey($key);

        if ($value === null) {
            throw new InvalidArgumentException('Missing or invalid configuration value');
        }

        parent::set($key, $value);
    }

    /**
     * Returns whether the module is installed.
     *
     * @return bool Active status.
     */
    public function isInstalled()
    {
        $isDirectoryExistent = is_dir(DIR_FS_CATALOG . 'ResponsiveFilemanager');
        $isActive = gm_get_conf('MODULE_CENTER_RESPONSIVEFILEMANAGER_INSTALLED') === '1';

        return ($isDirectoryExistent && $isActive);
    }

    /**
     * Verifies, that the given configuration key is valid.
     *
     * @param string $key Configuration key (without namespace prefix).
     * @throws InvalidArgumentException Missing or invalid configuration key.
     */
    protected function validateKey($key)
    {
        if (!array_key_exists($key, $this->defaultConfigurationValues)) {
            throw new InvalidArgumentException('Invalid configuration key');
        }
    }

    /**
     * Sets the default configuration values.
     *
     * @return ResponsiveFileManagerConfigurationStorage Same instance for method chaining.
     */
    protected function setDefaultConfigurationValues()
    {
        $this->defaultConfigurationValues = [
	        'use_in_ckeditor'                   => '1',
	        'use_in_product_and_category_pages' => '1',
	        'use_in_manufacturer_pages'         => '1',
	        'use_in_content_manager_pages'      => '1',
	        'use_in_attribute_pages'            => '1',
	        'use_in_property_pages'             => '1',
	        'use_in_banner_manager_pages'       => '1',
	        'use_in_shipping_status_pages'      => '1',
	        'use_in_email_pages'                => '1'
        ];

        return $this;
    }
}
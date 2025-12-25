<?php
/*
 * --------------------------------------------------------------
 *   GambioOmnibusPolicyConfigurationStorage.inc.php 2022-05-24
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2022 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

/**
 * Class GambioOmnibusPolicyConfigurationStorage
 */
class GambioOmnibusPolicyConfigurationStorage extends ConfigurationStorage
{
    /**
     * Configuration storage namespace
     */
    const STORAGE_NAMESPACE = 'modules/gambio/omnibus_policy/configuration';
    /**
     * Default configuration storage
     *
     * @var array
     */
    private $defaults;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(self::STORAGE_NAMESPACE);

        $this->defaults = [
            'product_listing_card_show_badge'    => '0',
            'product_info_page_show_badge'       => '0',
            'product_info_page_show_text_before' => '0',
            'product_info_page_show_text_after'  => '0',
        ];
    }


    /**
     * Return a configuration value
     *
     * @param string $key Configuration key
     *
     * @return string Configuration value
     * @throws InvalidArgumentException Invalid key
     */
    public function get($key)
    {
        $this->isAllowed($key);

        $value = parent::get($key);

        if ($value === false && array_key_exists($key, $this->defaults)) {
            $value = $this->defaults[$key];
        }

        return $value;
    }


    /**
     * Return all configuration key/value pairs
     *
     * @return array Key/Value pairs
     */
    public function getAll()
    {
        $values = parent::get_all();

        foreach ($this->defaults as $key => $defaultValue) {
            if (!array_key_exists($key, $values)) {
                $values[$key] = $defaultValue;
            }
        }

        return $values;
    }


    /**
     * Set a configuration value
     *
     * @param string $key   Configuration key
     * @param string $value Configuration value
     *
     * @return $this Same instance
     * @throws InvalidArgumentException Invalid key
     */
    public function set($key, $value)
    {
        $this->isAllowed($key);

        parent::set($key, $value);

        return $this;
    }


    /**
     * Set multiple configuration values at one time
     * Note: input data should ['key' => 'value']
     *
     * @param array $data
     *
     * @return $this
     */
    public function setAll($data = [])
    {
        $keys = array_keys($data);
        array_walk($keys, [$this, 'isAllowed']);

        parent::set_all($data);

        return $this;
    }


    /**
     * Removes a configuration value
     *
     * @param string $key Configuration key
     *
     * @return $this Same instance
     * @throws InvalidArgumentException Invalid key
     */
    public function delete($key)
    {
        $this->isAllowed($key);

        parent::delete($key);

        return $this;
    }


    /**
     * Removes all configuration values related to this storage
     *
     * @return $this Same instance
     * @throws InvalidArgumentException Invalid key
     */
    public function deleteAll()
    {
        parent::delete_all();

        return $this;
    }


    /**
     * @param string $key
     * @param bool   $isOptional when false, return will be surprised and replaced with exception
     *
     * @note $key is being trimmed and changed in memory for backward-compatibility reasons
     *
     * @return bool
     */
    private function isAllowed(&$key, $isOptional = false)
    {
        $key = trim($key, '/');

        if (!array_key_exists($key, $this->defaults)) {
            if ($isOptional) {
                return false;
            }
            throw new InvalidArgumentException(
                sprintf("Invalid configuration key '%s'", $key)
            );
        }
        return true;
    }
}
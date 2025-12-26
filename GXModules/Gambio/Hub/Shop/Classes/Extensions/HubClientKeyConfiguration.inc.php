<?php

/* --------------------------------------------------------------
   HubClientKeyConfiguration.inc.php 2020-02-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use HubPublic\ValueObjects\HubClientKey;

/**
 * Class HubClientKeyConfiguration
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
class HubClientKeyConfiguration implements HubClientKeyConfigurationInterface
{
    /**
     * Configuration key.
     *
     * @var string
     */
    protected $configurationKey = 'GAMBIO_HUB_CLIENT_KEY';


    /**
     * Sets the hub client key in the database configuration table.
     *
     * @param \HubPublic\ValueObjects\HubClientKey $clientKey The client key to be saved.
     *
     * @return HubClientKeyConfiguration Returns same instance for chained method calls.
     */
    public function set(HubClientKey $clientKey)
    {
        gm_set_conf('GAMBIO_HUB_CLIENT_KEY', $clientKey->asString());

        return $this;
    }


    /**
     * Returns the hub client key from the database.
     *
     * @return string Returns the hub client key.
     *
     * @throws RuntimeException If no Gambio Hub exists.
     */
    public function get()
    {
        return gm_get_conf('GAMBIO_HUB_CLIENT_KEY');
    }


    /**
     * Returns the hub client key as a HubClientKey instance.
     *
     * @return \HubPublic\ValueObjects\HubClientKey
     */
    public function getClientKey()
    {
        return new HubClientKey($this->get());
    }
}

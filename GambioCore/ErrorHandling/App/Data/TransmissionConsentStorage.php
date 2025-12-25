<?php
/*--------------------------------------------------------------
   TransmissionConsentStorage.php 2023-05-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\App\Data;

use Gambio\Core\Configuration\App\ConfigurationService;

/**
 * Class TransmissionConsentStorage
 *
 * @package Gambio\Core\ErrorHandling\App\Data
 */
class TransmissionConsentStorage
{
    /**
     * The modules installation status is stored under this key in the database.
     */
    private const MODULE_INSTALLED_CONFIGURATION_KEY = 'gm_configuration/MODULE_CENTER_ERRORREPORTING_INSTALLED';
    
    
    /**
     * TransmissionConsentStorage constructor.
     *
     * @param ConfigurationService $service
     */
    public function __construct(private ConfigurationService $service) { }
    
    /**
     * Does the main admin consent to exception data being transmitted?
     *
     * The main admin can give consent in 2 ways
     * * checking a checkbox during the installation.
     * * installing the module "Send error reports"
     *
     * @return bool
     */
    public function consentedToDataTransmission(): bool
    {
        $config = $this->service->find(static::MODULE_INSTALLED_CONFIGURATION_KEY);

        return $config ? $config->value() === '1' : false;
    }
}
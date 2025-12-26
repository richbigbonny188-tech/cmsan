<?php
/* --------------------------------------------------------------
   AfterbuyGlobalRepository.php 2023-01-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\Service;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotEnabledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotInstalledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\AfterbuyGlobal;

/**
 * Interface AfterbuyGlobalRepository
 *
 * @package GXModules\Gambio\Afterbuy\AfterbuyCommon\Service
 */
interface AfterbuyGlobalRepository
{
    /**
     * Returns the afterbuy global model, used to define and authorize Afterbuy-API requests.
     *
     * @param string $callName
     *
     * @return AfterbuyGlobal
     * @throws AfterbuyNotEnabledException|AfterbuyNotInstalledException
     */
    public function getAfterbuyGlobal(string $callName): AfterbuyGlobal;
    
    
    /**
     * Checks if the afterbuy module is installed.
     *
     * @return bool
     */
    public function isInstalled(): bool;
    
    
    /**
     * Checks if the afterbuy module is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;
    
    
    /**
     * Checks if Afterbuy XML-API credentials are available and not empty.
     *
     * @return bool
     */
    public function hasXmlCredentials(): bool;
    
    
    /**
     * Returns the log level configuration of the afterbuy module.
     *
     * @return string
     */
    public function getLogLevel(): string;
}
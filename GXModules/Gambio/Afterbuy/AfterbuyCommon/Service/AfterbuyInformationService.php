<?php
/* --------------------------------------------------------------
   AfterbuyInformationService.php 2023-01-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\Service;

/**
 * Interface AfterbuyInformationService
 *
 * @package GXModules\Gambio\Afterbuy\AfterbuyCommon\Service
 */
interface AfterbuyInformationService
{
    /**
     * Checks if the afterbuy module is installed and enabled.
     *
     * @return bool
     */
    public function isInstalledAndEnabled(): bool;
    
    
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
     * Returns the log level configuration of the afterbuy module.
     *
     * @return string
     */
    public function getLogLevel(): string;
}

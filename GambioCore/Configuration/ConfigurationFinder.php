<?php
/* --------------------------------------------------------------
 ConfigurationFinder.php 2020-09-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration;

/**
 * Interface ConfigurationFinder
 * @package    Gambio\Core\Configuration
 *
 * @deprecated This interface will be replaced with Gambio\Core\Configuration\Services\ConfigurationFinder in the first
 *             version of the GX 4.5.x series. We do this in order to fulfill the requirements for the new modules
 *             directory structure.
 */
interface ConfigurationFinder extends Services\ConfigurationFinder
{
}
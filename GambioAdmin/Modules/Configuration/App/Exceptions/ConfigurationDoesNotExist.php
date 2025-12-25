<?php
/* --------------------------------------------------------------
   ConfigurationDoesNotExist.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Exceptions;

use Exception;

/**
 * Class ConfigurationDoesNotExist
 *
 * @package Gambio\Admin\Modules\Configuration\App\Exceptions
 */
class ConfigurationDoesNotExist extends Exception
{
    /**
     * @param string $key
     *
     * @return ConfigurationDoesNotExist
     */
    public static function withKey(string $key): ConfigurationDoesNotExist
    {
        return new self('Configuration with key "' . $key . '" does not exist.');
    }
}
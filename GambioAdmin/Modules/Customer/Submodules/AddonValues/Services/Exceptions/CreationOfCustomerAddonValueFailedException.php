<?php
/* --------------------------------------------------------------
   CreationOfCustomerAddonValueFailedException.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions;

use Exception;

/**
 * Class CreationOfCustomerAddonValueFailedException
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Services\Exceptions
 * @codeCoverageIgnore
 */
class CreationOfCustomerAddonValueFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return CreationOfCustomerAddonValueFailedException
     */
    public static function becauseOfException(Exception $exception): CreationOfCustomerAddonValueFailedException
    {
        return new self(sprintf('Could not create customer addon value because of previous error (%s).',
                                get_class($exception)), 0, $exception);
    }
}
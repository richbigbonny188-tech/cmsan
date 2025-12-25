<?php
/*--------------------------------------------------------------
   StorageOfCustomerFailedException.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services\Exceptions;

use Exception;

/**
 * Class StorageOfCustomerFailedException
 *
 * @package Gambio\Admin\Modules\Customer\Services\Exceptions
 */
class StorageOfCustomerFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfCustomerFailedException
     */
    public static function becauseOfException(Exception $exception): StorageOfCustomerFailedException
    {
        return new self(sprintf('Could not create customer because of previous error (%s).', get_class($exception)),
                        0,
                        $exception);
    }
}
<?php
/*--------------------------------------------------------------
   DeletionOfCustomerFailedException.php 2022-01-19
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
 * Class DeletionOfCustomerFailedException
 *
 * @package Gambio\Admin\Modules\Customer\Services\Exceptions
 */
class DeletionOfCustomerFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return DeletionOfCustomerFailedException
     */
    public static function becauseOfException(Exception $exception): DeletionOfCustomerFailedException
    {
        return new self(sprintf('Could not create customer because of previous error (%s).', get_class($exception)),
                        0,
                        $exception);
    }
}
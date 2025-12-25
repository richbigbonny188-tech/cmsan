<?php
/*--------------------------------------------------------------
   CustomerEmailAddressMustBeUniqueException.php 2022-07-25
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
 * Class CustomerEmailAddressMustBeUniqueException
 *
 * @package Gambio\Admin\Modules\Customer\Services\Exceptions
 */
class CustomerEmailAddressMustBeUniqueException extends Exception
{
    /**
     * @param string $email
     *
     * @return CustomerEmailAddressMustBeUniqueException
     */
    public static function fromEmailAddress(string $email): CustomerEmailAddressMustBeUniqueException
    {
        return new self(sprintf('Email address "%s" is already taken.', $email), 1);
    }
}
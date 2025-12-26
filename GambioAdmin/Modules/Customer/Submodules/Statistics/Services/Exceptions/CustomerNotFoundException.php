<?php
/*--------------------------------------------------------------
   CustomerNotFoundException.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Model\ValueObjects\CustomerId;

/**
 * Class CustomerNotFoundException
 *
 * @package Gambio\Admin\Modules\CustomerStatistics\Services\Exceptions
 */
class CustomerNotFoundException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return CustomerNotFoundException
     */
    public static function becauseOfException(Exception $exception): CustomerNotFoundException
    {
        return new self('Could not read customer statistics because of previous error.', 0, $exception);
    }
    
    
    /**
     * @param CustomerId $customerId
     *
     * @return CustomerNotFoundException
     */
    public static function customerNotFound(CustomerId $customerId): CustomerNotFoundException
    {
        $message = 'No customer was found with the id "%s"';
        $message = sprintf($message, $customerId->value());
        
        return new self($message, 1);
    }
}
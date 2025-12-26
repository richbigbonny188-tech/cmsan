<?php
/*--------------------------------------------------------------
   CustomerOrderDoesNotExist.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\OrderId;

/**
 * Class CustomerOrderDoesNotExist
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\Services\Exceptions
 */
class CustomerOrderDoesNotExist extends Exception
{
    /**
     * @param OrderId $orderId
     *
     * @return CustomerOrderDoesNotExist
     */
    public static function forOrderId(OrderId $orderId): CustomerOrderDoesNotExist
    {
        $message = 'No order exist with the id "%s"';
        
        return new self(sprintf($message, $orderId->value()), 1);
    }
}
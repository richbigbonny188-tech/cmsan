<?php
/*--------------------------------------------------------------
   SalesRecordNotFoundException.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects\ProductId;

/**
 * Class SalesRecordNotFoundException
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\Exceptions
 */
class SalesRecordNotFoundException extends Exception
{
    /**
     * @param ProductId $id
     *
     * @return SalesRecordNotFoundException
     */
    public static function create(ProductId $id): SalesRecordNotFoundException
    {
       $message = '';
       $message = sprintf($message, $id->value());
       
       return new self($message, 1);
    }
}
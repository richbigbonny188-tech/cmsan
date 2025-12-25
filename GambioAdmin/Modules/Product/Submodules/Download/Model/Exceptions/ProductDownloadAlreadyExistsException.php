<?php
/*--------------------------------------------------------------------
 ProductDownloadAlreadyExistsException.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductId;

/**
 * Class ProductDownloadAlreadyExistsException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions
 */
class ProductDownloadAlreadyExistsException extends Exception
{
    /**
     * @param ProductId              $productId
     * @param OptionAndOptionValueId $optionAndOptionValueId
     *
     * @return ProductDownloadAlreadyExistsException
     */
    public static function forProductIdAndOptionAndOptionValueId(
        ProductId              $productId,
        OptionAndOptionValueId $optionAndOptionValueId
    ): ProductDownloadAlreadyExistsException {
        $message = 'A product option "%s" already exist for the product id "%s"';
        
        return new static(sprintf($message, (string)$optionAndOptionValueId, $productId->value()));
    }
}
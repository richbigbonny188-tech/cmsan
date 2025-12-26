<?php
/*--------------------------------------------------------------------
 AdditionalOptionAlreadyExistsException.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;

/**
 * Class AdditionalOptionAlreadyExistsException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions
 */
class AdditionalOptionAlreadyExistsException extends Exception
{
    /**
     * @param ProductId              $productId
     * @param OptionAndOptionValueId $optionAndOptionValueId
     *
     * @return AdditionalOptionAlreadyExistsException
     */
    public static function forProductIdAndOptionAndOptionValueId(
        ProductId              $productId,
        OptionAndOptionValueId $optionAndOptionValueId
    ): AdditionalOptionAlreadyExistsException {
        $message = 'An additional option "%s" already exist for the product id "%s"';
        
        return new self(sprintf($message, (string)$optionAndOptionValueId, $productId->value()));
    }
}
<?php
/*--------------------------------------------------------------
   ProductVariantCombinationAlreadyExists.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions;

use Exception;

/**
 * Class ProductVariantCombinationAlreadyExists
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions
 */
class ProductVariantCombinationAlreadyExists extends Exception
{
    /**
     * @param int $productId
     * @param string $optionAndOptionValueId
     *
     * @return ProductVariantCombinationAlreadyExists
     */
    public static function forProductIdAndCombinationString(
        int    $productId,
        string $optionAndOptionValueId
    ): ProductVariantCombinationAlreadyExists
    {
        return new self('Variant "' . $optionAndOptionValueId . '" for product ID ' . $productId . ' already exists.');
    }
}
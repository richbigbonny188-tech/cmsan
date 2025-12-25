<?php
/*--------------------------------------------------------------
   StorageOfProductVariantsFailed.php 2023-06-27
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
 * Class StorageOfProductVariantsFailed
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions
 */
class StorageOfProductVariantsFailed extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfProductVariantsFailed
     */
    public static function becauseOfException(Exception $exception): StorageOfProductVariantsFailed
    {
        return new self('Could not store product variants because of previous exception.', 0, $exception);
    }


    /**
     * @param int $productId
     * @param string $combination
     *
     * @return StorageOfProductVariantsFailed
     */
    public static function becauseCombinationAlreadyExists(
        int    $productId,
        string $combination
    ): StorageOfProductVariantsFailed
    {
        return new self('Could not store product variant, because a different product variant for the product ID '
            . $productId . ' and the combination "' . $combination . '" already exists.');
    }


    /**
     * @param int $optionId
     * @param int $variantId
     *
     * @return StorageOfProductVariantsFailed
     */
    public static function becauseOptionAllreadyExists(int $optionId, int $variantId): StorageOfProductVariantsFailed
    {
        $message = sprintf('Can\'t add option "%s" since it exists in the variant with the id "%s"', $optionId, $variantId);

        return new static($message, 1);
    }
}
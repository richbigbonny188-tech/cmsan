<?php
/*--------------------------------------------------------------
   ProductVariantDoesNotExist.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Class ProductVariantDoesNotExist
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions
 */
class ProductVariantDoesNotExist extends Exception
{
    /**
     * @param ProductVariantId $id
     *
     * @return ProductVariantDoesNotExist
     */
    public static function forProductVariantId(ProductVariantId $id): ProductVariantDoesNotExist
    {
        return new self('Variant with ID ' . $id->value() . ' does not exist.');
    }
}
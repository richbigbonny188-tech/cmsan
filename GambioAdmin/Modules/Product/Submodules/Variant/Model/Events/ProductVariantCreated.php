<?php
/*--------------------------------------------------------------
   ProductVariantCreated.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Class ProductVariantCreated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events
 * @codeCoverageIgnore
 */
class ProductVariantCreated
{
    /**
     * ProductVariantCreated constructor.
     *
     * @param ProductVariantId $id
     */
    public function __construct(protected ProductVariantId $id) { }
    
    
    /**
     * @param ProductVariantId $id
     *
     * @return ProductVariantCreated
     */
    public static function create(ProductVariantId $id): ProductVariantCreated
    {
        return new static($id);
    }
    
    
    /**
     * @return ProductVariantId
     */
    public function variantId(): ProductVariantId
    {
        return $this->id;
    }
}
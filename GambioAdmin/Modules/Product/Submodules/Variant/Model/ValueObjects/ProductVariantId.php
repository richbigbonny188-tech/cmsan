<?php
/*--------------------------------------------------------------
   VariantId.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class VariantId
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects
 */
class ProductVariantId
{
    /**
     * ProductVariantId constructor.
     *
     * @param int $variantId
     */
    private function __construct(private int $variantId)
    {
    }
    
    
    /**
     * @param int $variantId
     *
     * @return static
     */
    public static function create(int $variantId): static
    {
        Assert::greaterThan($variantId, 0, 'The product variant ID must be a positive integer. Got: %s');
        
        return new static($variantId);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->variantId;
    }
}

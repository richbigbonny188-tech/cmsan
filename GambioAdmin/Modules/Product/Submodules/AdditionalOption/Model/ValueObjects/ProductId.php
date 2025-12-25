<?php
/*--------------------------------------------------------------------
 ProductId.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ProductId
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects
 */
class ProductId
{
    /**
     * ProductId constructor.
     *
     * @param int $productId
     */
    private function __construct(private int $productId) { }
    
    
    /**
     * @param int $productId
     *
     * @return ProductId
     */
    public static function create(int $productId): ProductId
    {
        Assert::greaterThan($productId, 0, 'The product ID must be a positive integer. Got: %s');
        
        return new self($productId);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->productId;
    }
}
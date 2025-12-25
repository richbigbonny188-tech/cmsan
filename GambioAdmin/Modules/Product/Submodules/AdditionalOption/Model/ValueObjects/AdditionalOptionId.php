<?php
/*--------------------------------------------------------------------
 AdditionalOptionId.php 2023-06-06
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
 * Class AdditionalOptionId
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects
 */
class AdditionalOptionId
{
    /**
     * ProductOptionId constructor.
     *
     * @param int $id
     */
    private function __construct(private int $id) { }
    
    
    /**
     * @param int $productOptionId
     *
     * @return static
     */
    public static function create(int $productOptionId): static
    {
        Assert::greaterThan($productOptionId, 0, 'The product option ID must be a positive integer. Got: %s');
        
        return new static($productOptionId);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->id;
    }
}
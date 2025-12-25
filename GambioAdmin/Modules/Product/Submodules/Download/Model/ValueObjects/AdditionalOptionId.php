<?php
/*--------------------------------------------------------------------
 ProductOptionId.php 2021-09-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ProductOptionId
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects
 */
class AdditionalOptionId
{
    /**
     * ProductOptionId constructor.
     *
     * @param int $value
     */
    private function __construct(private int $value) { }
    
    
    /**
     * @param int $additionalOptionId
     *
     * @return static
     */
    public static function create(int $additionalOptionId): AdditionalOptionId
    {
        Assert::greaterThan($additionalOptionId, 0, 'The product option ID must be a positive integer. Got: %s');
        
        return new static($additionalOptionId);
    }
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }
}
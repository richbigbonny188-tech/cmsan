<?php
/*--------------------------------------------------------------
   CategoryId.php 2023-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class CategoryId
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects
 */
class CategoryId
{
    /**
     * CategoryId constructor.
     *
     * @param int $value
     */
    private function __construct(private int $value) { }
    
    
    /**
     * @param int $value
     *
     * @return CategoryId
     */
    public static function create(int $value): CategoryId
    {
        Assert::greaterThanEq($value, 0, 'Given category ID must be greater than or equal to 0. Got: %s');
        
        return new self($value);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }
}
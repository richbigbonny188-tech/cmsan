<?php
/*--------------------------------------------------------------
   ProductId.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ProductId
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects
 */
class ProductId
{
    private int $value;
    
    
    /**
     * OptionId constructor.
     *
     * @param int $value
     */
    private function __construct(int $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param int $value
     *
     * @return ProductId
     */
    public static function create(int $value): ProductId
    {
        Assert::greaterThan($value, 0, 'Given products ID must be greater than 0. Got: %s');
        
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
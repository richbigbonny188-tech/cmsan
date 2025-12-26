<?php
/*--------------------------------------------------------------
   CustomerAddressId.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class CustomerAddressId
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Address\ValueObjects
 */
class CustomerAddressId
{
    private int $value;
    
    
    /**
     * @param int $value
     */
    private function __construct(int $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param int $value
     *
     * @return CustomerAddressId
     */
    public static function create(int $value): CustomerAddressId
    {
        Assert::greaterThan($value, 0, 'Given customer address ID must be greater than 0. Got: %s');
        
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
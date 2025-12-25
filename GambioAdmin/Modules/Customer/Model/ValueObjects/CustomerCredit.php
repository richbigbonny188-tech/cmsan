<?php
/*--------------------------------------------------------------
   CustomerCredit.php 2021-12-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
 
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class CustomerCredit
 *
 * @package Gambio\Admin\Modules\Customer\Model\ValueObjects
 */
class CustomerCredit
{
    public const MAXIMUM_CUSTOMER_CREDIT = 100000000.0000;
    private float $credit;
    
    
    /**
     * @param float $credit
     */
    public function __construct(float $credit)
    {
        $this->credit = $credit;
    }
    
    
    /**
     * @param float $credit
     *
     * @return CustomerCredit
     */
    public static function create(float $credit = 0.0):  CustomerCredit
    {
        Assert::lessThan($credit, static::MAXIMUM_CUSTOMER_CREDIT, 'Expected a value less than %2$s. Got: %s');
        
        return new self($credit);
    }
    
    
    /**
     * @return float
     */
    public function value(): float
    {
        return $this->credit;
    }
}
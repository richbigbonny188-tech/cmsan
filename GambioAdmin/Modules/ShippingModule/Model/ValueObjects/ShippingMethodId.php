<?php
/*--------------------------------------------------------------
   ShippingMethodId.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ShippingMethodId
 *
 * @package Gambio\Admin\Modules\ShippingModule\Model\ValueObjects
 */
class ShippingMethodId
{
    private string $value;
    
    
    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param string $value
     *
     * @return ShippingMethodId
     */
    public static function create(string $value): ShippingMethodId
    {
        Assert::notWhitespaceOnly($value, 'Shipping method ID can\'t be whitespace only.');
        
        return new static($value);
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}
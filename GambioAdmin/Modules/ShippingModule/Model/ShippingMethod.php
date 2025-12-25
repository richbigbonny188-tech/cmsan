<?php
/*--------------------------------------------------------------
   ShippingMethod.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\Model;

use Gambio\Admin\Modules\ShippingModule\Model\ValueObjects\ShippingMethodId;
use Webmozart\Assert\Assert;

/**
 * Class ShippingMethod
 *
 * @package Gambio\Admin\Modules\ShippingModule\Model
 */
class ShippingMethod
{
    private ShippingMethodId $id;
    private string           $name;
    
    
    /**
     * @param ShippingMethodId $id
     * @param string           $name
     */
    public function __construct(ShippingMethodId $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
    
    
    /**
     * @param ShippingMethodId $id
     * @param string           $name
     *
     * @return ShippingMethod
     */
    public static function create(ShippingMethodId $id, string $name): ShippingMethod
    {
        Assert::notWhitespaceOnly($name, 'Shipping methods name can\'t be whitespace only.');
        
        return new static($id, $name);
    }
    
    
    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id->value();
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'id'   => $this->id(),
            'name' => $this->name(),
        ];
    }
}
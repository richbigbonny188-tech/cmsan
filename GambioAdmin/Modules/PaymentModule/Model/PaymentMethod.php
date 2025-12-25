<?php
/*--------------------------------------------------------------
   PaymentMethod.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\Model;

use Gambio\Admin\Modules\PaymentModule\Model\ValueObjects\PaymentMethodId;
use Webmozart\Assert\Assert;

/**
 * Class PaymentMethod
 *
 * @package Gambio\Admin\Modules\PaymentModule\Model
 */
class PaymentMethod
{
    private PaymentMethodId $id;
    private string          $name;
    
    
    /**
     * PaymentMethod constructor.
     *
     * @param PaymentMethodId $id
     * @param string          $name
     */
    public function __construct(PaymentMethodId $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
    
    
    /**
     * @param PaymentMethodId $id
     * @param string          $name
     *
     * @return PaymentMethod
     */
    public static function create(PaymentMethodId $id, string $name): PaymentMethod
    {
        Assert::notWhitespaceOnly($name, 'Payment methods name can\'t be whitespace only.');
        
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
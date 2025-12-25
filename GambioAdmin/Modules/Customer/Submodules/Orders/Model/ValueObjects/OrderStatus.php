<?php
/*--------------------------------------------------------------
   OrderStatus.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects;

use Gambio\Admin\Modules\CustomerOrders\Services\Exceptions\LanguageCodeDoesNotExistException;
use Webmozart\Assert\Assert;

/**
 * Class OrderStatus
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects
 */
class OrderStatus
{
    private array $orderStatus;
    
    
    /**
     * @param array $orderStatus
     */
    private function __construct(array $orderStatus)
    {
        $this->orderStatus = $orderStatus;
    }
    
    
    /**
     * @param array $orderStatus
     *
     * @return OrderStatus
     */
    public static function create(array $orderStatus): OrderStatus
    {
        Assert::minLength($orderStatus['label'], 1);
        Assert::minLength($orderStatus['color'], 1);
        
        return new self($orderStatus);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->orderStatus;
    }
    
    
    /**
     * @return string
     */
    public function color(): string
    {
        return $this->orderStatus['color'];
    }
    
    
    /**
     * @return string
     */
    public function label(): string
    {
        return $this->orderStatus['label'];
    }
}
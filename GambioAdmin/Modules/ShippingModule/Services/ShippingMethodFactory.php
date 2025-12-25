<?php
/*--------------------------------------------------------------
   CustomerFactory.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\Services;

use Gambio\Admin\Modules\ShippingModule\Model\Collections\ShippingMethods;
use Gambio\Admin\Modules\ShippingModule\Model\ShippingMethod;
use Gambio\Admin\Modules\ShippingModule\Model\ValueObjects\ShippingMethodId;

/**
 * Class ShippingMethodFactory
 *
 * @package Gambio\Admin\Modules\ShippingModule\Services
 */
class ShippingMethodFactory
{
    /**
     * Creates and returns a shipping method ID.
     *
     * @param string $shippingMethodId
     *
     * @return ShippingMethodId
     */
    public function createShippingMethodId(string $shippingMethodId): ShippingMethodId
    {
        return ShippingMethodId::create($shippingMethodId);
    }
    
    
    /**
     * Creates and returns a collection of shipping methods.
     *
     * @param ShippingMethod ...$shippingMethods
     *
     * @return ShippingMethods
     */
    public function createShippingMethods(ShippingMethod ...$shippingMethods): ShippingMethods
    {
        return ShippingMethods::create(...$shippingMethods);
    }
}
<?php
/*--------------------------------------------------------------
   CustomerDisallowedShippingMethodsMapper.php 2022-10-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\App\Data;

use Gambio\Admin\Modules\ShippingModule\Model\Collections\ShippingMethods;
use Gambio\Admin\Modules\ShippingModule\Model\ShippingMethod;
use Gambio\Admin\Modules\ShippingModule\Services\ShippingMethodFactory;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class CustomerDisallowedShippingMethodsMapper
 *
 * @package Gambio\Admin\Modules\ShippingModule\App\Data
 */
class CustomerDisallowedShippingMethodsMapper extends ShippingMethodFactory
{
    private TextManager $textManager;
    
    
    /**
     * @param TextManager $textManager
     */
    public function __construct(TextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * @param string $shippingMethodData
     *
     * @return ShippingMethods
     */
    public function mapShippingMethods(string $shippingMethodData): ShippingMethods
    {
        $shippingMethodIds = preg_split('#\s?,\s?#', $shippingMethodData);
        $shippingMethodIds = array_map('trim', $shippingMethodIds);
        $shippingMethodIds = array_map('strtolower', $shippingMethodIds);
        $shippingMethodIds = array_filter($shippingMethodIds, 'strlen');
        
        return $this->createShippingMethods(...array_map([$this, 'mapShippingMethod'], $shippingMethodIds));
    }
    
    
    /**
     * @param string $shippingMethodId
     *
     * @return ShippingMethod
     */
    public function mapShippingMethod(string $shippingMethodId): ShippingMethod
    {
        return ShippingMethod::create($this->createShippingMethodId($shippingMethodId),
                                      $this->shippingMethodNameFromId($shippingMethodId));
    }
    
    
    /**
     * @param string $shippingMethodId
     *
     * @return string
     */
    private function shippingMethodNameFromId(string $shippingMethodId): string
    {
        $phrase = 'MODULE_SHIPPING_' . strtoupper($shippingMethodId) . '_TEXT_TITLE';
        
        return $this->textManager->getPhraseText($phrase, $shippingMethodId);
    }
}
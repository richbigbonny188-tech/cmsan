<?php
/*--------------------------------------------------------------
   CustomerDisallowedPaymentMethodsMapper.php 2022-10-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\App\Data;

use Gambio\Admin\Modules\PaymentModule\Model\Collections\PaymentMethods;
use Gambio\Admin\Modules\PaymentModule\Model\PaymentMethod;
use Gambio\Admin\Modules\PaymentModule\Services\PaymentMethodFactory;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class CustomerDisallowedPaymentMethodsMapper
 *
 * @package Gambio\Admin\Modules\PaymentModule\App\Data
 */
class CustomerDisallowedPaymentMethodsMapper extends PaymentMethodFactory
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
     * @param string $paymentMethodData
     *
     * @return PaymentMethods
     */
    public function mapPaymentMethods(string $paymentMethodData): PaymentMethods
    {
        $paymentMethodIds = preg_split('#\s?,\s?#', $paymentMethodData);
        $paymentMethodIds = array_map('trim', $paymentMethodIds);
        $paymentMethodIds = array_map('strtolower', $paymentMethodIds);
        $paymentMethodIds = array_filter($paymentMethodIds, 'strlen');
        
        return $this->createPaymentMethods(...array_map([$this, 'mapPaymentMethod'], $paymentMethodIds));
    }
    
    
    /**
     * @param string $paymentMethodId
     *
     * @return PaymentMethod
     */
    public function mapPaymentMethod(string $paymentMethodId): PaymentMethod
    {
        return PaymentMethod::create($this->createPaymentMethodId($paymentMethodId),
                                     $this->paymentMethodNameFromId($paymentMethodId));
    }
    
    
    /**
     * @param string $paymentMethodId
     *
     * @return string
     */
    private function paymentMethodNameFromId(string $paymentMethodId): string
    {
        $phrase = 'MODULE_PAYMENT_' . strtoupper($paymentMethodId) . '_TEXT_TITLE';
        
        return $this->textManager->getPhraseText($phrase, $paymentMethodId);
    }
}
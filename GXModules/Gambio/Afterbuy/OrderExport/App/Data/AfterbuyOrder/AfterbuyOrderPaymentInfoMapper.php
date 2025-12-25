<?php
/* --------------------------------------------------------------
   AfterbuyOrderPaymentInfoMapper.php 2023-02-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder;

use GXModules\Gambio\Afterbuy\OrderExport\Model\Request\PaymentInfo;

/**
 * Class AfterbuyOrderPaymentInfoMapper
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class AfterbuyOrderPaymentInfoMapper
{
    private AfterbuyOrderPaymentInfoReader $reader;
    
    
    /**
     * AfterbuyOrderPaymentInfoMapper constructor.
     *
     * @param AfterbuyOrderPaymentInfoReader $reader
     */
    public function __construct(AfterbuyOrderPaymentInfoReader $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * Maps order data to payment information.
     *
     * @param array $data
     *
     * @return PaymentInfo
     */
    public function map(array $data): PaymentInfo
    {
        $paymentMethod = $data['gambio_hub_module_title'] ? : $data['payment_method'];
        
        $paymentMethod         = $this->reader->getPaymentMethod($paymentMethod);
        $paymentDate           = $this->reader->getPaymentDate($data);
        $alreadyPaid           = $this->reader->getAlreadyPaid($data);
        $paymentAdditionalCost = null;
        $sendPaymentMail       = null;
        
        return new PaymentInfo($paymentMethod, $paymentDate, $alreadyPaid, $paymentAdditionalCost, $sendPaymentMail);
    }
}
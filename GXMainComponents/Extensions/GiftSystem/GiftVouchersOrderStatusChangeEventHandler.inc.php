<?php
/* --------------------------------------------------------------
   GiftVouchersOrderStatusChangeEventHandler.inc.php 2020-01-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class GiftVouchersOrderStatusChangeEventHandler
{
    /** @var GiftVouchersConfigurationStorage */
    protected $configuration;
    /** @var bool */
    protected $isGiftSystemInstalled;
    
    
    public function __construct()
    {
        $this->isGiftSystemInstalled = filter_var(gm_get_conf('MODULE_CENTER_GIFTSYSTEM_INSTALLED'),
                                                  FILTER_VALIDATE_BOOLEAN) === true;
        
        if ($this->isGiftSystemInstalled === true) {
            /** @var GiftVouchersConfigurationStorage configuration */
            $this->configuration = MainFactory::create('GiftVouchersConfigurationStorage');
        }
    }
    
    
    public function __invoke(IdType $orderId, IntType $newOrderStatusId)
    {
        if ($this->isGiftSystemInstalled === false) {
            return;
        }
        $releaseOrderStatuses = $this->configuration->get('releaseOrderStatuses');
        $isNewStatusAReleaseStatus = in_array($newOrderStatusId->asInt(), $releaseOrderStatuses, true);
        if ($isNewStatusAReleaseStatus === true) {
            /** @var GiftVouchersService $giftVouchersService */
            $giftVouchersService = MainFactory::create('GiftVouchersService', $this->configuration);
            $unreleasedQueueEntries  = $giftVouchersService->getUnreleasedQueueEntriesByOrder($orderId);
            if (!empty($unreleasedQueueEntries)) {
                /** @var OrderReadServiceInterface $orderReadService */
                $orderReadService = StaticGXCoreLoader::getService('OrderRead');
                /** @var OrderInterface $order */
                $order = $orderReadService->getOrderById($orderId);
                /** @var GiftVouchersMailService $giftVouchersMailService */
                $giftVouchersMailService = MainFactory::create('GiftVouchersMailService', $giftVouchersService);
                $gvMailText              = MainFactory::create('LanguageTextManager', 'gv_mail');
                $subject                 = $gvMailText->get_text('TEXT_SUBJECT_VOUCHER_ORDERED');
                $customerName            = $order->getCustomerAddress()->getFirstname() . ' ' . $order->getCustomerAddress()
                        ->getLastname();
                $mailMessage                 = '';
    
                foreach ($unreleasedQueueEntries as $unreleasedQueueEntry) {
                    try {
                        $coupon = $giftVouchersService->releaseQueueEntry(new IdType((int)$unreleasedQueueEntry['unique_id']));
                        try {
                            $giftVouchersMailService->sendMail($coupon->getCouponCode()->asString(),
                                                               $order->getCustomerEmail(),
                                                               $customerName,
                                                               $mailMessage,
                                                               $subject);
                            $giftVouchersMailService->storeCouponEmailTrack($coupon->getCouponId(),
                                                                            $order->getCustomerEmail());
                        } catch (InvalidCouponCodeException $e) {
                            // pass
                        }
                    } catch (InvalidGiftVouchersQueueIdException $e) {
                        // pass, for now – @todo: error handling in event handlers
                    }
                }
            }
        }
    }
}

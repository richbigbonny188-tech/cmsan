<?php
/* --------------------------------------------------------------
  ProductWarrantyNoticeContentControl.inc.php 2023-04-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeStaticServiceFactory;

class ProductWarrantyNoticeContentControl extends ProductWarrantyNoticeContentControl_parent
{
    use CheckoutSessionTrait;
    
    public function proceed()
    {
        if ($this->checkForUsedOrRenewedProducts() === false) {
            $this->unsetWarrantyNoticeSession();
        } elseif ($this->validateProductWarrantyNotice() === false) {
            $textPhraseService            = ProductConditionNoticeStaticServiceFactory::createTextPhraseService();
            $_SESSION['gm_error_message'] = urlencode($textPhraseService->getTextPhrase('message_stack_warranty_notice_not_confirmed'));
            $this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
        }
    
        parent::proceed();
    
        return true;
    }
    
    
    /**
     * @return bool
     */
    protected function validateProductWarrantyNotice(): bool
    {
        $warrantyNoticeService = ProductConditionNoticeStaticServiceFactory::createWarrantyNoticeService();
        $showAgreement         = $warrantyNoticeService->isWarrantyNoticeEnabled();
        $confirmationNeeded    = $warrantyNoticeService->isWarrantyNoticeMandatory();
        $agreementAccepted     = ($this->v_data_array['POST']['warranty_notice'] ?? null) === 'accepted';
        
        if ($showAgreement === false) {
            $this->unsetWarrantyNoticeSession();
            
            return true;
        }
        
        if ($this->issetWarrantyNoticeSession()
            && ($this->getWarrantyNoticeSessionValue() === 'accepted'
                || $this->getWarrantyNoticeSessionValue() === 'shown')) {
            return true;
        }
        
        if (($confirmationNeeded && $agreementAccepted)) {
            $this->setWarrantyNoticeSession('accepted');
            
            return true;
        }
        
        if ($confirmationNeeded === false) {
            $this->setWarrantyNoticeSession('shown');
            
            return true;
        }
        
        $this->setWarrantyNoticeSession('declined');
        
        return false;
    }
    
    
    /**
     * @return bool
     */
    protected function checkForUsedOrRenewedProducts(): bool
    {
        $order = new order();
        $usedProductService = ProductConditionNoticeStaticServiceFactory::createUsedProductService();
        $productIds         = array_map(function (array $product): int {
            preg_match('/^\d+/', $product['id'], $matches);
            
            return (int)$matches[0];
        }, $order->products ?? []);
        
        return $usedProductService->containsAUsedOrRenewedProduct(...$productIds);
    }
}

<?php
/* --------------------------------------------------------------
  ProductConditionNoticeCheckoutProcessProcess.inc.php 2023-04-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeStaticServiceFactory;

class ProductConditionNoticeCheckoutProcessProcess extends ProductConditionNoticeCheckoutProcessProcess_parent
{
    use CheckoutSessionTrait;
    
    public function check_redirect()
    {
        $redirectFlag = parent::check_redirect();
        if ($redirectFlag) {
            return true;
        }
        
        if ($this->checkForUsedOrRenewedProducts() === false) {
            $this->unsetConditionNoticeSession();
        
            return false;
        }
    
        if ($this->validateProductConditionNotice() === false) {
            $textPhraseService            = ProductConditionNoticeStaticServiceFactory::createTextPhraseService();
            $_SESSION['gm_error_message'] = urlencode($textPhraseService->getTextPhrase('message_stack_condition_notice_not_confirmed'));
            $this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_CONFIRMATION, 'error=condition_notice_not_confirmed', 'SSL', true, false));
            return true;
        }
        
        return false;
    }
    
    protected function validateProductConditionNotice(): bool
    {
        $conditionNoticeService = ProductConditionNoticeStaticServiceFactory::createConditionNoticeService();
        $showAgreement         = $conditionNoticeService->isConditionNoticeEnabled();
        $confirmationNeeded    = $conditionNoticeService->isConditionNoticeMandatory();
        $agreementAccepted     = ($this->v_data_array['POST']['condition_notice'] ?? null) === 'accepted';
    
        if ($showAgreement === false) {
            $this->unsetConditionNoticeSession();
        
            return true;
        }
    
        if ($this->issetConditionNoticeSession()
            && ($this->getConditionNoticeSessionValue() === 'accepted'
                || $this->getConditionNoticeSessionValue() === 'shown')) {
            return true;
        }
    
        if (($confirmationNeeded && $agreementAccepted)) {
            $this->setConditionNoticeSession('accepted');
        
            return true;
        }
    
        if ($confirmationNeeded === false) {
            $this->setConditionNoticeSession('shown');
        
            return true;
        }
    
        $this->setConditionNoticeSession('declined');
    
        return false;
    }
    
    
    protected function _getOrderAddonValuesCollection()
    {
        $addonValues = parent::_getOrderAddonValuesCollection();
        
        if ($this->issetConditionNoticeSession()) {
            $addonValues->setValue('ConditionNotice', $this->getConditionNoticeSessionValue());
        }
        
        if ($this->issetWarrantyNoticeSession()) {
            $addonValues->setValue('WarrantyNotice', $this->getWarrantyNoticeSessionValue());
        }
        
        return $addonValues;
    }
    
    
    /**
     * @return bool
     */
    protected function checkForUsedOrRenewedProducts(): bool
    {
        $order = $GLOBALS['order'] ?? new order();
        $usedProductService = ProductConditionNoticeStaticServiceFactory::createUsedProductService();
        $productIds         = array_map(function (array $product): int {
            preg_match('/^\d+/', $product['id'], $matches);
            
            return (int)$matches[0];
        }, $order->products ?? []);
        
        return $usedProductService->containsAUsedOrRenewedProduct(...$productIds);
    }
}

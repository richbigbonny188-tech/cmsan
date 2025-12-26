<?php
/* --------------------------------------------------------------
   ProductConditionNoticeContentView.inc.php 2021-12-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeStaticServiceFactory;

class ProductConditionNoticeContentView extends ProductConditionNoticeContentView_parent
{
    use CheckoutSessionTrait;
    
    public function prepare_data()
    {
        parent::prepare_data();

        if ($this->checkForUsedOrRenewedProducts() === false) {
            $this->content_array['SHOW_CONDITION_NOTICE'] = false;
        
            return;
        }
    
        $conditionNoticeService = ProductConditionNoticeStaticServiceFactory::createConditionNoticeService();
        
        $this->content_array['SHOW_CONDITION_NOTICE']         = $conditionNoticeService->isConditionNoticeEnabled();
        $this->content_array['CONDITION_NOTICE_IS_MANDATORY'] = $conditionNoticeService->isConditionNoticeMandatory();
        $this->content_array['CONDITION_NOTICE_TEXT']         = $conditionNoticeService->getConditionNoticeText($_SESSION['language_code']);
    
        $this->unsetConditionNoticeSession();
    
        if (isset($_SESSION['gm_error_message'])) {
            $this->content_array['ERROR'] = urldecode($_SESSION['gm_error_message']);
        }
    }
    
    
    /**
     * @return bool
     */
    protected function checkForUsedOrRenewedProducts(): bool
    {
        $usedProductService = ProductConditionNoticeStaticServiceFactory::createUsedProductService();
        $productIds         = array_map(function (array $product): int {
            preg_match('/^\d+/', $product['id'], $matches);
            
            return (int)$matches[0];
        }, $this->coo_order->products ?? []);
        
        return $usedProductService->containsAUsedOrRenewedProduct(...$productIds);
    }
}


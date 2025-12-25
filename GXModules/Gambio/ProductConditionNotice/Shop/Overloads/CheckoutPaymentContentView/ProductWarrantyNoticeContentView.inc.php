<?php
/* --------------------------------------------------------------
   ProductWarrantyNoticeContentView.inc.php 2021-12-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeStaticServiceFactory;

class ProductWarrantyNoticeContentView extends ProductWarrantyNoticeContentView_parent
{
    use CheckoutSessionTrait;
    
    public function prepare_data()
    {
        parent::prepare_data();
        
        if ($this->checkForUsedOrRenewedProducts() === false) {
            $this->content_array['SHOW_WARRANTY_NOTICE'] = false;
            
            return;
        }
        
        $warrantyNoticeService = ProductConditionNoticeStaticServiceFactory::createWarrantyNoticeService();
        
        $this->content_array['SHOW_WARRANTY_NOTICE']         = $warrantyNoticeService->isWarrantyNoticeEnabled();
        $this->content_array['WARRANTY_NOTICE_IS_MANDATORY'] = $warrantyNoticeService->isWarrantyNoticeMandatory();
        $this->content_array['WARRANTY_NOTICE_TEXT']         = $warrantyNoticeService->getWarrantyNoticeText($_SESSION['language_code']);
        
        $this->unsetWarrantyNoticeSession();
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

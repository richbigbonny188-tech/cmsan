<?php
/* --------------------------------------------------------------
   ProductConditionNoticeOrderExtender.inc.php 2021-12-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeStaticServiceFactory;
use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeTextPhraseService;

/**
 * Class ProductConditionNoticeOrderExtender
 */
class ProductConditionNoticeOrderExtender extends ProductConditionNoticeOrderExtender_parent
{
    /**
     * @return void
     */
    public function proceed()
    {
        $textPhraseService = ProductConditionNoticeStaticServiceFactory::createTextPhraseService();
        $orderReadService  = StaticGXCoreLoader::getService('OrderRead');
        $orderObject       = $orderReadService->getOrderById(new IdType($_GET['oID']));
        
        $orderInformation = array_merge($this->getConditionNoticeInformation($orderObject, $textPhraseService),
                                        $this->getWarrantyNoticeInformation($orderObject, $textPhraseService));
        
        if (count($orderInformation) > 0) {
            $this->v_output_buffer['below_order_info_heading'] = $textPhraseService->getTextPhrase('order_details_heading');
            $this->v_output_buffer['below_order_info']         = implode('<br><br>', $orderInformation);
            
            $this->addContent();
        }
        
        parent::proceed();
    }
    
    
    /**
     * @param OrderInterface                          $orderObject
     * @param ProductConditionNoticeTextPhraseService $textPhraseService
     *
     * @return array|string[]
     */
    protected function getConditionNoticeInformation(
        OrderInterface                          $orderObject,
        ProductConditionNoticeTextPhraseService $textPhraseService
    ): array {
        $orderAddonValues = $orderObject->getAddonValues();
        
        if ($orderAddonValues->keyExists('ConditionNotice') === false) {
            return [];
        }
        
        $date = $orderObject->getPurchaseDateTime()->format('d.m.Y H:i:s');
        $text = $textPhraseService->getTextPhrase('order_details_condition_notice_shown');
        $icon = '';
        
        if ($orderObject->getAddonValue(new StringType('ConditionNotice')) === 'accepted') {
            $icon = '<i class="fa fa-check fa-lg"></i> ';
            $text = $textPhraseService->getTextPhrase('order_details_condition_notice');
            $text .= ':<br>';
            $text .= $icon . ' ' . $textPhraseService->getTextPhrase('order_details_condition_notice_accepted');
        }
        
        return [$text . ' (' . $date . ')'];
    }
    
    
    /**
     * @param OrderInterface                          $orderObject
     * @param ProductConditionNoticeTextPhraseService $textPhraseService
     *
     * @return array|string[]
     */
    protected function getWarrantyNoticeInformation(
        OrderInterface                          $orderObject,
        ProductConditionNoticeTextPhraseService $textPhraseService
    ): array {
        $orderAddonValues = $orderObject->getAddonValues();
        
        if ($orderAddonValues->keyExists('WarrantyNotice') === false) {
            return [];
        }
        
        $date = $orderObject->getPurchaseDateTime()->format('d.m.Y H:i:s');
        $text = $textPhraseService->getTextPhrase('order_details_warranty_notice_shown');
        $icon = '';
        
        if ($orderObject->getAddonValue(new StringType('WarrantyNotice')) === 'accepted') {
            $icon = '<i class="fa fa-check fa-lg"></i> ';
            $text = $textPhraseService->getTextPhrase('order_details_warranty_notice');
            $text .= ':<br>';
            $text .= $icon . ' ' . $textPhraseService->getTextPhrase('order_details_warranty_notice_accepted');
        }
        
        return [$text . ' (' . $date . ')'];
    }
}
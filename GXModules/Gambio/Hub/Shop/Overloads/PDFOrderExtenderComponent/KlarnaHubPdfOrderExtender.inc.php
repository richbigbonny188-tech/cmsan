<?php
/* --------------------------------------------------------------
   KlarnaHubPdfOrderExtender.inc.php 2022-04-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaHubPdfOrderExtender extends KlarnaHubPdfOrderExtender_parent
{
    public function extendOrderInfo($orderInfo)
    {
        $orderInfo = parent::extendOrderInfo($orderInfo);
        if ($_GET['type'] === 'invoice') {
            $orderService  = StaticGXCoreLoader::getService('OrderRead');
            $order         = $orderService->getOrderById(new IdType((int)$_GET['oID']));
            $paymentType   = $order->getPaymentType();
            $paymentModule = $paymentType->getModule();
            
            if (in_array($paymentModule,
                         ['KlarnaPaylaterHub', 'KlarnaPaynowHub', 'KlarnaSliceitHub', 'KlarnaBanktransferHub'],
                         true)) {
                $languageId             = $this->v_data_array['order']->info['languages_id'];
                $language               = MainFactory::create('LanguageTextManager',
                                                              'gambio_hub_klarna_hub',
                                                              $languageId);
                $orderInfo['KlarnaHub'] = [
                    0 => 'Klarna',
                    1 => $language->get_text('InvoiceNote'),
                ];
            }
        }
        
        return $orderInfo;
    }
    
    
    public function extendPdfFooter($footer)
    {
        $footer = parent::extendPdfFooter($footer);
        
        if ($_GET['type'] === 'invoice') {
            $orderService  = StaticGXCoreLoader::getService('OrderRead');
            $order         = $orderService->getOrderById(new IdType((int)$_GET['oID']));
            $paymentType   = $order->getPaymentType();
            $paymentModule = $paymentType->getModule();
            
            if (in_array($paymentModule,
                         ['KlarnaPaylaterHub', 'KlarnaPaynowHub', 'KlarnaSliceitHub', 'KlarnaBanktransferHub'],
                         true)) {
                
                foreach ($this->getColumnsToRemove() as $column) {
                    if (isset($footer[$column])) {
                        unset($footer[$column]);
                    }
                }
                
                $footer = array_values($footer);
            }
        }
        
        return $footer;
    }
    
    
    /**
     * As parent::extendPdfFooter($footer) returns an array skipping empty footer cells, the specified columns to remove
     * have to be mapped accordingly.
     *
     * @return array
     */
    protected function getColumnsToRemove()
    {
        $footerIndexMapping = [];
        $shift              = 0;
        foreach ($this->getFooterCells() as $index => $cell) {
            if (!empty($cell)) {
                $footerIndexMapping[$index] = $index - $shift;
            } else {
                $shift += 1;
            }
        }
        
        $footerColumnsToRemove = $this->getColumnsToRemoveConfiguration();
        
        foreach ($footerColumnsToRemove as $key => $column) {
            // ignore empty cells
            if (!isset($footerIndexMapping[$column])) {
                unset($footerColumnsToRemove[$key]);
                continue;
            }
            
            // map cell index
            $footerColumnsToRemove[$key] = $footerIndexMapping[$column];
        }
        
        return array_unique($footerColumnsToRemove);
    }
    
    
    /**
     * Returns the columns to remove as configures in the Gambio Admin.
     *
     * @return array
     */
    protected function getColumnsToRemoveConfiguration()
    {
        $footerReplace = gm_get_conf('PDF_FOOTER_REPLACE_ARRAY');
        if (empty($footerReplace) || $footerReplace === 'null') {
            $footerReplace = gm_get_conf('PDF_FOOTER_REPLACE_COLUMN');
        }
        
        return explode(', ', (string)$footerReplace);
    }
    
    
    /**
     * Returns the footer cells as configured in the Gambio Admin.
     *
     * @return array
     */
    protected function getFooterCells()
    {
        return gm_get_content([
                                  'GM_PDF_FOOTER_CELL_1',
                                  'GM_PDF_FOOTER_CELL_2',
                                  'GM_PDF_FOOTER_CELL_3',
                                  'GM_PDF_FOOTER_CELL_4',
                              ],
                              $this->v_data_array['order']->info['languages_id'],
                              'NUMERIC');
    }
}

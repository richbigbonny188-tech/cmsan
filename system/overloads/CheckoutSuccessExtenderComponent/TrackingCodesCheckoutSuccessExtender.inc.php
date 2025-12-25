<?php
/* --------------------------------------------------------------
   TrackingCodesCheckoutSuccessExtender.inc.php 2019-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class TrackingCodesCheckoutSuccessExtender extends TrackingCodesCheckoutSuccessExtender_parent
{
    public function proceed()
    {
        parent::proceed();
        if ((bool)gm_get_conf('GM_TRACKING_CODE_CHECKOUT_SUCCESS_USE')) {
            $html    = (string)gm_get_conf('GM_TRACKING_CODE_CHECKOUT_SUCCESS');
            $htmlOutput = $html;
            if ((bool)gm_get_conf('GM_TRACKING_CODE_CHECKOUT_SUCCESS_USE_SMARTY')) {
                $content = [
                    'legacyOrder' => $this->v_data_array['coo_order'],
                ];
                if (isset($this->v_data_array['orders_id']) && !empty($this->v_data_array['orders_id'])) {
                    $ordersId = new IdType((int)$this->v_data_array['orders_id']);
                    /** @var OrderReadService $orderReadService */
                    $orderReadService = StaticGXCoreLoader::getService('OrderRead');
                    /** @var OrderInterface $gxOrder */
                    $gxOrder          = $orderReadService->getOrderById($ordersId);
                    $content['order'] = $gxOrder;
                }
    
                $contentView = MainFactory::create('ContentView');
                $contentView->set_content_template_from_string($html, false);
                $contentView->set_flat_assigns(true);
                foreach ($content as $contentKey => $contentValue) {
                    $contentView->set_content_data($contentKey, $contentValue);
                }
                try {
                    $htmlOutput = $contentView->get_html();
                } catch (Exception $e) {
                    gm_set_conf('GM_TRACKING_CODE_CHECKOUT_SUCCESS_USE_SMARTY', false);
                }
            }
            $this->html_output_array['TRACKING_CODE_CHECKOUT_SUCCESS'] = $htmlOutput;
        }
    }
}

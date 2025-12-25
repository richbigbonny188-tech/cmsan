<?php
/* --------------------------------------------------------------
   GiftVoucherOrderExtender.inc.php 2019-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GiftVoucherOrderExtender extends GiftVoucherOrderExtender_parent
{
    public function proceed()
    {
        parent::proceed();
        $phrases  = MainFactory::create('LanguageTextManager', 'ot_gv');
        $ordersId = new IdType((int)$this->v_data_array['GET']['oID']);
        /** @var \OrderReadService $orderRead */
        $orderRead = StaticGXCoreLoader::getService('OrderRead');
        $order = $orderRead->getOrderById($ordersId);
        $db       = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $gvRedeemData = $db->get_where('coupon_gv_redeem_track', ['orders_id' => $ordersId->asInt()])->result_array();
        $tableData = [];
        foreach ($gvRedeemData as $gvRedeemDatum) {
            $tableData[] = [
                'coupon_code' => $gvRedeemDatum['coupon_code'] === 'balance' ? $phrases->get_text('CUSTOMER_BALANCE') : $gvRedeemDatum['coupon_code'],
                'amount' => number_format($gvRedeemDatum['amount'], 2) . ' ' . $order->getCurrencyCode()->getCode(),
            ];
        }
        
        if (!empty($tableData))
        {
            /** @var \ContentView $contentView */
            $contentView = MainFactory::create('ContentView');
            $contentView->set_template_dir(DIR_FS_CATALOG . 'admin/html/content');
            $contentView->set_content_template('order_gift_vouchers.html');
            $contentView->set_content_data('gvRedeemData', $tableData);
            $content = $contentView->get_html();
            $this->addContentToCollection('below_order_info', $content, $phrases->get_text('MODULE_ORDER_TOTAL_GV_TITLE'));
        }
    }
}

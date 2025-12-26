<?php
/* --------------------------------------------------------------
   PaymentInstructionOrderExtender.inc.php 2018-02-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PaymentInstructionOrderExtender extends PaymentInstructionOrderExtender_parent
{
	public function proceed()
	{
		parent::proceed();
		$ordersId = (int)$this->v_data_array['GET']['oID'];
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$paymentInstructionQuery = $db->get_where('orders_payment_instruction', ['orders_id' => $ordersId]);
		$paymentInstruction = $paymentInstructionQuery->row_array();
		if(!empty($paymentInstruction))
		{
			$phrases = MainFactory::create('LanguageTextManager', 'admin_order_payment_instruction');
			/** @var \ContentView $contentView */
			$contentView = MainFactory::create('ContentView');
			$contentView->set_template_dir(DIR_FS_CATALOG . 'admin/html/content');
			$contentView->set_content_template('order_payment_instruction.html');
			$contentView->set_content_data('payment_instruction', $paymentInstruction);
			$content = $contentView->get_html();
			$this->addContentToCollection('below_order_info', $content, $phrases->get_text('payment_instruction'));
		}
	}
}

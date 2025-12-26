/* --------------------------------------------------------------
 update_order_lines.js 2017-12-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Triggers the update of the shop order lines recalculation so that both Klarna and shop share the same price amounts.
 */
$(function() {
	'use strict';
	
	/**
	 * Initializes the module.
	 *
	 * @private
	 */
	const init = () => {
		const orderId = KlarnaHub.Lib.getUrlParameter('oID');
		
		if (!orderId) {
			return;
		}
		
		const $orderDetailsTable = $('#order-details-table');
		
		const $spinner = jse.libs.loading_spinner.show($orderDetailsTable);
		
		const url = 'orders_edit.php?action=save_order&oID=' + orderId;
		
		const data = {
			customers_status_id: '',
			oID: orderId,
			cID: '',
			recalculate: '1'
		};
		
		$.post(url, data)
			.done((response) => {
				const $html = $(response);
				
				$orderDetailsTable.replaceWith($html.find('#order-details-table').parent().html());
				
				jse.libs.loading_spinner.hide($spinner);
			});
	};
	
	KlarnaHub.on('ready', () => init());
}); 
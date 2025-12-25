/* --------------------------------------------------------------
 order_overview.js 2016-12-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gambio Hub Order Overview Module
 *
 * This file will make sure that the order overview page will display the correct order data. It can be included through
 * the use of an extender.
 *
 * It requires an element with the "data-gambio-hub-source-selector" which contains the selector of a script
 * tag with the payment module information. This script tag must have the type "application/json" and must contain
 * a JSON object with "orderId": "gambioHubPaymentModule" key-value pairs.
 */
(function() {
	
	'use strict';
	
	// Initialize the page modifications.
	function initialize() {
		var sourceSelector = $('[data-gambio-hub-source-selector]').data('gambioHubSourceSelector');
		var paymentModules = JSON.parse($(sourceSelector).text());
		
		// Iterate through the table entries and correct the payment method values. 
		var $table = $('.dataTableHeadingRow').parents('table');
		
		$table.find('.dataTableRow,.dataTableRowSelected').not('.dataTableHeadingRow').each(function(index, tr) {
			var $td = $(tr).find('[title="gambio_hub"]');
			var orderId = +$(tr).find('td:eq(1) a').text();
			
			if ($td.length && paymentModules[orderId]) {
				$td.text(paymentModules[orderId]);
			}
		});
	}
	
	// Initialize the module once the page is ready (without jQuery).
	if (document.readyState != 'loading') {
		initialize();
	} else {
		document.addEventListener('DOMContentLoaded', initialize);
	}
})();
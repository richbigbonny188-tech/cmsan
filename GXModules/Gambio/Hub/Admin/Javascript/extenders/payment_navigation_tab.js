/* --------------------------------------------------------------
 payment_navigation_tab.js 2017-02-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gambio Payment Navigation Tab
 *
 * This file generates the tab navigation for the old payment module page.
 */
(function() {
	
	'use strict';
	
	// Initialize the page modifications.
	function initialize() {
		var html = $('<table><tr><td class="dataTableHeadingContent"><a href="admin.php?do=HubConfiguration/paymentMethods">'
			+ jse.core.lang.translate('BOX_HEADING_HUB_PAYMENT', 'admin_general')
			+ '</a></td><td class="dataTableHeadingContent">'
			+ jse.core.lang.translate('BOX_HEADING_HUB_PAYMENT_MISC', 'admin_general') + '</td></tr></table>');
		var $target = $('.pageHeading');
		$target.append(html);
	}
	
	// Initialize the module once the page is ready (without jQuery).
	if (document.readyState != 'loading') {
		initialize();
	} else {
		document.addEventListener('DOMContentLoaded', initialize);
	}
})();
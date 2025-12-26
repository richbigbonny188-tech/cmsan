/* --------------------------------------------------------------
 hide_new_product_table.js 2017-11-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Hides the new product table for KlarnaHub orders.
 */
(function() {
	'use strict';
	
	// Hide the table directory (no need to wait till the page has finished loading).
	document.querySelector('.orders-edit-table:last-of-type').classList.add('hidden');
})();
/* --------------------------------------------------------------
 disable_edit_button_dropdown.js 2017-11-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Disables the order edit action button.
 */
(function() {
	'use strict';
	
	/**
	 * Initializes the module.
	 *
	 * @private
	 */
	const init = () => {
		$('.js-button-dropdown li').each((index, li) => {
			const $li = $(li);
			
			const onclickAttribute = $li.attr('onclick');
			
			if (onclickAttribute && onclickAttribute.includes('orders_edit.php')) {
				$li.remove();
			}
		});
	};
	
	KlarnaHub.on('ready', () => init());
})(); 
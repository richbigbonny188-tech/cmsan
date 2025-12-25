/* --------------------------------------------------------------
 disable_other_order_edit_buttons.js 2017-11-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Disables the rest order edit buttons (address and payment, shipping and sums).
 */
(function() {
	'use strict';
	
	/**
	 * Initializes the module.
	 *
	 * @private
	 */
	const init = () => {
		const $links = $('.page-nav-tabs a');
		
		$links.each((index, link) => {
			$(link)
				.css({
					'opacity': 0.5,
					'background': '#FFF',
				})
				.attr('href', '#')
				.off('click');
		});
	};
	
	KlarnaHub.on('ready', () => init());
})();
/* --------------------------------------------------------------
 disable_higher_order_amounts.js 2019-01-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Disable higher order amounts of order edit form.
 */
(function() {
	'use strict';
	
	/**
	 * Initializes the module.
	 */
	const init = () => {
		const $form = $('[name="product_edit"]');
		
		$form.each((index, form) => {
			const $form = $(form);
			
			$form.data('originalValues', {
				index,
				products_quantity: $form.find('[name="products_quantity"]').val(),
				products_price: $form.find('[name="products_price"]').val()
			});
		});
		
		const $target = $('[name="products_quantity"], [name="products_tax"], [name="products_price"]');
		
		$target
			.on('keyup keypress', (event) => {
				const keyCode = event.keyCode || event.which;
				
				if (keyCode === 13) {
					event.preventDefault();
					return false;
				}
			})
			.on('change', (event) => {
				const $input = $(event.target);
				
				const originalValues = $input.closest('form').data('originalValues');
				
				const fieldName = $input.attr('name');
				
				const originalValue = originalValues[fieldName];
				
				if (isNaN(originalValue)) {
					return;
				}
				
				if (Number($input.val()) > Number(originalValue)) {
					KlarnaHub.Lib.showMessage('Klarna', KlarnaHub.Config.lang.ONLY_LOWER_AMOUNTS_ARE_ALLOWED);
					event.target.value = originalValue;
				}
			});
	};
	
	KlarnaHub.on('ready', () => init());
})();

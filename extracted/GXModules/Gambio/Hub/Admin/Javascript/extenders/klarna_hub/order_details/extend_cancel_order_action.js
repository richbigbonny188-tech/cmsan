/* --------------------------------------------------------------
 extend_cancel_order_action.js 2017-11-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Extends cancel-order row action (will call the respective KlarnaHub callback).
 */
(function() {
	'use strict';
	
	/**
	 * Initializes the module.
	 *
	 * @private
	 */
	const init = () => {
		const $form = $('#multi_cancel_confirm_form');
		
		const $sourceControlGroup = $form.find('.single-checkbox:first').closest('.control-group');
		
		const $controlGroup = $sourceControlGroup.clone(true);
		
		const $label = $controlGroup.find('label');
		$label.text(KlarnaHub.Config.lang.NOTIFY_KLARNA);
		
		const $singleCheckbox = $controlGroup.find('.single-checkbox');
		const $checkbox = $controlGroup.find('input:checkbox');
		$checkbox.addClass('notify-klarna');
		
		$controlGroup.insertBefore($sourceControlGroup.next());
		
		$form.on('submit', (event) => {
			event.preventDefault();
			
			if (!$checkbox.prop('checked')) {
				$form[0].submit(); 
				return;
			}
			
			KlarnaHub.Api.executeCancelOrder().then(() => $form[0].submit());
		});
		
		$('.js-button-dropdown .cancel-order').on('click', () => {
			$checkbox.prop('checked', true); 
			$singleCheckbox.addClass('checked');
		}); 
	};
	
	KlarnaHub.on('ready', () => init());
})(); 

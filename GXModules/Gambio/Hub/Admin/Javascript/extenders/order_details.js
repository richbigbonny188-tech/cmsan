/* --------------------------------------------------------------
 order_details.js 2019-02-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gambio Hub Order Details Module
 *
 * This file will make sure that the order details page will display the correct order data. It can be included through
 * the use of an extender.
 *
 * It requires an element with the "data-gambio-hub-payment-module" and the "data-gambio-hub-target-selector"
 * attributes which contain the module name and target element selector, for the replacement of the payment module name
 * in the order details page.
 * 
 * Order Status Comments Support: 
 * 
 * Hub module frontend iframe can send the "hub:status:comments" message in order to display the order status change
 * modal with pre-filled comments. 
 * 
 * The child iframe document must post the following message: 
 * 
 * ```javascript
 * parent.postMessage({
 *   type: 'hub:status:comments', 
 *   comments: 'Value for the comment box.' 
 * }, '*');
 * ```
 */
(function() {
	
	'use strict';
	
	// Callbacks for messages coming from hub module boxes.
	const messageHandlers = {
		'hub:status:comments': data => {
			// Open the status change modal and pre-fill the comment box.
			const clickEvent = document.createEvent('HTMLEvents');
			clickEvent.initEvent('click', true,  false);
			document.querySelector('.btn.update-order-status').dispatchEvent(clickEvent);
			
			const textarea = document.querySelector('form[name="update_orders_status_form"] textarea[name="gm_comments"]');
			textarea.value = data.comments;
		},
		'hub:load_url': data => {
			document.location = data.url;
		}
	};
	
	// Initialize the page modifications.
	function initialize() {
		const paymentModule = $('[data-gambio-hub-payment-module]').data('gambioHubPaymentModule');
		const paymentModuleTitle = $('[data-gambio-hub-payment-module-title]').data('gambioHubPaymentModuleTitle');
		const $target = $('[title="gambio_hub"]');
		$target.html(paymentModuleTitle);
		$target.attr('title', paymentModule);
		
		// Remove default frame from #hub-order-frontend iframe. 
		const $iframes = $('#hub-order-frontend, #hub-order-frontend-cft');
		
		$iframes.each(function(index, iframe) {
			if (iframe) {
				iframe.parentNode.style.padding = '0'; 
				iframe.parentNode.previousElementSibling.style.display = 'none';
				iframe.parentNode.parentNode.style.border = 'none';
				
				// Display loading spinner.
				$(document).on('JSENGINE_INIT_FINISHED', () => {
					const postfix = jse.core.config.get('environment') === 'production' ? '.min' : '';
					const loadingSpinnerUrl = `${jse.core.config.get('appUrl')}/JSEngine/build/libs/loading_spinner${postfix}.js`;
					
					jse.core.module_loader.require([loadingSpinnerUrl], () => {
						const $spinner = jse.libs.loading_spinner.show($(iframe));
						
						iframe.onload = () => {
							jse.libs.loading_spinner.hide($spinner);
							iframe.onload = null;
						};
						
						setTimeout(function() {
							if ($spinner.length) {
								$spinner.remove();
							}
						}, 3000);
					});
				});
			}
		}); 
		
		// Listen for iframe messages. 
		window.addEventListener('message', event => {
			const messageType = event.data.type;
			
			if (messageHandlers[messageType]) {
				messageHandlers[messageType](event.data);
			}
		}, false);
	}
	
	// Initialize the module once the page is ready (without jQuery).
	if (document.readyState !== 'loading') {
		initialize();
	} else {
		document.addEventListener('DOMContentLoaded', initialize);
	}
})();

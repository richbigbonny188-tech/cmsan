/* --------------------------------------------------------------
 extend_tracking_codes_button.js 2022-03-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Extends the add tracking code modal with a "notify-klarna" checkbox.
 *
 */
(function() {
	'use strict';
	
	/**
	 * Initializes the module.
	 *
	 * @private
	 */
	const init = () => {
		// Tracking code selectors.
        let $shippingCompanySelect = $('#delivery-service');
        let $trackingCodeText      = $('#tracking-number');
        
        $('.notify-klarna-container').show();
        
        $trackingCodeText.one('change', () => {
            const $submitButton = $trackingCodeText.closest('.ui-dialog').find('.btn-primary');
            
            $submitButton.hide();
            
            $submitButton.after('<button type="button" class="btn btn-primary ui-button ui-corner-all ui-widget" id="btn-add-tracking-number">'
                + $submitButton.html() + '</button>');
            
            const $addTrackingCodeButton = $('#btn-add-tracking-number');
            
            $addTrackingCodeButton.off('click.notifyklarna').on('click.notifyklarna', () => {
                if ($shippingCompanySelect.val() === '') {
                    return;
                }
                
                if ($trackingCodeText.val() === '') {
                    return;
                }
                
                const shippingCompany = $shippingCompanySelect.find('option:selected').text();
                const trackingNumber = $trackingCodeText.val();
                
                if ($('#notify-klarna').is(':checked')) {
                    KlarnaHub.Api.executeAddTrackingCode(shippingCompany, trackingNumber).then($submitButton.trigger('click'));
                } else {
                    $submitButton.trigger('click');
                }
                
            });
        });
	};
	
	KlarnaHub.on('ready', () => init());
})(); 
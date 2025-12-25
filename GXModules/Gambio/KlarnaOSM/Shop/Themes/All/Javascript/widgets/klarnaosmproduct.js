/* --------------------------------------------------------------
   klarnaosmproduct.js 2022-06-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

klarnaosm.widgets.module(
    'klarnaosmproduct',
    [
        gambio.source + '/libs/events'
    ],
    function(data) {
        'use strict';
        
        // ########## VARIABLE INITIALIZATION ##########
        var $this = $(this),
            defaults = {},
            options = $.extend(true, {}, defaults, data),
            module = {};
        
        module.init = function(done) {
            if (typeof $ !== 'undefined' && typeof jse !== 'undefined') {
                $(window).on(jse.libs.theme.events.STICKYBOX_CONTENT_CHANGE(), function() {
                    let kosmPrice = document.getElementById('kosm_price');
                    if (kosmPrice === null) {
                        return;
                    }
                    let kosmPriceData = JSON.parse(kosmPrice.textContent),
                        price = kosmPriceData.price;
                    document.querySelectorAll('div.klarnaosm klarna-placement').forEach(function(e) {
                        e.dataset.purchaseAmount = price;
                    });
                    
                    window.KlarnaOnsiteService = window.KlarnaOnsiteService || [];
                    window.KlarnaOnsiteService.push({eventName: 'refresh-placements'});
                });
            }
            done();
        };
        
        return module;
    }
);

/* --------------------------------------------------------------
 disable_edit_address_button.js 2020-09-10
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Disables the order address edit button.
 */
(function() {
    'use strict';
    
    /**
     * Initializes the module.
     *
     * @private
     */
    const init = () => {
        const $links = $('.frame-head .head-link a');
        
        $links.each((index, link) => {
            let $link = $(link);
            
            if ($link.attr('href').includes('edit_action=address') ||
                $link.attr('href').includes('orders_edit.php')) {
                $link
                    .parent()
                    .append(
                        $('<span/>', {
                            'text': $link.text().trim()
                        })
                    )
                    .css({
                        'opacity': .6,
                        'color': 'gray',
                        'background': 'none',
                        'cursor': 'not-allowed'
                    });
                
                $link.remove();
            }
        });
    };
    
    EasyCreditHub.on('ready', () => init());
})(); 

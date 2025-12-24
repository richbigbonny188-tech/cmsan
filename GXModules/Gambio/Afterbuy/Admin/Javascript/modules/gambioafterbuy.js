/* --------------------------------------------------------------
   gambioafterbuy.js 2018-05-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


$(function () {
    'use strict';

    const $table = $('.orders .table-main');

    $table.on('init.dt', function () {
        const _initSingleAction = function ($table) {
            $table.find('.btn-group.dropdown').each(function () {
                const orderId = $(this).parents('tr').data('id');
                const defaultRowAction = $table.data('defaultRowAction') || 'edit';

                jse.libs.button_dropdown.addAction($(this), {
                    text: jse.core.lang.translate('button_send', 'afterbuy'),
                    href: '',
                    class: 'afterbuy-send',
                    data: {configurationValue: 'afterbuy-send'},
                    isDefault: defaultRowAction === 'afterbuy-send',
                    callback: (event) => {
                        event.preventDefault();

                        $.ajax({
                            url: jse.core.config.get('appUrl') +
                                '/admin/admin.php?do=GambioAfterbuyAjax/SendOrder&orderId=' + orderId,
                            success: (data) => {
                                alert(data.message);
                            },
                            error: () => {
                                console.log('Afterbuy send error');
                            }
                        });
                    }
                });
            });
        };

        $table.on('draw.dt', () => _initSingleAction($table));
        _initSingleAction($table);
    });
});

'use strict';

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

    var $table = $('.orders .table-main');

    $table.on('init.dt', function () {
        var _initSingleAction = function _initSingleAction($table) {
            $table.find('.btn-group.dropdown').each(function () {
                var orderId = $(this).parents('tr').data('id');
                var defaultRowAction = $table.data('defaultRowAction') || 'edit';

                jse.libs.button_dropdown.addAction($(this), {
                    text: jse.core.lang.translate('button_send', 'afterbuy'),
                    href: '',
                    class: 'afterbuy-send',
                    data: { configurationValue: 'afterbuy-send' },
                    isDefault: defaultRowAction === 'afterbuy-send',
                    callback: function callback(event) {
                        event.preventDefault();

                        $.ajax({
                            url: jse.core.config.get('appUrl') + '/admin/admin.php?do=GambioAfterbuyAjax/SendOrder&orderId=' + orderId,
                            success: function success(data) {
                                alert(data.message);
                            },
                            error: function error() {
                                console.log('Afterbuy send error');
                            }
                        });
                    }
                });
            });
        };

        $table.on('draw.dt', function () {
            return _initSingleAction($table);
        });
        _initSingleAction($table);
    });
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvbW9kdWxlcy9nYW1iaW9hZnRlcmJ1eS5qcyJdLCJuYW1lcyI6WyIkIiwiJHRhYmxlIiwib24iLCJfaW5pdFNpbmdsZUFjdGlvbiIsImZpbmQiLCJlYWNoIiwib3JkZXJJZCIsInBhcmVudHMiLCJkYXRhIiwiZGVmYXVsdFJvd0FjdGlvbiIsImpzZSIsImxpYnMiLCJidXR0b25fZHJvcGRvd24iLCJhZGRBY3Rpb24iLCJ0ZXh0IiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJocmVmIiwiY2xhc3MiLCJjb25maWd1cmF0aW9uVmFsdWUiLCJpc0RlZmF1bHQiLCJjYWxsYmFjayIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJhamF4IiwidXJsIiwiY29uZmlnIiwiZ2V0Iiwic3VjY2VzcyIsImFsZXJ0IiwibWVzc2FnZSIsImVycm9yIiwiY29uc29sZSIsImxvZyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVdBQSxFQUFFLFlBQVk7QUFDVjs7QUFFQSxRQUFNQyxTQUFTRCxFQUFFLHFCQUFGLENBQWY7O0FBRUFDLFdBQU9DLEVBQVAsQ0FBVSxTQUFWLEVBQXFCLFlBQVk7QUFDN0IsWUFBTUMsb0JBQW9CLFNBQXBCQSxpQkFBb0IsQ0FBVUYsTUFBVixFQUFrQjtBQUN4Q0EsbUJBQU9HLElBQVAsQ0FBWSxxQkFBWixFQUFtQ0MsSUFBbkMsQ0FBd0MsWUFBWTtBQUNoRCxvQkFBTUMsVUFBVU4sRUFBRSxJQUFGLEVBQVFPLE9BQVIsQ0FBZ0IsSUFBaEIsRUFBc0JDLElBQXRCLENBQTJCLElBQTNCLENBQWhCO0FBQ0Esb0JBQU1DLG1CQUFtQlIsT0FBT08sSUFBUCxDQUFZLGtCQUFaLEtBQW1DLE1BQTVEOztBQUVBRSxvQkFBSUMsSUFBSixDQUFTQyxlQUFULENBQXlCQyxTQUF6QixDQUFtQ2IsRUFBRSxJQUFGLENBQW5DLEVBQTRDO0FBQ3hDYywwQkFBTUosSUFBSUssSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsYUFBeEIsRUFBdUMsVUFBdkMsQ0FEa0M7QUFFeENDLDBCQUFNLEVBRmtDO0FBR3hDQywyQkFBTyxlQUhpQztBQUl4Q1gsMEJBQU0sRUFBQ1ksb0JBQW9CLGVBQXJCLEVBSmtDO0FBS3hDQywrQkFBV1oscUJBQXFCLGVBTFE7QUFNeENhLDhCQUFVLGtCQUFDQyxLQUFELEVBQVc7QUFDakJBLDhCQUFNQyxjQUFOOztBQUVBeEIsMEJBQUV5QixJQUFGLENBQU87QUFDSEMsaUNBQUtoQixJQUFJSyxJQUFKLENBQVNZLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLFFBQXBCLElBQ0QsMkRBREMsR0FDNkR0QixPQUYvRDtBQUdIdUIscUNBQVMsaUJBQUNyQixJQUFELEVBQVU7QUFDZnNCLHNDQUFNdEIsS0FBS3VCLE9BQVg7QUFDSCw2QkFMRTtBQU1IQyxtQ0FBTyxpQkFBTTtBQUNUQyx3Q0FBUUMsR0FBUixDQUFZLHFCQUFaO0FBQ0g7QUFSRSx5QkFBUDtBQVVIO0FBbkJ1QyxpQkFBNUM7QUFxQkgsYUF6QkQ7QUEwQkgsU0EzQkQ7O0FBNkJBakMsZUFBT0MsRUFBUCxDQUFVLFNBQVYsRUFBcUI7QUFBQSxtQkFBTUMsa0JBQWtCRixNQUFsQixDQUFOO0FBQUEsU0FBckI7QUFDQUUsMEJBQWtCRixNQUFsQjtBQUNILEtBaENEO0FBaUNILENBdENEIiwiZmlsZSI6IkFkbWluL0phdmFzY3JpcHQvbW9kdWxlcy9nYW1iaW9hZnRlcmJ1eS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICBnYW1iaW9hZnRlcmJ1eS5qcyAyMDE4LTA1LTI5XG4gICBHYW1iaW8gR21iSFxuICAgaHR0cDovL3d3dy5nYW1iaW8uZGVcbiAgIENvcHlyaWdodCAoYykgMjAxOCBHYW1iaW8gR21iSFxuICAgUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gICBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAgIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4qL1xuXG5cbiQoZnVuY3Rpb24gKCkge1xuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIGNvbnN0ICR0YWJsZSA9ICQoJy5vcmRlcnMgLnRhYmxlLW1haW4nKTtcblxuICAgICR0YWJsZS5vbignaW5pdC5kdCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgY29uc3QgX2luaXRTaW5nbGVBY3Rpb24gPSBmdW5jdGlvbiAoJHRhYmxlKSB7XG4gICAgICAgICAgICAkdGFibGUuZmluZCgnLmJ0bi1ncm91cC5kcm9wZG93bicpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGNvbnN0IG9yZGVySWQgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZGF0YSgnaWQnKTtcbiAgICAgICAgICAgICAgICBjb25zdCBkZWZhdWx0Um93QWN0aW9uID0gJHRhYmxlLmRhdGEoJ2RlZmF1bHRSb3dBY3Rpb24nKSB8fCAnZWRpdCc7XG5cbiAgICAgICAgICAgICAgICBqc2UubGlicy5idXR0b25fZHJvcGRvd24uYWRkQWN0aW9uKCQodGhpcyksIHtcbiAgICAgICAgICAgICAgICAgICAgdGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2J1dHRvbl9zZW5kJywgJ2FmdGVyYnV5JyksXG4gICAgICAgICAgICAgICAgICAgIGhyZWY6ICcnLFxuICAgICAgICAgICAgICAgICAgICBjbGFzczogJ2FmdGVyYnV5LXNlbmQnLFxuICAgICAgICAgICAgICAgICAgICBkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnYWZ0ZXJidXktc2VuZCd9LFxuICAgICAgICAgICAgICAgICAgICBpc0RlZmF1bHQ6IGRlZmF1bHRSb3dBY3Rpb24gPT09ICdhZnRlcmJ1eS1zZW5kJyxcbiAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2s6IChldmVudCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJy9hZG1pbi9hZG1pbi5waHA/ZG89R2FtYmlvQWZ0ZXJidXlBamF4L1NlbmRPcmRlciZvcmRlcklkPScgKyBvcmRlcklkLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IChkYXRhKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFsZXJ0KGRhdGEubWVzc2FnZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBlcnJvcjogKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnQWZ0ZXJidXkgc2VuZCBlcnJvcicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAkdGFibGUub24oJ2RyYXcuZHQnLCAoKSA9PiBfaW5pdFNpbmdsZUFjdGlvbigkdGFibGUpKTtcbiAgICAgICAgX2luaXRTaW5nbGVBY3Rpb24oJHRhYmxlKTtcbiAgICB9KTtcbn0pO1xuIl19

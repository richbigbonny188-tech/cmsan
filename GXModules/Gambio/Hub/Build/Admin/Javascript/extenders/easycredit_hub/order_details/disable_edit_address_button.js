'use strict';

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
(function () {
    'use strict';

    /**
     * Initializes the module.
     *
     * @private
     */

    var init = function init() {
        var $links = $('.frame-head .head-link a');

        $links.each(function (index, link) {
            var $link = $(link);

            if ($link.attr('href').includes('edit_action=address') || $link.attr('href').includes('orders_edit.php')) {
                $link.parent().append($('<span/>', {
                    'text': $link.text().trim()
                })).css({
                    'opacity': .6,
                    'color': 'gray',
                    'background': 'none',
                    'cursor': 'not-allowed'
                });

                $link.remove();
            }
        });
    };

    EasyCreditHub.on('ready', function () {
        return init();
    });
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2Vhc3ljcmVkaXRfaHViL29yZGVyX2RldGFpbHMvZGlzYWJsZV9lZGl0X2FkZHJlc3NfYnV0dG9uLmpzIl0sIm5hbWVzIjpbImluaXQiLCIkbGlua3MiLCIkIiwiZWFjaCIsImluZGV4IiwibGluayIsIiRsaW5rIiwiYXR0ciIsImluY2x1ZGVzIiwicGFyZW50IiwiYXBwZW5kIiwidGV4dCIsInRyaW0iLCJjc3MiLCJyZW1vdmUiLCJFYXN5Q3JlZGl0SHViIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0EsQ0FBQyxZQUFXO0FBQ1I7O0FBRUE7Ozs7OztBQUtBLFFBQU1BLE9BQU8sU0FBUEEsSUFBTyxHQUFNO0FBQ2YsWUFBTUMsU0FBU0MsRUFBRSwwQkFBRixDQUFmOztBQUVBRCxlQUFPRSxJQUFQLENBQVksVUFBQ0MsS0FBRCxFQUFRQyxJQUFSLEVBQWlCO0FBQ3pCLGdCQUFJQyxRQUFRSixFQUFFRyxJQUFGLENBQVo7O0FBRUEsZ0JBQUlDLE1BQU1DLElBQU4sQ0FBVyxNQUFYLEVBQW1CQyxRQUFuQixDQUE0QixxQkFBNUIsS0FDQUYsTUFBTUMsSUFBTixDQUFXLE1BQVgsRUFBbUJDLFFBQW5CLENBQTRCLGlCQUE1QixDQURKLEVBQ29EO0FBQ2hERixzQkFDS0csTUFETCxHQUVLQyxNQUZMLENBR1FSLEVBQUUsU0FBRixFQUFhO0FBQ1QsNEJBQVFJLE1BQU1LLElBQU4sR0FBYUMsSUFBYjtBQURDLGlCQUFiLENBSFIsRUFPS0MsR0FQTCxDQU9TO0FBQ0QsK0JBQVcsRUFEVjtBQUVELDZCQUFTLE1BRlI7QUFHRCxrQ0FBYyxNQUhiO0FBSUQsOEJBQVU7QUFKVCxpQkFQVDs7QUFjQVAsc0JBQU1RLE1BQU47QUFDSDtBQUNKLFNBckJEO0FBc0JILEtBekJEOztBQTJCQUMsa0JBQWNDLEVBQWQsQ0FBaUIsT0FBakIsRUFBMEI7QUFBQSxlQUFNaEIsTUFBTjtBQUFBLEtBQTFCO0FBQ0gsQ0FwQ0QiLCJmaWxlIjoiQWRtaW4vSmF2YXNjcmlwdC9leHRlbmRlcnMvZWFzeWNyZWRpdF9odWIvb3JkZXJfZGV0YWlscy9kaXNhYmxlX2VkaXRfYWRkcmVzc19idXR0b24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGRpc2FibGVfZWRpdF9hZGRyZXNzX2J1dHRvbi5qcyAyMDIwLTA5LTEwXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAyMCBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBEaXNhYmxlcyB0aGUgb3JkZXIgYWRkcmVzcyBlZGl0IGJ1dHRvbi5cbiAqL1xuKGZ1bmN0aW9uKCkge1xuICAgICd1c2Ugc3RyaWN0JztcbiAgICBcbiAgICAvKipcbiAgICAgKiBJbml0aWFsaXplcyB0aGUgbW9kdWxlLlxuICAgICAqXG4gICAgICogQHByaXZhdGVcbiAgICAgKi9cbiAgICBjb25zdCBpbml0ID0gKCkgPT4ge1xuICAgICAgICBjb25zdCAkbGlua3MgPSAkKCcuZnJhbWUtaGVhZCAuaGVhZC1saW5rIGEnKTtcbiAgICAgICAgXG4gICAgICAgICRsaW5rcy5lYWNoKChpbmRleCwgbGluaykgPT4ge1xuICAgICAgICAgICAgbGV0ICRsaW5rID0gJChsaW5rKTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgaWYgKCRsaW5rLmF0dHIoJ2hyZWYnKS5pbmNsdWRlcygnZWRpdF9hY3Rpb249YWRkcmVzcycpIHx8XG4gICAgICAgICAgICAgICAgJGxpbmsuYXR0cignaHJlZicpLmluY2x1ZGVzKCdvcmRlcnNfZWRpdC5waHAnKSkge1xuICAgICAgICAgICAgICAgICRsaW5rXG4gICAgICAgICAgICAgICAgICAgIC5wYXJlbnQoKVxuICAgICAgICAgICAgICAgICAgICAuYXBwZW5kKFxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnPHNwYW4vPicsIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAndGV4dCc6ICRsaW5rLnRleHQoKS50cmltKClcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIClcbiAgICAgICAgICAgICAgICAgICAgLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAnb3BhY2l0eSc6IC42LFxuICAgICAgICAgICAgICAgICAgICAgICAgJ2NvbG9yJzogJ2dyYXknLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ2JhY2tncm91bmQnOiAnbm9uZScsXG4gICAgICAgICAgICAgICAgICAgICAgICAnY3Vyc29yJzogJ25vdC1hbGxvd2VkJ1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgICAgICAkbGluay5yZW1vdmUoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfTtcbiAgICBcbiAgICBFYXN5Q3JlZGl0SHViLm9uKCdyZWFkeScsICgpID0+IGluaXQoKSk7XG59KSgpOyBcbiJdfQ==

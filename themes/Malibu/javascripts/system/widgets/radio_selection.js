'use strict';

/* --------------------------------------------------------------
 radio_selection.js 2018-02-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('radio_selection', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {
        selection: '.list-group-item',
        className: 'active',
        init: false
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########


    var _changeHandler = function _changeHandler() {
        var $self = $(this),
            $row = $self.closest(options.selection);

        $this.find(options.selection).removeClass(options.className);

        $self.closest(options.selection).addClass(options.className);

        if ($self.parent().hasClass('shipping-submodule-selection')) {
            $('.shipping-submodule-title .shipping-module-selection input:radio').not($self).prop('checked', false);
            $row.find('.shipping-submodule-title .shipping-module-selection input:radio').prop('checked', true);
        } else if ($self.hasClass('placeholder-radio')) {
            $row.find('.shipping-submodule-selection input:radio').first().prop('checked', true);
            $('.shipping-submodule-title .shipping-module-selection input:radio').not($self).prop('checked', false);
        } else {
            $('.shipping-submodule-title .shipping-module-selection input:radio').prop('checked', false);
        }
    };

    var _changeHandlerCheckbox = function _changeHandlerCheckbox() {
        var $self = $(this),
            $row = $self.closest(options.selection),
            checked = $self.prop('checked');

        if (checked) {
            $row.addClass(options.className);
        } else {
            $row.removeClass(options.className);
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        $this.on('change', 'input:radio:not(:disabled)', _changeHandler).on('change', 'input:checkbox', _changeHandlerCheckbox);

        if (options.init) {
            $this.find('input:checkbox, input:radio:checked:not(:disabled)').trigger('change', []);
        }

        $this.find('.list-group-item').on('click', function () {
            $(this).find('label input:radio:not(:disabled):not(.placeholder-radio)').first().prop('checked', true).trigger('change');
        });

        $this.find('.list-group-item').each(function () {
            if ($(this).find('label input:radio:not(:disabled)').length > 0) {
                $(this).css({ cursor: 'pointer' });
            }
        });

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcmFkaW9fc2VsZWN0aW9uLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJzZWxlY3Rpb24iLCJjbGFzc05hbWUiLCJpbml0Iiwib3B0aW9ucyIsImV4dGVuZCIsIl9jaGFuZ2VIYW5kbGVyIiwiJHNlbGYiLCIkcm93IiwiY2xvc2VzdCIsImZpbmQiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwicGFyZW50IiwiaGFzQ2xhc3MiLCJub3QiLCJwcm9wIiwiZmlyc3QiLCJfY2hhbmdlSGFuZGxlckNoZWNrYm94IiwiY2hlY2tlZCIsImRvbmUiLCJvbiIsInRyaWdnZXIiLCJlYWNoIiwibGVuZ3RoIiwiY3NzIiwiY3Vyc29yIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUFzQixpQkFBdEIsRUFBeUMsRUFBekMsRUFBNkMsVUFBVUMsSUFBVixFQUFnQjs7QUFFekQ7O0FBRUo7O0FBRUksUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxXQUFXO0FBQ1BDLG1CQUFXLGtCQURKO0FBRVBDLG1CQUFXLFFBRko7QUFHUEMsY0FBTTtBQUhDLEtBRGY7QUFBQSxRQU1JQyxVQUFVTCxFQUFFTSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJMLFFBQW5CLEVBQTZCSCxJQUE3QixDQU5kO0FBQUEsUUFPSUQsU0FBUyxFQVBiOztBQVVKOzs7QUFHSSxRQUFJVSxpQkFBaUIsU0FBakJBLGNBQWlCLEdBQVk7QUFDN0IsWUFBSUMsUUFBUVIsRUFBRSxJQUFGLENBQVo7QUFBQSxZQUNJUyxPQUFPRCxNQUFNRSxPQUFOLENBQWNMLFFBQVFILFNBQXRCLENBRFg7O0FBR0FILGNBQ0tZLElBREwsQ0FDVU4sUUFBUUgsU0FEbEIsRUFFS1UsV0FGTCxDQUVpQlAsUUFBUUYsU0FGekI7O0FBSUFLLGNBQ0tFLE9BREwsQ0FDYUwsUUFBUUgsU0FEckIsRUFFS1csUUFGTCxDQUVjUixRQUFRRixTQUZ0Qjs7QUFJQSxZQUFJSyxNQUFNTSxNQUFOLEdBQWVDLFFBQWYsQ0FBd0IsOEJBQXhCLENBQUosRUFBNkQ7QUFDekRmLGNBQUUsa0VBQUYsRUFBc0VnQixHQUF0RSxDQUEwRVIsS0FBMUUsRUFBaUZTLElBQWpGLENBQXNGLFNBQXRGLEVBQWlHLEtBQWpHO0FBQ0FSLGlCQUFLRSxJQUFMLENBQVUsa0VBQVYsRUFBOEVNLElBQTlFLENBQW1GLFNBQW5GLEVBQThGLElBQTlGO0FBQ0gsU0FIRCxNQUdPLElBQUlULE1BQU1PLFFBQU4sQ0FBZSxtQkFBZixDQUFKLEVBQXlDO0FBQzVDTixpQkFBS0UsSUFBTCxDQUFVLDJDQUFWLEVBQXVETyxLQUF2RCxHQUErREQsSUFBL0QsQ0FBb0UsU0FBcEUsRUFBK0UsSUFBL0U7QUFDQWpCLGNBQUUsa0VBQUYsRUFBc0VnQixHQUF0RSxDQUEwRVIsS0FBMUUsRUFBaUZTLElBQWpGLENBQXNGLFNBQXRGLEVBQWlHLEtBQWpHO0FBQ0gsU0FITSxNQUdBO0FBQ0hqQixjQUFFLGtFQUFGLEVBQXNFaUIsSUFBdEUsQ0FBMkUsU0FBM0UsRUFBc0YsS0FBdEY7QUFDSDtBQUNKLEtBckJEOztBQXVCQSxRQUFJRSx5QkFBeUIsU0FBekJBLHNCQUF5QixHQUFZO0FBQ3JDLFlBQUlYLFFBQVFSLEVBQUUsSUFBRixDQUFaO0FBQUEsWUFDSVMsT0FBT0QsTUFBTUUsT0FBTixDQUFjTCxRQUFRSCxTQUF0QixDQURYO0FBQUEsWUFFSWtCLFVBQVVaLE1BQU1TLElBQU4sQ0FBVyxTQUFYLENBRmQ7O0FBS0EsWUFBSUcsT0FBSixFQUFhO0FBQ1RYLGlCQUFLSSxRQUFMLENBQWNSLFFBQVFGLFNBQXRCO0FBQ0gsU0FGRCxNQUVPO0FBQ0hNLGlCQUFLRyxXQUFMLENBQWlCUCxRQUFRRixTQUF6QjtBQUNIO0FBQ0osS0FYRDs7QUFjSjs7QUFFSTs7OztBQUlBTixXQUFPTyxJQUFQLEdBQWMsVUFBVWlCLElBQVYsRUFBZ0I7QUFDMUJ0QixjQUNLdUIsRUFETCxDQUNRLFFBRFIsRUFDa0IsNEJBRGxCLEVBQ2dEZixjQURoRCxFQUVLZSxFQUZMLENBRVEsUUFGUixFQUVrQixnQkFGbEIsRUFFb0NILHNCQUZwQzs7QUFJQSxZQUFJZCxRQUFRRCxJQUFaLEVBQWtCO0FBQ2RMLGtCQUNLWSxJQURMLENBQ1Usb0RBRFYsRUFFS1ksT0FGTCxDQUVhLFFBRmIsRUFFdUIsRUFGdkI7QUFHSDs7QUFFRHhCLGNBQU1ZLElBQU4sQ0FBVyxrQkFBWCxFQUErQlcsRUFBL0IsQ0FBa0MsT0FBbEMsRUFBMkMsWUFBWTtBQUNuRHRCLGNBQUUsSUFBRixFQUFRVyxJQUFSLENBQWEsMERBQWIsRUFBeUVPLEtBQXpFLEdBQWlGRCxJQUFqRixDQUFzRixTQUF0RixFQUFpRyxJQUFqRyxFQUF1R00sT0FBdkcsQ0FBK0csUUFBL0c7QUFDSCxTQUZEOztBQUlBeEIsY0FBTVksSUFBTixDQUFXLGtCQUFYLEVBQStCYSxJQUEvQixDQUFvQyxZQUFZO0FBQzVDLGdCQUFJeEIsRUFBRSxJQUFGLEVBQVFXLElBQVIsQ0FBYSxrQ0FBYixFQUFpRGMsTUFBakQsR0FBMEQsQ0FBOUQsRUFBaUU7QUFDN0R6QixrQkFBRSxJQUFGLEVBQVEwQixHQUFSLENBQVksRUFBQ0MsUUFBUSxTQUFULEVBQVo7QUFDSDtBQUNKLFNBSkQ7O0FBTUFOO0FBQ0gsS0F0QkQ7O0FBd0JBO0FBQ0EsV0FBT3hCLE1BQVA7QUFDSCxDQXhGRCIsImZpbGUiOiJ3aWRnZXRzL3JhZGlvX3NlbGVjdGlvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcmFkaW9fc2VsZWN0aW9uLmpzIDIwMTgtMDItMDVcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE4IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoJ3JhZGlvX3NlbGVjdGlvbicsIFtdLCBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgc2VsZWN0aW9uOiAnLmxpc3QtZ3JvdXAtaXRlbScsXG4gICAgICAgICAgICBjbGFzc05hbWU6ICdhY3RpdmUnLFxuICAgICAgICAgICAgaW5pdDogZmFsc2VcbiAgICAgICAgfSxcbiAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgIG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblxuICAgIHZhciBfY2hhbmdlSGFuZGxlciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICRyb3cgPSAkc2VsZi5jbG9zZXN0KG9wdGlvbnMuc2VsZWN0aW9uKTtcblxuICAgICAgICAkdGhpc1xuICAgICAgICAgICAgLmZpbmQob3B0aW9ucy5zZWxlY3Rpb24pXG4gICAgICAgICAgICAucmVtb3ZlQ2xhc3Mob3B0aW9ucy5jbGFzc05hbWUpO1xuXG4gICAgICAgICRzZWxmXG4gICAgICAgICAgICAuY2xvc2VzdChvcHRpb25zLnNlbGVjdGlvbilcbiAgICAgICAgICAgIC5hZGRDbGFzcyhvcHRpb25zLmNsYXNzTmFtZSk7XG5cbiAgICAgICAgaWYgKCRzZWxmLnBhcmVudCgpLmhhc0NsYXNzKCdzaGlwcGluZy1zdWJtb2R1bGUtc2VsZWN0aW9uJykpIHtcbiAgICAgICAgICAgICQoJy5zaGlwcGluZy1zdWJtb2R1bGUtdGl0bGUgLnNoaXBwaW5nLW1vZHVsZS1zZWxlY3Rpb24gaW5wdXQ6cmFkaW8nKS5ub3QoJHNlbGYpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG4gICAgICAgICAgICAkcm93LmZpbmQoJy5zaGlwcGluZy1zdWJtb2R1bGUtdGl0bGUgLnNoaXBwaW5nLW1vZHVsZS1zZWxlY3Rpb24gaW5wdXQ6cmFkaW8nKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG4gICAgICAgIH0gZWxzZSBpZiAoJHNlbGYuaGFzQ2xhc3MoJ3BsYWNlaG9sZGVyLXJhZGlvJykpIHtcbiAgICAgICAgICAgICRyb3cuZmluZCgnLnNoaXBwaW5nLXN1Ym1vZHVsZS1zZWxlY3Rpb24gaW5wdXQ6cmFkaW8nKS5maXJzdCgpLnByb3AoJ2NoZWNrZWQnLCB0cnVlKTtcbiAgICAgICAgICAgICQoJy5zaGlwcGluZy1zdWJtb2R1bGUtdGl0bGUgLnNoaXBwaW5nLW1vZHVsZS1zZWxlY3Rpb24gaW5wdXQ6cmFkaW8nKS5ub3QoJHNlbGYpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAkKCcuc2hpcHBpbmctc3VibW9kdWxlLXRpdGxlIC5zaGlwcGluZy1tb2R1bGUtc2VsZWN0aW9uIGlucHV0OnJhZGlvJykucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcbiAgICAgICAgfVxuICAgIH07XG5cbiAgICB2YXIgX2NoYW5nZUhhbmRsZXJDaGVja2JveCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICRyb3cgPSAkc2VsZi5jbG9zZXN0KG9wdGlvbnMuc2VsZWN0aW9uKSxcbiAgICAgICAgICAgIGNoZWNrZWQgPSAkc2VsZi5wcm9wKCdjaGVja2VkJyk7XG5cblxuICAgICAgICBpZiAoY2hlY2tlZCkge1xuICAgICAgICAgICAgJHJvdy5hZGRDbGFzcyhvcHRpb25zLmNsYXNzTmFtZSk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAkcm93LnJlbW92ZUNsYXNzKG9wdGlvbnMuY2xhc3NOYW1lKTtcbiAgICAgICAgfVxuICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAvKipcbiAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgKi9cbiAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG4gICAgICAgICR0aGlzXG4gICAgICAgICAgICAub24oJ2NoYW5nZScsICdpbnB1dDpyYWRpbzpub3QoOmRpc2FibGVkKScsIF9jaGFuZ2VIYW5kbGVyKVxuICAgICAgICAgICAgLm9uKCdjaGFuZ2UnLCAnaW5wdXQ6Y2hlY2tib3gnLCBfY2hhbmdlSGFuZGxlckNoZWNrYm94KTtcblxuICAgICAgICBpZiAob3B0aW9ucy5pbml0KSB7XG4gICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgIC5maW5kKCdpbnB1dDpjaGVja2JveCwgaW5wdXQ6cmFkaW86Y2hlY2tlZDpub3QoOmRpc2FibGVkKScpXG4gICAgICAgICAgICAgICAgLnRyaWdnZXIoJ2NoYW5nZScsIFtdKTtcbiAgICAgICAgfVxuXG4gICAgICAgICR0aGlzLmZpbmQoJy5saXN0LWdyb3VwLWl0ZW0nKS5vbignY2xpY2snLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAkKHRoaXMpLmZpbmQoJ2xhYmVsIGlucHV0OnJhZGlvOm5vdCg6ZGlzYWJsZWQpOm5vdCgucGxhY2Vob2xkZXItcmFkaW8pJykuZmlyc3QoKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSkudHJpZ2dlcignY2hhbmdlJyk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICR0aGlzLmZpbmQoJy5saXN0LWdyb3VwLWl0ZW0nKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGlmICgkKHRoaXMpLmZpbmQoJ2xhYmVsIGlucHV0OnJhZGlvOm5vdCg6ZGlzYWJsZWQpJykubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgICQodGhpcykuY3NzKHtjdXJzb3I6ICdwb2ludGVyJ30pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICByZXR1cm4gbW9kdWxlO1xufSk7Il19

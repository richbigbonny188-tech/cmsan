'use strict';

/* --------------------------------------------------------------
 responsive.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.theme.responsive = jse.libs.theme.responsive || {};

/**
 * ## Honeygrid Responsive Utilities Library
 *
 * Library to make the theme responsive. This function depends on jQuery.
 *
 * @module Honeygrid/Libs/responsive
 * @exports jse.libs.theme.responsive
 */
(function (exports) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $body = $('body'),
        current = null,
        timer = null,
        breakpoints = [{
        id: 20,
        name: 'too small',
        width: 480
    }, {
        id: 40,
        name: 'xs',
        width: 768
    }, {
        id: 60,
        name: 'sm',
        width: 992
    }, {
        id: 80,
        name: 'md',
        width: 1200
    }, {
        id: 100,
        name: 'lg',
        width: null
    }];

    // ########## EVENT HANDLER ##########

    /**
     * Returns the breakpoint of the current page,
     * false if no breakpoint could be identified.
     *
     * @return Breakpoint
     */
    var _getBreakpoint = function _getBreakpoint() {
        var width = window.innerWidth,
            result = null;

        // check if page is loaded inside an iframe and, if appropriate, set the iframe's width
        if (window.self !== window.top) {
            document.body.style.overflow = 'hidden';
            width = document.body.clientWidth;
            document.body.style.overflow = 'visible';
        }

        if (width === 0) {
            timer = setTimeout(function () {
                _getBreakpoint();
            }, 10);
            current = $.extend({}, breakpoints[0]); // set default breakpoint value
            return false;
        }

        $.each(breakpoints, function (i, v) {
            if (!v.width || width < v.width) {

                result = $.extend({}, v);
                return false;
            }
        });

        if (result && (!current || current.id !== result.id)) {
            current = $.extend({}, result);
            clearTimeout(timer);
            timer = setTimeout(function () {
                // @todo This lib depends on the existence of the events lib (both are loaded asynchronously).
                if (jse.libs.theme.events !== undefined) {
                    $body.trigger(jse.libs.theme.events.BREAKPOINT(), current);
                }
            }, 10);
        }
    };

    // ########## INITIALIZATION ##########

    _getBreakpoint();

    $(window).on('resize', _getBreakpoint);

    /**
     * @todo rename method to "getBreakpoint".
     */
    exports.breakpoint = function () {
        return current;
    };
})(jse.libs.theme.responsive);

jse.libs.template = jse.libs.template || {};
jse.libs.template.responsive = jse.libs.theme.responsive;
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxpYnMvcmVzcG9uc2l2ZS5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwidGhlbWUiLCJyZXNwb25zaXZlIiwiZXhwb3J0cyIsIiRib2R5IiwiJCIsImN1cnJlbnQiLCJ0aW1lciIsImJyZWFrcG9pbnRzIiwiaWQiLCJuYW1lIiwid2lkdGgiLCJfZ2V0QnJlYWtwb2ludCIsIndpbmRvdyIsImlubmVyV2lkdGgiLCJyZXN1bHQiLCJzZWxmIiwidG9wIiwiZG9jdW1lbnQiLCJib2R5Iiwic3R5bGUiLCJvdmVyZmxvdyIsImNsaWVudFdpZHRoIiwic2V0VGltZW91dCIsImV4dGVuZCIsImVhY2giLCJpIiwidiIsImNsZWFyVGltZW91dCIsImV2ZW50cyIsInVuZGVmaW5lZCIsInRyaWdnZXIiLCJCUkVBS1BPSU5UIiwib24iLCJicmVha3BvaW50IiwidGVtcGxhdGUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLFVBQWYsR0FBNEJILElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFlQyxVQUFmLElBQTZCLEVBQXpEOztBQUVBOzs7Ozs7OztBQVFDLFdBQVVDLE9BQVYsRUFBbUI7O0FBRWhCOztBQUVBOztBQUVBLFFBQUlDLFFBQVFDLEVBQUUsTUFBRixDQUFaO0FBQUEsUUFDSUMsVUFBVSxJQURkO0FBQUEsUUFFSUMsUUFBUSxJQUZaO0FBQUEsUUFHSUMsY0FBYyxDQUNWO0FBQ0lDLFlBQUksRUFEUjtBQUVJQyxjQUFNLFdBRlY7QUFHSUMsZUFBTztBQUhYLEtBRFUsRUFNVjtBQUNJRixZQUFJLEVBRFI7QUFFSUMsY0FBTSxJQUZWO0FBR0lDLGVBQU87QUFIWCxLQU5VLEVBV1Y7QUFDSUYsWUFBSSxFQURSO0FBRUlDLGNBQU0sSUFGVjtBQUdJQyxlQUFPO0FBSFgsS0FYVSxFQWdCVjtBQUNJRixZQUFJLEVBRFI7QUFFSUMsY0FBTSxJQUZWO0FBR0lDLGVBQU87QUFIWCxLQWhCVSxFQXFCVjtBQUNJRixZQUFJLEdBRFI7QUFFSUMsY0FBTSxJQUZWO0FBR0lDLGVBQU87QUFIWCxLQXJCVSxDQUhsQjs7QUFnQ0E7O0FBRUE7Ozs7OztBQU1BLFFBQUlDLGlCQUFpQixTQUFqQkEsY0FBaUIsR0FBWTtBQUM3QixZQUFJRCxRQUFRRSxPQUFPQyxVQUFuQjtBQUFBLFlBQ0lDLFNBQVMsSUFEYjs7QUFHQTtBQUNBLFlBQUlGLE9BQU9HLElBQVAsS0FBZ0JILE9BQU9JLEdBQTNCLEVBQWdDO0FBQzVCQyxxQkFBU0MsSUFBVCxDQUFjQyxLQUFkLENBQW9CQyxRQUFwQixHQUErQixRQUEvQjtBQUNBVixvQkFBUU8sU0FBU0MsSUFBVCxDQUFjRyxXQUF0QjtBQUNBSixxQkFBU0MsSUFBVCxDQUFjQyxLQUFkLENBQW9CQyxRQUFwQixHQUErQixTQUEvQjtBQUNIOztBQUVELFlBQUlWLFVBQVUsQ0FBZCxFQUFpQjtBQUNiSixvQkFBUWdCLFdBQVcsWUFBWTtBQUMzQlg7QUFDSCxhQUZPLEVBRUwsRUFGSyxDQUFSO0FBR0FOLHNCQUFVRCxFQUFFbUIsTUFBRixDQUFTLEVBQVQsRUFBYWhCLFlBQVksQ0FBWixDQUFiLENBQVYsQ0FKYSxDQUkyQjtBQUN4QyxtQkFBTyxLQUFQO0FBQ0g7O0FBRURILFVBQUVvQixJQUFGLENBQU9qQixXQUFQLEVBQW9CLFVBQVVrQixDQUFWLEVBQWFDLENBQWIsRUFBZ0I7QUFDaEMsZ0JBQUksQ0FBQ0EsRUFBRWhCLEtBQUgsSUFBWUEsUUFBUWdCLEVBQUVoQixLQUExQixFQUFpQzs7QUFFN0JJLHlCQUFTVixFQUFFbUIsTUFBRixDQUFTLEVBQVQsRUFBYUcsQ0FBYixDQUFUO0FBQ0EsdUJBQU8sS0FBUDtBQUNIO0FBQ0osU0FORDs7QUFTQSxZQUFJWixXQUFXLENBQUNULE9BQUQsSUFBWUEsUUFBUUcsRUFBUixLQUFlTSxPQUFPTixFQUE3QyxDQUFKLEVBQXNEO0FBQ2xESCxzQkFBVUQsRUFBRW1CLE1BQUYsQ0FBUyxFQUFULEVBQWFULE1BQWIsQ0FBVjtBQUNBYSx5QkFBYXJCLEtBQWI7QUFDQUEsb0JBQVFnQixXQUFXLFlBQVk7QUFDM0I7QUFDQSxvQkFBSXhCLElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFlNEIsTUFBZixLQUEwQkMsU0FBOUIsRUFBeUM7QUFDckMxQiwwQkFBTTJCLE9BQU4sQ0FBY2hDLElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFlNEIsTUFBZixDQUFzQkcsVUFBdEIsRUFBZCxFQUFrRDFCLE9BQWxEO0FBQ0g7QUFDSixhQUxPLEVBS0wsRUFMSyxDQUFSO0FBTUg7QUFDSixLQXRDRDs7QUF5Q0E7O0FBRUFNOztBQUVBUCxNQUFFUSxNQUFGLEVBQVVvQixFQUFWLENBQWEsUUFBYixFQUF1QnJCLGNBQXZCOztBQUVBOzs7QUFHQVQsWUFBUStCLFVBQVIsR0FBcUIsWUFBWTtBQUM3QixlQUFPNUIsT0FBUDtBQUNILEtBRkQ7QUFJSCxDQXBHQSxFQW9HQ1AsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLFVBcEdoQixDQUFEOztBQXNHQUgsSUFBSUMsSUFBSixDQUFTbUMsUUFBVCxHQUFvQnBDLElBQUlDLElBQUosQ0FBU21DLFFBQVQsSUFBcUIsRUFBekM7QUFDQXBDLElBQUlDLElBQUosQ0FBU21DLFFBQVQsQ0FBa0JqQyxVQUFsQixHQUErQkgsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLFVBQTlDIiwiZmlsZSI6ImxpYnMvcmVzcG9uc2l2ZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcmVzcG9uc2l2ZS5qcyAyMDE2LTAyLTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMudGhlbWUucmVzcG9uc2l2ZSA9IGpzZS5saWJzLnRoZW1lLnJlc3BvbnNpdmUgfHwge307XG5cbi8qKlxuICogIyMgSG9uZXlncmlkIFJlc3BvbnNpdmUgVXRpbGl0aWVzIExpYnJhcnlcbiAqXG4gKiBMaWJyYXJ5IHRvIG1ha2UgdGhlIHRoZW1lIHJlc3BvbnNpdmUuIFRoaXMgZnVuY3Rpb24gZGVwZW5kcyBvbiBqUXVlcnkuXG4gKlxuICogQG1vZHVsZSBIb25leWdyaWQvTGlicy9yZXNwb25zaXZlXG4gKiBAZXhwb3J0cyBqc2UubGlicy50aGVtZS5yZXNwb25zaXZlXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICB2YXIgJGJvZHkgPSAkKCdib2R5JyksXG4gICAgICAgIGN1cnJlbnQgPSBudWxsLFxuICAgICAgICB0aW1lciA9IG51bGwsXG4gICAgICAgIGJyZWFrcG9pbnRzID0gW1xuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIGlkOiAyMCxcbiAgICAgICAgICAgICAgICBuYW1lOiAndG9vIHNtYWxsJyxcbiAgICAgICAgICAgICAgICB3aWR0aDogNDgwXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgIGlkOiA0MCxcbiAgICAgICAgICAgICAgICBuYW1lOiAneHMnLFxuICAgICAgICAgICAgICAgIHdpZHRoOiA3NjhcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgaWQ6IDYwLFxuICAgICAgICAgICAgICAgIG5hbWU6ICdzbScsXG4gICAgICAgICAgICAgICAgd2lkdGg6IDk5MlxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBpZDogODAsXG4gICAgICAgICAgICAgICAgbmFtZTogJ21kJyxcbiAgICAgICAgICAgICAgICB3aWR0aDogMTIwMFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICBpZDogMTAwLFxuICAgICAgICAgICAgICAgIG5hbWU6ICdsZycsXG4gICAgICAgICAgICAgICAgd2lkdGg6IG51bGxcbiAgICAgICAgICAgIH1cbiAgICAgICAgXTtcblxuXG4gICAgLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgdGhlIGJyZWFrcG9pbnQgb2YgdGhlIGN1cnJlbnQgcGFnZSxcbiAgICAgKiBmYWxzZSBpZiBubyBicmVha3BvaW50IGNvdWxkIGJlIGlkZW50aWZpZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIEJyZWFrcG9pbnRcbiAgICAgKi9cbiAgICB2YXIgX2dldEJyZWFrcG9pbnQgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHZhciB3aWR0aCA9IHdpbmRvdy5pbm5lcldpZHRoLFxuICAgICAgICAgICAgcmVzdWx0ID0gbnVsbDtcblxuICAgICAgICAvLyBjaGVjayBpZiBwYWdlIGlzIGxvYWRlZCBpbnNpZGUgYW4gaWZyYW1lIGFuZCwgaWYgYXBwcm9wcmlhdGUsIHNldCB0aGUgaWZyYW1lJ3Mgd2lkdGhcbiAgICAgICAgaWYgKHdpbmRvdy5zZWxmICE9PSB3aW5kb3cudG9wKSB7XG4gICAgICAgICAgICBkb2N1bWVudC5ib2R5LnN0eWxlLm92ZXJmbG93ID0gJ2hpZGRlbic7XG4gICAgICAgICAgICB3aWR0aCA9IGRvY3VtZW50LmJvZHkuY2xpZW50V2lkdGg7XG4gICAgICAgICAgICBkb2N1bWVudC5ib2R5LnN0eWxlLm92ZXJmbG93ID0gJ3Zpc2libGUnO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKHdpZHRoID09PSAwKSB7XG4gICAgICAgICAgICB0aW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIF9nZXRCcmVha3BvaW50KCk7XG4gICAgICAgICAgICB9LCAxMCk7XG4gICAgICAgICAgICBjdXJyZW50ID0gJC5leHRlbmQoe30sIGJyZWFrcG9pbnRzWzBdKTsgLy8gc2V0IGRlZmF1bHQgYnJlYWtwb2ludCB2YWx1ZVxuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgJC5lYWNoKGJyZWFrcG9pbnRzLCBmdW5jdGlvbiAoaSwgdikge1xuICAgICAgICAgICAgaWYgKCF2LndpZHRoIHx8IHdpZHRoIDwgdi53aWR0aCkge1xuXG4gICAgICAgICAgICAgICAgcmVzdWx0ID0gJC5leHRlbmQoe30sIHYpO1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cblxuICAgICAgICBpZiAocmVzdWx0ICYmICghY3VycmVudCB8fCBjdXJyZW50LmlkICE9PSByZXN1bHQuaWQpKSB7XG4gICAgICAgICAgICBjdXJyZW50ID0gJC5leHRlbmQoe30sIHJlc3VsdCk7XG4gICAgICAgICAgICBjbGVhclRpbWVvdXQodGltZXIpO1xuICAgICAgICAgICAgdGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAvLyBAdG9kbyBUaGlzIGxpYiBkZXBlbmRzIG9uIHRoZSBleGlzdGVuY2Ugb2YgdGhlIGV2ZW50cyBsaWIgKGJvdGggYXJlIGxvYWRlZCBhc3luY2hyb25vdXNseSkuXG4gICAgICAgICAgICAgICAgaWYgKGpzZS5saWJzLnRoZW1lLmV2ZW50cyAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICAgICAgICAgICRib2R5LnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLkJSRUFLUE9JTlQoKSwgY3VycmVudCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSwgMTApO1xuICAgICAgICB9XG4gICAgfTtcblxuXG4gICAgLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICBfZ2V0QnJlYWtwb2ludCgpO1xuXG4gICAgJCh3aW5kb3cpLm9uKCdyZXNpemUnLCBfZ2V0QnJlYWtwb2ludCk7XG5cbiAgICAvKipcbiAgICAgKiBAdG9kbyByZW5hbWUgbWV0aG9kIHRvIFwiZ2V0QnJlYWtwb2ludFwiLlxuICAgICAqL1xuICAgIGV4cG9ydHMuYnJlYWtwb2ludCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgcmV0dXJuIGN1cnJlbnQ7XG4gICAgfTtcblxufShqc2UubGlicy50aGVtZS5yZXNwb25zaXZlKSk7XG5cbmpzZS5saWJzLnRlbXBsYXRlID0ganNlLmxpYnMudGVtcGxhdGUgfHwge307XG5qc2UubGlicy50ZW1wbGF0ZS5yZXNwb25zaXZlID0ganNlLmxpYnMudGhlbWUucmVzcG9uc2l2ZTtcbiJdfQ==

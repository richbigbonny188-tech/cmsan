'use strict';

/* --------------------------------------------------------------
 share_cart.js 2016-04-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('share_cart', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {
        model: {
            lang: jse.core.config.get('appUrl') + '/shop.php?do=JsTranslations&section=shared_shopping_cart'
        }
    };

    var _copyHandler = function _copyHandler() {
        var sharedCartUrl = document.querySelector('.shared_cart_url'),
            copySupported = document.queryCommandSupported('copy'),
            $cartResponseWrapper = $('.share-cart-response-wrapper'),
            error = false,
            commandSuccessful,
            txt;

        sharedCartUrl.select();
        try {
            commandSuccessful = document.execCommand('copy');
        } catch (err) {
            jse.core.debug.log('Error occurred when copying!');
            error = true;
        }

        txt = !commandSuccessful || !copySupported || error ? module.model.lang.text_warning : module.model.lang.text_notice;

        $cartResponseWrapper.find('p').first().text(txt);
        $cartResponseWrapper.show();
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        $this.on('click', _copyHandler);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2hhcmVfY2FydC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIm1vZGVsIiwibGFuZyIsImpzZSIsImNvcmUiLCJjb25maWciLCJnZXQiLCJfY29weUhhbmRsZXIiLCJzaGFyZWRDYXJ0VXJsIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwiY29weVN1cHBvcnRlZCIsInF1ZXJ5Q29tbWFuZFN1cHBvcnRlZCIsIiRjYXJ0UmVzcG9uc2VXcmFwcGVyIiwiZXJyb3IiLCJjb21tYW5kU3VjY2Vzc2Z1bCIsInR4dCIsInNlbGVjdCIsImV4ZWNDb21tYW5kIiwiZXJyIiwiZGVidWciLCJsb2ciLCJ0ZXh0X3dhcm5pbmciLCJ0ZXh0X25vdGljZSIsImZpbmQiLCJmaXJzdCIsInRleHQiLCJzaG93IiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSxZQURKLEVBR0ksRUFISixFQUtJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRUE7O0FBRUEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxXQUFXLEVBRGY7QUFBQSxRQUVJQyxVQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQUZkO0FBQUEsUUFHSUQsU0FBUztBQUNMTyxlQUFPO0FBQ0hDLGtCQUFNQyxJQUFJQyxJQUFKLENBQVNDLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLFFBQXBCLElBQWdDO0FBRG5DO0FBREYsS0FIYjs7QUFTQSxRQUFJQyxlQUFlLFNBQWZBLFlBQWUsR0FBWTtBQUMzQixZQUFJQyxnQkFBZ0JDLFNBQVNDLGFBQVQsQ0FBdUIsa0JBQXZCLENBQXBCO0FBQUEsWUFDSUMsZ0JBQWdCRixTQUFTRyxxQkFBVCxDQUErQixNQUEvQixDQURwQjtBQUFBLFlBRUlDLHVCQUF1QmhCLEVBQUUsOEJBQUYsQ0FGM0I7QUFBQSxZQUdJaUIsUUFBUSxLQUhaO0FBQUEsWUFJSUMsaUJBSko7QUFBQSxZQUl1QkMsR0FKdkI7O0FBTUFSLHNCQUFjUyxNQUFkO0FBQ0EsWUFBSTtBQUNBRixnQ0FBb0JOLFNBQVNTLFdBQVQsQ0FBcUIsTUFBckIsQ0FBcEI7QUFDSCxTQUZELENBRUUsT0FBT0MsR0FBUCxFQUFZO0FBQ1ZoQixnQkFBSUMsSUFBSixDQUFTZ0IsS0FBVCxDQUFlQyxHQUFmLENBQW1CLDhCQUFuQjtBQUNBUCxvQkFBUSxJQUFSO0FBQ0g7O0FBRURFLGNBQU8sQ0FBQ0QsaUJBQUQsSUFBc0IsQ0FBQ0osYUFBdkIsSUFDQUcsS0FERCxHQUNVcEIsT0FBT08sS0FBUCxDQUFhQyxJQUFiLENBQWtCb0IsWUFENUIsR0FDMkM1QixPQUFPTyxLQUFQLENBQWFDLElBQWIsQ0FBa0JxQixXQURuRTs7QUFHQVYsNkJBQXFCVyxJQUFyQixDQUEwQixHQUExQixFQUErQkMsS0FBL0IsR0FBdUNDLElBQXZDLENBQTRDVixHQUE1QztBQUNBSCw2QkFBcUJjLElBQXJCO0FBQ0gsS0FwQkQ7O0FBdUJBOztBQUVBOzs7O0FBSUFqQyxXQUFPa0MsSUFBUCxHQUFjLFVBQVVDLElBQVYsRUFBZ0I7QUFDMUJqQyxjQUFNa0MsRUFBTixDQUFTLE9BQVQsRUFBa0J2QixZQUFsQjs7QUFFQXNCO0FBQ0gsS0FKRDs7QUFNQTtBQUNBLFdBQU9uQyxNQUFQO0FBQ0gsQ0F6REwiLCJmaWxlIjoid2lkZ2V0cy9zaGFyZV9jYXJ0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzaGFyZV9jYXJ0LmpzIDIwMTYtMDQtMDdcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ3NoYXJlX2NhcnQnLFxuXG4gICAgW10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7fSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge1xuICAgICAgICAgICAgICAgIG1vZGVsOiB7XG4gICAgICAgICAgICAgICAgICAgIGxhbmc6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9zaG9wLnBocD9kbz1Kc1RyYW5zbGF0aW9ucyZzZWN0aW9uPXNoYXJlZF9zaG9wcGluZ19jYXJ0J1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG5cbiAgICAgICAgdmFyIF9jb3B5SGFuZGxlciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciBzaGFyZWRDYXJ0VXJsID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignLnNoYXJlZF9jYXJ0X3VybCcpLFxuICAgICAgICAgICAgICAgIGNvcHlTdXBwb3J0ZWQgPSBkb2N1bWVudC5xdWVyeUNvbW1hbmRTdXBwb3J0ZWQoJ2NvcHknKSxcbiAgICAgICAgICAgICAgICAkY2FydFJlc3BvbnNlV3JhcHBlciA9ICQoJy5zaGFyZS1jYXJ0LXJlc3BvbnNlLXdyYXBwZXInKSxcbiAgICAgICAgICAgICAgICBlcnJvciA9IGZhbHNlLFxuICAgICAgICAgICAgICAgIGNvbW1hbmRTdWNjZXNzZnVsLCB0eHQ7XG5cbiAgICAgICAgICAgIHNoYXJlZENhcnRVcmwuc2VsZWN0KCk7XG4gICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgIGNvbW1hbmRTdWNjZXNzZnVsID0gZG9jdW1lbnQuZXhlY0NvbW1hbmQoJ2NvcHknKTtcbiAgICAgICAgICAgIH0gY2F0Y2ggKGVycikge1xuICAgICAgICAgICAgICAgIGpzZS5jb3JlLmRlYnVnLmxvZygnRXJyb3Igb2NjdXJyZWQgd2hlbiBjb3B5aW5nIScpO1xuICAgICAgICAgICAgICAgIGVycm9yID0gdHJ1ZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdHh0ID0gKCFjb21tYW5kU3VjY2Vzc2Z1bCB8fCAhY29weVN1cHBvcnRlZFxuICAgICAgICAgICAgICAgIHx8IGVycm9yKSA/IG1vZHVsZS5tb2RlbC5sYW5nLnRleHRfd2FybmluZyA6IG1vZHVsZS5tb2RlbC5sYW5nLnRleHRfbm90aWNlO1xuXG4gICAgICAgICAgICAkY2FydFJlc3BvbnNlV3JhcHBlci5maW5kKCdwJykuZmlyc3QoKS50ZXh0KHR4dCk7XG4gICAgICAgICAgICAkY2FydFJlc3BvbnNlV3JhcHBlci5zaG93KCk7XG4gICAgICAgIH07XG5cblxuICAgICAgICAvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAgICAgJHRoaXMub24oJ2NsaWNrJywgX2NvcHlIYW5kbGVyKTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTsiXX0=

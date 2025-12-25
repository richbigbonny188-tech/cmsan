'use strict';

/* --------------------------------------------------------------
 cookie_bar.js 2016-06-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Used for hiding the Cookie-Bar on click or on page change
 */
gambio.widgets.module('cookie_bar', ['xhr'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {
        closeBtn: '.close-button',
        url: 'shop.php?do=CookieBar'
    },
        options = $.extend(true, {}, defaults, data),
        module = {},
        expiry = new Date();

    // ########## EVENT HANDLER ##########


    /**
     * Shows the Cookie-Bar
     *
     * @private
     */
    var _showCookieBar = function _showCookieBar() {
        $this.css('display', 'table');
    };

    /**
     * Hides the Cookie-Bar, if the hiding cookie is set or if a link or button to close the Cookie-Bar is clicked
     *
     * @private
     */
    var _hideCookieBar = function _hideCookieBar() {
        $this.hide();
    };

    /**
     * Sets the hiding cookie
     *
     * @private
     */
    var _setCookie = function _setCookie() {
        jse.libs.xhr.get({
            url: options.url
        }, true).done(function () {
            _hideCookieBar();
        });
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        $(options.closeBtn).on('click', _setCookie);

        if (window.localStorage !== undefined) {
            if (localStorage.getItem('cookieBarSeen') === '1') {
                _setCookie();
            } else {
                localStorage.setItem('cookieBarSeen', '1');
                _showCookieBar();
            }
        } else {
            _showCookieBar();
        }

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY29va2llX2Jhci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwiY2xvc2VCdG4iLCJ1cmwiLCJvcHRpb25zIiwiZXh0ZW5kIiwiZXhwaXJ5IiwiRGF0ZSIsIl9zaG93Q29va2llQmFyIiwiY3NzIiwiX2hpZGVDb29raWVCYXIiLCJoaWRlIiwiX3NldENvb2tpZSIsImpzZSIsImxpYnMiLCJ4aHIiLCJnZXQiLCJkb25lIiwiaW5pdCIsIm9uIiwid2luZG93IiwibG9jYWxTdG9yYWdlIiwidW5kZWZpbmVkIiwiZ2V0SXRlbSIsInNldEl0ZW0iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLFlBREosRUFHSSxDQUFDLEtBQUQsQ0FISixFQUtJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRUE7O0FBRUEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxXQUFXO0FBQ1BDLGtCQUFVLGVBREg7QUFFUEMsYUFBSztBQUZFLEtBRGY7QUFBQSxRQUtJQyxVQUFVSixFQUFFSyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJKLFFBQW5CLEVBQTZCSCxJQUE3QixDQUxkO0FBQUEsUUFNSUQsU0FBUyxFQU5iO0FBQUEsUUFPSVMsU0FBUyxJQUFJQyxJQUFKLEVBUGI7O0FBVUE7OztBQUdBOzs7OztBQUtBLFFBQUlDLGlCQUFpQixTQUFqQkEsY0FBaUIsR0FBWTtBQUM3QlQsY0FBTVUsR0FBTixDQUFVLFNBQVYsRUFBcUIsT0FBckI7QUFDSCxLQUZEOztBQUtBOzs7OztBQUtBLFFBQUlDLGlCQUFpQixTQUFqQkEsY0FBaUIsR0FBWTtBQUM3QlgsY0FBTVksSUFBTjtBQUNILEtBRkQ7O0FBS0E7Ozs7O0FBS0EsUUFBSUMsYUFBYSxTQUFiQSxVQUFhLEdBQVk7QUFDekJDLFlBQUlDLElBQUosQ0FBU0MsR0FBVCxDQUFhQyxHQUFiLENBQWlCO0FBQ2JiLGlCQUFLQyxRQUFRRDtBQURBLFNBQWpCLEVBRUcsSUFGSCxFQUVTYyxJQUZULENBRWMsWUFBWTtBQUN0QlA7QUFDSCxTQUpEO0FBS0gsS0FORDs7QUFRQTs7QUFFQTs7OztBQUlBYixXQUFPcUIsSUFBUCxHQUFjLFVBQVVELElBQVYsRUFBZ0I7QUFDMUJqQixVQUFFSSxRQUFRRixRQUFWLEVBQW9CaUIsRUFBcEIsQ0FBdUIsT0FBdkIsRUFBZ0NQLFVBQWhDOztBQUVBLFlBQUlRLE9BQU9DLFlBQVAsS0FBd0JDLFNBQTVCLEVBQXVDO0FBQ25DLGdCQUFJRCxhQUFhRSxPQUFiLENBQXFCLGVBQXJCLE1BQTBDLEdBQTlDLEVBQW1EO0FBQy9DWDtBQUNILGFBRkQsTUFFTztBQUNIUyw2QkFBYUcsT0FBYixDQUFxQixlQUFyQixFQUFzQyxHQUF0QztBQUNBaEI7QUFDSDtBQUNKLFNBUEQsTUFPTztBQUNIQTtBQUNIOztBQUVEUztBQUNILEtBZkQ7O0FBaUJBO0FBQ0EsV0FBT3BCLE1BQVA7QUFDSCxDQWxGTCIsImZpbGUiOiJ3aWRnZXRzL2Nvb2tpZV9iYXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNvb2tpZV9iYXIuanMgMjAxNi0wNi0xNVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogVXNlZCBmb3IgaGlkaW5nIHRoZSBDb29raWUtQmFyIG9uIGNsaWNrIG9yIG9uIHBhZ2UgY2hhbmdlXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnY29va2llX2JhcicsXG5cbiAgICBbJ3hociddLFxuXG4gICAgZnVuY3Rpb24gKGRhdGEpIHtcblxuICAgICAgICAndXNlIHN0cmljdCc7XG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIGNsb3NlQnRuOiAnLmNsb3NlLWJ1dHRvbicsXG4gICAgICAgICAgICAgICAgdXJsOiAnc2hvcC5waHA/ZG89Q29va2llQmFyJ1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge30sXG4gICAgICAgICAgICBleHBpcnkgPSBuZXcgRGF0ZSgpO1xuXG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBTaG93cyB0aGUgQ29va2llLUJhclxuICAgICAgICAgKlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zaG93Q29va2llQmFyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgJHRoaXMuY3NzKCdkaXNwbGF5JywgJ3RhYmxlJyk7XG4gICAgICAgIH07XG5cblxuICAgICAgICAvKipcbiAgICAgICAgICogSGlkZXMgdGhlIENvb2tpZS1CYXIsIGlmIHRoZSBoaWRpbmcgY29va2llIGlzIHNldCBvciBpZiBhIGxpbmsgb3IgYnV0dG9uIHRvIGNsb3NlIHRoZSBDb29raWUtQmFyIGlzIGNsaWNrZWRcbiAgICAgICAgICpcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfaGlkZUNvb2tpZUJhciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICR0aGlzLmhpZGUoKTtcbiAgICAgICAgfTtcblxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBTZXRzIHRoZSBoaWRpbmcgY29va2llXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3NldENvb2tpZSA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGpzZS5saWJzLnhoci5nZXQoe1xuICAgICAgICAgICAgICAgIHVybDogb3B0aW9ucy51cmxcbiAgICAgICAgICAgIH0sIHRydWUpLmRvbmUoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIF9oaWRlQ29va2llQmFyKCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAgICAgJChvcHRpb25zLmNsb3NlQnRuKS5vbignY2xpY2snLCBfc2V0Q29va2llKTtcblxuICAgICAgICAgICAgaWYgKHdpbmRvdy5sb2NhbFN0b3JhZ2UgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIGlmIChsb2NhbFN0b3JhZ2UuZ2V0SXRlbSgnY29va2llQmFyU2VlbicpID09PSAnMScpIHtcbiAgICAgICAgICAgICAgICAgICAgX3NldENvb2tpZSgpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGxvY2FsU3RvcmFnZS5zZXRJdGVtKCdjb29raWVCYXJTZWVuJywgJzEnKTtcbiAgICAgICAgICAgICAgICAgICAgX3Nob3dDb29raWVCYXIoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIF9zaG93Q29va2llQmFyKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7Il19

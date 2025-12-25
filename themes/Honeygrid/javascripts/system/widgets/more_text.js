'use strict';

/* --------------------------------------------------------------
 more_text.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Enables the 'more' or '...' buttons in long text fields.
 */
gambio.widgets.module('more_text', [gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        transition = {
        classClose: 'hide',
        open: true,
        calcHeight: true
    },
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Event handler for the click event on the '...'-more
     * button. It starts the transition to open the full
     * text
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _openText = function _openText(e) {
        e.preventDefault();

        var $self = $(this),
            $container = $self.closest('.more-text-container'),
            $fullText = $container.children('.more-text-full');

        $self.hide();
        $fullText.trigger(jse.libs.theme.events.TRANSITION(), transition);
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $this.on('click', '.more-text-container .more-text-link', _openText);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbW9yZV90ZXh0LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwidHJhbnNpdGlvbiIsImNsYXNzQ2xvc2UiLCJvcGVuIiwiY2FsY0hlaWdodCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9vcGVuVGV4dCIsImUiLCJwcmV2ZW50RGVmYXVsdCIsIiRzZWxmIiwiJGNvbnRhaW5lciIsImNsb3Nlc3QiLCIkZnVsbFRleHQiLCJjaGlsZHJlbiIsImhpZGUiLCJ0cmlnZ2VyIiwianNlIiwibGlicyIsInRoZW1lIiwiZXZlbnRzIiwiVFJBTlNJVElPTiIsImluaXQiLCJkb25lIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLFdBREosRUFHSSxDQUNJRixPQUFPRyxNQUFQLEdBQWdCLGNBRHBCLENBSEosRUFPSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVSOztBQUVRLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsYUFBYTtBQUNUQyxvQkFBWSxNQURIO0FBRVRDLGNBQU0sSUFGRztBQUdUQyxvQkFBWTtBQUhILEtBRGpCO0FBQUEsUUFNSUMsV0FBVyxFQU5mO0FBQUEsUUFPSUMsVUFBVU4sRUFBRU8sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QlAsSUFBN0IsQ0FQZDtBQUFBLFFBUUlGLFNBQVMsRUFSYjs7QUFXUjs7QUFFUTs7Ozs7OztBQU9BLFFBQUlZLFlBQVksU0FBWkEsU0FBWSxDQUFVQyxDQUFWLEVBQWE7QUFDekJBLFVBQUVDLGNBQUY7O0FBRUEsWUFBSUMsUUFBUVgsRUFBRSxJQUFGLENBQVo7QUFBQSxZQUNJWSxhQUFhRCxNQUFNRSxPQUFOLENBQWMsc0JBQWQsQ0FEakI7QUFBQSxZQUVJQyxZQUFZRixXQUFXRyxRQUFYLENBQW9CLGlCQUFwQixDQUZoQjs7QUFJQUosY0FBTUssSUFBTjtBQUNBRixrQkFBVUcsT0FBVixDQUFrQkMsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLE1BQWYsQ0FBc0JDLFVBQXRCLEVBQWxCLEVBQXNEckIsVUFBdEQ7QUFDSCxLQVREOztBQVlSOztBQUVROzs7O0FBSUFMLFdBQU8yQixJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjs7QUFFMUJ6QixjQUFNMEIsRUFBTixDQUFTLE9BQVQsRUFBa0Isc0NBQWxCLEVBQTBEakIsU0FBMUQ7O0FBRUFnQjtBQUNILEtBTEQ7O0FBT0E7QUFDQSxXQUFPNUIsTUFBUDtBQUNILENBNURMIiwiZmlsZSI6IndpZGdldHMvbW9yZV90ZXh0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb3JlX3RleHQuanMgMjAxNi0wMy0wOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogRW5hYmxlcyB0aGUgJ21vcmUnIG9yICcuLi4nIGJ1dHRvbnMgaW4gbG9uZyB0ZXh0IGZpZWxkcy5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdtb3JlX3RleHQnLFxuXG4gICAgW1xuICAgICAgICBnYW1iaW8uc291cmNlICsgJy9saWJzL2V2ZW50cydcbiAgICBdLFxuXG4gICAgZnVuY3Rpb24gKGRhdGEpIHtcblxuICAgICAgICAndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgICAgICB0cmFuc2l0aW9uID0ge1xuICAgICAgICAgICAgICAgIGNsYXNzQ2xvc2U6ICdoaWRlJyxcbiAgICAgICAgICAgICAgICBvcGVuOiB0cnVlLFxuICAgICAgICAgICAgICAgIGNhbGNIZWlnaHQ6IHRydWVcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHt9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIGZvciB0aGUgY2xpY2sgZXZlbnQgb24gdGhlICcuLi4nLW1vcmVcbiAgICAgICAgICogYnV0dG9uLiBJdCBzdGFydHMgdGhlIHRyYW5zaXRpb24gdG8gb3BlbiB0aGUgZnVsbFxuICAgICAgICAgKiB0ZXh0XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfb3BlblRleHQgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICRjb250YWluZXIgPSAkc2VsZi5jbG9zZXN0KCcubW9yZS10ZXh0LWNvbnRhaW5lcicpLFxuICAgICAgICAgICAgICAgICRmdWxsVGV4dCA9ICRjb250YWluZXIuY2hpbGRyZW4oJy5tb3JlLXRleHQtZnVsbCcpO1xuXG4gICAgICAgICAgICAkc2VsZi5oaWRlKCk7XG4gICAgICAgICAgICAkZnVsbFRleHQudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcbiAgICAgICAgfTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICAgICAkdGhpcy5vbignY2xpY2snLCAnLm1vcmUtdGV4dC1jb250YWluZXIgLm1vcmUtdGV4dC1saW5rJywgX29wZW5UZXh0KTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTtcbiJdfQ==

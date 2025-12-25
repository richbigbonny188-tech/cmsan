'use strict';

/* --------------------------------------------------------------
 history.js 2015-07-22 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Simple component that adds browser history-functionality
 * to elements (back, forward & refresh)
 */
gambio.widgets.module('history', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Event handler that executes the browser
     * history functionality depending on the
     * given data
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _navigate = function _navigate(e) {
        e.preventDefault();

        history.go(e.data.step);
    };

    /**
     * Event handler that executes the browser
     * refresh functionality
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _refresh = function _refresh(e) {
        e.preventDefault();

        location.reload();
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $this.on('click', '.history-back', { step: -1 }, _navigate).on('click', '.history-forward', { step: 1 }, _navigate).on('click', '.history-refresh', _refresh);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaGlzdG9yeS5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9uYXZpZ2F0ZSIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImhpc3RvcnkiLCJnbyIsInN0ZXAiLCJfcmVmcmVzaCIsImxvY2F0aW9uIiwicmVsb2FkIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUFzQixTQUF0QixFQUFpQyxFQUFqQyxFQUFxQyxVQUFVQyxJQUFWLEVBQWdCOztBQUVqRDs7QUFFSjs7QUFFSSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFdBQVcsRUFEZjtBQUFBLFFBRUlDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBRmQ7QUFBQSxRQUdJRCxTQUFTLEVBSGI7O0FBS0o7O0FBRUk7Ozs7Ozs7QUFPQSxRQUFJTyxZQUFZLFNBQVpBLFNBQVksQ0FBVUMsQ0FBVixFQUFhO0FBQ3pCQSxVQUFFQyxjQUFGOztBQUVBQyxnQkFBUUMsRUFBUixDQUFXSCxFQUFFUCxJQUFGLENBQU9XLElBQWxCO0FBQ0gsS0FKRDs7QUFNQTs7Ozs7O0FBTUEsUUFBSUMsV0FBVyxTQUFYQSxRQUFXLENBQVVMLENBQVYsRUFBYTtBQUN4QkEsVUFBRUMsY0FBRjs7QUFFQUssaUJBQVNDLE1BQVQ7QUFDSCxLQUpEOztBQU1KOztBQUVJOzs7O0FBSUFmLFdBQU9nQixJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjs7QUFFMUJmLGNBQ0tnQixFQURMLENBQ1EsT0FEUixFQUNpQixlQURqQixFQUNrQyxFQUFDTixNQUFNLENBQUMsQ0FBUixFQURsQyxFQUM4Q0wsU0FEOUMsRUFFS1csRUFGTCxDQUVRLE9BRlIsRUFFaUIsa0JBRmpCLEVBRXFDLEVBQUNOLE1BQU0sQ0FBUCxFQUZyQyxFQUVnREwsU0FGaEQsRUFHS1csRUFITCxDQUdRLE9BSFIsRUFHaUIsa0JBSGpCLEVBR3FDTCxRQUhyQzs7QUFLQUk7QUFDSCxLQVJEOztBQVVBO0FBQ0EsV0FBT2pCLE1BQVA7QUFDSCxDQXhERCIsImZpbGUiOiJ3aWRnZXRzL2hpc3RvcnkuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGhpc3RvcnkuanMgMjAxNS0wNy0yMiBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogU2ltcGxlIGNvbXBvbmVudCB0aGF0IGFkZHMgYnJvd3NlciBoaXN0b3J5LWZ1bmN0aW9uYWxpdHlcbiAqIHRvIGVsZW1lbnRzIChiYWNrLCBmb3J3YXJkICYgcmVmcmVzaClcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdoaXN0b3J5JywgW10sIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgZGVmYXVsdHMgPSB7fSxcbiAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgIG1vZHVsZSA9IHt9O1xuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgLyoqXG4gICAgICogRXZlbnQgaGFuZGxlciB0aGF0IGV4ZWN1dGVzIHRoZSBicm93c2VyXG4gICAgICogaGlzdG9yeSBmdW5jdGlvbmFsaXR5IGRlcGVuZGluZyBvbiB0aGVcbiAgICAgKiBnaXZlbiBkYXRhXG4gICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF9uYXZpZ2F0ZSA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICBoaXN0b3J5LmdvKGUuZGF0YS5zdGVwKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogRXZlbnQgaGFuZGxlciB0aGF0IGV4ZWN1dGVzIHRoZSBicm93c2VyXG4gICAgICogcmVmcmVzaCBmdW5jdGlvbmFsaXR5XG4gICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF9yZWZyZXNoID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIGxvY2F0aW9uLnJlbG9hZCgpO1xuICAgIH07XG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgLyoqXG4gICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICogQGNvbnN0cnVjdG9yXG4gICAgICovXG4gICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICR0aGlzXG4gICAgICAgICAgICAub24oJ2NsaWNrJywgJy5oaXN0b3J5LWJhY2snLCB7c3RlcDogLTF9LCBfbmF2aWdhdGUpXG4gICAgICAgICAgICAub24oJ2NsaWNrJywgJy5oaXN0b3J5LWZvcndhcmQnLCB7c3RlcDogMX0sIF9uYXZpZ2F0ZSlcbiAgICAgICAgICAgIC5vbignY2xpY2snLCAnLmhpc3RvcnktcmVmcmVzaCcsIF9yZWZyZXNoKTtcblxuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICByZXR1cm4gbW9kdWxlO1xufSk7Il19

'use strict';

/* --------------------------------------------------------------
 header.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that adds a class to a defined object if the page is
 * scrolled to a given position at least. It is used to set
 * the header size
 */
gambio.widgets.module('header', [gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $window = $(window),
        $header = null,
        hover = false,
        currentPosition = null,
        scrollUpCounter = 0,
        transition = {},
        timeout = 0,
        defaults = {
        // Selector that defines the header element
        header: '#header',
        // Position in px that needs to be reached to minimize the header
        scrollPosition: 200,
        // Class that gets added if the scrollPosition gets reached
        stickyClass: 'sticky',
        // Maximize the target on mouse hover
        hover: false,
        // Tolerance in px that is used to detect scrolling up
        tolerance: 5
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Handler that gets called by scrolling down / up
     * the site. If the position is lower than the
     * scrollPosition from options, the header gets maximized
     * else it gets minimized
     * @private
     */
    var _scrollHandler = function _scrollHandler() {
        var position = $(document).scrollTop(),
            hasClass = $header.hasClass(options.stickyClass),
            scrollUp = currentPosition > position;

        if (position > options.scrollPosition && !scrollUp) {
            // Proceed if scrolling down under the minimum position given by the options
            scrollUpCounter = 0;
            if (!hasClass && !hover) {
                // Proceed if the class isn't set yet and the header isn't hovered with the mouse
                transition.open = false;
                $header.trigger(jse.libs.theme.events.TRANSITION(), transition).trigger(jse.libs.theme.events.OPEN_FLYOUT(), [$this]);
            }
        } else {
            scrollUpCounter += 1;
            if (hasClass && (options.scrollPosition > position || scrollUpCounter > options.tolerance)) {
                // Proceed if the the minimum position set in the option isn't reached
                // or a specific count of pixel is scrolled up
                transition.open = true;
                $header.trigger(jse.libs.theme.events.TRANSITION(), transition);
            }
        }

        clearTimeout(timeout);
        timeout = setTimeout(function () {
            $window.trigger(jse.libs.theme.events.REPOSITIONS_STICKYBOX());
        }, 250);

        // Store the current position
        currentPosition = position;
    };

    /**
     * Handler for the mouseenter event on the
     * header. It will remove the minimizer-class
     * from the header container and set the internal
     * header hover state to true
     * @private
     */
    var _mouseEnterHandler = function _mouseEnterHandler() {
        hover = true;
        transition.open = true;
        $header.trigger(jse.libs.theme.events.TRANSITION(), transition);
    };

    /**
     * Handler for the mouseout event on the header
     * container. On mouse out, the hover state will
     * be set to false, and the header state will be
     * set by the current scroll position
     * @private
     */
    var _mouseOutHandler = function _mouseOutHandler() {
        hover = false;
        _scrollHandler();
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $header = $this.find(options.header);
        currentPosition = $(document).scrollTop();
        transition.classClose = options.stickyClass;

        $window.on('scroll', _scrollHandler);

        // Add event handler for the mouseover events
        // this can cause problems with flickering menus!
        if (options.hover) {
            $header.on('mouseenter', _mouseEnterHandler).on('mouseleave', _mouseOutHandler);
        }

        // Set the initial state of the header
        _scrollHandler();

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaGVhZGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJHdpbmRvdyIsIndpbmRvdyIsIiRoZWFkZXIiLCJob3ZlciIsImN1cnJlbnRQb3NpdGlvbiIsInNjcm9sbFVwQ291bnRlciIsInRyYW5zaXRpb24iLCJ0aW1lb3V0IiwiZGVmYXVsdHMiLCJoZWFkZXIiLCJzY3JvbGxQb3NpdGlvbiIsInN0aWNreUNsYXNzIiwidG9sZXJhbmNlIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zY3JvbGxIYW5kbGVyIiwicG9zaXRpb24iLCJkb2N1bWVudCIsInNjcm9sbFRvcCIsImhhc0NsYXNzIiwic2Nyb2xsVXAiLCJvcGVuIiwidHJpZ2dlciIsImpzZSIsImxpYnMiLCJ0aGVtZSIsImV2ZW50cyIsIlRSQU5TSVRJT04iLCJPUEVOX0ZMWU9VVCIsImNsZWFyVGltZW91dCIsInNldFRpbWVvdXQiLCJSRVBPU0lUSU9OU19TVElDS1lCT1giLCJfbW91c2VFbnRlckhhbmRsZXIiLCJfbW91c2VPdXRIYW5kbGVyIiwiaW5pdCIsImRvbmUiLCJmaW5kIiwiY2xhc3NDbG9zZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLFFBREosRUFHSSxDQUNJRixPQUFPRyxNQUFQLEdBQWdCLGNBRHBCLENBSEosRUFPSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVSOztBQUVRLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsVUFBVUQsRUFBRUUsTUFBRixDQURkO0FBQUEsUUFFSUMsVUFBVSxJQUZkO0FBQUEsUUFHSUMsUUFBUSxLQUhaO0FBQUEsUUFJSUMsa0JBQWtCLElBSnRCO0FBQUEsUUFLSUMsa0JBQWtCLENBTHRCO0FBQUEsUUFNSUMsYUFBYSxFQU5qQjtBQUFBLFFB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RCxvQkFDS29DLEVBREwsQ0FDUSxZQURSLEVBQ3NCTixrQkFEdEIsRUFFS00sRUFGTCxDQUVRLFlBRlIsRUFFc0JMLGdCQUZ0QjtBQUdIOztBQUVEO0FBQ0FsQjs7QUFFQW9CO0FBQ0gsS0FwQkQ7O0FBc0JBO0FBQ0EsV0FBT3hDLE1BQVA7QUFDSCxDQXhJTCIsImZpbGUiOiJ3aWRnZXRzL2hlYWRlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gaGVhZGVyLmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IGFkZHMgYSBjbGFzcyB0byBhIGRlZmluZWQgb2JqZWN0IGlmIHRoZSBwYWdlIGlzXG4gKiBzY3JvbGxlZCB0byBhIGdpdmVuIHBvc2l0aW9uIGF0IGxlYXN0LiBJdCBpcyB1c2VkIHRvIHNldFxuICogdGhlIGhlYWRlciBzaXplXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnaGVhZGVyJyxcblxuICAgIFtcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnXG4gICAgXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgJHdpbmRvdyA9ICQod2luZG93KSxcbiAgICAgICAgICAgICRoZWFkZXIgPSBudWxsLFxuICAgICAgICAgICAgaG92ZXIgPSBmYWxzZSxcbiAgICAgICAgICAgIGN1cnJlbnRQb3NpdGlvbiA9IG51bGwsXG4gICAgICAgICAgICBzY3JvbGxVcENvdW50ZXIgPSAwLFxuICAgICAgICAgICAgdHJhbnNpdGlvbiA9IHt9LFxuICAgICAgICAgICAgdGltZW91dCA9IDAsXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICAvLyBTZWxlY3RvciB0aGF0IGRlZmluZXMgdGhlIGhlYWRlciBlbGVtZW50XG4gICAgICAgICAgICAgICAgaGVhZGVyOiAnI2hlYWRlcicsXG4gICAgICAgICAgICAgICAgLy8gUG9zaXRpb24gaW4gcHggdGhhdCBuZWVkcyB0byBiZSByZWFjaGVkIHRvIG1pbmltaXplIHRoZSBoZWFkZXJcbiAgICAgICAgICAgICAgICBzY3JvbGxQb3NpdGlvbjogMjAwLFxuICAgICAgICAgICAgICAgIC8vIENsYXNzIHRoYXQgZ2V0cyBhZGRlZCBpZiB0aGUgc2Nyb2xsUG9zaXRpb24gZ2V0cyByZWFjaGVkXG4gICAgICAgICAgICAgICAgc3RpY2t5Q2xhc3M6ICdzdGlja3knLFxuICAgICAgICAgICAgICAgIC8vIE1heGltaXplIHRoZSB0YXJnZXQgb24gbW91c2UgaG92ZXJcbiAgICAgICAgICAgICAgICBob3ZlcjogZmFsc2UsXG4gICAgICAgICAgICAgICAgLy8gVG9sZXJhbmNlIGluIHB4IHRoYXQgaXMgdXNlZCB0byBkZXRlY3Qgc2Nyb2xsaW5nIHVwXG4gICAgICAgICAgICAgICAgdG9sZXJhbmNlOiA1XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIYW5kbGVyIHRoYXQgZ2V0cyBjYWxsZWQgYnkgc2Nyb2xsaW5nIGRvd24gLyB1cFxuICAgICAgICAgKiB0aGUgc2l0ZS4gSWYgdGhlIHBvc2l0aW9uIGlzIGxvd2VyIHRoYW4gdGhlXG4gICAgICAgICAqIHNjcm9sbFBvc2l0aW9uIGZyb20gb3B0aW9ucywgdGhlIGhlYWRlciBnZXRzIG1heGltaXplZFxuICAgICAgICAgKiBlbHNlIGl0IGdldHMgbWluaW1pemVkXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3Njcm9sbEhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgcG9zaXRpb24gPSAkKGRvY3VtZW50KS5zY3JvbGxUb3AoKSxcbiAgICAgICAgICAgICAgICBoYXNDbGFzcyA9ICRoZWFkZXIuaGFzQ2xhc3Mob3B0aW9ucy5zdGlja3lDbGFzcyksXG4gICAgICAgICAgICAgICAgc2Nyb2xsVXAgPSBjdXJyZW50UG9zaXRpb24gPiBwb3NpdGlvbjtcblxuICAgICAgICAgICAgaWYgKHBvc2l0aW9uID4gb3B0aW9ucy5zY3JvbGxQb3NpdGlvbiAmJiAhc2Nyb2xsVXApIHtcbiAgICAgICAgICAgICAgICAvLyBQcm9jZWVkIGlmIHNjcm9sbGluZyBkb3duIHVuZGVyIHRoZSBtaW5pbXVtIHBvc2l0aW9uIGdpdmVuIGJ5IHRoZSBvcHRpb25zXG4gICAgICAgICAgICAgICAgc2Nyb2xsVXBDb3VudGVyID0gMDtcbiAgICAgICAgICAgICAgICBpZiAoIWhhc0NsYXNzICYmICFob3Zlcikge1xuICAgICAgICAgICAgICAgICAgICAvLyBQcm9jZWVkIGlmIHRoZSBjbGFzcyBpc24ndCBzZXQgeWV0IGFuZCB0aGUgaGVhZGVyIGlzbid0IGhvdmVyZWQgd2l0aCB0aGUgbW91c2VcbiAgICAgICAgICAgICAgICAgICAgdHJhbnNpdGlvbi5vcGVuID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgICRoZWFkZXJcbiAgICAgICAgICAgICAgICAgICAgICAgIC50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pXG4gICAgICAgICAgICAgICAgICAgICAgICAudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuT1BFTl9GTFlPVVQoKSwgWyR0aGlzXSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBzY3JvbGxVcENvdW50ZXIgKz0gMTtcbiAgICAgICAgICAgICAgICBpZiAoaGFzQ2xhc3MgJiYgKG9wdGlvbnMuc2Nyb2xsUG9zaXRpb24gPiBwb3NpdGlvbiB8fCBzY3JvbGxVcENvdW50ZXIgPiBvcHRpb25zLnRvbGVyYW5jZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gUHJvY2VlZCBpZiB0aGUgdGhlIG1pbmltdW0gcG9zaXRpb24gc2V0IGluIHRoZSBvcHRpb24gaXNuJ3QgcmVhY2hlZFxuICAgICAgICAgICAgICAgICAgICAvLyBvciBhIHNwZWNpZmljIGNvdW50IG9mIHBpeGVsIGlzIHNjcm9sbGVkIHVwXG4gICAgICAgICAgICAgICAgICAgIHRyYW5zaXRpb24ub3BlbiA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgICRoZWFkZXIudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNsZWFyVGltZW91dCh0aW1lb3V0KTtcbiAgICAgICAgICAgIHRpbWVvdXQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAkd2luZG93LnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLlJFUE9TSVRJT05TX1NUSUNLWUJPWCgpKTtcbiAgICAgICAgICAgIH0sIDI1MCk7XG5cbiAgICAgICAgICAgIC8vIFN0b3JlIHRoZSBjdXJyZW50IHBvc2l0aW9uXG4gICAgICAgICAgICBjdXJyZW50UG9zaXRpb24gPSBwb3NpdGlvbjtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGFuZGxlciBmb3IgdGhlIG1vdXNlZW50ZXIgZXZlbnQgb24gdGhlXG4gICAgICAgICAqIGhlYWRlci4gSXQgd2lsbCByZW1vdmUgdGhlIG1pbmltaXplci1jbGFzc1xuICAgICAgICAgKiBmcm9tIHRoZSBoZWFkZXIgY29udGFpbmVyIGFuZCBzZXQgdGhlIGludGVybmFsXG4gICAgICAgICAqIGhlYWRlciBob3ZlciBzdGF0ZSB0byB0cnVlXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX21vdXNlRW50ZXJIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgaG92ZXIgPSB0cnVlO1xuICAgICAgICAgICAgdHJhbnNpdGlvbi5vcGVuID0gdHJ1ZTtcbiAgICAgICAgICAgICRoZWFkZXIudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGFuZGxlciBmb3IgdGhlIG1vdXNlb3V0IGV2ZW50IG9uIHRoZSBoZWFkZXJcbiAgICAgICAgICogY29udGFpbmVyLiBPbiBtb3VzZSBvdXQsIHRoZSBob3ZlciBzdGF0ZSB3aWxsXG4gICAgICAgICAqIGJlIHNldCB0byBmYWxzZSwgYW5kIHRoZSBoZWFkZXIgc3RhdGUgd2lsbCBiZVxuICAgICAgICAgKiBzZXQgYnkgdGhlIGN1cnJlbnQgc2Nyb2xsIHBvc2l0aW9uXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX21vdXNlT3V0SGFuZGxlciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGhvdmVyID0gZmFsc2U7XG4gICAgICAgICAgICBfc2Nyb2xsSGFuZGxlcigpO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgICAgICogQGNvbnN0cnVjdG9yXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cbiAgICAgICAgICAgICRoZWFkZXIgPSAkdGhpcy5maW5kKG9wdGlvbnMuaGVhZGVyKTtcbiAgICAgICAgICAgIGN1cnJlbnRQb3NpdGlvbiA9ICQoZG9jdW1lbnQpLnNjcm9sbFRvcCgpO1xuICAgICAgICAgICAgdHJhbnNpdGlvbi5jbGFzc0Nsb3NlID0gb3B0aW9ucy5zdGlja3lDbGFzcztcblxuICAgICAgICAgICAgJHdpbmRvdy5vbignc2Nyb2xsJywgX3Njcm9sbEhhbmRsZXIpO1xuXG4gICAgICAgICAgICAvLyBBZGQgZXZlbnQgaGFuZGxlciBmb3IgdGhlIG1vdXNlb3ZlciBldmVudHNcbiAgICAgICAgICAgIC8vIHRoaXMgY2FuIGNhdXNlIHByb2JsZW1zIHdpdGggZmxpY2tlcmluZyBtZW51cyFcbiAgICAgICAgICAgIGlmIChvcHRpb25zLmhvdmVyKSB7XG4gICAgICAgICAgICAgICAgJGhlYWRlclxuICAgICAgICAgICAgICAgICAgICAub24oJ21vdXNlZW50ZXInLCBfbW91c2VFbnRlckhhbmRsZXIpXG4gICAgICAgICAgICAgICAgICAgIC5vbignbW91c2VsZWF2ZScsIF9tb3VzZU91dEhhbmRsZXIpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBTZXQgdGhlIGluaXRpYWwgc3RhdGUgb2YgdGhlIGhlYWRlclxuICAgICAgICAgICAgX3Njcm9sbEhhbmRsZXIoKTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTtcbiJdfQ==

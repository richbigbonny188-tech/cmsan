'use strict';

/* --------------------------------------------------------------
 pageup.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component that displays a "Page Up" button if the
 * page is not at top position. On click the page
 * scrolls up to top
 */
gambio.widgets.module('pageup', [gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $window = $(window),
        visible = false,
        transition = {},
        defaults = {
        top: 200, // Pixel from top needs to be reached before the button gets displayed
        duration: 300, // Animation time to scroll up
        showClass: 'visible' // Class that gets added to show the pageup element (else it will be hidden)
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Event handler for the scroll event.
     * If the current scroll position ist higher
     * than the position given by options.top
     * the button gets displayed.
     * @private
     */
    var _scrollHandler = function _scrollHandler() {
        var show = $window.scrollTop() > options.top;

        if (show && !visible) {
            visible = true;
            transition.open = true;
            $this.trigger(jse.libs.theme.events.TRANSITION(), transition);
        } else if (!show && visible) {
            visible = false;
            transition.open = false;
            $this.trigger(jse.libs.theme.events.TRANSITION(), transition);
        }
    };

    /**
     * Event handler for clicking on the
     * page-up button. It scrolls up the
     * page.
     * @private
     */
    var _clickHandler = function _clickHandler(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: '0' }, options.duration);
    };

    // ########## INITIALIZATION ##########


    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        transition.classOpen = options.showClass;

        $window.on('scroll', _scrollHandler);
        $this.on('click', _clickHandler);

        _scrollHandler();

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGFnZXVwLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJHdpbmRvdyIsIndpbmRvdyIsInZpc2libGUiLCJ0cmFuc2l0aW9uIiwiZGVmYXVsdHMiLCJ0b3AiLCJkdXJhdGlvbiIsInNob3dDbGFzcyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2Nyb2xsSGFuZGxlciIsInNob3ciLCJzY3JvbGxUb3AiLCJvcGVuIiwidHJpZ2dlciIsImpzZSIsImxpYnMiLCJ0aGVtZSIsImV2ZW50cyIsIlRSQU5TSVRJT04iLCJfY2xpY2tIYW5kbGVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwiYW5pbWF0ZSIsImluaXQiLCJkb25lIiwiY2xhc3NPcGVuIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksUUFESixFQUdJLENBQ0lGLE9BQU9HLE1BQVAsR0FBZ0IsY0FEcEIsQ0FISixFQU9JLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRVI7O0FBRVEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxVQUFVRCxFQUFFRSxNQUFGLENBRGQ7QUFBQSxRQUVJQyxVQUFVLEtBRmQ7QUFBQSxRQUdJQyxhQUFhLEVBSGpCO0FBQUEsUUFJSUMsV0FBVztBQUNQQyxhQUFLLEdBREUsRUFDVTtBQUNqQkMsa0JBQVUsR0FGSCxFQUVlO0FBQ3RCQyxtQkFBVyxTQUhKLENBR2dCO0FBSGhCLEtBSmY7QUFBQSxRQVNJQyxVQUFVVCxFQUFFVSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJMLFFBQW5CLEVBQTZCUCxJQUE3QixDQVRkO0FBQUEsUUFVSUYsU0FBUyxFQVZiOztBQWFSOztBQUVROzs7Ozs7O0FBT0EsUUFBSWUsaUJBQWlCLFNBQWpCQSxjQUFpQixHQUFZO0FBQzdCLFlBQUlDLE9BQU9YLFFBQVFZLFNBQVIsS0FBc0JKLFFBQVFILEdBQXpDOztBQUVBLFlBQUlNLFFBQVEsQ0FBQ1QsT0FBYixFQUFzQjtBQUNsQkEsc0JBQVUsSUFBVjtBQUNBQyx1QkFBV1UsSUFBWCxHQUFrQixJQUFsQjtBQUNBZixrQkFBTWdCLE9BQU4sQ0FBY0MsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLE1BQWYsQ0FBc0JDLFVBQXRCLEVBQWQsRUFBa0RoQixVQUFsRDtBQUNILFNBSkQsTUFJTyxJQUFJLENBQUNRLElBQUQsSUFBU1QsT0FBYixFQUFzQjtBQUN6QkEsc0JBQVUsS0FBVjtBQUNBQyx1QkFBV1UsSUFBWCxHQUFrQixLQUFsQjtBQUNBZixrQkFBTWdCLE9BQU4sQ0FBY0MsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLE1BQWYsQ0FBc0JDLFVBQXRCLEVBQWQsRUFBa0RoQixVQUFsRDtBQUNIO0FBQ0osS0FaRDs7QUFlQTs7Ozs7O0FBTUEsUUFBSWlCLGdCQUFnQixTQUFoQkEsYUFBZ0IsQ0FBVUMsQ0FBVixFQUFhO0FBQzdCQSxVQUFFQyxjQUFGO0FBQ0F2QixVQUFFLFlBQUYsRUFBZ0J3QixPQUFoQixDQUF3QixFQUFDWCxXQUFXLEdBQVosRUFBeEIsRUFBMENKLFFBQVFGLFFBQWxEO0FBQ0gsS0FIRDs7QUFLUjs7O0FBR1E7Ozs7QUFJQVgsV0FBTzZCLElBQVAsR0FBYyxVQUFVQyxJQUFWLEVBQWdCOztBQUUxQnRCLG1CQUFXdUIsU0FBWCxHQUF1QmxCLFFBQVFELFNBQS9COztBQUVBUCxnQkFBUTJCLEVBQVIsQ0FBVyxRQUFYLEVBQXFCakIsY0FBckI7QUFDQVosY0FBTTZCLEVBQU4sQ0FBUyxPQUFULEVBQWtCUCxhQUFsQjs7QUFFQVY7O0FBRUFlO0FBQ0gsS0FWRDs7QUFZQTtBQUNBLFdBQU85QixNQUFQO0FBQ0gsQ0FsRkwiLCJmaWxlIjoid2lkZ2V0cy9wYWdldXAuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHBhZ2V1cC5qcyAyMDE2LTAzLTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBDb21wb25lbnQgdGhhdCBkaXNwbGF5cyBhIFwiUGFnZSBVcFwiIGJ1dHRvbiBpZiB0aGVcbiAqIHBhZ2UgaXMgbm90IGF0IHRvcCBwb3NpdGlvbi4gT24gY2xpY2sgdGhlIHBhZ2VcbiAqIHNjcm9sbHMgdXAgdG8gdG9wXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAncGFnZXVwJyxcblxuICAgIFtcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnXG4gICAgXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgJHdpbmRvdyA9ICQod2luZG93KSxcbiAgICAgICAgICAgIHZpc2libGUgPSBmYWxzZSxcbiAgICAgICAgICAgIHRyYW5zaXRpb24gPSB7fSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIHRvcDogMjAwLCAgICAgICAgLy8gUGl4ZWwgZnJvbSB0b3AgbmVlZHMgdG8gYmUgcmVhY2hlZCBiZWZvcmUgdGhlIGJ1dHRvbiBnZXRzIGRpc3BsYXllZFxuICAgICAgICAgICAgICAgIGR1cmF0aW9uOiAzMDAsICAgICAgICAvLyBBbmltYXRpb24gdGltZSB0byBzY3JvbGwgdXBcbiAgICAgICAgICAgICAgICBzaG93Q2xhc3M6ICd2aXNpYmxlJyAgIC8vIENsYXNzIHRoYXQgZ2V0cyBhZGRlZCB0byBzaG93IHRoZSBwYWdldXAgZWxlbWVudCAoZWxzZSBpdCB3aWxsIGJlIGhpZGRlbilcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgICAgIG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBzY3JvbGwgZXZlbnQuXG4gICAgICAgICAqIElmIHRoZSBjdXJyZW50IHNjcm9sbCBwb3NpdGlvbiBpc3QgaGlnaGVyXG4gICAgICAgICAqIHRoYW4gdGhlIHBvc2l0aW9uIGdpdmVuIGJ5IG9wdGlvbnMudG9wXG4gICAgICAgICAqIHRoZSBidXR0b24gZ2V0cyBkaXNwbGF5ZWQuXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3Njcm9sbEhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgc2hvdyA9ICR3aW5kb3cuc2Nyb2xsVG9wKCkgPiBvcHRpb25zLnRvcDtcblxuICAgICAgICAgICAgaWYgKHNob3cgJiYgIXZpc2libGUpIHtcbiAgICAgICAgICAgICAgICB2aXNpYmxlID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB0cmFuc2l0aW9uLm9wZW4gPSB0cnVlO1xuICAgICAgICAgICAgICAgICR0aGlzLnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLlRSQU5TSVRJT04oKSwgdHJhbnNpdGlvbik7XG4gICAgICAgICAgICB9IGVsc2UgaWYgKCFzaG93ICYmIHZpc2libGUpIHtcbiAgICAgICAgICAgICAgICB2aXNpYmxlID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgdHJhbnNpdGlvbi5vcGVuID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgJHRoaXMudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIGZvciBjbGlja2luZyBvbiB0aGVcbiAgICAgICAgICogcGFnZS11cCBidXR0b24uIEl0IHNjcm9sbHMgdXAgdGhlXG4gICAgICAgICAqIHBhZ2UuXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2NsaWNrSGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAkKCdodG1sLCBib2R5JykuYW5pbWF0ZSh7c2Nyb2xsVG9wOiAnMCd9LCBvcHRpb25zLmR1cmF0aW9uKTtcbiAgICAgICAgfTtcblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICAgICB0cmFuc2l0aW9uLmNsYXNzT3BlbiA9IG9wdGlvbnMuc2hvd0NsYXNzO1xuXG4gICAgICAgICAgICAkd2luZG93Lm9uKCdzY3JvbGwnLCBfc2Nyb2xsSGFuZGxlcik7XG4gICAgICAgICAgICAkdGhpcy5vbignY2xpY2snLCBfY2xpY2tIYW5kbGVyKTtcblxuICAgICAgICAgICAgX3Njcm9sbEhhbmRsZXIoKTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTtcbiJdfQ==

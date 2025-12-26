'use strict';

/* --------------------------------------------------------------
 mobile_menu.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that performs the actions for the topbar menu
 * buttons in mobile view. It opens / closes the menu items
 * after a click on a button was performed (or in special
 * cases opens a link).
 */
gambio.widgets.module('mobile_menu', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        $buttons = null,
        mobile = null,
        scrollTop,
        // scroll top backup
    scrollLeft,
        // scroll top backup
    defaults = {
        breakpoint: 40, // Minimum breakpoint to switch to mobile view
        buttonActiveClass: 'active', // Class that is set to the active button
        addClass: 'in' // Class to add to the menu contents if opened
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Function that sets and removes the classes
     * to the corresponding menu contents. If a data
     * object is given, open the corresponding menu while
     * closing all others. If no data is given, close all
     * menus.
     * @param       {object}    buttonData    [OPTIONAL] data object of the pressed button
     * @private
     */
    var _setClasses = function _setClasses(buttonData) {
        var found = false;

        $buttons.each(function () {
            var $self = $(this),
                d = $(this).parseModuleData('mobile_menu');

            if (!buttonData || d && d.target !== buttonData.target) {
                // The target of the button isn't the one delivered by "buttonData"
                $(d.target).removeClass(options.addClass);
                $self.removeClass(options.buttonActiveClass);
                $body.removeClass(d.bodyClass);
            } else if (d && !found) {
                // The target is the same as the one delivered by buttonData
                // AND it wasn't opened / closed in this loop before
                var $target = $(d.target);
                $target.toggleClass(options.addClass);

                // Add or remove classes to the body and the buttons
                // depending on the state. The if / else case is used
                // to be more fail safe than a toggle
                if ($target.hasClass(options.addClass)) {
                    $body.addClass(d.bodyClass);
                    $self.addClass(options.buttonActiveClass);
                    if ($self.data('mobilemenuToggleContentVisibility') !== undefined) {
                        _toggleContentVisibility(false);
                    }
                } else {
                    $body.removeClass(d.bodyClass);
                    $self.removeClass(options.buttonActiveClass);
                    if ($self.data('mobilemenuToggleContentVisibility') !== undefined) {
                        _toggleContentVisibility(true);
                    }
                }

                // Set a flag that the target has been processed
                found = true;
            }
        });
    };

    /**
     * Toggle Content Visibility
     *
     * In some occasions some container elements cover the complete mobile screen but due to
     * buggy behavior the scrolling of the page is still available. Use this method to hide the
     * page content and solve the scrolling problem.
     *
     * @param {bool} state Sets whether the content is visible or not.
     *
     * @private
     */
    var _toggleContentVisibility = function _toggleContentVisibility(state) {
        var $content = $('#wrapper, #footer'),
            $document = $(document);

        if (state) {
            $content.show();
            $document.scrollTop(scrollTop);
            $document.scrollLeft(scrollLeft);
            scrollTop = scrollLeft = null; // reset
        } else {
            if (!scrollTop) {
                scrollTop = $document.scrollTop(); // backup
            }
            if (!scrollLeft) {
                scrollLeft = $document.scrollLeft(); // backup
            }
            $content.hide();
        }
    };

    // ########## EVENT HANDLER ##########

    /**
     * Event handler for the click event on the
     * buttons. In case the button is a menu button
     * the corresponding menu entry gets shown, while
     * all other menus getting closed
     * @private
     */
    var _clickHandler = function _clickHandler() {
        var $self = $(this),
            buttonData = $self.parseModuleData('mobile_menu');

        if (buttonData.target) {
            // Set the classes for the open / close state of the menu
            _setClasses(buttonData);
        } else if (buttonData.location) {
            // Open a link
            location.href = buttonData.location;
        }
    };

    /**
     * Event handler that listens on the
     * "breakpoint" event. On every breakpoint
     * the function checks if there is a switch
     * from desktop to mobile. In case that
     * happens, all opened menus getting closed
     * @param       {object}    e       jQuery event object
     * @param       {object}    d       Data object that contains the information belonging to the current breakpoint
     * @private
     */
    var _breakpointHandler = function _breakpointHandler(e, d) {
        if (d.id > options.breakpoint && mobile) {
            // Close all menus on switch to desktop view
            _setClasses(null);
            $('#wrapper, #footer').show();
            mobile = false;
        } else if (d.id <= options.breakpoint && !mobile) {
            // Close all menus on switch to mobile view
            _setClasses(null);
            mobile = true;
        }
    };

    /**
     * Navbar Topbar Item Click
     *
     * This handler must close the other opened frames because only one item should be visible.
     *
     * @private
     */
    var _clickTopBarItemHandler = function _clickTopBarItemHandler() {
        if ($(this).parent().hasClass('open')) {
            return;
        }
        $('.navbar-categories').find('.navbar-topbar-item.open').removeClass('open');
        $('#categories .navbar-collapse:first').animate({
            scrollTop: $(this).parent().position().top + $(this).parent().height() - $('#header .navbar-header').height()
        }, 500);
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        mobile = jse.libs.theme.responsive.breakpoint().id <= options.breakpoint;
        $buttons = $this.find('button');

        $body.on(jse.libs.theme.events.BREAKPOINT(), _breakpointHandler);
        $('.navbar-categories').on('mouseup', '.navbar-topbar-item > a', _clickTopBarItemHandler);
        $this.on('click', 'button', _clickHandler);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbW9iaWxlX21lbnUuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkYm9keSIsIiRidXR0b25zIiwibW9iaWxlIiwic2Nyb2xsVG9wIiwic2Nyb2xsTGVmdCIsImRlZmF1bHRzIiwiYnJlYWtwb2ludCIsImJ1dHRvbkFjdGl2ZUNsYXNzIiwiYWRkQ2xhc3MiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3NldENsYXNzZXMiLCJidXR0b25EYXRhIiwiZm91bmQiLCJlYWNoIiwiJHNlbGYiLCJkIiwicGFyc2VNb2R1bGVEYXRhIiwidGFyZ2V0IiwicmVtb3ZlQ2xhc3MiLCJib2R5Q2xhc3MiLCIkdGFyZ2V0IiwidG9nZ2xlQ2xhc3MiLCJoYXNDbGFzcyIsInVuZGVmaW5lZCIsIl90b2dnbGVDb250ZW50VmlzaWJpbGl0eSIsInN0YXRlIiwiJGNvbnRlbnQiLCIkZG9jdW1lbnQiLCJkb2N1bWVudCIsInNob3ciLCJoaWRlIiwiX2NsaWNrSGFuZGxlciIsImxvY2F0aW9uIiwiaHJlZiIsIl9icmVha3BvaW50SGFuZGxlciIsImUiLCJpZCIsIl9jbGlja1RvcEJhckl0ZW1IYW5kbGVyIiwicGFyZW50IiwiZmluZCIsImFuaW1hdGUiLCJwb3NpdGlvbiIsInRvcCIsImhlaWdodCIsImluaXQiLCJkb25lIiwianNlIiwibGlicyIsInRoZW1lIiwicmVzcG9uc2l2ZSIsIm9uIiwiZXZlbnRzIiwiQlJFQUtQT0lOVCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7QUFNQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksYUFESixFQUdJLENBQ0lGLE9BQU9HLE1BQVAsR0FBZ0IsY0FEcEIsRUFFSUgsT0FBT0csTUFBUCxHQUFnQixrQkFGcEIsQ0FISixFQVFJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRVI7O0FBRVEsUUFBSUMsUUFBUUMsRUFBRSxJ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RixFQUFRd0MsTUFBUixHQUFpQkcsUUFBakIsR0FBNEJDLEdBQTVCLEdBQWtDNUMsRUFBRSxJQUFGLEVBQ3hDd0MsTUFEd0MsR0FFeENLLE1BRndDLEVBQWxDLEdBRUs3QyxFQUFFLHdCQUFGLEVBQTRCNkMsTUFBNUI7QUFINEIsU0FBaEQsRUFJRyxHQUpIO0FBS0gsS0FWRDs7QUFhUjs7QUFFUTs7OztBQUlBakQsV0FBT2tELElBQVAsR0FBYyxVQUFVQyxJQUFWLEVBQWdCOztBQUUxQjVDLGlCQUFTNkMsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLFVBQWYsQ0FBMEI1QyxVQUExQixHQUF1QytCLEVBQXZDLElBQTZDNUIsUUFBUUgsVUFBOUQ7QUFDQUwsbUJBQVdILE1BQU0wQyxJQUFOLENBQVcsUUFBWCxDQUFYOztBQUVBeEMsY0FBTW1ELEVBQU4sQ0FBU0osSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVHLE1BQWYsQ0FBc0JDLFVBQXRCLEVBQVQsRUFBNkNsQixrQkFBN0M7QUFDQXBDLFVBQUUsb0JBQUYsRUFBd0JvRCxFQUF4QixDQUEyQixTQUEzQixFQUFzQyx5QkFBdEMsRUFBaUViLHVCQUFqRTtBQUNBeEMsY0FBTXFELEVBQU4sQ0FBUyxPQUFULEVBQWtCLFFBQWxCLEVBQTRCbkIsYUFBNUI7O0FBRUFjO0FBQ0gsS0FWRDs7QUFZQTtBQUNBLFdBQU9uRCxNQUFQO0FBQ0gsQ0F2TUwiLCJmaWxlIjoid2lkZ2V0cy9tb2JpbGVfbWVudS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbW9iaWxlX21lbnUuanMgMjAxNi0wMy0wOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogV2lkZ2V0IHRoYXQgcGVyZm9ybXMgdGhlIGFjdGlvbnMgZm9yIHRoZSB0b3BiYXIgbWVudVxuICogYnV0dG9ucyBpbiBtb2JpbGUgdmlldy4gSXQgb3BlbnMgLyBjbG9zZXMgdGhlIG1lbnUgaXRlbXNcbiAqIGFmdGVyIGEgY2xpY2sgb24gYSBidXR0b24gd2FzIHBlcmZvcm1lZCAob3IgaW4gc3BlY2lhbFxuICogY2FzZXMgb3BlbnMgYSBsaW5rKS5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdtb2JpbGVfbWVudScsXG5cbiAgICBbXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJyxcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9yZXNwb25zaXZlJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICRib2R5ID0gJCgnYm9keScpLFxuICAgICAgICAgICAgJGJ1dHRvbnMgPSBudWxsLFxuICAgICAgICAgICAgbW9iaWxlID0gbnVsbCxcbiAgICAgICAgICAgIHNjcm9sbFRvcCwgLy8gc2Nyb2xsIHRvcCBiYWNrdXBcbiAgICAgICAgICAgIHNjcm9sbExlZnQsIC8vIHNjcm9sbCB0b3AgYmFja3VwXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICBicmVha3BvaW50OiA0MCwgLy8gTWluaW11bSBicmVha3BvaW50IHRvIHN3aXRjaCB0byBtb2JpbGUgdmlld1xuICAgICAgICAgICAgICAgIGJ1dHRvbkFjdGl2ZUNsYXNzOiAnYWN0aXZlJywgLy8gQ2xhc3MgdGhhdCBpcyBzZXQgdG8gdGhlIGFjdGl2ZSBidXR0b25cbiAgICAgICAgICAgICAgICBhZGRDbGFzczogJ2luJyAvLyBDbGFzcyB0byBhZGQgdG8gdGhlIG1lbnUgY29udGVudHMgaWYgb3BlbmVkXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBGdW5jdGlvbiB0aGF0IHNldHMgYW5kIHJlbW92ZXMgdGhlIGNsYXNzZXNcbiAgICAgICAgICogdG8gdGhlIGNvcnJlc3BvbmRpbmcgbWVudSBjb250ZW50cy4gSWYgYSBkYXRhXG4gICAgICAgICAqIG9iamVjdCBpcyBnaXZlbiwgb3BlbiB0aGUgY29ycmVzcG9uZGluZyBtZW51IHdoaWxlXG4gICAgICAgICAqIGNsb3NpbmcgYWxsIG90aGVycy4gSWYgbm8gZGF0YSBpcyBnaXZlbiwgY2xvc2UgYWxsXG4gICAgICAgICAqIG1lbnVzLlxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgYnV0dG9uRGF0YSAgICBbT1BUSU9OQUxdIGRhdGEgb2JqZWN0IG9mIHRoZSBwcmVzc2VkIGJ1dHRvblxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zZXRDbGFzc2VzID0gZnVuY3Rpb24gKGJ1dHRvbkRhdGEpIHtcbiAgICAgICAgICAgIHZhciBmb3VuZCA9IGZhbHNlO1xuXG4gICAgICAgICAgICAkYnV0dG9ucy5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICBkID0gJCh0aGlzKS5wYXJzZU1vZHVsZURhdGEoJ21vYmlsZV9tZW51Jyk7XG5cbiAgICAgICAgICAgICAgICBpZiAoIWJ1dHRvbkRhdGEgfHwgKGQgJiYgZC50YXJnZXQgIT09IGJ1dHRvbkRhdGEudGFyZ2V0KSkge1xuICAgICAgICAgICAgICAgICAgICAvLyBUaGUgdGFyZ2V0IG9mIHRoZSBidXR0b24gaXNuJ3QgdGhlIG9uZSBkZWxpdmVyZWQgYnkgXCJidXR0b25EYXRhXCJcbiAgICAgICAgICAgICAgICAgICAgJChkLnRhcmdldCkucmVtb3ZlQ2xhc3Mob3B0aW9ucy5hZGRDbGFzcyk7XG4gICAgICAgICAgICAgICAgICAgICRzZWxmLnJlbW92ZUNsYXNzKG9wdGlvbnMuYnV0dG9uQWN0aXZlQ2xhc3MpO1xuICAgICAgICAgICAgICAgICAgICAkYm9keS5yZW1vdmVDbGFzcyhkLmJvZHlDbGFzcyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmIChkICYmICFmb3VuZCkge1xuICAgICAgICAgICAgICAgICAgICAvLyBUaGUgdGFyZ2V0IGlzIHRoZSBzYW1lIGFzIHRoZSBvbmUgZGVsaXZlcmVkIGJ5IGJ1dHRvbkRhdGFcbiAgICAgICAgICAgICAgICAgICAgLy8gQU5EIGl0IHdhc24ndCBvcGVuZWQgLyBjbG9zZWQgaW4gdGhpcyBsb29wIGJlZm9yZVxuICAgICAgICAgICAgICAgICAgICB2YXIgJHRhcmdldCA9ICQoZC50YXJnZXQpO1xuICAgICAgICAgICAgICAgICAgICAkdGFyZ2V0LnRvZ2dsZUNsYXNzKG9wdGlvbnMuYWRkQ2xhc3MpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIEFkZCBvciByZW1vdmUgY2xhc3NlcyB0byB0aGUgYm9keSBhbmQgdGhlIGJ1dHRvbnNcbiAgICAgICAgICAgICAgICAgICAgLy8gZGVwZW5kaW5nIG9uIHRoZSBzdGF0ZS4gVGhlIGlmIC8gZWxzZSBjYXNlIGlzIHVzZWRcbiAgICAgICAgICAgICAgICAgICAgLy8gdG8gYmUgbW9yZSBmYWlsIHNhZmUgdGhhbiBhIHRvZ2dsZVxuICAgICAgICAgICAgICAgICAgICBpZiAoJHRhcmdldC5oYXNDbGFzcyhvcHRpb25zLmFkZENsYXNzKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGJvZHkuYWRkQ2xhc3MoZC5ib2R5Q2xhc3MpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGYuYWRkQ2xhc3Mob3B0aW9ucy5idXR0b25BY3RpdmVDbGFzcyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJHNlbGYuZGF0YSgnbW9iaWxlbWVudVRvZ2dsZUNvbnRlbnRWaXNpYmlsaXR5JykgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIF90b2dnbGVDb250ZW50VmlzaWJpbGl0eShmYWxzZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkYm9keS5yZW1vdmVDbGFzcyhkLmJvZHlDbGFzcyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkc2VsZi5yZW1vdmVDbGFzcyhvcHRpb25zLmJ1dHRvbkFjdGl2ZUNsYXNzKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkc2VsZi5kYXRhKCdtb2JpbGVtZW51VG9nZ2xlQ29udGVudFZpc2liaWxpdHknKSAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgX3RvZ2dsZUNvbnRlbnRWaXNpYmlsaXR5KHRydWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gU2V0IGEgZmxhZyB0aGF0IHRoZSB0YXJnZXQgaGFzIGJlZW4gcHJvY2Vzc2VkXG4gICAgICAgICAgICAgICAgICAgIGZvdW5kID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBUb2dnbGUgQ29udGVudCBWaXNpYmlsaXR5XG4gICAgICAgICAqXG4gICAgICAgICAqIEluIHNvbWUgb2NjYXNpb25zIHNvbWUgY29udGFpbmVyIGVsZW1lbnRzIGNvdmVyIHRoZSBjb21wbGV0ZSBtb2JpbGUgc2NyZWVuIGJ1dCBkdWUgdG9cbiAgICAgICAgICogYnVnZ3kgYmVoYXZpb3IgdGhlIHNjcm9sbGluZyBvZiB0aGUgcGFnZSBpcyBzdGlsbCBhdmFpbGFibGUuIFVzZSB0aGlzIG1ldGhvZCB0byBoaWRlIHRoZVxuICAgICAgICAgKiBwYWdlIGNvbnRlbnQgYW5kIHNvbHZlIHRoZSBzY3JvbGxpbmcgcHJvYmxlbS5cbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtib29sfSBzdGF0ZSBTZXRzIHdoZXRoZXIgdGhlIGNvbnRlbnQgaXMgdmlzaWJsZSBvciBub3QuXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3RvZ2dsZUNvbnRlbnRWaXNpYmlsaXR5ID0gZnVuY3Rpb24gKHN0YXRlKSB7XG4gICAgICAgICAgICB2YXIgJGNvbnRlbnQgPSAkKCcjd3JhcHBlciwgI2Zvb3RlcicpLFxuICAgICAgICAgICAgICAgICRkb2N1bWVudCA9ICQoZG9jdW1lbnQpO1xuXG4gICAgICAgICAgICBpZiAoc3RhdGUpIHtcbiAgICAgICAgICAgICAgICAkY29udGVudC5zaG93KCk7XG4gICAgICAgICAgICAgICAgJGRvY3VtZW50LnNjcm9sbFRvcChzY3JvbGxUb3ApO1xuICAgICAgICAgICAgICAgICRkb2N1bWVudC5zY3JvbGxMZWZ0KHNjcm9sbExlZnQpO1xuICAgICAgICAgICAgICAgIHNjcm9sbFRvcCA9IHNjcm9sbExlZnQgPSBudWxsOyAvLyByZXNldFxuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBpZiAoIXNjcm9sbFRvcCkge1xuICAgICAgICAgICAgICAgICAgICBzY3JvbGxUb3AgPSAkZG9jdW1lbnQuc2Nyb2xsVG9wKCk7IC8vIGJhY2t1cFxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBpZiAoIXNjcm9sbExlZnQpIHtcbiAgICAgICAgICAgICAgICAgICAgc2Nyb2xsTGVmdCA9ICRkb2N1bWVudC5zY3JvbGxMZWZ0KCk7IC8vIGJhY2t1cFxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAkY29udGVudC5oaWRlKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIGNsaWNrIGV2ZW50IG9uIHRoZVxuICAgICAgICAgKiBidXR0b25zLiBJbiBjYXNlIHRoZSBidXR0b24gaXMgYSBtZW51IGJ1dHRvblxuICAgICAgICAgKiB0aGUgY29ycmVzcG9uZGluZyBtZW51IGVudHJ5IGdldHMgc2hvd24sIHdoaWxlXG4gICAgICAgICAqIGFsbCBvdGhlciBtZW51cyBnZXR0aW5nIGNsb3NlZFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9jbGlja0hhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgIGJ1dHRvbkRhdGEgPSAkc2VsZi5wYXJzZU1vZHVsZURhdGEoJ21vYmlsZV9tZW51Jyk7XG5cbiAgICAgICAgICAgIGlmIChidXR0b25EYXRhLnRhcmdldCkge1xuICAgICAgICAgICAgICAgIC8vIFNldCB0aGUgY2xhc3NlcyBmb3IgdGhlIG9wZW4gLyBjbG9zZSBzdGF0ZSBvZiB0aGUgbWVudVxuICAgICAgICAgICAgICAgIF9zZXRDbGFzc2VzKGJ1dHRvbkRhdGEpO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChidXR0b25EYXRhLmxvY2F0aW9uKSB7XG4gICAgICAgICAgICAgICAgLy8gT3BlbiBhIGxpbmtcbiAgICAgICAgICAgICAgICBsb2NhdGlvbi5ocmVmID0gYnV0dG9uRGF0YS5sb2NhdGlvbjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogRXZlbnQgaGFuZGxlciB0aGF0IGxpc3RlbnMgb24gdGhlXG4gICAgICAgICAqIFwiYnJlYWtwb2ludFwiIGV2ZW50LiBPbiBldmVyeSBicmVha3BvaW50XG4gICAgICAgICAqIHRoZSBmdW5jdGlvbiBjaGVja3MgaWYgdGhlcmUgaXMgYSBzd2l0Y2hcbiAgICAgICAgICogZnJvbSBkZXNrdG9wIHRvIG1vYmlsZS4gSW4gY2FzZSB0aGF0XG4gICAgICAgICAqIGhhcHBlbnMsIGFsbCBvcGVuZWQgbWVudXMgZ2V0dGluZyBjbG9zZWRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZCAgICAgICBEYXRhIG9iamVjdCB0aGF0IGNvbnRhaW5zIHRoZSBpbmZvcm1hdGlvbiBiZWxvbmdpbmcgdG8gdGhlIGN1cnJlbnQgYnJlYWtwb2ludFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9icmVha3BvaW50SGFuZGxlciA9IGZ1bmN0aW9uIChlLCBkKSB7XG4gICAgICAgICAgICBpZiAoZC5pZCA+IG9wdGlvbnMuYnJlYWtwb2ludCAmJiBtb2JpbGUpIHtcbiAgICAgICAgICAgICAgICAvLyBDbG9zZSBhbGwgbWVudXMgb24gc3dpdGNoIHRvIGRlc2t0b3Agdmlld1xuICAgICAgICAgICAgICAgIF9zZXRDbGFzc2VzKG51bGwpO1xuICAgICAgICAgICAgICAgICQoJyN3cmFwcGVyLCAjZm9vdGVyJykuc2hvdygpO1xuICAgICAgICAgICAgICAgIG1vYmlsZSA9IGZhbHNlO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChkLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludCAmJiAhbW9iaWxlKSB7XG4gICAgICAgICAgICAgICAgLy8gQ2xvc2UgYWxsIG1lbnVzIG9uIHN3aXRjaCB0byBtb2JpbGUgdmlld1xuICAgICAgICAgICAgICAgIF9zZXRDbGFzc2VzKG51bGwpO1xuICAgICAgICAgICAgICAgIG1vYmlsZSA9IHRydWU7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIE5hdmJhciBUb3BiYXIgSXRlbSBDbGlja1xuICAgICAgICAgKlxuICAgICAgICAgKiBUaGlzIGhhbmRsZXIgbXVzdCBjbG9zZSB0aGUgb3RoZXIgb3BlbmVkIGZyYW1lcyBiZWNhdXNlIG9ubHkgb25lIGl0ZW0gc2hvdWxkIGJlIHZpc2libGUuXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2NsaWNrVG9wQmFySXRlbUhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBpZiAoJCh0aGlzKS5wYXJlbnQoKS5oYXNDbGFzcygnb3BlbicpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgJCgnLm5hdmJhci1jYXRlZ29yaWVzJykuZmluZCgnLm5hdmJhci10b3BiYXItaXRlbS5vcGVuJykucmVtb3ZlQ2xhc3MoJ29wZW4nKTtcbiAgICAgICAgICAgICQoJyNjYXRlZ29yaWVzIC5uYXZiYXItY29sbGFwc2U6Zmlyc3QnKS5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICBzY3JvbGxUb3A6ICQodGhpcykucGFyZW50KCkucG9zaXRpb24oKS50b3AgKyAkKHRoaXMpXG4gICAgICAgICAgICAgICAgICAgIC5wYXJlbnQoKVxuICAgICAgICAgICAgICAgICAgICAuaGVpZ2h0KCkgLSAkKCcjaGVhZGVyIC5uYXZiYXItaGVhZGVyJykuaGVpZ2h0KClcbiAgICAgICAgICAgIH0sIDUwMCk7XG4gICAgICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblxuICAgICAgICAgICAgbW9iaWxlID0ganNlLmxpYnMudGhlbWUucmVzcG9uc2l2ZS5icmVha3BvaW50KCkuaWQgPD0gb3B0aW9ucy5icmVha3BvaW50O1xuICAgICAgICAgICAgJGJ1dHRvbnMgPSAkdGhpcy5maW5kKCdidXR0b24nKTtcblxuICAgICAgICAgICAgJGJvZHkub24oanNlLmxpYnMudGhlbWUuZXZlbnRzLkJSRUFLUE9JTlQoKSwgX2JyZWFrcG9pbnRIYW5kbGVyKTtcbiAgICAgICAgICAgICQoJy5uYXZiYXItY2F0ZWdvcmllcycpLm9uKCdtb3VzZXVwJywgJy5uYXZiYXItdG9wYmFyLWl0ZW0gPiBhJywgX2NsaWNrVG9wQmFySXRlbUhhbmRsZXIpO1xuICAgICAgICAgICAgJHRoaXMub24oJ2NsaWNrJywgJ2J1dHRvbicsIF9jbGlja0hhbmRsZXIpO1xuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

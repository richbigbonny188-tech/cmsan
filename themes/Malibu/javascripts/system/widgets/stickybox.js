'use strict';

/* --------------------------------------------------------------
 stickybox.js 2017-01-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that keeps an element between the two elements in view
 */
gambio.widgets.module('stickybox', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $window = $(window),
        $header = null,
        $footer = null,
        $outerWrapper = null,
        bottom = null,
        top = null,
        elementHeight = null,
        elementWidth = null,
        elementOffset = null,
        fixedTopPosition = null,
        documentHeight = null,
        headerFixed = null,
        css = null,
        timer = null,
        initialOffset = null,
        initialTop = null,
        initialHeader = null,
        initialMarginTop = null,
        skipped = 0,
        checkFit = true,
        lastFit = null,
        defaults = {
        breakpoint: 60, // The breakpoint, since which this script calculates the position
        outerWrapper: '#outer-wrapper', // Selector to set the header's margin top
        header: 'header', // Selector to set the header height
        footer: '.product-info-listings, footer', // Selector to set the footer height
        offsetTopReferenceSelector: '#breadcrumb_navi, .product-info', // Reference selector to set the top position of the sticky box
        marginTop: 15, // Add a space between header/footer and content container
        marginBottom: 0, // Add a space between header/footer and content container
        zIndex: 1000, // Sets the z-index in fixed mode
        cpuOptimization: false, // If set to true, the number of events in "smoothness" gets skipped
        smoothness: 10, // The higher the value, the more scroll events gets skipped
        smoothnessDelay: 150, // The delay after the last scroll event the cpu optimization fires an recalculate event
        stage: '#stage', // Selector to set teaser slider height
        errorBox: 'table.box-error, table.box-warning' // Selector to set error box height
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Calculates all necessary positions,
     * offsets and dimensions
     * @private
     */
    var _calculateDimensions = function _calculateDimensions() {
        top = $header.outerHeight();
        bottom = $footer.offset().top;
        top += options.marginTop;
        bottom -= options.marginBottom;

        elementHeight = $this.outerHeight();
        elementWidth = $this.outerWidth();
        elementOffset = elementOffset || $this.offset();

        documentHeight = $(document).height();

        var cssTop = options.marginTop;
        if (headerFixed) {
            cssTop = top;
        }

        css = {
            'position': 'fixed',
            'top': cssTop + 'px',
            'left': elementOffset.left + 'px',
            'z-index': options.zIndex,
            'width': elementWidth
        };
    };

    /**
     * Checks if the available space between
     * the header & footer is enough to set
     * the container sticky
     * @return         {boolean}           If true, there is enough space to set it sticky
     * @private
     */
    var _fitInView = function _fitInView() {

        if (checkFit) {
            checkFit = false;

            _resetPosition();

            window.setTimeout(function () {
                checkFit = true;
            }, 100);

            lastFit = document.documentElement.clientHeight - document.getElementById('header').offsetHeight;
        }

        return lastFit > elementHeight;
    };

    /**
     * Helper function that gets called on scroll. In case
     * the content could be displayed without being sticky,
     * the sticky-styles were removed, else a check is
     * performed if the top of the element needs to be
     * adjusted in case that it would overlap with the
     * footer otherwise.
     * @param       {number}     scrollPosition      Current scroll position of the page
     * @private
     */
    var _calcPosition = function _calcPosition(scrollPosition) {
        if (headerFixed) {
            var elementBottom = scrollPosition + top + elementHeight + options.marginBottom,
                overlapping = elementBottom - bottom,
                currentTop = parseFloat($this.css('top')),
                newTop = initialTop - (initialHeader - top) + scrollPosition;

            newTop = newTop < initialTop ? initialTop : newTop;
            newTop -= overlapping - top;

            if (top + scrollPosition <= elementOffset.top) {
                _resetPosition();
            } else if (overlapping > 0) {
                if (bottom - scrollPosition < elementHeight + initialHeader - initialTop) {
                    newTop = bottom - elementHeight - initialHeader + initialTop - initialMarginTop;
                    _resetPosition();
                    $this.css({ top: newTop + 'px' });
                } else if (Math.abs(currentTop - newTop) >= 0.5) {
                    _resetPosition();
                    $this.css({ top: newTop + 'px' });
                }
            } else if ($this.css('position') !== 'fixed' || $this.css('top') !== css.top) {
                $this.css(css);
            }
        } else {
            if (scrollPosition <= elementOffset.top - options.marginTop) {
                _resetPosition();
            } else if (bottom - scrollPosition + options.marginTop < elementHeight - initialTop - options.marginTop) {
                newTop = bottom - elementHeight - initialHeader + initialTop - initialMarginTop;
                _resetPosition();
                $this.css({ top: newTop + 'px' });
            } else if ($this.css('position') !== 'fixed' || $this.css('top') !== css.top) {
                $this.css(css);
            }
        }
    };

    /**
     * In case that the CPU optimization
     * is enabled, skipp a certain count
     * of scroll events before recalculating
     * the position.
     * @return     {boolean}           True if this event shall be processed
     * @private
     */
    var _cpuOptimization = function _cpuOptimization() {
        skipped += 1;
        clearTimeout(timer);
        if (skipped < options.smoothness) {
            timer = setTimeout(function () {
                $window.trigger('scroll.stickybox', true);
            }, options.smoothnessDelay);
            return false;
        }
        skipped = 0;
        return true;
    };

    /**
     * Set the initial top position of the sticky box. A correction is necessary, if the breadcrumb is longer than
     * one line.
     *
     * @private
     */
    var _fixInitialTopPosition = function _fixInitialTopPosition() {
        var offsetTop = $this.offset().top,
            targetOffsetTop = $(options.offsetTopReferenceSelector).first().offset().top,
            offsetDifference = offsetTop - targetOffsetTop,
            topPosition = parseFloat($this.css('top'));

        fixedTopPosition = topPosition - offsetDifference;

        _resetPosition();
    };

    /**
     * Restore initial position of the sticky box by removing its style attribute and setting the fixed top position.
     *
     * @private
     */
    var _resetPosition = function _resetPosition() {
        $this.removeAttr('style');

        if (jse.libs.theme.responsive.breakpoint().name === 'md' || jse.libs.theme.responsive.breakpoint().name === 'lg') {
            $this.css('top', fixedTopPosition + 'px');
        } else {
            $this.css('top', '');
        }
    };

    // ########## EVENT HANDLER ##########

    /**
     * Event handler for the scroll event. It gets the
     * upper border of the content element and calls
     * individual methods depending on the sticky state.
     * To perform better on low end CPUs it checks if
     * scroll events shall be skipped.
     * @private
     */
    var _checkPosition = function _checkPosition(e, d) {

        if (options.cpuOptimization && !d && !_cpuOptimization()) {
            return true;
        }

        if (jse.libs.theme.responsive.breakpoint().id > options.breakpoint) {
            _calculateDimensions();
            var scrollPosition = $window.scrollTop(),
                fit = _fitInView();

            if (fit) {
                _calcPosition(scrollPosition);
            }
        }
    };

    /**
     * Handler for the resize event. On browser
     * resize it is resetting the state to calculate
     * a new position
     * @private
     */
    var _resizeHandler = function _resizeHandler() {
        _resetPosition();
        elementOffset = null;
        skipped = 0;
        initialOffset = $this.offset().top;

        _checkPosition();
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        var sliderHeight = 0,
            errorBoxHeight = 0,
            marginTop = 0,
            marginBottom = 0;

        $outerWrapper = $(options.outerWrapper);
        $header = $(options.header);
        $footer = $(options.footer);

        if ($(options.stage).length > 0) {
            sliderHeight = $(options.stage).outerHeight();
        }

        $(options.errorBox).each(function () {
            marginTop = parseInt($(this).css('margin-top'), 10);
            marginBottom = parseInt($(this).css('margin-bottom'), 10);

            errorBoxHeight += $(this).outerHeight();
            errorBoxHeight += marginTop;
            errorBoxHeight += marginBottom;
        });

        var errorBoxElements = $(options.errorBox).length;

        if (errorBoxElements >= 2) {
            errorBoxHeight = errorBoxHeight - marginTop * (errorBoxElements - 1);
        }

        _fixInitialTopPosition();

        initialOffset = $this.offset().top;
        initialTop = parseFloat($this.css('top'));
        initialHeader = $header.outerHeight() + options.marginTop + sliderHeight + errorBoxHeight;
        initialMarginTop = parseFloat($outerWrapper.css('margin-top').replace(/[^\d]/, ''));
        headerFixed = $header.css('position') === 'fixed';

        if (!jse.core.config.get('mobile')) {
            _checkPosition();

            $window.on('resize', _resizeHandler).on('scroll.stickybox', _checkPosition).on(jse.libs.theme.events.REPOSITIONS_STICKYBOX(), _resizeHandler);
        } else {
            $('body').on(jse.libs.theme.events.BREAKPOINT(), _fixInitialTopPosition);
        }

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc3RpY2t5Ym94LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJHdpbmRvdyIsIndpbmRvdyIsIiRoZWFkZXIiLCIkZm9vdGVyIiwiJG91dGVyV3JhcHBlciIsImJvdHRvbSIsInRvcCIsImVsZW1lbnRIZWlnaHQiLCJlbGVtZW50V2lkdGgiLCJlbGVtZW50T2Zmc2V0IiwiZml4ZWRUb3BQb3NpdGlvbiIsImRvY3VtZW50SGVpZ2h0IiwiaGVhZGVyRml4ZWQiLCJjc3MiLCJ0aW1lciIsImluaXRpYWxPZmZzZXQiLCJpbml0aWFsVG9wIiwiaW5pdGlhbEhlYWRlciIsImluaXRpYWxNYXJnaW5Ub3AiLCJza2lwcGVkIiwiY2hlY2tGaXQiLCJsYXN0Rml0IiwiZGVmYXVsdHMiLCJicmVha3BvaW50Iiwib3V0ZXJXcmFwcGVyIiwiaGVhZGVyIiwiZm9vdGVyIiwib2Zmc2V0VG9wUmVmZXJlbmNlU2VsZWN0b3IiLCJtYXJnaW5Ub3AiLCJtYXJnaW5Cb3R0b20iLCJ6SW5kZXgiLCJjcHVPcHRpbWl6YXRpb24iLCJzbW9vdGhuZXNzIiwic21vb3RobmVzc0RlbGF5Iiwic3RhZ2UiLCJlcnJvckJveCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfY2FsY3VsYXRlRGltZW5zaW9ucyIsIm91dGVySGVpZ2h0Iiwib2Zmc2V0Iiwib3V0ZXJXaWR0aCIsImRvY3VtZW50IiwiaGVpZ2h0IiwiY3NzVG9wIiwibGVmdCIsIl9maXRJblZpZXciLCJfcmVzZXRQb3NpdGlvbiIsInNldFRpbWVvdXQiLCJkb2N1bWVudEVsZW1lbnQiLCJjbGllbnRIZWlnaHQiLCJnZXRFbGVtZW50QnlJZCIsIm9mZnNldEhlaWdodCIsIl9jYWxjUG9zaXRpb24iLCJzY3JvbGxQb3NpdGlvbiIsImVsZW1lbnRCb3R0b20iLCJvdmVybGFwcGluZyIsImN1cnJlbnRUb3AiLCJwYXJzZUZsb2F0IiwibmV3VG9wIiwiTWF0aCIsImFicyIsIl9jcHVPcHRpbWl6YXRpb24iLCJjbGVhclRpbWVvdXQiLCJ0cmlnZ2VyIiwiX2ZpeEluaXRpYWxUb3BQb3NpdGlvbiIsIm9mZnNldFRvcCIsInRhcmdldE9mZnNldFRvcCIsImZpcnN0Iiwib2Zmc2V0RGlmZmVyZW5jZSIsInRvcFBvc2l0aW9uIiwicmVtb3ZlQXR0ciIsImpzZSIsImxpYnMiLCJ0aGVtZSIsInJlc3BvbnNpdmUiLCJuYW1lIiwiX2NoZWNrUG9zaXRpb24iLCJlIiwiZCIsImlkIiwic2Nyb2xsVG9wIiwiZml0IiwiX3Jlc2l6ZUhhbmRsZXIiLCJpbml0IiwiZG9uZSIsInNsaWRlckhlaWdodCIsImVycm9yQm94SGVpZ2h0IiwibGVuZ3RoIiwiZWFjaCIsInBhcnNlSW50IiwiZXJyb3JCb3hFbGVtZW50cyIsInJlcGxhY2UiLCJjb3JlIiwiY29uZmlnIiwiZ2V0Iiwib24iLCJldmVudHMiLCJSRVBPU0lUSU9OU19TVElDS1lCT1giLCJCUkVBS1BPSU5UIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSxXQURKLEVBR0ksQ0FDSUYsT0FBT0csTUFBUCxHQUFnQixjQURwQixFQUVJSCxPQUFPRyxNQUFQLEdBQWdCLGtCQUZwQixDQUhKLEVBUUksVUFBVUMsSUFBVixFQUFnQjs7QUFFWjs7QUFFUjs7QUFFUSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFVBQVVELEVBQUVFLE1BQUYsQ0FEZDtBQUFBLFFBRUlDLFVBQVUsSUFGZDtBQUFBLFFBR0lDLFVBQVUsSUFIZDtBQUFBLFFBSUlDLGdCQUFnQixJQUpwQjtBQUFBLFFBS0lDLFNBQVMsSUFMYjtBQUFBLFFBTUlDLE1BQU0sSUFOVjtBQUFBLFFBT0lDLGdCQUFnQixJQVBwQjtBQUFBLFFBUUlDLGVBQWUsSUFSbkI7QUFBQSxRQVNJQyxnQkFBZ0IsSUFUcEI7QUFBQSxRQVVJQyxtQkFBbUIsSUFWdkI7QUFBQSxRQVdJ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Q0FBYjtBQUNBSSx3QkFBZ0JmLFFBQVFxQyxXQUFSLEtBQXdCSCxRQUFRUixTQUFoQyxHQUE0QzBELFlBQTVDLEdBQTJEQyxjQUEzRTtBQUNBckUsMkJBQW1Cd0MsV0FBV3RELGNBQWNTLEdBQWQsQ0FBa0IsWUFBbEIsRUFBZ0MrRSxPQUFoQyxDQUF3QyxPQUF4QyxFQUFpRCxFQUFqRCxDQUFYLENBQW5CO0FBQ0FoRixzQkFBY1YsUUFBUVcsR0FBUixDQUFZLFVBQVosTUFBNEIsT0FBMUM7O0FBRUEsWUFBSSxDQUFDMkQsSUFBSXFCLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsQ0FBTCxFQUFvQztBQUNoQ2xCOztBQUVBN0Usb0JBQ0tnRyxFQURMLENBQ1EsUUFEUixFQUNrQmIsY0FEbEIsRUFFS2EsRUFGTCxDQUVRLGtCQUZSLEVBRTRCbkIsY0FGNUIsRUFHS21CLEVBSEwsQ0FHUXhCLElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFldUIsTUFBZixDQUFzQkMscUJBQXRCLEVBSFIsRUFHdURmLGNBSHZEO0FBSUgsU0FQRCxNQU9PO0FBQ0hwRixjQUFFLE1BQUYsRUFBVWlHLEVBQVYsQ0FBYXhCLElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFldUIsTUFBZixDQUFzQkUsVUFBdEIsRUFBYixFQUFpRGxDLHNCQUFqRDtBQUNIOztBQUVEb0I7QUFDSCxLQWpERDs7QUFtREE7QUFDQSxXQUFPMUYsTUFBUDtBQUNILENBNVRMIiwiZmlsZSI6IndpZGdldHMvc3RpY2t5Ym94LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzdGlja3lib3guanMgMjAxNy0wMS0xMlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTcgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogV2lkZ2V0IHRoYXQga2VlcHMgYW4gZWxlbWVudCBiZXR3ZWVuIHRoZSB0d28gZWxlbWVudHMgaW4gdmlld1xuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ3N0aWNreWJveCcsXG5cbiAgICBbXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJyxcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9yZXNwb25zaXZlJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICR3aW5kb3cgPSAkKHdpbmRvdyksXG4gICAgICAgICAgICAkaGVhZGVyID0gbnVsbCxcbiAgICAgICAgICAgICRmb290ZXIgPSBudWxsLFxuICAgICAgICAgICAgJG91dGVyV3JhcHBlciA9IG51bGwsXG4gICAgICAgICAgICBib3R0b20gPSBudWxsLFxuICAgICAgICAgICAgdG9wID0gbnVsbCxcbiAgICAgICAgICAgIGVsZW1lbnRIZWlnaHQgPSBudWxsLFxuICAgICAgICAgICAgZWxlbWVudFdpZHRoID0gbnVsbCxcbiAgICAgICAgICAgIGVsZW1lbnRPZmZzZXQgPSBudWxsLFxuICAgICAgICAgICAgZml4ZWRUb3BQb3NpdGlvbiA9IG51bGwsXG4gICAgICAgICAgICBkb2N1bWVudEhlaWdodCA9IG51bGwsXG4gICAgICAgICAgICBoZWFkZXJGaXhlZCA9IG51bGwsXG4gICAgICAgICAgICBjc3MgPSBudWxsLFxuICAgICAgICAgICAgdGltZXIgPSBudWxsLFxuICAgICAgICAgICAgaW5pdGlhbE9mZnNldCA9IG51bGwsXG4gICAgICAgICAgICBpbml0aWFsVG9wID0gbnVsbCxcbiAgICAgICAgICAgIGluaXRpYWxIZWFkZXIgPSBudWxsLFxuICAgICAgICAgICAgaW5pdGlhbE1hcmdpblRvcCA9IG51bGwsXG4gICAgICAgICAgICBza2lwcGVkID0gMCxcbiAgICAgICAgICAgIGNoZWNrRml0ID0gdHJ1ZSxcbiAgICAgICAgICAgIGxhc3RGaXQgPSBudWxsLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAgICAgYnJlYWtwb2ludDogNjAsIC8vIFRoZSBicmVha3BvaW50LCBzaW5jZSB3aGljaCB0aGlzIHNjcmlwdCBjYWxjdWxhdGVzIHRoZSBwb3NpdGlvblxuICAgICAgICAgICAgICAgIG91dGVyV3JhcHBlcjogJyNvdXRlci13cmFwcGVyJywgLy8gU2VsZWN0b3IgdG8gc2V0IHRoZSBoZWFkZXIncyBtYXJnaW4gdG9wXG4gICAgICAgICAgICAgICAgaGVhZGVyOiAnaGVhZGVyJywgLy8gU2VsZWN0b3IgdG8gc2V0IHRoZSBoZWFkZXIgaGVpZ2h0XG4gICAgICAgICAgICAgICAgZm9vdGVyOiAnLnByb2R1Y3QtaW5mby1saXN0aW5ncywgZm9vdGVyJywgLy8gU2VsZWN0b3IgdG8gc2V0IHRoZSBmb290ZXIgaGVpZ2h0XG4gICAgICAgICAgICAgICAgb2Zmc2V0VG9wUmVmZXJlbmNlU2VsZWN0b3I6ICcjYnJlYWRjcnVtYl9uYXZpLCAucHJvZHVjdC1pbmZvJywgLy8gUmVmZXJlbmNlIHNlbGVjdG9yIHRvIHNldCB0aGUgdG9wIHBvc2l0aW9uIG9mIHRoZSBzdGlja3kgYm94XG4gICAgICAgICAgICAgICAgbWFyZ2luVG9wOiAxNSwgLy8gQWRkIGEgc3BhY2UgYmV0d2VlbiBoZWFkZXIvZm9vdGVyIGFuZCBjb250ZW50IGNvbnRhaW5lclxuICAgICAgICAgICAgICAgIG1hcmdpbkJvdHRvbTogMCwgLy8gQWRkIGEgc3BhY2UgYmV0d2VlbiBoZWFkZXIvZm9vdGVyIGFuZCBjb250ZW50IGNvbnRhaW5lclxuICAgICAgICAgICAgICAgIHpJbmRleDogMTAwMCwgLy8gU2V0cyB0aGUgei1pbmRleCBpbiBmaXhlZCBtb2RlXG4gICAgICAgICAgICAgICAgY3B1T3B0aW1pemF0aW9uOiBmYWxzZSwgLy8gSWYgc2V0IHRvIHRydWUsIHRoZSBudW1iZXIgb2YgZXZlbnRzIGluIFwic21vb3RobmVzc1wiIGdldHMgc2tpcHBlZFxuICAgICAgICAgICAgICAgIHNtb290aG5lc3M6IDEwLCAvLyBUaGUgaGlnaGVyIHRoZSB2YWx1ZSwgdGhlIG1vcmUgc2Nyb2xsIGV2ZW50cyBnZXRzIHNraXBwZWRcbiAgICAgICAgICAgICAgICBzbW9vdGhuZXNzRGVsYXk6IDE1MCwgLy8gVGhlIGRlbGF5IGFmdGVyIHRoZSBsYXN0IHNjcm9sbCBldmVudCB0aGUgY3B1IG9wdGltaXphdGlvbiBmaXJlcyBhbiByZWNhbGN1bGF0ZSBldmVudFxuICAgICAgICAgICAgICAgIHN0YWdlOiAnI3N0YWdlJywgLy8gU2VsZWN0b3IgdG8gc2V0IHRlYXNlciBzbGlkZXIgaGVpZ2h0XG4gICAgICAgICAgICAgICAgZXJyb3JCb3g6ICd0YWJsZS5ib3gtZXJyb3IsIHRhYmxlLmJveC13YXJuaW5nJyAvLyBTZWxlY3RvciB0byBzZXQgZXJyb3IgYm94IGhlaWdodFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIENhbGN1bGF0ZXMgYWxsIG5lY2Vzc2FyeSBwb3NpdGlvbnMsXG4gICAgICAgICAqIG9mZnNldHMgYW5kIGRpbWVuc2lvbnNcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2FsY3VsYXRlRGltZW5zaW9ucyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRvcCA9ICRoZWFkZXIub3V0ZXJIZWlnaHQoKTtcbiAgICAgICAgICAgIGJvdHRvbSA9ICRmb290ZXIub2Zmc2V0KCkudG9wO1xuICAgICAgICAgICAgdG9wICs9IG9wdGlvbnMubWFyZ2luVG9wO1xuICAgICAgICAgICAgYm90dG9tIC09IG9wdGlvbnMubWFyZ2luQm90dG9tO1xuXG4gICAgICAgICAgICBlbGVtZW50SGVpZ2h0ID0gJHRoaXMub3V0ZXJIZWlnaHQoKTtcbiAgICAgICAgICAgIGVsZW1lbnRXaWR0aCA9ICR0aGlzLm91dGVyV2lkdGgoKTtcbiAgICAgICAgICAgIGVsZW1lbnRPZmZzZXQgPSBlbGVtZW50T2Zmc2V0IHx8ICR0aGlzLm9mZnNldCgpO1xuXG4gICAgICAgICAgICBkb2N1bWVudEhlaWdodCA9ICQoZG9jdW1lbnQpLmhlaWdodCgpO1xuXG4gICAgICAgICAgICB2YXIgY3NzVG9wID0gb3B0aW9ucy5tYXJnaW5Ub3A7XG4gICAgICAgICAgICBpZiAoaGVhZGVyRml4ZWQpIHtcbiAgICAgICAgICAgICAgICBjc3NUb3AgPSB0b3A7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNzcyA9IHtcbiAgICAgICAgICAgICAgICAncG9zaXRpb24nOiAnZml4ZWQnLFxuICAgICAgICAgICAgICAgICd0b3AnOiBjc3NUb3AgKyAncHgnLFxuICAgICAgICAgICAgICAgICdsZWZ0JzogZWxlbWVudE9mZnNldC5sZWZ0ICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAnei1pbmRleCc6IG9wdGlvbnMuekluZGV4LFxuICAgICAgICAgICAgICAgICd3aWR0aCc6IGVsZW1lbnRXaWR0aFxuICAgICAgICAgICAgfTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogQ2hlY2tzIGlmIHRoZSBhdmFpbGFibGUgc3BhY2UgYmV0d2VlblxuICAgICAgICAgKiB0aGUgaGVhZGVyICYgZm9vdGVyIGlzIGVub3VnaCB0byBzZXRcbiAgICAgICAgICogdGhlIGNvbnRhaW5lciBzdGlja3lcbiAgICAgICAgICogQHJldHVybiAgICAgICAgIHtib29sZWFufSAgICAgICAgICAgSWYgdHJ1ZSwgdGhlcmUgaXMgZW5vdWdoIHNwYWNlIHRvIHNldCBpdCBzdGlja3lcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfZml0SW5WaWV3ID0gZnVuY3Rpb24gKCkge1xuXG4gICAgICAgICAgICBpZiAoY2hlY2tGaXQpIHtcbiAgICAgICAgICAgICAgICBjaGVja0ZpdCA9IGZhbHNlO1xuXG4gICAgICAgICAgICAgICAgX3Jlc2V0UG9zaXRpb24oKTtcblxuICAgICAgICAgICAgICAgIHdpbmRvdy5zZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgY2hlY2tGaXQgPSB0cnVlO1xuICAgICAgICAgICAgICAgIH0sIDEwMCk7XG5cbiAgICAgICAgICAgICAgICBsYXN0Rml0ID0gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LmNsaWVudEhlaWdodCAtIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdoZWFkZXInKS5vZmZzZXRIZWlnaHQ7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiBsYXN0Rml0ID4gZWxlbWVudEhlaWdodDtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgZ2V0cyBjYWxsZWQgb24gc2Nyb2xsLiBJbiBjYXNlXG4gICAgICAgICAqIHRoZSBjb250ZW50IGNvdWxkIGJlIGRpc3BsYXllZCB3aXRob3V0IGJlaW5nIHN0aWNreSxcbiAgICAgICAgICogdGhlIHN0aWNreS1zdHlsZXMgd2VyZSByZW1vdmVkLCBlbHNlIGEgY2hlY2sgaXNcbiAgICAgICAgICogcGVyZm9ybWVkIGlmIHRoZSB0b3Agb2YgdGhlIGVsZW1lbnQgbmVlZHMgdG8gYmVcbiAgICAgICAgICogYWRqdXN0ZWQgaW4gY2FzZSB0aGF0IGl0IHdvdWxkIG92ZXJsYXAgd2l0aCB0aGVcbiAgICAgICAgICogZm9vdGVyIG90aGVyd2lzZS5cbiAgICAgICAgICogQHBhcmFtICAgICAgIHtudW1iZXJ9ICAgICBzY3JvbGxQb3NpdGlvbiAgICAgIEN1cnJlbnQgc2Nyb2xsIHBvc2l0aW9uIG9mIHRoZSBwYWdlXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2NhbGNQb3NpdGlvbiA9IGZ1bmN0aW9uIChzY3JvbGxQb3NpdGlvbikge1xuICAgICAgICAgICAgaWYgKGhlYWRlckZpeGVkKSB7XG4gICAgICAgICAgICAgICAgdmFyIGVsZW1lbnRCb3R0b20gPSBzY3JvbGxQb3NpdGlvbiArIHRvcCArIGVsZW1lbnRIZWlnaHQgKyBvcHRpb25zLm1hcmdpbkJvdHRvbSxcbiAgICAgICAgICAgICAgICAgICAgb3ZlcmxhcHBpbmcgPSBlbGVtZW50Qm90dG9tIC0gYm90dG9tLFxuICAgICAgICAgICAgICAgICAgICBjdXJyZW50VG9wID0gcGFyc2VGbG9hdCgkdGhpcy5jc3MoJ3RvcCcpKSxcbiAgICAgICAgICAgICAgICAgICAgbmV3VG9wID0gaW5pdGlhbFRvcCAtIChpbml0aWFsSGVhZGVyIC0gdG9wKSArIHNjcm9sbFBvc2l0aW9uO1xuXG4gICAgICAgICAgICAgICAgbmV3VG9wID0gKG5ld1RvcCA8IGluaXRpYWxUb3ApID8gaW5pdGlhbFRvcCA6IG5ld1RvcDtcbiAgICAgICAgICAgICAgICBuZXdUb3AgLT0gb3ZlcmxhcHBpbmcgLSB0b3A7XG5cbiAgICAgICAgICAgICAgICBpZiAodG9wICsgc2Nyb2xsUG9zaXRpb24gPD0gZWxlbWVudE9mZnNldC50b3ApIHtcbiAgICAgICAgICAgICAgICAgICAgX3Jlc2V0UG9zaXRpb24oKTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKG92ZXJsYXBwaW5nID4gMCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoYm90dG9tIC0gc2Nyb2xsUG9zaXRpb24gPCBlbGVtZW50SGVpZ2h0ICsgaW5pdGlhbEhlYWRlciAtIGluaXRpYWxUb3ApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld1RvcCA9IGJvdHRvbSAtIGVsZW1lbnRIZWlnaHQgLSBpbml0aWFsSGVhZGVyICsgaW5pdGlhbFRvcCAtIGluaXRpYWxNYXJnaW5Ub3A7XG4gICAgICAgICAgICAgICAgICAgICAgICBfcmVzZXRQb3NpdGlvbigpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXMuY3NzKHt0b3A6IG5ld1RvcCArICdweCd9KTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmIChNYXRoLmFicyhjdXJyZW50VG9wIC0gbmV3VG9wKSA+PSAwLjUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIF9yZXNldFBvc2l0aW9uKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkdGhpcy5jc3Moe3RvcDogbmV3VG9wICsgJ3B4J30pO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmICgkdGhpcy5jc3MoJ3Bvc2l0aW9uJykgIT09ICdmaXhlZCcgfHwgJHRoaXMuY3NzKCd0b3AnKSAhPT0gY3NzLnRvcCkge1xuICAgICAgICAgICAgICAgICAgICAkdGhpcy5jc3MoY3NzKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIGlmIChzY3JvbGxQb3NpdGlvbiA8PSBlbGVtZW50T2Zmc2V0LnRvcCAtIG9wdGlvbnMubWFyZ2luVG9wKSB7XG4gICAgICAgICAgICAgICAgICAgIF9yZXNldFBvc2l0aW9uKCk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmIChib3R0b20gLSBzY3JvbGxQb3NpdGlvbiArIG9wdGlvbnMubWFyZ2luVG9wIDwgZWxlbWVudEhlaWdodCAtIGluaXRpYWxUb3AgLSBvcHRpb25zLm1hcmdpblRvcCkge1xuICAgICAgICAgICAgICAgICAgICBuZXdUb3AgPSBib3R0b20gLSBlbGVtZW50SGVpZ2h0IC0gaW5pdGlhbEhlYWRlciArIGluaXRpYWxUb3AgLSBpbml0aWFsTWFyZ2luVG9wO1xuICAgICAgICAgICAgICAgICAgICBfcmVzZXRQb3NpdGlvbigpO1xuICAgICAgICAgICAgICAgICAgICAkdGhpcy5jc3Moe3RvcDogbmV3VG9wICsgJ3B4J30pO1xuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoJHRoaXMuY3NzKCdwb3NpdGlvbicpICE9PSAnZml4ZWQnIHx8ICR0aGlzLmNzcygndG9wJykgIT09IGNzcy50b3ApIHtcbiAgICAgICAgICAgICAgICAgICAgJHRoaXMuY3NzKGNzcyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluIGNhc2UgdGhhdCB0aGUgQ1BVIG9wdGltaXphdGlvblxuICAgICAgICAgKiBpcyBlbmFibGVkLCBza2lwcCBhIGNlcnRhaW4gY291bnRcbiAgICAgICAgICogb2Ygc2Nyb2xsIGV2ZW50cyBiZWZvcmUgcmVjYWxjdWxhdGluZ1xuICAgICAgICAgKiB0aGUgcG9zaXRpb24uXG4gICAgICAgICAqIEByZXR1cm4gICAgIHtib29sZWFufSAgICAgICAgICAgVHJ1ZSBpZiB0aGlzIGV2ZW50IHNoYWxsIGJlIHByb2Nlc3NlZFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9jcHVPcHRpbWl6YXRpb24gPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBza2lwcGVkICs9IDE7XG4gICAgICAgICAgICBjbGVhclRpbWVvdXQodGltZXIpO1xuICAgICAgICAgICAgaWYgKHNraXBwZWQgPCBvcHRpb25zLnNtb290aG5lc3MpIHtcbiAgICAgICAgICAgICAgICB0aW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAkd2luZG93LnRyaWdnZXIoJ3Njcm9sbC5zdGlja3lib3gnLCB0cnVlKTtcbiAgICAgICAgICAgICAgICB9LCBvcHRpb25zLnNtb290aG5lc3NEZWxheSk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgc2tpcHBlZCA9IDA7XG4gICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogU2V0IHRoZSBpbml0aWFsIHRvcCBwb3NpdGlvbiBvZiB0aGUgc3RpY2t5IGJveC4gQSBjb3JyZWN0aW9uIGlzIG5lY2Vzc2FyeSwgaWYgdGhlIGJyZWFkY3J1bWIgaXMgbG9uZ2VyIHRoYW5cbiAgICAgICAgICogb25lIGxpbmUuXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2ZpeEluaXRpYWxUb3BQb3NpdGlvbiA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciBvZmZzZXRUb3AgPSAkdGhpcy5vZmZzZXQoKS50b3AsXG4gICAgICAgICAgICAgICAgdGFyZ2V0T2Zmc2V0VG9wID0gJChvcHRpb25zLm9mZnNldFRvcFJlZmVyZW5jZVNlbGVjdG9yKS5maXJzdCgpLm9mZnNldCgpLnRvcCxcbiAgICAgICAgICAgICAgICBvZmZzZXREaWZmZXJlbmNlID0gb2Zmc2V0VG9wIC0gdGFyZ2V0T2Zmc2V0VG9wLFxuICAgICAgICAgICAgICAgIHRvcFBvc2l0aW9uID0gcGFyc2VGbG9hdCgkdGhpcy5jc3MoJ3RvcCcpKTtcblxuICAgICAgICAgICAgZml4ZWRUb3BQb3NpdGlvbiA9IHRvcFBvc2l0aW9uIC0gb2Zmc2V0RGlmZmVyZW5jZTtcblxuICAgICAgICAgICAgX3Jlc2V0UG9zaXRpb24oKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogUmVzdG9yZSBpbml0aWFsIHBvc2l0aW9uIG9mIHRoZSBzdGlja3kgYm94IGJ5IHJlbW92aW5nIGl0cyBzdHlsZSBhdHRyaWJ1dGUgYW5kIHNldHRpbmcgdGhlIGZpeGVkIHRvcCBwb3NpdGlvbi5cbiAgICAgICAgICpcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfcmVzZXRQb3NpdGlvbiA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICR0aGlzLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG5cbiAgICAgICAgICAgIGlmIChqc2UubGlicy50aGVtZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKS5uYW1lID09PSAnbWQnXG4gICAgICAgICAgICAgICAgfHwganNlLmxpYnMudGhlbWUucmVzcG9uc2l2ZS5icmVha3BvaW50KCkubmFtZSA9PT0gJ2xnJykge1xuICAgICAgICAgICAgICAgICR0aGlzLmNzcygndG9wJywgZml4ZWRUb3BQb3NpdGlvbiArICdweCcpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkdGhpcy5jc3MoJ3RvcCcsICcnKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIGZvciB0aGUgc2Nyb2xsIGV2ZW50LiBJdCBnZXRzIHRoZVxuICAgICAgICAgKiB1cHBlciBib3JkZXIgb2YgdGhlIGNvbnRlbnQgZWxlbWVudCBhbmQgY2FsbHNcbiAgICAgICAgICogaW5kaXZpZHVhbCBtZXRob2RzIGRlcGVuZGluZyBvbiB0aGUgc3RpY2t5IHN0YXRlLlxuICAgICAgICAgKiBUbyBwZXJmb3JtIGJldHRlciBvbiBsb3cgZW5kIENQVXMgaXQgY2hlY2tzIGlmXG4gICAgICAgICAqIHNjcm9sbCBldmVudHMgc2hhbGwgYmUgc2tpcHBlZC5cbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2hlY2tQb3NpdGlvbiA9IGZ1bmN0aW9uIChlLCBkKSB7XG5cbiAgICAgICAgICAgIGlmIChvcHRpb25zLmNwdU9wdGltaXphdGlvbiAmJiAhZCAmJiAhX2NwdU9wdGltaXphdGlvbigpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChqc2UubGlicy50aGVtZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKS5pZCA+IG9wdGlvbnMuYnJlYWtwb2ludCkge1xuICAgICAgICAgICAgICAgIF9jYWxjdWxhdGVEaW1lbnNpb25zKCk7XG4gICAgICAgICAgICAgICAgdmFyIHNjcm9sbFBvc2l0aW9uID0gJHdpbmRvdy5zY3JvbGxUb3AoKSxcbiAgICAgICAgICAgICAgICAgICAgZml0ID0gX2ZpdEluVmlldygpO1xuXG4gICAgICAgICAgICAgICAgaWYgKGZpdCkge1xuICAgICAgICAgICAgICAgICAgICBfY2FsY1Bvc2l0aW9uKHNjcm9sbFBvc2l0aW9uKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhhbmRsZXIgZm9yIHRoZSByZXNpemUgZXZlbnQuIE9uIGJyb3dzZXJcbiAgICAgICAgICogcmVzaXplIGl0IGlzIHJlc2V0dGluZyB0aGUgc3RhdGUgdG8gY2FsY3VsYXRlXG4gICAgICAgICAqIGEgbmV3IHBvc2l0aW9uXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3Jlc2l6ZUhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBfcmVzZXRQb3NpdGlvbigpO1xuICAgICAgICAgICAgZWxlbWVudE9mZnNldCA9IG51bGw7XG4gICAgICAgICAgICBza2lwcGVkID0gMDtcbiAgICAgICAgICAgIGluaXRpYWxPZmZzZXQgPSAkdGhpcy5vZmZzZXQoKS50b3A7XG5cbiAgICAgICAgICAgIF9jaGVja1Bvc2l0aW9uKCk7XG4gICAgICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcbiAgICAgICAgICAgIHZhciBzbGlkZXJIZWlnaHQgPSAwLFxuICAgICAgICAgICAgICAgIGVycm9yQm94SGVpZ2h0ID0gMCxcbiAgICAgICAgICAgICAgICBtYXJnaW5Ub3AgPSAwLFxuICAgICAgICAgICAgICAgIG1hcmdpbkJvdHRvbSA9IDA7XG5cbiAgICAgICAgICAgICRvdXRlcldyYXBwZXIgPSAkKG9wdGlvbnMub3V0ZXJXcmFwcGVyKTtcbiAgICAgICAgICAgICRoZWFkZXIgPSAkKG9wdGlvbnMuaGVhZGVyKTtcbiAgICAgICAgICAgICRmb290ZXIgPSAkKG9wdGlvbnMuZm9vdGVyKTtcblxuICAgICAgICAgICAgaWYgKCQob3B0aW9ucy5zdGFnZSkubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgIHNsaWRlckhlaWdodCA9ICQob3B0aW9ucy5zdGFnZSkub3V0ZXJIZWlnaHQoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJChvcHRpb25zLmVycm9yQm94KS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBtYXJnaW5Ub3AgPSBwYXJzZUludCgkKHRoaXMpLmNzcygnbWFyZ2luLXRvcCcpLCAxMCk7XG4gICAgICAgICAgICAgICAgbWFyZ2luQm90dG9tID0gcGFyc2VJbnQoJCh0aGlzKS5jc3MoJ21hcmdpbi1ib3R0b20nKSwgMTApO1xuXG4gICAgICAgICAgICAgICAgZXJyb3JCb3hIZWlnaHQgKz0gJCh0aGlzKS5vdXRlckhlaWdodCgpO1xuICAgICAgICAgICAgICAgIGVycm9yQm94SGVpZ2h0ICs9IG1hcmdpblRvcDtcbiAgICAgICAgICAgICAgICBlcnJvckJveEhlaWdodCArPSBtYXJnaW5Cb3R0b207XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgdmFyIGVycm9yQm94RWxlbWVudHMgPSAkKG9wdGlvbnMuZXJyb3JCb3gpLmxlbmd0aDtcblxuICAgICAgICAgICAgaWYgKGVycm9yQm94RWxlbWVudHMgPj0gMikge1xuICAgICAgICAgICAgICAgIGVycm9yQm94SGVpZ2h0ID0gZXJyb3JCb3hIZWlnaHQgLSAobWFyZ2luVG9wICogKGVycm9yQm94RWxlbWVudHMgLSAxKSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIF9maXhJbml0aWFsVG9wUG9zaXRpb24oKTtcblxuICAgICAgICAgICAgaW5pdGlhbE9mZnNldCA9ICR0aGlzLm9mZnNldCgpLnRvcDtcbiAgICAgICAgICAgIGluaXRpYWxUb3AgPSBwYXJzZUZsb2F0KCR0aGlzLmNzcygndG9wJykpO1xuICAgICAgICAgICAgaW5pdGlhbEhlYWRlciA9ICRoZWFkZXIub3V0ZXJIZWlnaHQoKSArIG9wdGlvbnMubWFyZ2luVG9wICsgc2xpZGVySGVpZ2h0ICsgZXJyb3JCb3hIZWlnaHQ7XG4gICAgICAgICAgICBpbml0aWFsTWFyZ2luVG9wID0gcGFyc2VGbG9hdCgkb3V0ZXJXcmFwcGVyLmNzcygnbWFyZ2luLXRvcCcpLnJlcGxhY2UoL1teXFxkXS8sICcnKSk7XG4gICAgICAgICAgICBoZWFkZXJGaXhlZCA9ICRoZWFkZXIuY3NzKCdwb3NpdGlvbicpID09PSAnZml4ZWQnO1xuXG4gICAgICAgICAgICBpZiAoIWpzZS5jb3JlLmNvbmZpZy5nZXQoJ21vYmlsZScpKSB7XG4gICAgICAgICAgICAgICAgX2NoZWNrUG9zaXRpb24oKTtcblxuICAgICAgICAgICAgICAgICR3aW5kb3dcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdyZXNpemUnLCBfcmVzaXplSGFuZGxlcilcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdzY3JvbGwuc3RpY2t5Ym94JywgX2NoZWNrUG9zaXRpb24pXG4gICAgICAgICAgICAgICAgICAgIC5vbihqc2UubGlicy50aGVtZS5ldmVudHMuUkVQT1NJVElPTlNfU1RJQ0tZQk9YKCksIF9yZXNpemVIYW5kbGVyKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnYm9keScpLm9uKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5CUkVBS1BPSU5UKCksIF9maXhJbml0aWFsVG9wUG9zaXRpb24pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

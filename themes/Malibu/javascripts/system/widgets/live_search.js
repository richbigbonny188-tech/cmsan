'use strict';

/* --------------------------------------------------------------
 live_search.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that adds a autosuggest functionality to
 * the search box
 */
gambio.widgets.module('live_search', ['form', 'xhr', gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        $target = null,
        $input = null,
        ajaxCall = null,
        timeout = null,
        mobile = null,
        transition = {},
        defaults = {
        // The minimum diget count for the search needle
        needle: 3,
        // The selector where the result is placed
        target: '.search-result-container',
        // Delay (in ms) after the last keyup event is triggered (for ajax request)
        delay: 200,
        // URL to which the request ist posted
        url: 'shop.php?do=LiveSearch',
        // Minimum breakpoint to switch to mobile view
        breakpoint: 40,
        // If true, the layer will reopen on focus
        reopen: true,
        // Class that gets added to open the auto suggest layer
        classOpen: 'open'
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Helper function that sets the active
     * item inside the autosuggest layer
     * @param       {int}       index       The index of the item that is set to active
     * @private
     */
    var _setAutosuggestActive = function _setAutosuggestActive(index) {
        var $all = $target.find('li'),
            $element = $all.eq(index);

        $all.removeClass('active');

        if (index >= 0) {
            $element.addClass('active');
        }
    };

    /**
     * Handler for the key events (up / down arrow & enter)
     * If the autosuggest layer is opened, navigate through
     * the items of the list
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _autoSuggestNavigationHandler = function _autoSuggestNavigationHandler(e) {
        var $all = $target.find('li'),
            $active = $all.filter('.active'),
            index = null,
            href = null;

        // Handler for the different key codes
        switch (e.keyCode) {
            case 13:
                // ENTER
                if ($active.length) {
                    e.preventDefault();
                    e.stopPropagation();

                    href = $active.find('a').attr('href');

                    location.href = href;
                }
                break;
            case 38:
                // UP
                index = $active.length ? $active.index() - 1 : $all.length - 1;
                _setAutosuggestActive(index);
                break;
            case 40:
                // DOWN
                index = $active.length ? $active.index() + 1 : 0;
                _setAutosuggestActive(index);
                break;
            default:
                break;
        }
    };

    /**
     * Helper function to show the ajax
     * result in the search dropdown
     * @param       {string}      content     HTML markup
     * @private
     */
    var _show = function _show(content) {
        transition.open = true;
        $target.html(content).trigger(jse.libs.theme.events.TRANSITION(), transition);

        // Inform other layers
        $this.trigger(jse.libs.theme.events.OPEN_FLYOUT(), [$this]);

        $this.off('keydown.autosuggest').on('keydown.autosuggest', _autoSuggestNavigationHandler);
    };

    /**
     * Helper function to hide the dropdown
     * @private
     */
    var _hide = function _hide() {
        transition.open = false;
        $target.off().one(jse.libs.theme.events.TRANSITION_FINISHED(), function () {
            $target.empty();
        }).trigger(jse.libs.theme.events.TRANSITION(), transition);

        $this.off('keydown.autosuggest');
    };

    // ########## EVENT HANDLER ##########

    /**
     * Handler for the keyup event inside the search
     * input field. It performs an ajax request after
     * a given delay time to relieve the server
     * @private
     */
    var _keyupHandler = function _keyupHandler(e) {

        if ($.inArray(e.keyCode, [13, 37, 38, 39, 40]) > -1) {
            return true;
        }

        var dataset = jse.libs.form.getData($this);

        // Clear timeout irrespective of
        // the needle length
        if (timeout) {
            clearTimeout(timeout);
        }

        // Only proceed if the needle contains
        // at least a certain number of digits
        if (dataset.keywords.length < options.needle) {
            _hide();
            return;
        }

        timeout = setTimeout(function () {
            // Abort a pending ajax request
            if (ajaxCall) {
                ajaxCall.abort();
            }

            // Request the server for the search result
            ajaxCall = jse.libs.xhr.post({
                url: options.url,
                data: dataset,
                dataType: 'html'
            }, true).done(function (result) {
                if (result) {
                    _show(result);
                } else {
                    _hide();
                }
            });
        }, options.delay);
    };

    /**
     * Helper handler to reopen the autosuggests
     * on category dropdown change by triggering
     * the focus event. This needs the option
     * "reopen" to be set
     * @private
     */
    var _categoryChangeHandler = function _categoryChangeHandler() {
        $input.trigger('focus', []);
    };

    /**
     * Handles the switch between the breakpoints. If
     * a switch between desktop & mobile view is detected
     * the autosuggest layer will be closed
     * again
     * @private
     */
    var _breakpointHandler = function _breakpointHandler() {

        var switchToMobile = jse.libs.theme.responsive.breakpoint().id <= options.breakpoint && !mobile,
            switchToDesktop = jse.libs.theme.responsive.breakpoint().id > options.breakpoint && mobile;

        if (switchToMobile || switchToDesktop) {
            $target.removeClass(options.classOpen);
        }
    };

    /**
     * Event handler for closing the autosuggest
     * if the user interacts with the page
     * outside of the layer
     * @param       {object}    e       jQuery event object
     * @param       {object}    d       jQuery selection of the event emitter
     * @private
     */
    var _closeFlyout = function _closeFlyout(e, d) {
        if (d !== $this && !$this.find($(e.target)).length) {
            $target.removeClass(options.classOpen);
            $input.trigger('blur', []);
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        var focus = options.reopen ? ' focus' : '';

        mobile = jse.libs.theme.responsive.breakpoint().id <= options.breakpoint;
        transition.classOpen = options.classOpen;
        $target = $this.find(options.target);
        $input = $this.find('input');
        $target.hide();

        $body.on(jse.libs.theme.events.OPEN_FLYOUT() + ' click', _closeFlyout).on(jse.libs.theme.events.BREAKPOINT(), _breakpointHandler);

        $this.on('keyup' + focus, 'input', _keyupHandler).on('change', 'select', _categoryChangeHandler);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbGl2ZV9zZWFyY2guanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkYm9keSIsIiR0YXJnZXQiLCIkaW5wdXQiLCJhamF4Q2FsbCIsInRpbWVvdXQiLCJtb2JpbGUiLCJ0cmFuc2l0aW9uIiwiZGVmYXVsdHMiLCJuZWVkbGUiLCJ0YXJnZXQiLCJkZWxheSIsInVybCIsImJyZWFrcG9pbnQiLCJyZW9wZW4iLCJjbGFzc09wZW4iLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3NldEF1dG9zdWdnZXN0QWN0aXZlIiwiaW5kZXgiLCIkYWxsIiwiZmluZCIsIiRlbGVtZW50IiwiZXEiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiX2F1dG9TdWdnZXN0TmF2aWdhdGlvbkhhbmRsZXIiLCJlIiwiJGFjdGl2ZSIsImZpbHRlciIsImhyZWYiLCJrZXlDb2RlIiwibGVuZ3RoIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJhdHRyIiwibG9jYXRpb24iLCJfc2hvdyIsImNvbnRlbnQiLCJvcGVuIiwiaHRtbCIsInRyaWdnZXIiLCJqc2UiLCJsaWJzIiwidGhlbWUiLCJldmVudHMiLCJUUkFOU0lUSU9OIiwiT1BFTl9GTFlPVVQiLCJvZmYiLCJvbiIsIl9oaWRlIiwib25lIiwiVFJBTlNJVElPTl9GSU5JU0hFRCIsImVtcHR5IiwiX2tleXVwSGFuZGxlciIsImluQXJyYXkiLCJkYXRhc2V0IiwiZm9ybSIsImdldERhdGEiLCJjbGVhclRpbWVvdXQiLCJrZXl3b3JkcyIsInNldFRpbWVvdXQiLCJhYm9ydCIsInhociIsInBvc3QiLCJkYXRhVHlwZSIsImRvbmUiLCJyZXN1bHQiLCJfY2F0ZWdvcnlDaGFuZ2VIYW5kbGVyIiwiX2JyZWFrcG9pbnRIYW5kbGVyIiwic3dpdGNoVG9Nb2JpbGUiLCJyZXNwb25zaXZlIiwiaWQiLCJzd2l0Y2hUb0Rlc2t0b3AiLCJfY2xv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Q1EsVUFBVTRCLEtBRGxCLEVBQ3lCLE9BRHpCLEVBQ2tDdkIsYUFEbEMsRUFFS0wsRUFGTCxDQUVRLFFBRlIsRUFFa0IsUUFGbEIsRUFFNEJtQixzQkFGNUI7O0FBSUFGO0FBQ0gsS0FuQkQ7O0FBcUJBO0FBQ0EsV0FBT3RFLE1BQVA7QUFDSCxDQXZRTCIsImZpbGUiOiJ3aWRnZXRzL2xpdmVfc2VhcmNoLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBsaXZlX3NlYXJjaC5qcyAyMDE2LTAzLTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCBhZGRzIGEgYXV0b3N1Z2dlc3QgZnVuY3Rpb25hbGl0eSB0b1xuICogdGhlIHNlYXJjaCBib3hcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdsaXZlX3NlYXJjaCcsXG5cbiAgICBbXG4gICAgICAgICdmb3JtJyxcbiAgICAgICAgJ3hocicsXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJyxcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9yZXNwb25zaXZlJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICRib2R5ID0gJCgnYm9keScpLFxuICAgICAgICAgICAgJHRhcmdldCA9IG51bGwsXG4gICAgICAgICAgICAkaW5wdXQgPSBudWxsLFxuICAgICAgICAgICAgYWpheENhbGwgPSBudWxsLFxuICAgICAgICAgICAgdGltZW91dCA9IG51bGwsXG4gICAgICAgICAgICBtb2JpbGUgPSBudWxsLFxuICAgICAgICAgICAgdHJhbnNpdGlvbiA9IHt9LFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAgICAgLy8gVGhlIG1pbmltdW0gZGlnZXQgY291bnQgZm9yIHRoZSBzZWFyY2ggbmVlZGxlXG4gICAgICAgICAgICAgICAgbmVlZGxlOiAzLFxuICAgICAgICAgICAgICAgIC8vIFRoZSBzZWxlY3RvciB3aGVyZSB0aGUgcmVzdWx0IGlzIHBsYWNlZFxuICAgICAgICAgICAgICAgIHRhcmdldDogJy5zZWFyY2gtcmVzdWx0LWNvbnRhaW5lcicsXG4gICAgICAgICAgICAgICAgLy8gRGVsYXkgKGluIG1zKSBhZnRlciB0aGUgbGFzdCBrZXl1cCBldmVudCBpcyB0cmlnZ2VyZWQgKGZvciBhamF4IHJlcXVlc3QpXG4gICAgICAgICAgICAgICAgZGVsYXk6IDIwMCxcbiAgICAgICAgICAgICAgICAvLyBVUkwgdG8gd2hpY2ggdGhlIHJlcXVlc3QgaXN0IHBvc3RlZFxuICAgICAgICAgICAgICAgIHVybDogJ3Nob3AucGhwP2RvPUxpdmVTZWFyY2gnLFxuICAgICAgICAgICAgICAgIC8vIE1pbmltdW0gYnJlYWtwb2ludCB0byBzd2l0Y2ggdG8gbW9iaWxlIHZpZXdcbiAgICAgICAgICAgICAgICBicmVha3BvaW50OiA0MCxcbiAgICAgICAgICAgICAgICAvLyBJZiB0cnVlLCB0aGUgbGF5ZXIgd2lsbCByZW9wZW4gb24gZm9jdXNcbiAgICAgICAgICAgICAgICByZW9wZW46IHRydWUsXG4gICAgICAgICAgICAgICAgLy8gQ2xhc3MgdGhhdCBnZXRzIGFkZGVkIHRvIG9wZW4gdGhlIGF1dG8gc3VnZ2VzdCBsYXllclxuICAgICAgICAgICAgICAgIGNsYXNzT3BlbjogJ29wZW4nXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBzZXRzIHRoZSBhY3RpdmVcbiAgICAgICAgICogaXRlbSBpbnNpZGUgdGhlIGF1dG9zdWdnZXN0IGxheWVyXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7aW50fSAgICAgICBpbmRleCAgICAgICBUaGUgaW5kZXggb2YgdGhlIGl0ZW0gdGhhdCBpcyBzZXQgdG8gYWN0aXZlXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3NldEF1dG9zdWdnZXN0QWN0aXZlID0gZnVuY3Rpb24gKGluZGV4KSB7XG4gICAgICAgICAgICB2YXIgJGFsbCA9ICR0YXJnZXQuZmluZCgnbGknKSxcbiAgICAgICAgICAgICAgICAkZWxlbWVudCA9ICRhbGwuZXEoaW5kZXgpO1xuXG4gICAgICAgICAgICAkYWxsLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcblxuICAgICAgICAgICAgaWYgKGluZGV4ID49IDApIHtcbiAgICAgICAgICAgICAgICAkZWxlbWVudC5hZGRDbGFzcygnYWN0aXZlJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhhbmRsZXIgZm9yIHRoZSBrZXkgZXZlbnRzICh1cCAvIGRvd24gYXJyb3cgJiBlbnRlcilcbiAgICAgICAgICogSWYgdGhlIGF1dG9zdWdnZXN0IGxheWVyIGlzIG9wZW5lZCwgbmF2aWdhdGUgdGhyb3VnaFxuICAgICAgICAgKiB0aGUgaXRlbXMgb2YgdGhlIGxpc3RcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9hdXRvU3VnZ2VzdE5hdmlnYXRpb25IYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIHZhciAkYWxsID0gJHRhcmdldC5maW5kKCdsaScpLFxuICAgICAgICAgICAgICAgICRhY3RpdmUgPSAkYWxsLmZpbHRlcignLmFjdGl2ZScpLFxuICAgICAgICAgICAgICAgIGluZGV4ID0gbnVsbCxcbiAgICAgICAgICAgICAgICBocmVmID0gbnVsbDtcblxuICAgICAgICAgICAgLy8gSGFuZGxlciBmb3IgdGhlIGRpZmZlcmVudCBrZXkgY29kZXNcbiAgICAgICAgICAgIHN3aXRjaCAoZS5rZXlDb2RlKSB7XG4gICAgICAgICAgICAgICAgY2FzZSAxMzogLy8gRU5URVJcbiAgICAgICAgICAgICAgICAgICAgaWYgKCRhY3RpdmUubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBocmVmID0gJGFjdGl2ZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5maW5kKCdhJylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXR0cignaHJlZicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBsb2NhdGlvbi5ocmVmID0gaHJlZjtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBjYXNlIDM4OiAvLyBVUFxuICAgICAgICAgICAgICAgICAgICBpbmRleCA9ICgkYWN0aXZlLmxlbmd0aCkgPyAoJGFjdGl2ZS5pbmRleCgpIC0gMSkgOiAoJGFsbC5sZW5ndGggLSAxKTtcbiAgICAgICAgICAgICAgICAgICAgX3NldEF1dG9zdWdnZXN0QWN0aXZlKGluZGV4KTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSA0MDogLy8gRE9XTlxuICAgICAgICAgICAgICAgICAgICBpbmRleCA9ICgkYWN0aXZlLmxlbmd0aCkgPyAoJGFjdGl2ZS5pbmRleCgpICsgMSkgOiAwO1xuICAgICAgICAgICAgICAgICAgICBfc2V0QXV0b3N1Z2dlc3RBY3RpdmUoaW5kZXgpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBkZWZhdWx0OlxuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRvIHNob3cgdGhlIGFqYXhcbiAgICAgICAgICogcmVzdWx0IGluIHRoZSBzZWFyY2ggZHJvcGRvd25cbiAgICAgICAgICogQHBhcmFtICAgICAgIHtzdHJpbmd9ICAgICAgY29udGVudCAgICAgSFRNTCBtYXJrdXBcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfc2hvdyA9IGZ1bmN0aW9uIChjb250ZW50KSB7XG4gICAgICAgICAgICB0cmFuc2l0aW9uLm9wZW4gPSB0cnVlO1xuICAgICAgICAgICAgJHRhcmdldFxuICAgICAgICAgICAgICAgIC5odG1sKGNvbnRlbnQpXG4gICAgICAgICAgICAgICAgLnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLlRSQU5TSVRJT04oKSwgdHJhbnNpdGlvbik7XG5cbiAgICAgICAgICAgIC8vIEluZm9ybSBvdGhlciBsYXllcnNcbiAgICAgICAgICAgICR0aGlzLnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLk9QRU5fRkxZT1VUKCksIFskdGhpc10pO1xuXG4gICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgIC5vZmYoJ2tleWRvd24uYXV0b3N1Z2dlc3QnKVxuICAgICAgICAgICAgICAgIC5vbigna2V5ZG93bi5hdXRvc3VnZ2VzdCcsIF9hdXRvU3VnZ2VzdE5hdmlnYXRpb25IYW5kbGVyKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRvIGhpZGUgdGhlIGRyb3Bkb3duXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2hpZGUgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0cmFuc2l0aW9uLm9wZW4gPSBmYWxzZTtcbiAgICAgICAgICAgICR0YXJnZXRcbiAgICAgICAgICAgICAgICAub2ZmKClcbiAgICAgICAgICAgICAgICAub25lKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OX0ZJTklTSEVEKCksIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgJHRhcmdldC5lbXB0eSgpO1xuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgLnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLlRSQU5TSVRJT04oKSwgdHJhbnNpdGlvbik7XG5cbiAgICAgICAgICAgICR0aGlzLm9mZigna2V5ZG93bi5hdXRvc3VnZ2VzdCcpO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhhbmRsZXIgZm9yIHRoZSBrZXl1cCBldmVudCBpbnNpZGUgdGhlIHNlYXJjaFxuICAgICAgICAgKiBpbnB1dCBmaWVsZC4gSXQgcGVyZm9ybXMgYW4gYWpheCByZXF1ZXN0IGFmdGVyXG4gICAgICAgICAqIGEgZ2l2ZW4gZGVsYXkgdGltZSB0byByZWxpZXZlIHRoZSBzZXJ2ZXJcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfa2V5dXBIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcblxuICAgICAgICAgICAgaWYgKCQuaW5BcnJheShlLmtleUNvZGUsIFsxMywgMzcsIDM4LCAzOSwgNDBdKSA+IC0xKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHZhciBkYXRhc2V0ID0ganNlLmxpYnMuZm9ybS5nZXREYXRhKCR0aGlzKTtcblxuICAgICAgICAgICAgLy8gQ2xlYXIgdGltZW91dCBpcnJlc3BlY3RpdmUgb2ZcbiAgICAgICAgICAgIC8vIHRoZSBuZWVkbGUgbGVuZ3RoXG4gICAgICAgICAgICBpZiAodGltZW91dCkge1xuICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dCh0aW1lb3V0KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gT25seSBwcm9jZWVkIGlmIHRoZSBuZWVkbGUgY29udGFpbnNcbiAgICAgICAgICAgIC8vIGF0IGxlYXN0IGEgY2VydGFpbiBudW1iZXIgb2YgZGlnaXRzXG4gICAgICAgICAgICBpZiAoZGF0YXNldC5rZXl3b3Jkcy5sZW5ndGggPCBvcHRpb25zLm5lZWRsZSkge1xuICAgICAgICAgICAgICAgIF9oaWRlKCk7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aW1lb3V0ID0gc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgLy8gQWJvcnQgYSBwZW5kaW5nIGFqYXggcmVxdWVzdFxuICAgICAgICAgICAgICAgIGlmIChhamF4Q2FsbCkge1xuICAgICAgICAgICAgICAgICAgICBhamF4Q2FsbC5hYm9ydCgpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIFJlcXVlc3QgdGhlIHNlcnZlciBmb3IgdGhlIHNlYXJjaCByZXN1bHRcbiAgICAgICAgICAgICAgICBhamF4Q2FsbCA9IGpzZS5saWJzLnhoci5wb3N0KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBvcHRpb25zLnVybCxcbiAgICAgICAgICAgICAgICAgICAgZGF0YTogZGF0YXNldCxcbiAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdodG1sJ1xuICAgICAgICAgICAgICAgIH0sIHRydWUpLmRvbmUoZnVuY3Rpb24gKHJlc3VsdCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAocmVzdWx0KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBfc2hvdyhyZXN1bHQpO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgX2hpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSwgb3B0aW9ucy5kZWxheSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBoYW5kbGVyIHRvIHJlb3BlbiB0aGUgYXV0b3N1Z2dlc3RzXG4gICAgICAgICAqIG9uIGNhdGVnb3J5IGRyb3Bkb3duIGNoYW5nZSBieSB0cmlnZ2VyaW5nXG4gICAgICAgICAqIHRoZSBmb2N1cyBldmVudC4gVGhpcyBuZWVkcyB0aGUgb3B0aW9uXG4gICAgICAgICAqIFwicmVvcGVuXCIgdG8gYmUgc2V0XG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2NhdGVnb3J5Q2hhbmdlSGFuZGxlciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICRpbnB1dC50cmlnZ2VyKCdmb2N1cycsIFtdKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGFuZGxlcyB0aGUgc3dpdGNoIGJldHdlZW4gdGhlIGJyZWFrcG9pbnRzLiBJZlxuICAgICAgICAgKiBhIHN3aXRjaCBiZXR3ZWVuIGRlc2t0b3AgJiBtb2JpbGUgdmlldyBpcyBkZXRlY3RlZFxuICAgICAgICAgKiB0aGUgYXV0b3N1Z2dlc3QgbGF5ZXIgd2lsbCBiZSBjbG9zZWRcbiAgICAgICAgICogYWdhaW5cbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfYnJlYWtwb2ludEhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgIHZhciBzd2l0Y2hUb01vYmlsZSA9IGpzZS5saWJzLnRoZW1lLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludCAmJiAhbW9iaWxlLFxuICAgICAgICAgICAgICAgIHN3aXRjaFRvRGVza3RvcCA9IGpzZS5saWJzLnRoZW1lLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkID4gb3B0aW9ucy5icmVha3BvaW50ICYmIG1vYmlsZTtcblxuICAgICAgICAgICAgaWYgKHN3aXRjaFRvTW9iaWxlIHx8IHN3aXRjaFRvRGVza3RvcCkge1xuICAgICAgICAgICAgICAgICR0YXJnZXQucmVtb3ZlQ2xhc3Mob3B0aW9ucy5jbGFzc09wZW4pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIGZvciBjbG9zaW5nIHRoZSBhdXRvc3VnZ2VzdFxuICAgICAgICAgKiBpZiB0aGUgdXNlciBpbnRlcmFjdHMgd2l0aCB0aGUgcGFnZVxuICAgICAgICAgKiBvdXRzaWRlIG9mIHRoZSBsYXllclxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBkICAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGV2ZW50IGVtaXR0ZXJcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2xvc2VGbHlvdXQgPSBmdW5jdGlvbiAoZSwgZCkge1xuICAgICAgICAgICAgaWYgKGQgIT09ICR0aGlzICYmICEkdGhpcy5maW5kKCQoZS50YXJnZXQpKS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAkdGFyZ2V0LnJlbW92ZUNsYXNzKG9wdGlvbnMuY2xhc3NPcGVuKTtcbiAgICAgICAgICAgICAgICAkaW5wdXQudHJpZ2dlcignYmx1cicsIFtdKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICAgICB2YXIgZm9jdXMgPSBvcHRpb25zLnJlb3BlbiA/ICcgZm9jdXMnIDogJyc7XG5cbiAgICAgICAgICAgIG1vYmlsZSA9IGpzZS5saWJzLnRoZW1lLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludDtcbiAgICAgICAgICAgIHRyYW5zaXRpb24uY2xhc3NPcGVuID0gb3B0aW9ucy5jbGFzc09wZW47XG4gICAgICAgICAgICAkdGFyZ2V0ID0gJHRoaXMuZmluZChvcHRpb25zLnRhcmdldCk7XG4gICAgICAgICAgICAkaW5wdXQgPSAkdGhpcy5maW5kKCdpbnB1dCcpO1xuICAgICAgICAgICAgJHRhcmdldC5oaWRlKCk7XG5cbiAgICAgICAgICAgICRib2R5XG4gICAgICAgICAgICAgICAgLm9uKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5PUEVOX0ZMWU9VVCgpICsgJyBjbGljaycsIF9jbG9zZUZseW91dClcbiAgICAgICAgICAgICAgICAub24oanNlLmxpYnMudGhlbWUuZXZlbnRzLkJSRUFLUE9JTlQoKSwgX2JyZWFrcG9pbnRIYW5kbGVyKTtcblxuICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAub24oJ2tleXVwJyArIGZvY3VzLCAnaW5wdXQnLCBfa2V5dXBIYW5kbGVyKVxuICAgICAgICAgICAgICAgIC5vbignY2hhbmdlJywgJ3NlbGVjdCcsIF9jYXRlZ29yeUNoYW5nZUhhbmRsZXIpO1xuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

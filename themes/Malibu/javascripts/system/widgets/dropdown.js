'use strict';

/* --------------------------------------------------------------
 dropdown.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component to replace the default browser select
 * boxes with a more stylish html / css one
 */
gambio.widgets.module('dropdown', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        transition = {},
        defaults = {
        // Minimum breakpoint to switch to mobile view
        breakpoint: 40,
        // Container selector for the dropdown markup to look for
        container: '.custom-dropdown',
        // Class that gets added to opened flyouts (@ the container)
        openClass: 'open',
        // If true, the currently selected item gets hidden from the flyout
        hideActive: true,
        // Shortens the text shown in the button. Possible values: Any type of integer, null for do nothing
        shorten: 10,

        // or "fit" for autodetect length depending on the button size (only works with fixed with buttons)

        // Shortens the text inside the button on component init
        shortenOnInit: false,
        // If true the label will get shortened on mobile too
        shortenOnMobile: false,
        // If true, a change of the selectbox by the flyout is receipted trough a change trigger
        triggerChange: true,
        // If true, a change is triggered on no change of the selectbox also
        triggerNoChange: false
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Helper function that hides the currently active
     * element from the dropdown
     * @param       {object}        $container      jQuery selection of the dropdown container
     * @param       {object}        opt             JSON with custom settings for that container
     * @private
     */
    var _hideActive = function _hideActive($container, opt) {
        if (opt.hideActive) {
            var $select = $container.children('select'),
                value = $select.children(':selected').val();

            $container.find('li').show().children('a[data-rel="' + value + '"]').parent().hide();
        }
    };

    /**
     * Helper function to add a disabled class to the
     * disabled entries in the custom dropdown. Therefor
     * the original select is scanned for disabled entries
     * @param       {object}        $container      jQuery selection of the dropdown container
     * @private
     */
    var _setDisabled = function _setDisabled($container) {
        var $ul = $container.children(),
            $select = $container.children('select'),
            $disabled = $select.children(':disabled');

        // Remove all disabled classes first
        $ul.find('.disabled').removeClass('disabled');

        // Iterate through all entries that needs to
        // be disabled and add a class to them
        $disabled.each(function () {
            var $self = $(this),
                value = $self.val();

            $ul.find('a[data-rel="' + value + '"]').parent().addClass('disabled');
        });
    };

    /**
     * Helper function for the _shortenLabel-function.
     * This function shortens the label so that it fits
     * inside the button. Additional available siblings
     * of the text element were getting substracted from
     * the available button size.
     * @param       {object}    $button     jQuery selection of the button
     * @param       {string}    value       The value that should be set as the button text
     * @return     {string}                The shortened string
     * @private
     */
    var _shortenFit = function _shortenFit($button, value) {
        var $siblings = $button.children().not('.dropdown-name'),
            $textarea = $button.children('.dropdown-name'),
            width = $button.width(),
            length = value.length,
            name = '',
            shorten = false,
            i = 0,
            test = null;

        // Remove the siblings with from the available
        // full width of the button
        $siblings.each(function () {
            width -= $(this).outerWidth();
        });

        // Iterate through the label characters
        // and add one character at time to the button
        // if the textfield size grows larger than
        // the available width of the button cancel
        // the loop and take the last fitting value
        // as result
        for (i; i < length; i += 1) {
            test = value.substring(0, i) + '...';
            $textarea.text(test);

            if ($textarea.width() > width) {
                shorten = true;
                break;
            }

            name = test;
        }

        // If the text was shortened
        // return the shortened name
        // else the full name
        if (shorten) {
            return name;
        }
        return value;
    };

    /**
     * Helper function for the _shortenLabel-function.
     * This function shortens the label to a set number
     * of digets
     * @param       {string}    value       The value that should be set as the button text
     * @param       {object}    opt         JSON with custom settings for that container
     * @return     {string}                The shortened string
     * @private
     */
    var _shortenInt = function _shortenInt(value, opt) {
        var length = value.length,
            diff = length - opt.shorten;

        if (diff > 0) {
            diff += 3;
            return value.substring(0, length - diff) + '...';
        }

        return value;
    };

    /**
     * Function that chooses the correct shortener
     * subroutine for shortening the button text
     * (if needed) and returns the shortened value
     * to the caller
     * @param       {object}    $button     jQuery selection of the button
     * @param       {string}    value       The value that should be set as the button text
     * @param       {object}    opt         JSON with custom settings for that container
     * @return     {string}                The shortened string
     * @private
     */
    var _shortenLabel = function _shortenLabel($button, value, opt) {
        if (options.breakpoint < jse.libs.theme.responsive.breakpoint().id || opt.shortenOnMobile) {
            if (opt.shorten === 'fit') {
                value = _shortenFit($button, value);
            } else if (opt.shorten) {
                value = _shortenInt(value, opt);
            }
        }

        return value;
    };

    // ########## EVENT HANDLER ##########

    /**
     * Event handler that ist triggered on change
     * of the selectbox to force the dropdown to close
     * (needed on mobile devices, because of it's native
     * support for dropdowns)
     * @private
     */
    var _closeLayer = function _closeLayer() {
        var $self = $(this),
            $container = $self.closest(options.container),
            $select = $container.children('select'),
            dataset = $.extend({}, options, $container.parseModuleData('dropdown'));

        transition.open = false;
        $container.trigger(jse.libs.theme.events.TRANSITION(), transition);

        // Trigger the change event if the option is set
        if (dataset.triggerNoChange) {
            $select.trigger('change', []);
        }
    };

    /**
     * Function gets triggered on click on the button.
     * It switches the state of the dropdown visibility
     * @param           {object}    e       jQuery event object
     * @private
     */
    var _openLayer = function _openLayer(e) {
        e.preventDefault();
        e.stopPropagation();

        var $self = $(this),
            $container = $self.closest(options.container),
            $select = $container.children('select'),
            dataset = $.extend({}, options, $container.parseModuleData('dropdown'));

        if ($container.hasClass(options.openClass)) {
            // Remove the open class if the layer is opened
            transition.open = false;
            $container.trigger(jse.libs.theme.events.TRANSITION(), transition);

            // Trigger the change event if the option is set
            if (dataset.triggerNoChange) {
                $select.trigger('change', []);
            }
        } else {
            // Add the open class and inform other layers to close
            _hideActive($container, dataset);
            _setDisabled($container);

            transition.open = true;
            $container.trigger(jse.libs.theme.events.TRANSITION(), transition);
            $this.trigger(jse.libs.theme.events.OPEN_FLYOUT(), [$container]);
        }
    };

    /**
     * Handler that gets used if the user
     * selects a value from the custom dropdown.
     * If the value has changed, the view gets
     * updated and the original select gets set
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _selectEntry = function _selectEntry(e) {
        e.preventDefault();
        e.stopPropagation();

        var $self = $(this),
            $li = $self.parent();

        // If the item is disabled, do nothing
        if (!$li.hasClass('disabled')) {

            var $container = $self.closest(options.container),
                $button = $container.children('button'),
                $select = $container.children('select'),
                oldValue = $select.children(':selected').val(),
                newValue = $self.attr('data-rel'),
                name = $self.text(),
                dataset = $.extend({}, options, $container.parseModuleData('dropdown'));

            // Update the dropdown view if the
            // value has changed
            if (oldValue !== newValue) {
                // Set the button text
                var shortened = _shortenLabel($button, name, dataset);
                $button.children('.dropdown-name').text(shortened);

                // Set the "original" select box and
                // notify the browser / other js that the
                // value has changed
                $select.children('[value="' + newValue + '"]').prop('selected', true);

                // Trigger the change event if the option is set
                if (dataset.triggerChange) {
                    $select.trigger('change', []);
                }
            } else if (dataset.triggerNoChange) {
                // Trigger the change event if the option is set
                $select.trigger('change', []);
            }

            // Close the layer
            transition.open = false;
            $container.trigger(jse.libs.theme.events.TRANSITION(), transition);
        }
    };

    /**
     * Handles the switch between the breakpoint. If the
     * size of the button changes the text will be shortened
     * again to fit. If the view switches to mobile, this
     * behaviour is skipped the full name will be displayed
     * again
     * @private
     */
    var _breakpointHandler = function _breakpointHandler() {
        var $container = $this.find(options.container);

        if (options.breakpoint < jse.libs.theme.responsive.breakpoint().id || options.shortenOnMobile) {
            // If still in desktop mode, try to shorten the name
            $container.each(function () {
                var $self = $(this),
                    $button = $self.children('button'),
                    $textarea = $button.children('.dropdown-name'),
                    value = $self.find('select option:selected').text(),
                    dataset = $.extend({}, options, $self.parseModuleData('dropdown')),
                    shortened = _shortenLabel($button, value, dataset);

                $textarea.text(shortened);
            });
        } else {
            // If in mobile mode insert the complete name again
            // and close opened layers
            $container.removeClass(options.openClass).each(function () {
                var $self = $(this),
                    $textarea = $self.find('.dropdown-name'),
                    value = $self.find('select option:selected').text();

                $textarea.text(value);
            });
        }
    };

    /**
     * Handler for closing all dropdown flyouts if
     * somewhere on the page opens an other flyout
     * @param   {object}    e       jQuery event object
     * @param   {object}    d       jQuery selection of the event emitter
     * @private
     */
    var _closeFlyout = function _closeFlyout(e, d) {
        var $containers = $this.find(options.container),
            $exclude = d || $(e.target).closest(options.openClass);

        $containers = $containers.not($exclude);
        $containers.removeClass(options.openClass);
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        transition.classOpen = options.openClass;

        $body.on(jse.libs.theme.events.OPEN_FLYOUT() + ' click', _closeFlyout).on(jse.libs.theme.events.BREAKPOINT(), _breakpointHandler);

        $this.on('click', options.container + ' button', _openLayer).on('click', options.container + ' ul a', _selectEntry).on('change', options.container + ' select', _closeLayer);

        if (options.shortenOnInit) {
            _breakpointHandler();
        }

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvZHJvcGRvd24uanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkYm9keSIsInRyYW5zaXRpb24iLCJkZWZhdWx0cyIsImJyZWFrcG9pbnQiLCJjb250YWluZXIiLCJvcGVuQ2xhc3MiLCJoaWRlQWN0aXZlIiwic2hvcnRlbiIsInNob3J0ZW5PbkluaXQiLCJzaG9ydGVuT25Nb2JpbGUiLCJ0cmlnZ2VyQ2hhbmdlIiwidHJpZ2dlck5vQ2hhbmdlIiwib3B0aW9ucyIsImV4dGVuZCIsIl9oaWRlQWN0aXZlIiwiJGNvbnRhaW5lciIsIm9wdCIsIiRzZWxlY3QiLCJjaGlsZHJlbiIsInZhbHVlIiwidmFsIiwiZmluZCIsInNob3ciLCJwYXJlbnQiLCJoaWRlIiwiX3NldERpc2FibGVkIiwiJHVsIiwiJGRpc2FibGVkIiwicmVtb3ZlQ2xhc3MiLCJlYWNoIiwiJHNlbGYiLCJhZGRDbGFzcyIsIl9zaG9ydGVuRml0IiwiJGJ1dHRvbiIsIiRzaWJsaW5ncyIsIm5vdCIsIiR0ZXh0YXJlYSIsIndpZHRoIiwibGVuZ3RoIiwibmFtZSIsImkiLCJ0ZXN0Iiwib3V0ZXJXaWR0aCIsInN1YnN0cmluZyIsInRleHQiLCJfc2hvcnRlbkludCIsImRpZmYiLCJfc2hvcnRlbkxhYmVsIiwianNlIiwibGlicyIsInRoZW1lIiwicmVzcG9uc2l2ZSIsImlkIiwiX2Nsb3NlTGF5ZXIiLCJjbG9zZXN0IiwiZGF0YXNldCIsInBhcnNlTW9kdWxlRGF0YSIsIm9wZW4iLCJ0cmlnZ2VyIiwiZXZlbnRzIiwiVFJBTlNJVElPTiIsIl9vcGVuTGF5ZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJoYXNDbGFzcyIsIk9QRU5fRkxZT1VUIiwiX3NlbGVjdEVudHJ5IiwiJGxpIiwib2xkVmFsdWUiLCJuZXdWYWx1ZSIsImF0dHIiLCJzaG9ydGVuZWQiLCJwcm9wIiwiX2JyZWFrcG9pbnRIYW5kbGVyIiwiX2Nsb3NlRmx5b3V0IiwiZCIsIiRjb250YWluZXJzIiwiJGV4Y2x1ZGUiLCJ0YXJnZXQiLCJpbml0IiwiZG9uZSIsImNsYXNzT3BlbiIsIm9uIiwiQlJFQUtQT0lOVCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLFVBREosRUFHSSxDQUNJRixPQUFPRyxNQUFQLEdBQWdCLGNBRHBCLEVBRUlILE9BQU9HLE1BQVAsR0FBZ0Isa0JBRnBCLENBSEosRUFRSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVSOztBQUVRLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsUUFBUUQsRUFBRSxNQUFGLENBRFo7QUFBQSxRQUVJRSxhQUFhLEVBRmpCO0FBQUEsUUFHSUMsV0FBVztBQUNQO0FBQ0FDLG9CQUFZLEVBRkw7QUFHUDtBQUNBQyxtQkFBVyxrQkFKSjtBQUtQO0FBQ0FDLG1CQUFXLE1BTko7QUFPUDtBQUNBQyxvQkFBWSxJQVJMO0FBU1A7QUFDQUMsaUJBQVMsRUFWRjs7QUFZUDs7QUFFQTtBQUNBQyx1QkFBZSxLQWZSO0FBZ0JQO0FBQ0FDLHlCQUFpQixLQWpCVjtBQWtCUDtBQUNBQyx1QkFBZSxJQW5CUjtBQW9CUDtBQUNBQyx5QkFBaUI7QUFyQlYsS0FIZjtBQUFBLFFBMEJJQyxVQUFVYixFQUFFYyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJYLFFBQW5CLEVBQTZCTCxJQUE3QixDQTFCZDtBQUFBLFFBMkJJRixTQUFTLEVBM0JiOztBQThCUjs7QUFFUTs7Ozs7OztBQU9BLFFBQUltQixjQUFjLFNBQWRBLFdBQWMsQ0FBVUMsVUFBVixFQUFzQkMsR0FBdEIsRUFBMkI7QUFDekMsWUFBSUEsSUFBSVYsVUFBUixFQUFvQjtBQUNoQixnQkFBSVcsVUFBVUYsV0FDTEcsUUFESyxDQUNJLFFBREosQ0FBZDtBQUFBLGdCQUVJQyxRQUFRRixRQUNIQyxRQURHLENB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LFFBQVFSLFNBQVIsR0FBb0IsT0FGckMsRUFFOEMrRCxZQUY5QyxFQUdLZ0IsRUFITCxDQUdRLFFBSFIsRUFHa0J2RSxRQUFRUixTQUFSLEdBQW9CLFNBSHRDLEVBR2lEaUQsV0FIakQ7O0FBS0EsWUFBSXpDLFFBQVFKLGFBQVosRUFBMkI7QUFDdkJrRTtBQUNIOztBQUVETztBQUNILEtBbEJEOztBQW9CQTtBQUNBLFdBQU90RixNQUFQO0FBQ0gsQ0E3WUwiLCJmaWxlIjoid2lkZ2V0cy9kcm9wZG93bi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZHJvcGRvd24uanMgMjAxNi0wMy0wOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogQ29tcG9uZW50IHRvIHJlcGxhY2UgdGhlIGRlZmF1bHQgYnJvd3NlciBzZWxlY3RcbiAqIGJveGVzIHdpdGggYSBtb3JlIHN0eWxpc2ggaHRtbCAvIGNzcyBvbmVcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdkcm9wZG93bicsXG5cbiAgICBbXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJyxcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9yZXNwb25zaXZlJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICRib2R5ID0gJCgnYm9keScpLFxuICAgICAgICAgICAgdHJhbnNpdGlvbiA9IHt9LFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAgICAgLy8gTWluaW11bSBicmVha3BvaW50IHRvIHN3aXRjaCB0byBtb2JpbGUgdmlld1xuICAgICAgICAgICAgICAgIGJyZWFrcG9pbnQ6IDQwLFxuICAgICAgICAgICAgICAgIC8vIENvbnRhaW5lciBzZWxlY3RvciBmb3IgdGhlIGRyb3Bkb3duIG1hcmt1cCB0byBsb29rIGZvclxuICAgICAgICAgICAgICAgIGNvbnRhaW5lcjogJy5jdXN0b20tZHJvcGRvd24nLFxuICAgICAgICAgICAgICAgIC8vIENsYXNzIHRoYXQgZ2V0cyBhZGRlZCB0byBvcGVuZWQgZmx5b3V0cyAoQCB0aGUgY29udGFpbmVyKVxuICAgICAgICAgICAgICAgIG9wZW5DbGFzczogJ29wZW4nLFxuICAgICAgICAgICAgICAgIC8vIElmIHRydWUsIHRoZSBjdXJyZW50bHkgc2VsZWN0ZWQgaXRlbSBnZXRzIGhpZGRlbiBmcm9tIHRoZSBmbHlvdXRcbiAgICAgICAgICAgICAgICBoaWRlQWN0aXZlOiB0cnVlLFxuICAgICAgICAgICAgICAgIC8vIFNob3J0ZW5zIHRoZSB0ZXh0IHNob3duIGluIHRoZSBidXR0b24uIFBvc3NpYmxlIHZhbHVlczogQW55IHR5cGUgb2YgaW50ZWdlciwgbnVsbCBmb3IgZG8gbm90aGluZ1xuICAgICAgICAgICAgICAgIHNob3J0ZW46IDEwLFxuXG4gICAgICAgICAgICAgICAgLy8gb3IgXCJmaXRcIiBmb3IgYXV0b2RldGVjdCBsZW5ndGggZGVwZW5kaW5nIG9uIHRoZSBidXR0b24gc2l6ZSAob25seSB3b3JrcyB3aXRoIGZpeGVkIHdpdGggYnV0dG9ucylcblxuICAgICAgICAgICAgICAgIC8vIFNob3J0ZW5zIHRoZSB0ZXh0IGluc2lkZSB0aGUgYnV0dG9uIG9uIGNvbXBvbmVudCBpbml0XG4gICAgICAgICAgICAgICAgc2hvcnRlbk9uSW5pdDogZmFsc2UsXG4gICAgICAgICAgICAgICAgLy8gSWYgdHJ1ZSB0aGUgbGFiZWwgd2lsbCBnZXQgc2hvcnRlbmVkIG9uIG1vYmlsZSB0b29cbiAgICAgICAgICAgICAgICBzaG9ydGVuT25Nb2JpbGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIC8vIElmIHRydWUsIGEgY2hhbmdlIG9mIHRoZSBzZWxlY3Rib3ggYnkgdGhlIGZseW91dCBpcyByZWNlaXB0ZWQgdHJvdWdoIGEgY2hhbmdlIHRyaWdnZXJcbiAgICAgICAgICAgICAgICB0cmlnZ2VyQ2hhbmdlOiB0cnVlLFxuICAgICAgICAgICAgICAgIC8vIElmIHRydWUsIGEgY2hhbmdlIGlzIHRyaWdnZXJlZCBvbiBubyBjaGFuZ2Ugb2YgdGhlIHNlbGVjdGJveCBhbHNvXG4gICAgICAgICAgICAgICAgdHJpZ2dlck5vQ2hhbmdlOiBmYWxzZVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgaGlkZXMgdGhlIGN1cnJlbnRseSBhY3RpdmVcbiAgICAgICAgICogZWxlbWVudCBmcm9tIHRoZSBkcm9wZG93blxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgICRjb250YWluZXIgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBkcm9wZG93biBjb250YWluZXJcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBvcHQgICAgICAgICAgICAgSlNPTiB3aXRoIGN1c3RvbSBzZXR0aW5ncyBmb3IgdGhhdCBjb250YWluZXJcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfaGlkZUFjdGl2ZSA9IGZ1bmN0aW9uICgkY29udGFpbmVyLCBvcHQpIHtcbiAgICAgICAgICAgIGlmIChvcHQuaGlkZUFjdGl2ZSkge1xuICAgICAgICAgICAgICAgIHZhciAkc2VsZWN0ID0gJGNvbnRhaW5lclxuICAgICAgICAgICAgICAgICAgICAgICAgLmNoaWxkcmVuKCdzZWxlY3QnKSxcbiAgICAgICAgICAgICAgICAgICAgdmFsdWUgPSAkc2VsZWN0XG4gICAgICAgICAgICAgICAgICAgICAgICAuY2hpbGRyZW4oJzpzZWxlY3RlZCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAudmFsKCk7XG5cbiAgICAgICAgICAgICAgICAkY29udGFpbmVyXG4gICAgICAgICAgICAgICAgICAgIC5maW5kKCdsaScpXG4gICAgICAgICAgICAgICAgICAgIC5zaG93KClcbiAgICAgICAgICAgICAgICAgICAgLmNoaWxkcmVuKCdhW2RhdGEtcmVsPVwiJyArIHZhbHVlICsgJ1wiXScpXG4gICAgICAgICAgICAgICAgICAgIC5wYXJlbnQoKVxuICAgICAgICAgICAgICAgICAgICAuaGlkZSgpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdG8gYWRkIGEgZGlzYWJsZWQgY2xhc3MgdG8gdGhlXG4gICAgICAgICAqIGRpc2FibGVkIGVudHJpZXMgaW4gdGhlIGN1c3RvbSBkcm9wZG93bi4gVGhlcmVmb3JcbiAgICAgICAgICogdGhlIG9yaWdpbmFsIHNlbGVjdCBpcyBzY2FubmVkIGZvciBkaXNhYmxlZCBlbnRyaWVzXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgJGNvbnRhaW5lciAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGRyb3Bkb3duIGNvbnRhaW5lclxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zZXREaXNhYmxlZCA9IGZ1bmN0aW9uICgkY29udGFpbmVyKSB7XG4gICAgICAgICAgICB2YXIgJHVsID0gJGNvbnRhaW5lci5jaGlsZHJlbigpLFxuICAgICAgICAgICAgICAgICRzZWxlY3QgPSAkY29udGFpbmVyLmNoaWxkcmVuKCdzZWxlY3QnKSxcbiAgICAgICAgICAgICAgICAkZGlzYWJsZWQgPSAkc2VsZWN0LmNoaWxkcmVuKCc6ZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgLy8gUmVtb3ZlIGFsbCBkaXNhYmxlZCBjbGFzc2VzIGZpcnN0XG4gICAgICAgICAgICAkdWxcbiAgICAgICAgICAgICAgICAuZmluZCgnLmRpc2FibGVkJylcbiAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgIC8vIEl0ZXJhdGUgdGhyb3VnaCBhbGwgZW50cmllcyB0aGF0IG5lZWRzIHRvXG4gICAgICAgICAgICAvLyBiZSBkaXNhYmxlZCBhbmQgYWRkIGEgY2xhc3MgdG8gdGhlbVxuICAgICAgICAgICAgJGRpc2FibGVkLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgIHZhbHVlID0gJHNlbGYudmFsKCk7XG5cbiAgICAgICAgICAgICAgICAkdWxcbiAgICAgICAgICAgICAgICAgICAgLmZpbmQoJ2FbZGF0YS1yZWw9XCInICsgdmFsdWUgKyAnXCJdJylcbiAgICAgICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gZm9yIHRoZSBfc2hvcnRlbkxhYmVsLWZ1bmN0aW9uLlxuICAgICAgICAgKiBUaGlzIGZ1bmN0aW9uIHNob3J0ZW5zIHRoZSBsYWJlbCBzbyB0aGF0IGl0IGZpdHNcbiAgICAgICAgICogaW5zaWRlIHRoZSBidXR0b24uIEFkZGl0aW9uYWwgYXZhaWxhYmxlIHNpYmxpbmdzXG4gICAgICAgICAqIG9mIHRoZSB0ZXh0IGVsZW1lbnQgd2VyZSBnZXR0aW5nIHN1YnN0cmFjdGVkIGZyb21cbiAgICAgICAgICogdGhlIGF2YWlsYWJsZSBidXR0b24gc2l6ZS5cbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICRidXR0b24gICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGJ1dHRvblxuICAgICAgICAgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgdmFsdWUgICAgICAgVGhlIHZhbHVlIHRoYXQgc2hvdWxkIGJlIHNldCBhcyB0aGUgYnV0dG9uIHRleHRcbiAgICAgICAgICogQHJldHVybiAgICAge3N0cmluZ30gICAgICAgICAgICAgICAgVGhlIHNob3J0ZW5lZCBzdHJpbmdcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfc2hvcnRlbkZpdCA9IGZ1bmN0aW9uICgkYnV0dG9uLCB2YWx1ZSkge1xuICAgICAgICAgICAgdmFyICRzaWJsaW5ncyA9ICRidXR0b24uY2hpbGRyZW4oKS5ub3QoJy5kcm9wZG93bi1uYW1lJyksXG4gICAgICAgICAgICAgICAgJHRleHRhcmVhID0gJGJ1dHRvbi5jaGlsZHJlbignLmRyb3Bkb3duLW5hbWUnKSxcbiAgICAgICAgICAgICAgICB3aWR0aCA9ICRidXR0b24ud2lkdGgoKSxcbiAgICAgICAgICAgICAgICBsZW5ndGggPSB2YWx1ZS5sZW5ndGgsXG4gICAgICAgICAgICAgICAgbmFtZSA9ICcnLFxuICAgICAgICAgICAgICAgIHNob3J0ZW4gPSBmYWxzZSxcbiAgICAgICAgICAgICAgICBpID0gMCxcbiAgICAgICAgICAgICAgICB0ZXN0ID0gbnVsbDtcblxuICAgICAgICAgICAgLy8gUmVtb3ZlIHRoZSBzaWJsaW5ncyB3aXRoIGZyb20gdGhlIGF2YWlsYWJsZVxuICAgICAgICAgICAgLy8gZnVsbCB3aWR0aCBvZiB0aGUgYnV0dG9uXG4gICAgICAgICAgICAkc2libGluZ3MuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgd2lkdGggLT0gJCh0aGlzKS5vdXRlcldpZHRoKCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy8gSXRlcmF0ZSB0aHJvdWdoIHRoZSBsYWJlbCBjaGFyYWN0ZXJzXG4gICAgICAgICAgICAvLyBhbmQgYWRkIG9uZSBjaGFyYWN0ZXIgYXQgdGltZSB0byB0aGUgYnV0dG9uXG4gICAgICAgICAgICAvLyBpZiB0aGUgdGV4dGZpZWxkIHNpemUgZ3Jvd3MgbGFyZ2VyIHRoYW5cbiAgICAgICAgICAgIC8vIHRoZSBhdmFpbGFibGUgd2lkdGggb2YgdGhlIGJ1dHRvbiBjYW5jZWxcbiAgICAgICAgICAgIC8vIHRoZSBsb29wIGFuZCB0YWtlIHRoZSBsYXN0IGZpdHRpbmcgdmFsdWVcbiAgICAgICAgICAgIC8vIGFzIHJlc3VsdFxuICAgICAgICAgICAgZm9yIChpOyBpIDwgbGVuZ3RoOyBpICs9IDEpIHtcbiAgICAgICAgICAgICAgICB0ZXN0ID0gdmFsdWUuc3Vic3RyaW5nKDAsIGkpICsgJy4uLic7XG4gICAgICAgICAgICAgICAgJHRleHRhcmVhLnRleHQodGVzdCk7XG5cbiAgICAgICAgICAgICAgICBpZiAoJHRleHRhcmVhLndpZHRoKCkgPiB3aWR0aCkge1xuICAgICAgICAgICAgICAgICAgICBzaG9ydGVuID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgbmFtZSA9IHRlc3Q7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vIElmIHRoZSB0ZXh0IHdhcyBzaG9ydGVuZWRcbiAgICAgICAgICAgIC8vIHJldHVybiB0aGUgc2hvcnRlbmVkIG5hbWVcbiAgICAgICAgICAgIC8vIGVsc2UgdGhlIGZ1bGwgbmFtZVxuICAgICAgICAgICAgaWYgKHNob3J0ZW4pIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gbmFtZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiB2YWx1ZTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIGZvciB0aGUgX3Nob3J0ZW5MYWJlbC1mdW5jdGlvbi5cbiAgICAgICAgICogVGhpcyBmdW5jdGlvbiBzaG9ydGVucyB0aGUgbGFiZWwgdG8gYSBzZXQgbnVtYmVyXG4gICAgICAgICAqIG9mIGRpZ2V0c1xuICAgICAgICAgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgdmFsdWUgICAgICAgVGhlIHZhbHVlIHRoYXQgc2hvdWxkIGJlIHNldCBhcyB0aGUgYnV0dG9uIHRleHRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIG9wdCAgICAgICAgIEpTT04gd2l0aCBjdXN0b20gc2V0dGluZ3MgZm9yIHRoYXQgY29udGFpbmVyXG4gICAgICAgICAqIEByZXR1cm4gICAgIHtzdHJpbmd9ICAgICAgICAgICAgICAgIFRoZSBzaG9ydGVuZWQgc3RyaW5nXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3Nob3J0ZW5JbnQgPSBmdW5jdGlvbiAodmFsdWUsIG9wdCkge1xuICAgICAgICAgICAgdmFyIGxlbmd0aCA9IHZhbHVlLmxlbmd0aCxcbiAgICAgICAgICAgICAgICBkaWZmID0gbGVuZ3RoIC0gb3B0LnNob3J0ZW47XG5cbiAgICAgICAgICAgIGlmIChkaWZmID4gMCkge1xuICAgICAgICAgICAgICAgIGRpZmYgKz0gMztcbiAgICAgICAgICAgICAgICByZXR1cm4gdmFsdWUuc3Vic3RyaW5nKDAsIGxlbmd0aCAtIGRpZmYpICsgJy4uLic7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiB2YWx1ZTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogRnVuY3Rpb24gdGhhdCBjaG9vc2VzIHRoZSBjb3JyZWN0IHNob3J0ZW5lclxuICAgICAgICAgKiBzdWJyb3V0aW5lIGZvciBzaG9ydGVuaW5nIHRoZSBidXR0b24gdGV4dFxuICAgICAgICAgKiAoaWYgbmVlZGVkKSBhbmQgcmV0dXJucyB0aGUgc2hvcnRlbmVkIHZhbHVlXG4gICAgICAgICAqIHRvIHRoZSBjYWxsZXJcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICRidXR0b24gICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGJ1dHRvblxuICAgICAgICAgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgdmFsdWUgICAgICAgVGhlIHZhbHVlIHRoYXQgc2hvdWxkIGJlIHNldCBhcyB0aGUgYnV0dG9uIHRleHRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIG9wdCAgICAgICAgIEpTT04gd2l0aCBjdXN0b20gc2V0dGluZ3MgZm9yIHRoYXQgY29udGFpbmVyXG4gICAgICAgICAqIEByZXR1cm4gICAgIHtzdHJpbmd9ICAgICAgICAgICAgICAgIFRoZSBzaG9ydGVuZWQgc3RyaW5nXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3Nob3J0ZW5MYWJlbCA9IGZ1bmN0aW9uICgkYnV0dG9uLCB2YWx1ZSwgb3B0KSB7XG4gICAgICAgICAgICBpZiAob3B0aW9ucy5icmVha3BvaW50IDwganNlLmxpYnMudGhlbWUucmVzcG9uc2l2ZS5icmVha3BvaW50KCkuaWQgfHwgb3B0LnNob3J0ZW5Pbk1vYmlsZSkge1xuICAgICAgICAgICAgICAgIGlmIChvcHQuc2hvcnRlbiA9PT0gJ2ZpdCcpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFsdWUgPSBfc2hvcnRlbkZpdCgkYnV0dG9uLCB2YWx1ZSk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmIChvcHQuc2hvcnRlbikge1xuICAgICAgICAgICAgICAgICAgICB2YWx1ZSA9IF9zaG9ydGVuSW50KHZhbHVlLCBvcHQpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHZhbHVlO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEV2ZW50IGhhbmRsZXIgdGhhdCBpc3QgdHJpZ2dlcmVkIG9uIGNoYW5nZVxuICAgICAgICAgKiBvZiB0aGUgc2VsZWN0Ym94IHRvIGZvcmNlIHRoZSBkcm9wZG93biB0byBjbG9zZVxuICAgICAgICAgKiAobmVlZGVkIG9uIG1vYmlsZSBkZXZpY2VzLCBiZWNhdXNlIG9mIGl0J3MgbmF0aXZlXG4gICAgICAgICAqIHN1cHBvcnQgZm9yIGRyb3Bkb3ducylcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2xvc2VMYXllciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgJGNvbnRhaW5lciA9ICRzZWxmLmNsb3Nlc3Qob3B0aW9ucy5jb250YWluZXIpLFxuICAgICAgICAgICAgICAgICRzZWxlY3QgPSAkY29udGFpbmVyLmNoaWxkcmVuKCdzZWxlY3QnKSxcbiAgICAgICAgICAgICAgICBkYXRhc2V0ID0gJC5leHRlbmQoe30sIG9wdGlvbnMsICRjb250YWluZXIucGFyc2VNb2R1bGVEYXRhKCdkcm9wZG93bicpKTtcblxuICAgICAgICAgICAgdHJhbnNpdGlvbi5vcGVuID0gZmFsc2U7XG4gICAgICAgICAgICAkY29udGFpbmVyLnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLlRSQU5TSVRJT04oKSwgdHJhbnNpdGlvbik7XG5cbiAgICAgICAgICAgIC8vIFRyaWdnZXIgdGhlIGNoYW5nZSBldmVudCBpZiB0aGUgb3B0aW9uIGlzIHNldFxuICAgICAgICAgICAgaWYgKGRhdGFzZXQudHJpZ2dlck5vQ2hhbmdlKSB7XG4gICAgICAgICAgICAgICAgJHNlbGVjdC50cmlnZ2VyKCdjaGFuZ2UnLCBbXSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEZ1bmN0aW9uIGdldHMgdHJpZ2dlcmVkIG9uIGNsaWNrIG9uIHRoZSBidXR0b24uXG4gICAgICAgICAqIEl0IHN3aXRjaGVzIHRoZSBzdGF0ZSBvZiB0aGUgZHJvcGRvd24gdmlzaWJpbGl0eVxuICAgICAgICAgKiBAcGFyYW0gICAgICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9vcGVuTGF5ZXIgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAkY29udGFpbmVyID0gJHNlbGYuY2xvc2VzdChvcHRpb25zLmNvbnRhaW5lciksXG4gICAgICAgICAgICAgICAgJHNlbGVjdCA9ICRjb250YWluZXIuY2hpbGRyZW4oJ3NlbGVjdCcpLFxuICAgICAgICAgICAgICAgIGRhdGFzZXQgPSAkLmV4dGVuZCh7fSwgb3B0aW9ucywgJGNvbnRhaW5lci5wYXJzZU1vZHVsZURhdGEoJ2Ryb3Bkb3duJykpO1xuXG4gICAgICAgICAgICBpZiAoJGNvbnRhaW5lci5oYXNDbGFzcyhvcHRpb25zLm9wZW5DbGFzcykpIHtcbiAgICAgICAgICAgICAgICAvLyBSZW1vdmUgdGhlIG9wZW4gY2xhc3MgaWYgdGhlIGxheWVyIGlzIG9wZW5lZFxuICAgICAgICAgICAgICAgIHRyYW5zaXRpb24ub3BlbiA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICRjb250YWluZXIudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcblxuICAgICAgICAgICAgICAgIC8vIFRyaWdnZXIgdGhlIGNoYW5nZSBldmVudCBpZiB0aGUgb3B0aW9uIGlzIHNldFxuICAgICAgICAgICAgICAgIGlmIChkYXRhc2V0LnRyaWdnZXJOb0NoYW5nZSkge1xuICAgICAgICAgICAgICAgICAgICAkc2VsZWN0LnRyaWdnZXIoJ2NoYW5nZScsIFtdKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIC8vIEFkZCB0aGUgb3BlbiBjbGFzcyBhbmQgaW5mb3JtIG90aGVyIGxheWVycyB0byBjbG9zZVxuICAgICAgICAgICAgICAgIF9oaWRlQWN0aXZlKCRjb250YWluZXIsIGRhdGFzZXQpO1xuICAgICAgICAgICAgICAgIF9zZXREaXNhYmxlZCgkY29udGFpbmVyKTtcblxuICAgICAgICAgICAgICAgIHRyYW5zaXRpb24ub3BlbiA9IHRydWU7XG4gICAgICAgICAgICAgICAgJGNvbnRhaW5lci50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuICAgICAgICAgICAgICAgICR0aGlzLnRyaWdnZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLk9QRU5fRkxZT1VUKCksIFskY29udGFpbmVyXSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhhbmRsZXIgdGhhdCBnZXRzIHVzZWQgaWYgdGhlIHVzZXJcbiAgICAgICAgICogc2VsZWN0cyBhIHZhbHVlIGZyb20gdGhlIGN1c3RvbSBkcm9wZG93bi5cbiAgICAgICAgICogSWYgdGhlIHZhbHVlIGhhcyBjaGFuZ2VkLCB0aGUgdmlldyBnZXRzXG4gICAgICAgICAqIHVwZGF0ZWQgYW5kIHRoZSBvcmlnaW5hbCBzZWxlY3QgZ2V0cyBzZXRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zZWxlY3RFbnRyeSA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICRsaSA9ICRzZWxmLnBhcmVudCgpO1xuXG4gICAgICAgICAgICAvLyBJZiB0aGUgaXRlbSBpcyBkaXNhYmxlZCwgZG8gbm90aGluZ1xuICAgICAgICAgICAgaWYgKCEkbGkuaGFzQ2xhc3MoJ2Rpc2FibGVkJykpIHtcblxuICAgICAgICAgICAgICAgIHZhciAkY29udGFpbmVyID0gJHNlbGYuY2xvc2VzdChvcHRpb25zLmNvbnRhaW5lciksXG4gICAgICAgICAgICAgICAgICAgICRidXR0b24gPSAkY29udGFpbmVyLmNoaWxkcmVuKCdidXR0b24nKSxcbiAgICAgICAgICAgICAgICAgICAgJHNlbGVjdCA9ICRjb250YWluZXIuY2hpbGRyZW4oJ3NlbGVjdCcpLFxuICAgICAgICAgICAgICAgICAgICBvbGRWYWx1ZSA9ICRzZWxlY3QuY2hpbGRyZW4oJzpzZWxlY3RlZCcpLnZhbCgpLFxuICAgICAgICAgICAgICAgICAgICBuZXdWYWx1ZSA9ICRzZWxmLmF0dHIoJ2RhdGEtcmVsJyksXG4gICAgICAgICAgICAgICAgICAgIG5hbWUgPSAkc2VsZi50ZXh0KCksXG4gICAgICAgICAgICAgICAgICAgIGRhdGFzZXQgPSAkLmV4dGVuZCh7fSwgb3B0aW9ucywgJGNvbnRhaW5lci5wYXJzZU1vZHVsZURhdGEoJ2Ryb3Bkb3duJykpO1xuXG4gICAgICAgICAgICAgICAgLy8gVXBkYXRlIHRoZSBkcm9wZG93biB2aWV3IGlmIHRoZVxuICAgICAgICAgICAgICAgIC8vIHZhbHVlIGhhcyBjaGFuZ2VkXG4gICAgICAgICAgICAgICAgaWYgKG9sZFZhbHVlICE9PSBuZXdWYWx1ZSkge1xuICAgICAgICAgICAgICAgICAgICAvLyBTZXQgdGhlIGJ1dHRvbiB0ZXh0XG4gICAgICAgICAgICAgICAgICAgIHZhciBzaG9ydGVuZWQgPSBfc2hvcnRlbkxhYmVsKCRidXR0b24sIG5hbWUsIGRhdGFzZXQpO1xuICAgICAgICAgICAgICAgICAgICAkYnV0dG9uXG4gICAgICAgICAgICAgICAgICAgICAgICAuY2hpbGRyZW4oJy5kcm9wZG93bi1uYW1lJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC50ZXh0KHNob3J0ZW5lZCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gU2V0IHRoZSBcIm9yaWdpbmFsXCIgc2VsZWN0IGJveCBhbmRcbiAgICAgICAgICAgICAgICAgICAgLy8gbm90aWZ5IHRoZSBicm93c2VyIC8gb3RoZXIganMgdGhhdCB0aGVcbiAgICAgICAgICAgICAgICAgICAgLy8gdmFsdWUgaGFzIGNoYW5nZWRcbiAgICAgICAgICAgICAgICAgICAgJHNlbGVjdFxuICAgICAgICAgICAgICAgICAgICAgICAgLmNoaWxkcmVuKCdbdmFsdWU9XCInICsgbmV3VmFsdWUgKyAnXCJdJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5wcm9wKCdzZWxlY3RlZCcsIHRydWUpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIFRyaWdnZXIgdGhlIGNoYW5nZSBldmVudCBpZiB0aGUgb3B0aW9uIGlzIHNldFxuICAgICAgICAgICAgICAgICAgICBpZiAoZGF0YXNldC50cmlnZ2VyQ2hhbmdlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkc2VsZWN0LnRyaWdnZXIoJ2NoYW5nZScsIFtdKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAoZGF0YXNldC50cmlnZ2VyTm9DaGFuZ2UpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gVHJpZ2dlciB0aGUgY2hhbmdlIGV2ZW50IGlmIHRoZSBvcHRpb24gaXMgc2V0XG4gICAgICAgICAgICAgICAgICAgICRzZWxlY3QudHJpZ2dlcignY2hhbmdlJywgW10pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIENsb3NlIHRoZSBsYXllclxuICAgICAgICAgICAgICAgIHRyYW5zaXRpb24ub3BlbiA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICRjb250YWluZXIudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGFuZGxlcyB0aGUgc3dpdGNoIGJldHdlZW4gdGhlIGJyZWFrcG9pbnQuIElmIHRoZVxuICAgICAgICAgKiBzaXplIG9mIHRoZSBidXR0b24gY2hhbmdlcyB0aGUgdGV4dCB3aWxsIGJlIHNob3J0ZW5lZFxuICAgICAgICAgKiBhZ2FpbiB0byBmaXQuIElmIHRoZSB2aWV3IHN3aXRjaGVzIHRvIG1vYmlsZSwgdGhpc1xuICAgICAgICAgKiBiZWhhdmlvdXIgaXMgc2tpcHBlZCB0aGUgZnVsbCBuYW1lIHdpbGwgYmUgZGlzcGxheWVkXG4gICAgICAgICAqIGFnYWluXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2JyZWFrcG9pbnRIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyICRjb250YWluZXIgPSAkdGhpcy5maW5kKG9wdGlvbnMuY29udGFpbmVyKTtcblxuICAgICAgICAgICAgaWYgKG9wdGlvbnMuYnJlYWtwb2ludCA8IGpzZS5saWJzLnRoZW1lLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkIHx8IG9wdGlvbnMuc2hvcnRlbk9uTW9iaWxlKSB7XG4gICAgICAgICAgICAgICAgLy8gSWYgc3RpbGwgaW4gZGVza3RvcCBtb2RlLCB0cnkgdG8gc2hvcnRlbiB0aGUgbmFtZVxuICAgICAgICAgICAgICAgICRjb250YWluZXIuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgICAgICAkYnV0dG9uID0gJHNlbGYuY2hpbGRyZW4oJ2J1dHRvbicpLFxuICAgICAgICAgICAgICAgICAgICAgICAgJHRleHRhcmVhID0gJGJ1dHRvbi5jaGlsZHJlbignLmRyb3Bkb3duLW5hbWUnKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlID0gJHNlbGYuZmluZCgnc2VsZWN0IG9wdGlvbjpzZWxlY3RlZCcpLnRleHQoKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFzZXQgPSAkLmV4dGVuZCh7fSwgb3B0aW9ucywgJHNlbGYucGFyc2VNb2R1bGVEYXRhKCdkcm9wZG93bicpKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHNob3J0ZW5lZCA9IF9zaG9ydGVuTGFiZWwoJGJ1dHRvbiwgdmFsdWUsIGRhdGFzZXQpO1xuXG4gICAgICAgICAgICAgICAgICAgICR0ZXh0YXJlYS50ZXh0KHNob3J0ZW5lZCk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIC8vIElmIGluIG1vYmlsZSBtb2RlIGluc2VydCB0aGUgY29tcGxldGUgbmFtZSBhZ2FpblxuICAgICAgICAgICAgICAgIC8vIGFuZCBjbG9zZSBvcGVuZWQgbGF5ZXJzXG4gICAgICAgICAgICAgICAgJGNvbnRhaW5lclxuICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpXG4gICAgICAgICAgICAgICAgICAgIC5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHRleHRhcmVhID0gJHNlbGYuZmluZCgnLmRyb3Bkb3duLW5hbWUnKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YWx1ZSA9ICRzZWxmLmZpbmQoJ3NlbGVjdCBvcHRpb246c2VsZWN0ZWQnKS50ZXh0KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICR0ZXh0YXJlYS50ZXh0KHZhbHVlKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhhbmRsZXIgZm9yIGNsb3NpbmcgYWxsIGRyb3Bkb3duIGZseW91dHMgaWZcbiAgICAgICAgICogc29tZXdoZXJlIG9uIHRoZSBwYWdlIG9wZW5zIGFuIG90aGVyIGZseW91dFxuICAgICAgICAgKiBAcGFyYW0gICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHBhcmFtICAge29iamVjdH0gICAgZCAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBldmVudCBlbWl0dGVyXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2Nsb3NlRmx5b3V0ID0gZnVuY3Rpb24gKGUsIGQpIHtcbiAgICAgICAgICAgIHZhciAkY29udGFpbmVycyA9ICR0aGlzLmZpbmQob3B0aW9ucy5jb250YWluZXIpLFxuICAgICAgICAgICAgICAgICRleGNsdWRlID0gZCB8fCAkKGUudGFyZ2V0KS5jbG9zZXN0KG9wdGlvbnMub3BlbkNsYXNzKTtcblxuICAgICAgICAgICAgJGNvbnRhaW5lcnMgPSAkY29udGFpbmVycy5ub3QoJGV4Y2x1ZGUpO1xuICAgICAgICAgICAgJGNvbnRhaW5lcnMucmVtb3ZlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgICAgICogQGNvbnN0cnVjdG9yXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cbiAgICAgICAgICAgIHRyYW5zaXRpb24uY2xhc3NPcGVuID0gb3B0aW9ucy5vcGVuQ2xhc3M7XG5cbiAgICAgICAgICAgICRib2R5XG4gICAgICAgICAgICAgICAgLm9uKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5PUEVOX0ZMWU9VVCgpICsgJyBjbGljaycsIF9jbG9zZUZseW91dClcbiAgICAgICAgICAgICAgICAub24oanNlLmxpYnMudGhlbWUuZXZlbnRzLkJSRUFLUE9JTlQoKSwgX2JyZWFrcG9pbnRIYW5kbGVyKTtcblxuICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAub24oJ2NsaWNrJywgb3B0aW9ucy5jb250YWluZXIgKyAnIGJ1dHRvbicsIF9vcGVuTGF5ZXIpXG4gICAgICAgICAgICAgICAgLm9uKCdjbGljaycsIG9wdGlvbnMuY29udGFpbmVyICsgJyB1bCBhJywgX3NlbGVjdEVudHJ5KVxuICAgICAgICAgICAgICAgIC5vbignY2hhbmdlJywgb3B0aW9ucy5jb250YWluZXIgKyAnIHNlbGVjdCcsIF9jbG9zZUxheWVyKTtcblxuICAgICAgICAgICAgaWYgKG9wdGlvbnMuc2hvcnRlbk9uSW5pdCkge1xuICAgICAgICAgICAgICAgIF9icmVha3BvaW50SGFuZGxlcigpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

'use strict';

/* --------------------------------------------------------------
 checkbox.js 2015-10-23 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Checkbox Widget
 *
 * Toggles the default checkboxes and 2-options radio boxes to a mobile like styling. This
 * widget can also be used to provide CSS style-able HTML markup so that we can have checkboxes
 * that look better.
 *
 * Important: Place the "data-use-glyphicons" to the widget element in HTML in order to use
 * glyphicons instead of the font-awesome icon library (applies currently only to "single-checkbox"
 * mode).
 *
 * @module Widgets/checkbox
 */
gambio.widgets.module('checkbox', [],

/** @lends module:Widgets/checkbox */

function (data) {

    'use strict';

    // ------------------------------------------------------------------------
    // VARIABLE DEFINITION
    // ------------------------------------------------------------------------

    var
    /**
     * Widget Reference
     *
     * @type {object}
     */
    $this = $(this),


    /**
     * Default Options for Widget
     *
     * @type {object}
     */
    defaults = {
        'filter': '', // Only select checkboxes with the following selector

        // Url Switcher Options:

        'on_url': '', // Open url when switcher is turned on
        'off_url': '', // Open url when switcher is turned off
        'on_label': '', // Text shown on the switcher when turned on
        'off_label': '', // Text shown on the switcher when turned off
        'on_text': '', // Text shown next to the switcher when turned on
        'off_text': '', // Text shown next to the switcher when turned off
        'class': '', // Add class(es) to the on and off switcher
        'checked': false // Initial status of the switcher: true = on, false = off
    },


    /**
     * Status of mouse down event
     *
     * @type {boolean}
     */
    mouseDown = false,


    /**
     * Final Widget Options
     *
     * @type {object}
     */
    options = $.extend(true, {}, defaults, data),


    /**
     * Meta Object
     *
     * @type {object}
     */
    module = {};

    // ------------------------------------------------------------------------
    // EVENT HANDLERS
    // ------------------------------------------------------------------------

    /**
     * Change the styling of the new switcher depending on the original checkbox/radio box setting
     * Additionally set the new state of the original checkbox/radio box and trigger the change event on it.
     *
     * @private
     */
    var _switcherChangeHandler = function _switcherChangeHandler(event) {
        if ($(this).hasClass('disabled')) {
            return false;
        }

        var $self = $(this),
            $checkbox = $self.find('input:checkbox'),
            $onElement = $self.find('input:radio').first(),
            $offElement = $self.find('input:radio').last(),
            $select = $self.find('select').first(),
            dataset = $self.parent().data('checkbox');

        $self.toggleClass('checked');

        $self.find('.state-description').show().fadeOut('slow');

        $checkbox.prop('checked', $self.hasClass('checked'));

        $onElement.prop('checked', $self.hasClass('checked'));

        $offElement.prop('checked', !$self.hasClass('checked'));

        $select.find('option').removeAttr('selected');

        var selectOptionToSelect = $self.hasClass('checked') ? 1 : 0;

        $select.find('option[value="' + selectOptionToSelect + '"]').attr('selected', true);

        if (options.on_url !== '' && options.off_url !== '') {
            event.preventDefault();
            event.stopPropagation();

            if (options.checked) {
                window.location.href = options.off_url;
                options.checked = false;

                return false;
            }

            window.location.href = options.on_url;
            options.checked = true;
        }
    };

    /**
     * Change the styling of the new checkbox depending on the original checkbox setting
     * Additionally set the new state of the original checkbox and trigger the change event on it.
     *
     * @private
     */
    var _checkboxMouseDownHandler = function _checkboxMouseDownHandler(e) {
        //e.stopPropagation();

        if ($(this).hasClass('disabled')) {
            return false;
        }

        mouseDown = true;

        $(this).find('input:checkbox').focus();
    };

    /**
     * Imitate mouse up behaviour of the checkbox.
     *
     * @private
     */
    var _checkboxMouseUpHandler = function _checkboxMouseUpHandler(e) {
        //e.stopPropagation();

        if ($(this).hasClass('disabled')) {
            return false;
        }

        $(this).toggleClass('checked');
        $(this).find('input:checkbox').focus();
        //$(this).find('input:checkbox').trigger('click');

        mouseDown = false;
    };

    // ------------------------------------------------------------------------
    // INITIALISATION FUNCTIONS
    // ------------------------------------------------------------------------

    /**
     * Wrap the checkboxes and generate markup for the new checkbox style.
     *
     * @private
     */
    var _initCheckboxes = function _initCheckboxes($target) {

        var $container = $target || $this;

        $container.find('input:checkbox').filter(options.filter || '*').each(function () {
            var $self = $(this),
                dataset = $self.parseModuleData('checkbox'),
                className = dataset.className || '',
                title = $self.prop('title'),
                isChecked = $self.prop('checked') ? 'checked' : '',
                isDisabled = $self.prop('disabled') ? 'disabled' : '';

            if (typeof $self.data('single_checkbox') !== 'undefined') {
                $self.css({
                    'position': 'absolute',
                    'left': '-100000px'
                }).wrap('<span class="single-checkbox ' + isChecked + ' ' + isDisabled + '" title="' + title + '"></span>');

                var iconClass = options.useGlyphicons !== undefined ? 'glyphicon glyphicon-ok' : 'fa fa-check';

                $self.parent().append('<i class="' + iconClass + '"></i>');

                $self.on('focus', function () {
                    $('.single_checkbox').removeClass('focused');
                    $(this).parent().addClass('focused');
                });

                $self.on('blur', function () {
                    $(this).parent().removeClass('focused');
                });

                $self.on('change', function () {
                    if (mouseDown === false) {
                        $(this).parent().toggleClass('checked');
                    }
                });
            } else {
                var onText = $self.attr('data-checkbox-on_text') ? $self.attr('data-checkbox-on_text') : '<span class="fa fa-check"></span>';

                var offText = $self.attr('data-checkbox-on_text') ? $self.attr('data-checkbox-off_text') : '<span class="fa fa-times"></span>';

                $self.wrap('<div class="switcher ' + isChecked + ' ' + isDisabled + '" title="' + title + '"></div>').parent().data('checkbox', dataset).addClass(className).append('<div class="switcher-toggler"></div>' + '<div class="switcher-inner">' + '<div class="switcher-state-on">' + onText + '</div>' + '<div class="switcher-state-off">' + offText + '</div>' + '</div>' + '<div class="switcher-text-on">' + options.on_text + '</div>' + '<div class="switcher-text-off">' + options.off_text + '</div>');
            }
        });
    };

    /**
     * Wrap the radio boxes and generate markup for the new checkbox style.
     *
     * @private
     */
    var _initRadioOptions = function _initRadioOptions() {
        if ($this.find('input:radio').filter(options.filter || '*').length === 2) {
            var $onElement = $this.find('input:radio').filter(options.filter || '*').first(),
                onTitle = $onElement.prop('title'),
                $offElement = $this.find('input:radio').filter(options.filter || '*').last(),
                offTitle = $offElement.prop('title'),
                onLabel = options.on_label !== '' ? ' data-checkbox-label="' + options.on_label + '"' : '',
                offLabel = options.off_label !== '' ? ' data-checkbox-label="' + options.off_label + '"' : '',
                dataset = options,
                isChecked = $onElement.prop('checked') ? 'checked' : '',
                isDisabled = $onElement.prop('disabled') ? 'disabled' : '';

            var $switcher = $('<div class="switcher ' + isChecked + ' ' + isDisabled + '"></div>');

            $onElement.after($switcher);

            $onElement.appendTo($switcher);
            $offElement.appendTo($switcher);

            $switcher.data('checkbox', dataset).addClass(options.class).append('<div class="switcher-toggler"></div>' + '<div class="switcher-inner">' + '<div class="switcher-state-on" title="' + onTitle + '"' + onLabel + '><span class="fa fa-check"></span></div>' + '<div class="switcher-state-off" title="' + offTitle + '"' + offLabel + '><span class="fa fa-times"></span></div>' + '<div class="switcher-text-on">' + options.on_text + '</div>' + '<div class="switcher-text-off">' + options.off_text + '</div>' + '</div>');

            // toggle switcher if hidden radio option status changes (there is no default case for that)
            $onElement.on('change', function () {
                $(this).parent().toggleClass('checked');
            });

            // toggle switcher if hidden radio option status changes (there is no default case for that)
            $offElement.on('change', function () {
                $(this).parent().toggleClass('checked');
            });
        }
    };

    /**
     * Build markup for the URL switcher.
     *
     * @private
     */
    var _initUrlSwitcher = function _initUrlSwitcher() {
        if (options.on_url !== '' && options.off_url !== '') {
            var dataset = $this.parseModuleData('checkbox'),
                onLabel = options.on_label !== '' ? ' data-checkbox-label="' + options.on_label + '"' : '',
                offLabel = options.off_label !== '' ? ' data-checkbox-label="' + options.off_label + '"' : '',
                isChecked = options.checked ? 'checked' : '';

            $this.data('checkbox', dataset).addClass('switcher').addClass(isChecked).addClass(options.class).append('<div class="switcher-toggler"></div>' + '<div class="switcher-inner">' + '<div class="switcher-state-on" title="' + options.off_url + '"' + onLabel + '><span class="fa fa-check"></span></div>' + '<div class="switcher-state-off" title="' + options.on_url + '"' + offLabel + '><span class="fa fa-times"></span></div>' + '</div>').on('click', _switcherChangeHandler);
        }
    };

    /**
     * Bind events that change the checkbox or switcher.
     *
     * @private
     */
    var _initEventHandlers = function _initEventHandlers() {
        $this.on('click', '.switcher', _switcherChangeHandler);

        $this.off('mousedown', '.single-checkbox');
        $this.on('mousedown', '.single-checkbox', _checkboxMouseDownHandler);
        $this.off('mouseup', '.single-checkbox');
        $this.on('mouseup', '.single-checkbox', _checkboxMouseUpHandler);

        $this.on('mousedown', 'label', function () {
            mouseDown = true;
        });

        $this.on('mouseup', 'label', function () {
            mouseDown = false;
        });

        $this.on('FORM_UPDATE', function (e) {
            var $target = $(e.target);
            $target.find('input:checkbox').each(function () {
                var $self = $(this),
                    $wrapper = $self.closest('.switcher');

                if ($wrapper.length) {
                    $wrapper.find('div').remove();
                    $self.unwrap();
                }
            });

            _initCheckboxes($target);
        });
    };

    /**
     * Convert "yes/no" select elements to a switcher.
     *
     * The selects must have a "data-convert-checkbox" attribute in order to be processed by
     * this method.
     *
     * @private
     */
    var _initSelects = function _initSelects() {
        // Iterate over select fields
        $this.find('[data-convert-checkbox]').each(function (index, element) {
            // Selectors f
            var $optionTrue = $(element).find('option[value="1"]'),
                $optionFalse = $(element).find('option[value="0"]');

            // States
            var isChecked = $optionTrue.is(':selected') ? 'checked' : '',
                isDisabled = $(element).is(':disabled') ? 'disabled' : '';

            // Switcher theme
            var $switcher = $('<div class="switcher ' + isChecked + ' ' + isDisabled + '"></div>');
            $switcher.addClass($(element).data('newClass')).data('checkbox', options).append('<div class="switcher-toggler"></div>' + '<div class="switcher-inner">' + '<div class="switcher-state-on"><span class="fa fa-check"></span></div>' + '<div class="switcher-state-off"><span class="fa fa-times"></span></div>' + '</div>');

            $(element).after($switcher).appendTo($switcher).hide();
        });
    };

    // ------------------------------------------------------------------------
    // INITIALIZATION
    // ------------------------------------------------------------------------

    /**
     * Initialize method of the widget, called by the engine.
     */
    module.init = function (done) {
        _initCheckboxes();
        _initRadioOptions();
        _initSelects();
        _initUrlSwitcher();
        _initEventHandlers();
        done();
    };

    // Return data to module engine.
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY2hlY2tib3guanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm1vdXNlRG93biIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc3dpdGNoZXJDaGFuZ2VIYW5kbGVyIiwiZXZlbnQiLCJoYXNDbGFzcyIsIiRzZWxmIiwiJGNoZWNrYm94IiwiZmluZCIsIiRvbkVsZW1lbnQiLCJmaXJzdCIsIiRvZmZFbGVtZW50IiwibGFzdCIsIiRzZWxlY3QiLCJkYXRhc2V0IiwicGFyZW50IiwidG9nZ2xlQ2xhc3MiLCJzaG93IiwiZmFkZU91dCIsInByb3AiLCJyZW1vdmVBdHRyIiwic2VsZWN0T3B0aW9uVG9TZWxlY3QiLCJhdHRyIiwib25fdXJsIiwib2ZmX3VybCIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwiY2hlY2tlZCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsIl9jaGVja2JveE1vdXNlRG93bkhhbmRsZXIiLCJlIiwiZm9jdXMiLCJfY2hlY2tib3hNb3VzZVVwSGFuZGxlciIsIl9pbml0Q2hlY2tib3hlcyIsIiR0YXJnZXQiLCIkY29udGFpbmVyIiwiZmlsdGVyIiwiZWFjaCIsInBhcnNlTW9kdWxlRGF0YSIsImNsYXNzTmFtZSIsInRpdGxlIiwiaXNDaGVja2VkIiwiaXNEaXNhYmxlZCIsImNzcyIsIndyYXAiLCJpY29uQ2xhc3MiLCJ1c2VHbHlwaGljb25zIiwidW5kZWZpbmVkIiwiYXBwZW5kIiwib24iLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwib25UZXh0Iiwib2ZmVGV4dCIsIm9uX3RleHQiLCJvZmZfdGV4dCIsIl9pbml0UmFkaW9PcHRpb25zIiwibGVuZ3RoIiwib25UaXRsZSIsIm9mZlRpdGxlIiwib25MYWJlbCIsIm9uX2xhYmVsIiwib2ZmTGFiZWwiLCJvZmZfbGFiZWwiLCIkc3dpdGNoZXIiLCJhZnRlciIsImFwcGVuZFRvIiwiY2xhc3MiLCJfaW5pdFVybFN3aXRjaGVyIiwiX2luaXRFdmVudEhhbmRsZXJzIiwib2ZmIiwidGFyZ2V0IiwiJHdyYXBwZXIiLCJjbG9zZXN0IiwicmVtb3ZlIiwidW53cmFwIiwiX2luaXRTZWxlY3RzIiwiaW5kZXgiLCJlbGVtZW50IiwiJG9wdGlvblRydWUiLCIkb3B0aW9uRmFsc2UiLCJpcyIsImhpZGUiLCJpbml0IiwiZG9uZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7O0FBYUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLFVBREosRUFHSSxFQUhKOztBQUtJOztBQUVBLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0k7Ozs7O0FBS0FDLFlBQVFDLEVBQUUsSUFBRixDQU5aOzs7QUFRSTs7Ozs7QUFLQUMsZUFBVztBQUNQLGtCQUFVLEVBREgsRUFDTzs7QUFFZDs7QUFFQSxrQkFBVSxFQUxILEVBS087QUFDZCxtQkFBVyxFQU5KLEVBTVE7QUFDZixvQkFBWSxFQVBMLEVBT1M7QUFDaEIscUJBQWEsRUFSTixFQVFVO0FBQ2pCLG1CQUFXLEVBVEosRUFTUTtBQUNmLG9CQUFZLEVBVkwsRUFVUztBQUNoQixpQkFBUyxFQVhGLEVBV007QUFDYixtQkFBVyxLQVpKLENBWVU7QUFaVixLQWJmOzs7QUE0Qkk7Ozs7O0FBS0FDLGdCQUFZLEtBakNoQjs7O0FBbUNJOzs7OztBQUtBQyxjQUFVSCxFQUFFSSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJILFFBQW5CLEVBQTZCSCxJQUE3QixDQXhDZDs7O0FBMENJOzs7OztBQUtBRCxhQUFTLEVBL0NiOztBQWlEQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQU1BLFFBQUlRLHlCQUF5QixTQUF6QkEsc0JBQXlCLENBQVVDLEtBQVYsRUFBaUI7QUFDMUMsWUFBSU4sRUFBRSxJQUFGLEVBQVFPLFFBQVIsQ0FBaUIsVUFBakIsQ0FBSixFQUFrQztBQUM5QixtQkFB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Q0MsVUFBNUMsR0FBeUQsVUFBM0QsQ0FBaEI7QUFDQXNCLHNCQUNLYixRQURMLENBQ2N2RCxFQUFFa0YsT0FBRixFQUFXcEYsSUFBWCxDQUFnQixVQUFoQixDQURkLEVBRUtBLElBRkwsQ0FFVSxVQUZWLEVBRXNCSyxPQUZ0QixFQUdLaUQsTUFITCxDQUdZLHlDQUF5Qyw4QkFBekMsR0FDSix3RUFESSxHQUVKLHlFQUZJLEdBRXdFLFFBTHBGOztBQVFBcEQsY0FBRWtGLE9BQUYsRUFDS2IsS0FETCxDQUNXRCxTQURYLEVBRUtFLFFBRkwsQ0FFY0YsU0FGZCxFQUdLa0IsSUFITDtBQUlILFNBdkJEO0FBd0JILEtBMUJEOztBQTRCQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBekYsV0FBTzBGLElBQVAsR0FBYyxVQUFVQyxJQUFWLEVBQWdCO0FBQzFCbkQ7QUFDQXVCO0FBQ0FvQjtBQUNBUjtBQUNBQztBQUNBZTtBQUNILEtBUEQ7O0FBU0E7QUFDQSxXQUFPM0YsTUFBUDtBQUNILENBaGFMIiwiZmlsZSI6IndpZGdldHMvY2hlY2tib3guanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNoZWNrYm94LmpzIDIwMTUtMTAtMjMgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIENoZWNrYm94IFdpZGdldFxuICpcbiAqIFRvZ2dsZXMgdGhlIGRlZmF1bHQgY2hlY2tib3hlcyBhbmQgMi1vcHRpb25zIHJhZGlvIGJveGVzIHRvIGEgbW9iaWxlIGxpa2Ugc3R5bGluZy4gVGhpc1xuICogd2lkZ2V0IGNhbiBhbHNvIGJlIHVzZWQgdG8gcHJvdmlkZSBDU1Mgc3R5bGUtYWJsZSBIVE1MIG1hcmt1cCBzbyB0aGF0IHdlIGNhbiBoYXZlIGNoZWNrYm94ZXNcbiAqIHRoYXQgbG9vayBiZXR0ZXIuXG4gKlxuICogSW1wb3J0YW50OiBQbGFjZSB0aGUgXCJkYXRhLXVzZS1nbHlwaGljb25zXCIgdG8gdGhlIHdpZGdldCBlbGVtZW50IGluIEhUTUwgaW4gb3JkZXIgdG8gdXNlXG4gKiBnbHlwaGljb25zIGluc3RlYWQgb2YgdGhlIGZvbnQtYXdlc29tZSBpY29uIGxpYnJhcnkgKGFwcGxpZXMgY3VycmVudGx5IG9ubHkgdG8gXCJzaW5nbGUtY2hlY2tib3hcIlxuICogbW9kZSkuXG4gKlxuICogQG1vZHVsZSBXaWRnZXRzL2NoZWNrYm94XG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnY2hlY2tib3gnLFxuXG4gICAgW10sXG5cbiAgICAvKiogQGxlbmRzIG1vZHVsZTpXaWRnZXRzL2NoZWNrYm94ICovXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAgICAgLy8gVkFSSUFCTEUgREVGSU5JVElPTlxuICAgICAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgICAgICB2YXJcbiAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICogV2lkZ2V0IFJlZmVyZW5jZVxuICAgICAgICAgICAgICpcbiAgICAgICAgICAgICAqIEB0eXBlIHtvYmplY3R9XG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgICR0aGlzID0gJCh0aGlzKSxcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBEZWZhdWx0IE9wdGlvbnMgZm9yIFdpZGdldFxuICAgICAgICAgICAgICpcbiAgICAgICAgICAgICAqIEB0eXBlIHtvYmplY3R9XG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgICdmaWx0ZXInOiAnJywgLy8gT25seSBzZWxlY3QgY2hlY2tib3hlcyB3aXRoIHRoZSBmb2xsb3dpbmcgc2VsZWN0b3JcblxuICAgICAgICAgICAgICAgIC8vIFVybCBTd2l0Y2hlciBPcHRpb25zOlxuXG4gICAgICAgICAgICAgICAgJ29uX3VybCc6ICcnLCAvLyBPcGVuIHVybCB3aGVuIHN3aXRjaGVyIGlzIHR1cm5lZCBvblxuICAgICAgICAgICAgICAgICdvZmZfdXJsJzogJycsIC8vIE9wZW4gdXJsIHdoZW4gc3dpdGNoZXIgaXMgdHVybmVkIG9mZlxuICAgICAgICAgICAgICAgICdvbl9sYWJlbCc6ICcnLCAvLyBUZXh0IHNob3duIG9uIHRoZSBzd2l0Y2hlciB3aGVuIHR1cm5lZCBvblxuICAgICAgICAgICAgICAgICdvZmZfbGFiZWwnOiAnJywgLy8gVGV4dCBzaG93biBvbiB0aGUgc3dpdGNoZXIgd2hlbiB0dXJuZWQgb2ZmXG4gICAgICAgICAgICAgICAgJ29uX3RleHQnOiAnJywgLy8gVGV4dCBzaG93biBuZXh0IHRvIHRoZSBzd2l0Y2hlciB3aGVuIHR1cm5lZCBvblxuICAgICAgICAgICAgICAgICdvZmZfdGV4dCc6ICcnLCAvLyBUZXh0IHNob3duIG5leHQgdG8gdGhlIHN3aXRjaGVyIHdoZW4gdHVybmVkIG9mZlxuICAgICAgICAgICAgICAgICdjbGFzcyc6ICcnLCAvLyBBZGQgY2xhc3MoZXMpIHRvIHRoZSBvbiBhbmQgb2ZmIHN3aXRjaGVyXG4gICAgICAgICAgICAgICAgJ2NoZWNrZWQnOiBmYWxzZSAvLyBJbml0aWFsIHN0YXR1cyBvZiB0aGUgc3dpdGNoZXI6IHRydWUgPSBvbiwgZmFsc2UgPSBvZmZcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICogU3RhdHVzIG9mIG1vdXNlIGRvd24gZXZlbnRcbiAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgKiBAdHlwZSB7Ym9vbGVhbn1cbiAgICAgICAgICAgICAqL1xuICAgICAgICAgICAgbW91c2VEb3duID0gZmFsc2UsXG5cbiAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcbiAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgKiBAdHlwZSB7b2JqZWN0fVxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBNZXRhIE9iamVjdFxuICAgICAgICAgICAgICpcbiAgICAgICAgICAgICAqIEB0eXBlIHtvYmplY3R9XG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIG1vZHVsZSA9IHt9O1xuXG4gICAgICAgIC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICAgICAgICAvLyBFVkVOVCBIQU5ETEVSU1xuICAgICAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgICAgICAvKipcbiAgICAgICAgICogQ2hhbmdlIHRoZSBzdHlsaW5nIG9mIHRoZSBuZXcgc3dpdGNoZXIgZGVwZW5kaW5nIG9uIHRoZSBvcmlnaW5hbCBjaGVja2JveC9yYWRpbyBib3ggc2V0dGluZ1xuICAgICAgICAgKiBBZGRpdGlvbmFsbHkgc2V0IHRoZSBuZXcgc3RhdGUgb2YgdGhlIG9yaWdpbmFsIGNoZWNrYm94L3JhZGlvIGJveCBhbmQgdHJpZ2dlciB0aGUgY2hhbmdlIGV2ZW50IG9uIGl0LlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zd2l0Y2hlckNoYW5nZUhhbmRsZXIgPSBmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICAgICAgICAgIGlmICgkKHRoaXMpLmhhc0NsYXNzKCdkaXNhYmxlZCcpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICRjaGVja2JveCA9ICRzZWxmLmZpbmQoJ2lucHV0OmNoZWNrYm94JyksXG4gICAgICAgICAgICAgICAgJG9uRWxlbWVudCA9ICRzZWxmLmZpbmQoJ2lucHV0OnJhZGlvJykuZmlyc3QoKSxcbiAgICAgICAgICAgICAgICAkb2ZmRWxlbWVudCA9ICRzZWxmLmZpbmQoJ2lucHV0OnJhZGlvJykubGFzdCgpLFxuICAgICAgICAgICAgICAgICRzZWxlY3QgPSAkc2VsZi5maW5kKCdzZWxlY3QnKS5maXJzdCgpLFxuICAgICAgICAgICAgICAgIGRhdGFzZXQgPSAkc2VsZi5wYXJlbnQoKS5kYXRhKCdjaGVja2JveCcpO1xuXG4gICAgICAgICAgICAkc2VsZi50b2dnbGVDbGFzcygnY2hlY2tlZCcpO1xuXG4gICAgICAgICAgICAkc2VsZi5maW5kKCcuc3RhdGUtZGVzY3JpcHRpb24nKS5zaG93KCkuZmFkZU91dCgnc2xvdycpO1xuXG4gICAgICAgICAgICAkY2hlY2tib3hcbiAgICAgICAgICAgICAgICAucHJvcCgnY2hlY2tlZCcsICRzZWxmLmhhc0NsYXNzKCdjaGVja2VkJykpO1xuXG4gICAgICAgICAgICAkb25FbGVtZW50XG4gICAgICAgICAgICAgICAgLnByb3AoJ2NoZWNrZWQnLCAkc2VsZi5oYXNDbGFzcygnY2hlY2tlZCcpKTtcblxuICAgICAgICAgICAgJG9mZkVsZW1lbnRcbiAgICAgICAgICAgICAgICAucHJvcCgnY2hlY2tlZCcsICEkc2VsZi5oYXNDbGFzcygnY2hlY2tlZCcpKTtcblxuICAgICAgICAgICAgJHNlbGVjdFxuICAgICAgICAgICAgICAgIC5maW5kKCdvcHRpb24nKVxuICAgICAgICAgICAgICAgIC5yZW1vdmVBdHRyKCdzZWxlY3RlZCcpO1xuXG4gICAgICAgICAgICB2YXIgc2VsZWN0T3B0aW9uVG9TZWxlY3QgPSAkc2VsZi5oYXNDbGFzcygnY2hlY2tlZCcpID8gMSA6IDA7XG5cbiAgICAgICAgICAgICRzZWxlY3RcbiAgICAgICAgICAgICAgICAuZmluZCgnb3B0aW9uW3ZhbHVlPVwiJyArIHNlbGVjdE9wdGlvblRvU2VsZWN0ICsgJ1wiXScpXG4gICAgICAgICAgICAgICAgLmF0dHIoJ3NlbGVjdGVkJywgdHJ1ZSk7XG5cbiAgICAgICAgICAgIGlmIChvcHRpb25zLm9uX3VybCAhPT0gJycgJiYgb3B0aW9ucy5vZmZfdXJsICE9PSAnJykge1xuICAgICAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgICAgICBpZiAob3B0aW9ucy5jaGVja2VkKSB7XG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gb3B0aW9ucy5vZmZfdXJsO1xuICAgICAgICAgICAgICAgICAgICBvcHRpb25zLmNoZWNrZWQgPSBmYWxzZTtcblxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSBvcHRpb25zLm9uX3VybDtcbiAgICAgICAgICAgICAgICBvcHRpb25zLmNoZWNrZWQgPSB0cnVlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIENoYW5nZSB0aGUgc3R5bGluZyBvZiB0aGUgbmV3IGNoZWNrYm94IGRlcGVuZGluZyBvbiB0aGUgb3JpZ2luYWwgY2hlY2tib3ggc2V0dGluZ1xuICAgICAgICAgKiBBZGRpdGlvbmFsbHkgc2V0IHRoZSBuZXcgc3RhdGUgb2YgdGhlIG9yaWdpbmFsIGNoZWNrYm94IGFuZCB0cmlnZ2VyIHRoZSBjaGFuZ2UgZXZlbnQgb24gaXQuXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2NoZWNrYm94TW91c2VEb3duSGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAvL2Uuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIGlmICgkKHRoaXMpLmhhc0NsYXNzKCdkaXNhYmxlZCcpKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBtb3VzZURvd24gPSB0cnVlO1xuXG4gICAgICAgICAgICAkKHRoaXMpLmZpbmQoJ2lucHV0OmNoZWNrYm94JykuZm9jdXMoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW1pdGF0ZSBtb3VzZSB1cCBiZWhhdmlvdXIgb2YgdGhlIGNoZWNrYm94LlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9jaGVja2JveE1vdXNlVXBIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIC8vZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgaWYgKCQodGhpcykuaGFzQ2xhc3MoJ2Rpc2FibGVkJykpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICQodGhpcykudG9nZ2xlQ2xhc3MoJ2NoZWNrZWQnKTtcbiAgICAgICAgICAgICQodGhpcykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS5mb2N1cygpO1xuICAgICAgICAgICAgLy8kKHRoaXMpLmZpbmQoJ2lucHV0OmNoZWNrYm94JykudHJpZ2dlcignY2xpY2snKTtcblxuICAgICAgICAgICAgbW91c2VEb3duID0gZmFsc2U7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgICAgIC8vIElOSVRJQUxJU0FUSU9OIEZVTkNUSU9OU1xuICAgICAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuICAgICAgICAvKipcbiAgICAgICAgICogV3JhcCB0aGUgY2hlY2tib3hlcyBhbmQgZ2VuZXJhdGUgbWFya3VwIGZvciB0aGUgbmV3IGNoZWNrYm94IHN0eWxlLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9pbml0Q2hlY2tib3hlcyA9IGZ1bmN0aW9uICgkdGFyZ2V0KSB7XG5cbiAgICAgICAgICAgIHZhciAkY29udGFpbmVyID0gJHRhcmdldCB8fCAkdGhpcztcblxuICAgICAgICAgICAgJGNvbnRhaW5lclxuICAgICAgICAgICAgICAgIC5maW5kKCdpbnB1dDpjaGVja2JveCcpXG4gICAgICAgICAgICAgICAgLmZpbHRlcihvcHRpb25zLmZpbHRlciB8fCAnKicpXG4gICAgICAgICAgICAgICAgLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YXNldCA9ICRzZWxmLnBhcnNlTW9kdWxlRGF0YSgnY2hlY2tib3gnKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGNsYXNzTmFtZSA9IGRhdGFzZXQuY2xhc3NOYW1lIHx8ICcnLFxuICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGUgPSAkc2VsZi5wcm9wKCd0aXRsZScpLFxuICAgICAgICAgICAgICAgICAgICAgICAgaXNDaGVja2VkID0gKCRzZWxmLnByb3AoJ2NoZWNrZWQnKSkgPyAnY2hlY2tlZCcgOiAnJyxcbiAgICAgICAgICAgICAgICAgICAgICAgIGlzRGlzYWJsZWQgPSAoJHNlbGYucHJvcCgnZGlzYWJsZWQnKSkgPyAnZGlzYWJsZWQnIDogJyc7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHR5cGVvZiAkc2VsZi5kYXRhKCdzaW5nbGVfY2hlY2tib3gnKSAhPT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdwb3NpdGlvbic6ICdhYnNvbHV0ZScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdsZWZ0JzogJy0xMDAwMDBweCdcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC53cmFwKCc8c3BhbiBjbGFzcz1cInNpbmdsZS1jaGVja2JveCAnICsgaXNDaGVja2VkICsgJyAnICsgaXNEaXNhYmxlZCArICdcIiB0aXRsZT1cIicgK1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aXRsZSArICdcIj48L3NwYW4+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBpY29uQ2xhc3MgPSAob3B0aW9ucy51c2VHbHlwaGljb25zICE9PSB1bmRlZmluZWQpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPyAnZ2x5cGhpY29uIGdseXBoaWNvbi1vaydcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA6ICdmYSBmYS1jaGVjayc7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmLnBhcmVudCgpLmFwcGVuZCgnPGkgY2xhc3M9XCInICsgaWNvbkNsYXNzICsgJ1wiPjwvaT4nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGYub24oJ2ZvY3VzJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJy5zaW5nbGVfY2hlY2tib3gnKS5yZW1vdmVDbGFzcygnZm9jdXNlZCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucGFyZW50KCkuYWRkQ2xhc3MoJ2ZvY3VzZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkc2VsZi5vbignYmx1cicsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnBhcmVudCgpLnJlbW92ZUNsYXNzKCdmb2N1c2VkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGYub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAobW91c2VEb3duID09PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnBhcmVudCgpLnRvZ2dsZUNsYXNzKCdjaGVja2VkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBvblRleHQgPSAoJHNlbGYuYXR0cignZGF0YS1jaGVja2JveC1vbl90ZXh0JykpID8gJHNlbGYuYXR0cignZGF0YS1jaGVja2JveC1vbl90ZXh0JykgOlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICc8c3BhbiBjbGFzcz1cImZhIGZhLWNoZWNrXCI+PC9zcGFuPic7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBvZmZUZXh0ID0gKCRzZWxmLmF0dHIoJ2RhdGEtY2hlY2tib3gtb25fdGV4dCcpKSA/ICRzZWxmLmF0dHIoJ2RhdGEtY2hlY2tib3gtb2ZmX3RleHQnKSA6XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJzxzcGFuIGNsYXNzPVwiZmEgZmEtdGltZXNcIj48L3NwYW4+JztcblxuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGZcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAud3JhcCgnPGRpdiBjbGFzcz1cInN3aXRjaGVyICcgKyBpc0NoZWNrZWQgKyAnICcgKyBpc0Rpc2FibGVkICsgJ1wiIHRpdGxlPVwiJyArIHRpdGxlICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ1wiPjwvZGl2PicpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLnBhcmVudCgpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmRhdGEoJ2NoZWNrYm94JywgZGF0YXNldClcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoY2xhc3NOYW1lKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10b2dnbGVyXCI+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItaW5uZXJcIj4nICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1zdGF0ZS1vblwiPicgKyBvblRleHQgKyAnPC9kaXY+JyArXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb2ZmXCI+JyArIG9mZlRleHQgKyAnPC9kaXY+JyArICc8L2Rpdj4nICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10ZXh0LW9uXCI+JyArIG9wdGlvbnMub25fdGV4dCArICc8L2Rpdj4nICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10ZXh0LW9mZlwiPicgKyBvcHRpb25zLm9mZl90ZXh0ICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJzwvZGl2PidcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICApO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFdyYXAgdGhlIHJhZGlvIGJveGVzIGFuZCBnZW5lcmF0ZSBtYXJrdXAgZm9yIHRoZSBuZXcgY2hlY2tib3ggc3R5bGUuXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2luaXRSYWRpb09wdGlvbnMgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBpZiAoJHRoaXMuZmluZCgnaW5wdXQ6cmFkaW8nKS5maWx0ZXIob3B0aW9ucy5maWx0ZXIgfHwgJyonKS5sZW5ndGggPT09IDIpIHtcbiAgICAgICAgICAgICAgICB2YXIgJG9uRWxlbWVudCA9ICR0aGlzLmZpbmQoJ2lucHV0OnJhZGlvJykuZmlsdGVyKG9wdGlvbnMuZmlsdGVyIHx8ICcqJykuZmlyc3QoKSxcbiAgICAgICAgICAgICAgICAgICAgb25UaXRsZSA9ICRvbkVsZW1lbnQucHJvcCgndGl0bGUnKSxcbiAgICAgICAgICAgICAgICAgICAgJG9mZkVsZW1lbnQgPSAkdGhpcy5maW5kKCdpbnB1dDpyYWRpbycpLmZpbHRlcihvcHRpb25zLmZpbHRlciB8fCAnKicpLmxhc3QoKSxcbiAgICAgICAgICAgICAgICAgICAgb2ZmVGl0bGUgPSAkb2ZmRWxlbWVudC5wcm9wKCd0aXRsZScpLFxuICAgICAgICAgICAgICAgICAgICBvbkxhYmVsID0gKG9wdGlvbnMub25fbGFiZWwgIT09ICcnKSA/ICcgZGF0YS1jaGVja2JveC1sYWJlbD1cIicgKyBvcHRpb25zLm9uX2xhYmVsICsgJ1wiJyA6ICcnLFxuICAgICAgICAgICAgICAgICAgICBvZmZMYWJlbCA9IChvcHRpb25zLm9mZl9sYWJlbCAhPT0gJycpID8gJyBkYXRhLWNoZWNrYm94LWxhYmVsPVwiJyArIG9wdGlvbnMub2ZmX2xhYmVsICsgJ1wiJyA6XG4gICAgICAgICAgICAgICAgICAgICAgICAnJyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YXNldCA9IG9wdGlvbnMsXG4gICAgICAgICAgICAgICAgICAgIGlzQ2hlY2tlZCA9ICgkb25FbGVtZW50LnByb3AoJ2NoZWNrZWQnKSkgPyAnY2hlY2tlZCcgOiAnJyxcbiAgICAgICAgICAgICAgICAgICAgaXNEaXNhYmxlZCA9ICgkb25FbGVtZW50LnByb3AoJ2Rpc2FibGVkJykpID8gJ2Rpc2FibGVkJyA6ICcnO1xuXG4gICAgICAgICAgICAgICAgdmFyICRzd2l0Y2hlciA9ICQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlciAnICsgaXNDaGVja2VkICsgJyAnICsgaXNEaXNhYmxlZCArICdcIj48L2Rpdj4nKTtcblxuICAgICAgICAgICAgICAgICRvbkVsZW1lbnQuYWZ0ZXIoJHN3aXRjaGVyKTtcblxuICAgICAgICAgICAgICAgICRvbkVsZW1lbnQuYXBwZW5kVG8oJHN3aXRjaGVyKTtcbiAgICAgICAgICAgICAgICAkb2ZmRWxlbWVudC5hcHBlbmRUbygkc3dpdGNoZXIpO1xuXG4gICAgICAgICAgICAgICAgJHN3aXRjaGVyXG4gICAgICAgICAgICAgICAgICAgIC5kYXRhKCdjaGVja2JveCcsIGRhdGFzZXQpXG4gICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcyhvcHRpb25zLmNsYXNzKVxuICAgICAgICAgICAgICAgICAgICAuYXBwZW5kKCc8ZGl2IGNsYXNzPVwic3dpdGNoZXItdG9nZ2xlclwiPjwvZGl2PicgKyAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLWlubmVyXCI+JyArXG4gICAgICAgICAgICAgICAgICAgICAgICAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXN0YXRlLW9uXCIgdGl0bGU9XCInICsgb25UaXRsZSArICdcIicgKyBvbkxhYmVsICtcbiAgICAgICAgICAgICAgICAgICAgICAgICc+PHNwYW4gY2xhc3M9XCJmYSBmYS1jaGVja1wiPjwvc3Bhbj48L2Rpdj4nICtcbiAgICAgICAgICAgICAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb2ZmXCIgdGl0bGU9XCInICsgb2ZmVGl0bGUgKyAnXCInICsgb2ZmTGFiZWwgK1xuICAgICAgICAgICAgICAgICAgICAgICAgJz48c3BhbiBjbGFzcz1cImZhIGZhLXRpbWVzXCI+PC9zcGFuPjwvZGl2PicgKyAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXRleHQtb25cIj4nXG4gICAgICAgICAgICAgICAgICAgICAgICArIG9wdGlvbnMub25fdGV4dCArXG4gICAgICAgICAgICAgICAgICAgICAgICAnPC9kaXY+JyArXG4gICAgICAgICAgICAgICAgICAgICAgICAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXRleHQtb2ZmXCI+JyArIG9wdGlvbnMub2ZmX3RleHQgKyAnPC9kaXY+JyArICc8L2Rpdj4nXG4gICAgICAgICAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgICAgICAvLyB0b2dnbGUgc3dpdGNoZXIgaWYgaGlkZGVuIHJhZGlvIG9wdGlvbiBzdGF0dXMgY2hhbmdlcyAodGhlcmUgaXMgbm8gZGVmYXVsdCBjYXNlIGZvciB0aGF0KVxuICAgICAgICAgICAgICAgICRvbkVsZW1lbnQub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5wYXJlbnQoKS50b2dnbGVDbGFzcygnY2hlY2tlZCcpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy8gdG9nZ2xlIHN3aXRjaGVyIGlmIGhpZGRlbiByYWRpbyBvcHRpb24gc3RhdHVzIGNoYW5nZXMgKHRoZXJlIGlzIG5vIGRlZmF1bHQgY2FzZSBmb3IgdGhhdClcbiAgICAgICAgICAgICAgICAkb2ZmRWxlbWVudC5vbignY2hhbmdlJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnBhcmVudCgpLnRvZ2dsZUNsYXNzKCdjaGVja2VkJyk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogQnVpbGQgbWFya3VwIGZvciB0aGUgVVJMIHN3aXRjaGVyLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9pbml0VXJsU3dpdGNoZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBpZiAob3B0aW9ucy5vbl91cmwgIT09ICcnICYmIG9wdGlvbnMub2ZmX3VybCAhPT0gJycpIHtcbiAgICAgICAgICAgICAgICB2YXIgZGF0YXNldCA9ICR0aGlzLnBhcnNlTW9kdWxlRGF0YSgnY2hlY2tib3gnKSxcbiAgICAgICAgICAgICAgICAgICAgb25MYWJlbCA9IChvcHRpb25zLm9uX2xhYmVsICE9PSAnJykgPyAnIGRhdGEtY2hlY2tib3gtbGFiZWw9XCInICsgb3B0aW9ucy5vbl9sYWJlbCArICdcIicgOiAnJyxcbiAgICAgICAgICAgICAgICAgICAgb2ZmTGFiZWwgPSAob3B0aW9ucy5vZmZfbGFiZWwgIT09ICcnKSA/ICcgZGF0YS1jaGVja2JveC1sYWJlbD1cIicgKyBvcHRpb25zLm9mZl9sYWJlbCArICdcIicgOlxuICAgICAgICAgICAgICAgICAgICAgICAgJycsXG4gICAgICAgICAgICAgICAgICAgIGlzQ2hlY2tlZCA9IChvcHRpb25zLmNoZWNrZWQpID8gJ2NoZWNrZWQnIDogJyc7XG5cbiAgICAgICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgICAgICAuZGF0YSgnY2hlY2tib3gnLCBkYXRhc2V0KVxuICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ3N3aXRjaGVyJylcbiAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKGlzQ2hlY2tlZClcbiAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKG9wdGlvbnMuY2xhc3MpXG4gICAgICAgICAgICAgICAgICAgIC5hcHBlbmQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci10b2dnbGVyXCI+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItaW5uZXJcIj4nICtcbiAgICAgICAgICAgICAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb25cIiB0aXRsZT1cIicgKyBvcHRpb25zLm9mZl91cmwgKyAnXCInICsgb25MYWJlbCArXG4gICAgICAgICAgICAgICAgICAgICAgICAnPjxzcGFuIGNsYXNzPVwiZmEgZmEtY2hlY2tcIj48L3NwYW4+PC9kaXY+JyArICc8ZGl2IGNsYXNzPVwic3dpdGNoZXItc3RhdGUtb2ZmXCIgdGl0bGU9XCInICtcbiAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbnMub25fdXJsICsgJ1wiJyArXG4gICAgICAgICAgICAgICAgICAgICAgICBvZmZMYWJlbCArICc+PHNwYW4gY2xhc3M9XCJmYSBmYS10aW1lc1wiPjwvc3Bhbj48L2Rpdj4nICsgJzwvZGl2PidcbiAgICAgICAgICAgICAgICAgICAgKVxuICAgICAgICAgICAgICAgICAgICAub24oJ2NsaWNrJywgX3N3aXRjaGVyQ2hhbmdlSGFuZGxlcik7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEJpbmQgZXZlbnRzIHRoYXQgY2hhbmdlIHRoZSBjaGVja2JveCBvciBzd2l0Y2hlci5cbiAgICAgICAgICpcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfaW5pdEV2ZW50SGFuZGxlcnMgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAkdGhpcy5vbignY2xpY2snLCAnLnN3aXRjaGVyJywgX3N3aXRjaGVyQ2hhbmdlSGFuZGxlcik7XG5cbiAgICAgICAgICAgICR0aGlzLm9mZignbW91c2Vkb3duJywgJy5zaW5nbGUtY2hlY2tib3gnKTtcbiAgICAgICAgICAgICR0aGlzLm9uKCdtb3VzZWRvd24nLCAnLnNpbmdsZS1jaGVja2JveCcsIF9jaGVja2JveE1vdXNlRG93bkhhbmRsZXIpO1xuICAgICAgICAgICAgJHRoaXMub2ZmKCdtb3VzZXVwJywgJy5zaW5nbGUtY2hlY2tib3gnKTtcbiAgICAgICAgICAgICR0aGlzLm9uKCdtb3VzZXVwJywgJy5zaW5nbGUtY2hlY2tib3gnLCBfY2hlY2tib3hNb3VzZVVwSGFuZGxlcik7XG5cbiAgICAgICAgICAgICR0aGlzLm9uKCdtb3VzZWRvd24nLCAnbGFiZWwnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgbW91c2VEb3duID0gdHJ1ZTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkdGhpcy5vbignbW91c2V1cCcsICdsYWJlbCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBtb3VzZURvd24gPSBmYWxzZTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkdGhpcy5vbignRk9STV9VUERBVEUnLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgIHZhciAkdGFyZ2V0ID0gJChlLnRhcmdldCk7XG4gICAgICAgICAgICAgICAgJHRhcmdldFxuICAgICAgICAgICAgICAgICAgICAuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKVxuICAgICAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICR3cmFwcGVyID0gJHNlbGYuY2xvc2VzdCgnLnN3aXRjaGVyJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkd3JhcHBlci5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkd3JhcHBlclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnZGl2JylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmLnVud3JhcCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIF9pbml0Q2hlY2tib3hlcygkdGFyZ2V0KTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIENvbnZlcnQgXCJ5ZXMvbm9cIiBzZWxlY3QgZWxlbWVudHMgdG8gYSBzd2l0Y2hlci5cbiAgICAgICAgICpcbiAgICAgICAgICogVGhlIHNlbGVjdHMgbXVzdCBoYXZlIGEgXCJkYXRhLWNvbnZlcnQtY2hlY2tib3hcIiBhdHRyaWJ1dGUgaW4gb3JkZXIgdG8gYmUgcHJvY2Vzc2VkIGJ5XG4gICAgICAgICAqIHRoaXMgbWV0aG9kLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9pbml0U2VsZWN0cyA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIC8vIEl0ZXJhdGUgb3ZlciBzZWxlY3QgZmllbGRzXG4gICAgICAgICAgICAkdGhpcy5maW5kKCdbZGF0YS1jb252ZXJ0LWNoZWNrYm94XScpLmVhY2goZnVuY3Rpb24gKGluZGV4LCBlbGVtZW50KSB7XG4gICAgICAgICAgICAgICAgLy8gU2VsZWN0b3JzIGZcbiAgICAgICAgICAgICAgICB2YXIgJG9wdGlvblRydWUgPSAkKGVsZW1lbnQpLmZpbmQoJ29wdGlvblt2YWx1ZT1cIjFcIl0nKSxcbiAgICAgICAgICAgICAgICAgICAgJG9wdGlvbkZhbHNlID0gJChlbGVtZW50KS5maW5kKCdvcHRpb25bdmFsdWU9XCIwXCJdJyk7XG5cbiAgICAgICAgICAgICAgICAvLyBTdGF0ZXNcbiAgICAgICAgICAgICAgICB2YXIgaXNDaGVja2VkID0gJG9wdGlvblRydWUuaXMoJzpzZWxlY3RlZCcpID8gJ2NoZWNrZWQnIDogJycsXG4gICAgICAgICAgICAgICAgICAgIGlzRGlzYWJsZWQgPSAkKGVsZW1lbnQpLmlzKCc6ZGlzYWJsZWQnKSA/ICdkaXNhYmxlZCcgOiAnJztcblxuICAgICAgICAgICAgICAgIC8vIFN3aXRjaGVyIHRoZW1lXG4gICAgICAgICAgICAgICAgdmFyICRzd2l0Y2hlciA9ICQoJzxkaXYgY2xhc3M9XCJzd2l0Y2hlciAnICsgaXNDaGVja2VkICsgJyAnICsgaXNEaXNhYmxlZCArICdcIj48L2Rpdj4nKTtcbiAgICAgICAgICAgICAgICAkc3dpdGNoZXJcbiAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKCQoZWxlbWVudCkuZGF0YSgnbmV3Q2xhc3MnKSlcbiAgICAgICAgICAgICAgICAgICAgLmRhdGEoJ2NoZWNrYm94Jywgb3B0aW9ucylcbiAgICAgICAgICAgICAgICAgICAgLmFwcGVuZCgnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXRvZ2dsZXJcIj48L2Rpdj4nICsgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1pbm5lclwiPicgK1xuICAgICAgICAgICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJzd2l0Y2hlci1zdGF0ZS1vblwiPjxzcGFuIGNsYXNzPVwiZmEgZmEtY2hlY2tcIj48L3NwYW4+PC9kaXY+JyArXG4gICAgICAgICAgICAgICAgICAgICAgICAnPGRpdiBjbGFzcz1cInN3aXRjaGVyLXN0YXRlLW9mZlwiPjxzcGFuIGNsYXNzPVwiZmEgZmEtdGltZXNcIj48L3NwYW4+PC9kaXY+JyArICc8L2Rpdj4nXG4gICAgICAgICAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgICAgICAkKGVsZW1lbnQpXG4gICAgICAgICAgICAgICAgICAgIC5hZnRlcigkc3dpdGNoZXIpXG4gICAgICAgICAgICAgICAgICAgIC5hcHBlbmRUbygkc3dpdGNoZXIpXG4gICAgICAgICAgICAgICAgICAgIC5oaWRlKCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAgICAgLy8gSU5JVElBTElaQVRJT05cbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSB3aWRnZXQsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAgICAgX2luaXRDaGVja2JveGVzKCk7XG4gICAgICAgICAgICBfaW5pdFJhZGlvT3B0aW9ucygpO1xuICAgICAgICAgICAgX2luaXRTZWxlY3RzKCk7XG4gICAgICAgICAgICBfaW5pdFVybFN3aXRjaGVyKCk7XG4gICAgICAgICAgICBfaW5pdEV2ZW50SGFuZGxlcnMoKTtcbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

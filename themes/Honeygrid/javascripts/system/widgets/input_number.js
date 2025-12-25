'use strict';

/* --------------------------------------------------------------
 input_number.js 2019-07-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget to add + and - buttons to an input field
 */
gambio.widgets.module('input_number', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        separator = null,
        regex = null,
        quantityCheckDelay = 300,
        quantityCheckTimeout = null,
        defaults = {
        // Set the type of the number field. Can be "int" or "float"
        type: 'float',
        // Digits after the locale separator (. or ,)
        digits: 4,
        // Show digits if the are zero
        forceDigits: false,
        // Stepping of the numbers
        stepping: 1,
        // Minimum value of the input field
        min: 0,
        // Maximum value of the input field
        max: null,
        // Set the locale separator (e.g.: . or ,) or set it to "auto" for auto-detection
        separator: 'auto',
        // Initial delay after the mousedown event method gets called again
        delay: 500,
        // Minimum delay that is used for repeating the mousedown event method
        minDelay: 50
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Helper function that tries to detect the local
     * digits separator
     * @return     {string}        Returns the separator as a string
     * @private
     */
    var _getSeparator = function _getSeparator() {

        if (!options.separator || options.separator === 'auto') {
            var number = 1.1;
            options.separator = number.toLocaleString().substring(1, 2);
            return options.separator;
        }

        return options.separator;
    };

    /**
     * Function to calculate the new value of the input field
     * @param       {object}    e       jQuery event object that gets passed from the event listener below
     * @private
     */
    var _update = function _update(e) {
        var $target = e.data.target,
            type = e.data.type,
            dataset = e.data.dataset,
            value = $target.val(),
            normalized = options.separator === '.' ? value : value.replace(regex, '.'),
            number = dataset.type === 'int' ? parseInt(normalized, 10) : parseFloat(normalized),
            exponent = Math.pow(10, dataset.digits);

        // Check if the value inside the input field is a number
        if (isNaN(number)) {
            jse.core.debug.info('[NUMBERINPUT] Input is not a number');
            return;
        }

        // Add / substract the stepping value to the value inside the input field
        // If the value gets outside the boundaries set the value to the edge case
        if (type === 'plus') {
            number += dataset.stepping;
        } else {
            number -= dataset.stepping;
        }

        //Check the boundaries given
        number = typeof dataset.max === 'number' ? Math.min(number, dataset.max) : number;
        number = typeof dataset.min === 'number' ? Math.max(number, dataset.min) : number;

        // Round the value to the given digits count
        number = parseInt(Math.round(number * exponent), 10) / exponent;

        // Generate output string
        number = number.toString();

        // Add tailing zeros to get the defined number of digits
        if (dataset.forceDigits) {
            var separatorIndex = number.indexOf('.'),
                digits = null;

            if (separatorIndex === -1) {
                number = number + '.';
                separatorIndex = number.indexOf('.');
            }

            digits = number.length - separatorIndex - 1;

            while (digits < dataset.digits) {
                number += '0';
                digits += 1;
            }
        }

        if ($target.attr('type') !== 'number') {
            // Set the value to the input field in the correct locale
            number = number.replace('.', separator);
        }

        $target.val(number).trigger('keyup', []);

        _quantityCheck($target);
    };

    /**
     * Function to trigger the quantity check
     * @param {object} $target jQuery selector for the input field
     * @private
     */
    var _quantityCheck = function _quantityCheck($target) {
        quantityCheckTimeout = quantityCheckTimeout ? clearTimeout(quantityCheckTimeout) : null;
        quantityCheckTimeout = setTimeout(function () {
            // blur event of input field triggers the CheckStatus request sent in cart_handler widget
            $target.trigger('blur', []);
        }, quantityCheckDelay);
    };

    // ########## EVENT HANDLER ##########

    /**
     * Event handler for the mousedown event. On mousedown
     * on the buttons, the update function gets called after
     * a given delay (that gets shorter after time) as long as
     * no mouseup event is detected
     * @param       {object}    e       jQuery event object
     * @private
     *
     * @todo: search for proper solution to detect a touchend event on integrated android browsers
     */
    var _mouseDown = function _mouseDown(e) {

        e.preventDefault();

        var $target = e.data.target,
            dataset = $target.data(),
            timer = dataset.timer || null,
            delay = Math.max(dataset.delay || e.data.dataset.delay, e.data.dataset.minDelay);

        if (timer) {
            clearTimeout(timer);
        }

        timer = setTimeout(function () {
            _mouseDown(e);
        }, delay);

        $target.data({ delay: delay / 1.5, timer: timer });
        _update(e);
    };

    /**
     * Event handler for the mouseup (and mouseleave) event.
     * If triggered, the timer that gets started in the mouseDown
     * handler gets stopped and all values wil be reseted to the
     * initial state
     *
     * @param       {object}        e       jQuery event object
     * @private
     *
     * @todo: search for proper solution to detect a touchend event on integrated android browsers
     */
    var _mouseUp = function _mouseUp(e) {

        e.preventDefault();

        var $target = e.data ? e.data.target : null,
            dataset = $target !== null ? $target.data() : {},
            timer = dataset.timer;

        if (timer) {
            clearTimeout(timer);
            $target.data('delay', e.data.dataset.delay);
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        separator = _getSeparator();
        regex = new RegExp(separator, 'g');

        $this.find('.input-number').each(function () {
            var $self = $(this),
                $input = $self.find('input'),
                dataset = $.extend({}, options, $self.data());

            $self.on('mousedown touchstart', '.btn-plus', {
                dataset: dataset,
                type: 'plus',
                target: $input
            }, _mouseDown).on('mouseup mouseleave touchend', '.btn-plus', {
                dataset: dataset,
                type: 'plus',
                target: $input
            }, _mouseUp).on('mousedown touchstart', '.btn-minus', {
                dataset: dataset,
                type: 'minus',
                target: $input
            }, _mouseDown).on('mouseup mouseleave touchend', '.btn-minus', {
                dataset: dataset,
                type: 'minus',
                target: $input
            }, _mouseUp);
        });

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaW5wdXRfbnVtYmVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwic2VwYXJhdG9yIiwicmVnZXgiLCJxdWFudGl0eUNoZWNrRGVsYXkiLCJxdWFudGl0eUNoZWNrVGltZW91dCIsImRlZmF1bHRzIiwidHlwZSIsImRpZ2l0cyIsImZvcmNlRGlnaXRzIiwic3RlcHBpbmciLCJtaW4iLCJtYXgiLCJkZWxheSIsIm1pbkRlbGF5Iiwib3B0aW9ucyIsImV4dGVuZCIsIl9nZXRTZXBhcmF0b3IiLCJudW1iZXIiLCJ0b0xvY2FsZVN0cmluZyIsInN1YnN0cmluZyIsIl91cGRhdGUiLCJlIiwiJHRhcmdldCIsInRhcmdldCIsImRhdGFzZXQiLCJ2YWx1ZSIsInZhbCIsIm5vcm1hbGl6ZWQiLCJyZXBsYWNlIiwicGFyc2VJbnQiLCJwYXJzZUZsb2F0IiwiZXhwb25lbnQiLCJNYXRoIiwicG93IiwiaXNOYU4iLCJqc2UiLCJjb3JlIiwiZGVidWciLCJpbmZvIiwicm91bmQiLCJ0b1N0cmluZyIsInNlcGFyYXRvckluZGV4IiwiaW5kZXhPZiIsImxlbmd0aCIsImF0dHIiLCJ0cmlnZ2VyIiwiX3F1YW50aXR5Q2hlY2siLCJjbGVhclRpbWVvdXQiLCJzZXRUaW1lb3V0IiwiX21vdXNlRG93biIsInByZXZlbnREZWZhdWx0IiwidGltZXIiLCJfbW91c2VVcCIsImluaXQiLCJkb25lIiwiUmVnRXhwIiwiZmluZCIsImVhY2giLCIkc2VsZiIsIiRpbnB1dCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FBc0IsY0FBdEIsRUFBc0MsRUFBdEMsRUFBMEMsVUFBVUMsSUFBVixFQUFnQjs7QUFFdEQ7O0FBRUo7O0FBRUksUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxZQUFZLElBRGhCO0FBQUEs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RUFGLGNBQ0t5RCxJQURMLENBQ1UsZUFEVixFQUVLQyxJQUZMLENBRVUsWUFBWTtBQUNkLGdCQUFJQyxRQUFRMUQsRUFBRSxJQUFGLENBQVo7QUFBQSxnQkFDSTJELFNBQVNELE1BQU1GLElBQU4sQ0FBVyxPQUFYLENBRGI7QUFBQSxnQkFFSWhDLFVBQVV4QixFQUFFZSxNQUFGLENBQVMsRUFBVCxFQUFhRCxPQUFiLEVBQXNCNEMsTUFBTTVELElBQU4sRUFBdEIsQ0FGZDs7QUFJQTRELGtCQUNLRSxFQURMLENBQ1Esc0JBRFIsRUFDZ0MsV0FEaEMsRUFDNkM7QUFDckNwQyx5QkFBU0EsT0FENEI7QUFFckNsQixzQkFBTSxNQUYrQjtBQUdyQ2lCLHdCQUFRb0M7QUFINkIsYUFEN0MsRUFLT1YsVUFMUCxFQU1LVyxFQU5MLENBTVEsNkJBTlIsRUFNdUMsV0FOdkMsRUFNb0Q7QUFDNUNwQyx5QkFBU0EsT0FEbUM7QUFFNUNsQixzQkFBTSxNQUZzQztBQUc1Q2lCLHdCQUFRb0M7QUFIb0MsYUFOcEQsRUFVT1AsUUFWUCxFQVdLUSxFQVhMLENBV1Esc0JBWFIsRUFXZ0MsWUFYaEMsRUFXOEM7QUFDdENwQyx5QkFBU0EsT0FENkI7QUFFdENsQixzQkFBTSxPQUZnQztBQUd0Q2lCLHdCQUFRb0M7QUFIOEIsYUFYOUMsRUFlT1YsVUFmUCxFQWdCS1csRUFoQkwsQ0FnQlEsNkJBaEJSLEVBZ0J1QyxZQWhCdkMsRUFnQnFEO0FBQzdDcEMseUJBQVNBLE9BRG9DO0FBRTdDbEIsc0JBQU0sT0FGdUM7QUFHN0NpQix3QkFBUW9DO0FBSHFDLGFBaEJyRCxFQW9CT1AsUUFwQlA7QUFxQkgsU0E1Qkw7O0FBOEJBRTtBQUNILEtBcENEOztBQXNDQTtBQUNBLFdBQU96RCxNQUFQO0FBQ0gsQ0FoUEQiLCJmaWxlIjoid2lkZ2V0cy9pbnB1dF9udW1iZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGlucHV0X251bWJlci5qcyAyMDE5LTA3LTAzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxOSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdG8gYWRkICsgYW5kIC0gYnV0dG9ucyB0byBhbiBpbnB1dCBmaWVsZFxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoJ2lucHV0X251bWJlcicsIFtdLCBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgIHNlcGFyYXRvciA9IG51bGwsXG4gICAgICAgIHJlZ2V4ID0gbnVsbCxcbiAgICAgICAgcXVhbnRpdHlDaGVja0RlbGF5ID0gMzAwLFxuICAgICAgICBxdWFudGl0eUNoZWNrVGltZW91dCA9IG51bGwsXG4gICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgLy8gU2V0IHRoZSB0eXBlIG9mIHRoZSBudW1iZXIgZmllbGQuIENhbiBiZSBcImludFwiIG9yIFwiZmxvYXRcIlxuICAgICAgICAgICAgdHlwZTogJ2Zsb2F0JyxcbiAgICAgICAgICAgIC8vIERpZ2l0cyBhZnRlciB0aGUgbG9jYWxlIHNlcGFyYXRvciAoLiBvciAsKVxuICAgICAgICAgICAgZGlnaXRzOiA0LFxuICAgICAgICAgICAgLy8gU2hvdyBkaWdpdHMgaWYgdGhlIGFyZSB6ZXJvXG4gICAgICAgICAgICBmb3JjZURpZ2l0czogZmFsc2UsXG4gICAgICAgICAgICAvLyBTdGVwcGluZyBvZiB0aGUgbnVtYmVyc1xuICAgICAgICAgICAgc3RlcHBpbmc6IDEsXG4gICAgICAgICAgICAvLyBNaW5pbXVtIHZhbHVlIG9mIHRoZSBpbnB1dCBmaWVsZFxuICAgICAgICAgICAgbWluOiAwLFxuICAgICAgICAgICAgLy8gTWF4aW11bSB2YWx1ZSBvZiB0aGUgaW5wdXQgZmllbGRcbiAgICAgICAgICAgIG1heDogbnVsbCxcbiAgICAgICAgICAgIC8vIFNldCB0aGUgbG9jYWxlIHNlcGFyYXRvciAoZS5nLjogLiBvciAsKSBvciBzZXQgaXQgdG8gXCJhdXRvXCIgZm9yIGF1dG8tZGV0ZWN0aW9uXG4gICAgICAgICAgICBzZXBhcmF0b3I6ICdhdXRvJyxcbiAgICAgICAgICAgIC8vIEluaXRpYWwgZGVsYXkgYWZ0ZXIgdGhlIG1vdXNlZG93biBldmVudCBtZXRob2QgZ2V0cyBjYWxsZWQgYWdhaW5cbiAgICAgICAgICAgIGRlbGF5OiA1MDAsXG4gICAgICAgICAgICAvLyBNaW5pbXVtIGRlbGF5IHRoYXQgaXMgdXNlZCBmb3IgcmVwZWF0aW5nIHRoZSBtb3VzZWRvd24gZXZlbnQgbWV0aG9kXG4gICAgICAgICAgICBtaW5EZWxheTogNTBcbiAgICAgICAgfSxcbiAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgIG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cbiAgICAvKipcbiAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCB0cmllcyB0byBkZXRlY3QgdGhlIGxvY2FsXG4gICAgICogZGlnaXRzIHNlcGFyYXRvclxuICAgICAqIEByZXR1cm4gICAgIHtzdHJpbmd9ICAgICAgICBSZXR1cm5zIHRoZSBzZXBhcmF0b3IgYXMgYSBzdHJpbmdcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfZ2V0U2VwYXJhdG9yID0gZnVuY3Rpb24gKCkge1xuXG4gICAgICAgIGlmICghb3B0aW9ucy5zZXBhcmF0b3IgfHwgb3B0aW9ucy5zZXBhcmF0b3IgPT09ICdhdXRvJykge1xuICAgICAgICAgICAgdmFyIG51bWJlciA9IDEuMTtcbiAgICAgICAgICAgIG9wdGlvbnMuc2VwYXJhdG9yID0gbnVtYmVyLnRvTG9jYWxlU3RyaW5nKCkuc3Vic3RyaW5nKDEsIDIpO1xuICAgICAgICAgICAgcmV0dXJuIG9wdGlvbnMuc2VwYXJhdG9yO1xuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIG9wdGlvbnMuc2VwYXJhdG9yO1xuXG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIEZ1bmN0aW9uIHRvIGNhbGN1bGF0ZSB0aGUgbmV3IHZhbHVlIG9mIHRoZSBpbnB1dCBmaWVsZFxuICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3QgdGhhdCBnZXRzIHBhc3NlZCBmcm9tIHRoZSBldmVudCBsaXN0ZW5lciBiZWxvd1xuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF91cGRhdGUgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICB2YXIgJHRhcmdldCA9IGUuZGF0YS50YXJnZXQsXG4gICAgICAgICAgICB0eXBlID0gZS5kYXRhLnR5cGUsXG4gICAgICAgICAgICBkYXRhc2V0ID0gZS5kYXRhLmRhdGFzZXQsXG4gICAgICAgICAgICB2YWx1ZSA9ICR0YXJnZXQudmFsKCksXG4gICAgICAgICAgICBub3JtYWxpemVkID0gKG9wdGlvbnMuc2VwYXJhdG9yID09PSAnLicpID8gdmFsdWUgOiB2YWx1ZS5yZXBsYWNlKHJlZ2V4LCAnLicpLFxuICAgICAgICAgICAgbnVtYmVyID0gKGRhdGFzZXQudHlwZSA9PT0gJ2ludCcpID8gcGFyc2VJbnQobm9ybWFsaXplZCwgMTApIDogcGFyc2VGbG9hdChub3JtYWxpemVkKSxcbiAgICAgICAgICAgIGV4cG9uZW50ID0gTWF0aC5wb3coMTAsIGRhdGFzZXQuZGlnaXRzKTtcblxuICAgICAgICAvLyBDaGVjayBpZiB0aGUgdmFsdWUgaW5zaWRlIHRoZSBpbnB1dCBmaWVsZCBpcyBhIG51bWJlclxuICAgICAgICBpZiAoaXNOYU4obnVtYmVyKSkge1xuICAgICAgICAgICAganNlLmNvcmUuZGVidWcuaW5mbygnW05VTUJFUklOUFVUXSBJbnB1dCBpcyBub3QgYSBudW1iZXInKTtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEFkZCAvIHN1YnN0cmFjdCB0aGUgc3RlcHBpbmcgdmFsdWUgdG8gdGhlIHZhbHVlIGluc2lkZSB0aGUgaW5wdXQgZmllbGRcbiAgICAgICAgLy8gSWYgdGhlIHZhbHVlIGdldHMgb3V0c2lkZSB0aGUgYm91bmRhcmllcyBzZXQgdGhlIHZhbHVlIHRvIHRoZSBlZGdlIGNhc2VcbiAgICAgICAgaWYgKHR5cGUgPT09ICdwbHVzJykge1xuICAgICAgICAgICAgbnVtYmVyICs9IGRhdGFzZXQuc3RlcHBpbmc7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBudW1iZXIgLT0gZGF0YXNldC5zdGVwcGluZztcbiAgICAgICAgfVxuXG4gICAgICAgIC8vQ2hlY2sgdGhlIGJvdW5kYXJpZXMgZ2l2ZW5cbiAgICAgICAgbnVtYmVyID0gKHR5cGVvZiBkYXRhc2V0Lm1heCA9PT0gJ251bWJlcicpID8gTWF0aC5taW4obnVtYmVyLCBkYXRhc2V0Lm1heCkgOiBudW1iZXI7XG4gICAgICAgIG51bWJlciA9ICh0eXBlb2YgZGF0YXNldC5taW4gPT09ICdudW1iZXInKSA/IE1hdGgubWF4KG51bWJlciwgZGF0YXNldC5taW4pIDogbnVtYmVyO1xuXG4gICAgICAgIC8vIFJvdW5kIHRoZSB2YWx1ZSB0byB0aGUgZ2l2ZW4gZGlnaXRzIGNvdW50XG4gICAgICAgIG51bWJlciA9IHBhcnNlSW50KE1hdGgucm91bmQobnVtYmVyICogZXhwb25lbnQpLCAxMCkgLyBleHBvbmVudDtcblxuICAgICAgICAvLyBHZW5lcmF0ZSBvdXRwdXQgc3RyaW5nXG4gICAgICAgIG51bWJlciA9IG51bWJlci50b1N0cmluZygpO1xuXG4gICAgICAgIC8vIEFkZCB0YWlsaW5nIHplcm9zIHRvIGdldCB0aGUgZGVmaW5lZCBudW1iZXIgb2YgZGlnaXRzXG4gICAgICAgIGlmIChkYXRhc2V0LmZvcmNlRGlnaXRzKSB7XG4gICAgICAgICAgICB2YXIgc2VwYXJhdG9ySW5kZXggPSBudW1iZXIuaW5kZXhPZignLicpLFxuICAgICAgICAgICAgICAgIGRpZ2l0cyA9IG51bGw7XG5cbiAgICAgICAgICAgIGlmIChzZXBhcmF0b3JJbmRleCA9PT0gLTEpIHtcbiAgICAgICAgICAgICAgICBudW1iZXIgPSBudW1iZXIgKyAnLic7XG4gICAgICAgICAgICAgICAgc2VwYXJhdG9ySW5kZXggPSBudW1iZXIuaW5kZXhPZignLicpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBkaWdpdHMgPSBudW1iZXIubGVuZ3RoIC0gc2VwYXJhdG9ySW5kZXggLSAxO1xuXG4gICAgICAgICAgICB3aGlsZSAoZGlnaXRzIDwgZGF0YXNldC5kaWdpdHMpIHtcbiAgICAgICAgICAgICAgICBudW1iZXIgKz0gJzAnO1xuICAgICAgICAgICAgICAgIGRpZ2l0cyArPSAxO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICAgIFxuICAgICAgICBpZiAoJHRhcmdldC5hdHRyKCd0eXBlJykgIT09ICdudW1iZXInKSB7XG4gICAgICAgICAgICAvLyBTZXQgdGhlIHZhbHVlIHRvIHRoZSBpbnB1dCBmaWVsZCBpbiB0aGUgY29ycmVjdCBsb2NhbGVcbiAgICAgICAgICAgIG51bWJlciA9IG51bWJlci5yZXBsYWNlKCcuJywgc2VwYXJhdG9yKTtcbiAgICAgICAgfVxuICAgICAgICBcbiAgICAgICAgJHRhcmdldFxuICAgICAgICAgICAgLnZhbChudW1iZXIpXG4gICAgICAgICAgICAudHJpZ2dlcigna2V5dXAnLCBbXSk7XG5cbiAgICAgICAgX3F1YW50aXR5Q2hlY2soJHRhcmdldCk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIEZ1bmN0aW9uIHRvIHRyaWdnZXIgdGhlIHF1YW50aXR5IGNoZWNrXG4gICAgICogQHBhcmFtIHtvYmplY3R9ICR0YXJnZXQgalF1ZXJ5IHNlbGVjdG9yIGZvciB0aGUgaW5wdXQgZmllbGRcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIHZhciBfcXVhbnRpdHlDaGVjayA9IGZ1bmN0aW9uICgkdGFyZ2V0KSB7XG4gICAgICAgIHF1YW50aXR5Q2hlY2tUaW1lb3V0ID0gcXVhbnRpdHlDaGVja1RpbWVvdXQgPyBjbGVhclRpbWVvdXQocXVhbnRpdHlDaGVja1RpbWVvdXQpIDogbnVsbDtcbiAgICAgICAgcXVhbnRpdHlDaGVja1RpbWVvdXQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIC8vIGJsdXIgZXZlbnQgb2YgaW5wdXQgZmllbGQgdHJpZ2dlcnMgdGhlIENoZWNrU3RhdHVzIHJlcXVlc3Qgc2VudCBpbiBjYXJ0X2hhbmRsZXIgd2lkZ2V0XG4gICAgICAgICAgICAkdGFyZ2V0LnRyaWdnZXIoJ2JsdXInLCBbXSk7XG4gICAgICAgIH0sIHF1YW50aXR5Q2hlY2tEZWxheSk7XG4gICAgfTtcblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuICAgIC8qKlxuICAgICAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBtb3VzZWRvd24gZXZlbnQuIE9uIG1vdXNlZG93blxuICAgICAqIG9uIHRoZSBidXR0b25zLCB0aGUgdXBkYXRlIGZ1bmN0aW9uIGdldHMgY2FsbGVkIGFmdGVyXG4gICAgICogYSBnaXZlbiBkZWxheSAodGhhdCBnZXRzIHNob3J0ZXIgYWZ0ZXIgdGltZSkgYXMgbG9uZyBhc1xuICAgICAqIG5vIG1vdXNldXAgZXZlbnQgaXMgZGV0ZWN0ZWRcbiAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG4gICAgICogQHByaXZhdGVcbiAgICAgKlxuICAgICAqIEB0b2RvOiBzZWFyY2ggZm9yIHByb3BlciBzb2x1dGlvbiB0byBkZXRlY3QgYSB0b3VjaGVuZCBldmVudCBvbiBpbnRlZ3JhdGVkIGFuZHJvaWQgYnJvd3NlcnNcbiAgICAgKi9cbiAgICB2YXIgX21vdXNlRG93biA9IGZ1bmN0aW9uIChlKSB7XG5cbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIHZhciAkdGFyZ2V0ID0gZS5kYXRhLnRhcmdldCxcbiAgICAgICAgICAgIGRhdGFzZXQgPSAkdGFyZ2V0LmRhdGEoKSxcbiAgICAgICAgICAgIHRpbWVyID0gZGF0YXNldC50aW1lciB8fCBudWxsLFxuICAgICAgICAgICAgZGVsYXkgPSBNYXRoLm1heChkYXRhc2V0LmRlbGF5IHx8IGUuZGF0YS5kYXRhc2V0LmRlbGF5LCBlLmRhdGEuZGF0YXNldC5taW5EZWxheSk7XG5cbiAgICAgICAgaWYgKHRpbWVyKSB7XG4gICAgICAgICAgICBjbGVhclRpbWVvdXQodGltZXIpO1xuICAgICAgICB9XG5cbiAgICAgICAgdGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIF9tb3VzZURvd24oZSk7XG4gICAgICAgIH0sIGRlbGF5KTtcblxuICAgICAgICAkdGFyZ2V0LmRhdGEoe2RlbGF5OiBkZWxheSAvIDEuNSwgdGltZXI6IHRpbWVyfSk7XG4gICAgICAgIF91cGRhdGUoZSk7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBtb3VzZXVwIChhbmQgbW91c2VsZWF2ZSkgZXZlbnQuXG4gICAgICogSWYgdHJpZ2dlcmVkLCB0aGUgdGltZXIgdGhhdCBnZXRzIHN0YXJ0ZWQgaW4gdGhlIG1vdXNlRG93blxuICAgICAqIGhhbmRsZXIgZ2V0cyBzdG9wcGVkIGFuZCBhbGwgdmFsdWVzIHdpbCBiZSByZXNldGVkIHRvIHRoZVxuICAgICAqIGluaXRpYWwgc3RhdGVcbiAgICAgKlxuICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG4gICAgICogQHByaXZhdGVcbiAgICAgKlxuICAgICAqIEB0b2RvOiBzZWFyY2ggZm9yIHByb3BlciBzb2x1dGlvbiB0byBkZXRlY3QgYSB0b3VjaGVuZCBldmVudCBvbiBpbnRlZ3JhdGVkIGFuZHJvaWQgYnJvd3NlcnNcbiAgICAgKi9cbiAgICB2YXIgX21vdXNlVXAgPSBmdW5jdGlvbiAoZSkge1xuXG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB2YXIgJHRhcmdldCA9IGUuZGF0YSA/IGUuZGF0YS50YXJnZXQgOiBudWxsLFxuICAgICAgICAgICAgZGF0YXNldCA9ICgkdGFyZ2V0ICE9PSBudWxsKSA/ICR0YXJnZXQuZGF0YSgpIDoge30sXG4gICAgICAgICAgICB0aW1lciA9IGRhdGFzZXQudGltZXI7XG5cbiAgICAgICAgaWYgKHRpbWVyKSB7XG4gICAgICAgICAgICBjbGVhclRpbWVvdXQodGltZXIpO1xuICAgICAgICAgICAgJHRhcmdldC5kYXRhKCdkZWxheScsIGUuZGF0YS5kYXRhc2V0LmRlbGF5KTtcbiAgICAgICAgfVxuICAgIH07XG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgLyoqXG4gICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICogQGNvbnN0cnVjdG9yXG4gICAgICovXG4gICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgIHNlcGFyYXRvciA9IF9nZXRTZXBhcmF0b3IoKTtcbiAgICAgICAgcmVnZXggPSBuZXcgUmVnRXhwKHNlcGFyYXRvciwgJ2cnKTtcblxuICAgICAgICAkdGhpc1xuICAgICAgICAgICAgLmZpbmQoJy5pbnB1dC1udW1iZXInKVxuICAgICAgICAgICAgLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgICRpbnB1dCA9ICRzZWxmLmZpbmQoJ2lucHV0JyksXG4gICAgICAgICAgICAgICAgICAgIGRhdGFzZXQgPSAkLmV4dGVuZCh7fSwgb3B0aW9ucywgJHNlbGYuZGF0YSgpKTtcblxuICAgICAgICAgICAgICAgICRzZWxmXG4gICAgICAgICAgICAgICAgICAgIC5vbignbW91c2Vkb3duIHRvdWNoc3RhcnQnLCAnLmJ0bi1wbHVzJywge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YXNldDogZGF0YXNldCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6ICdwbHVzJyxcbiAgICAgICAgICAgICAgICAgICAgICAgIHRhcmdldDogJGlucHV0XG4gICAgICAgICAgICAgICAgICAgIH0sIF9tb3VzZURvd24pXG4gICAgICAgICAgICAgICAgICAgIC5vbignbW91c2V1cCBtb3VzZWxlYXZlIHRvdWNoZW5kJywgJy5idG4tcGx1cycsIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFzZXQ6IGRhdGFzZXQsXG4gICAgICAgICAgICAgICAgICAgICAgICB0eXBlOiAncGx1cycsXG4gICAgICAgICAgICAgICAgICAgICAgICB0YXJnZXQ6ICRpbnB1dFxuICAgICAgICAgICAgICAgICAgICB9LCBfbW91c2VVcClcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdtb3VzZWRvd24gdG91Y2hzdGFydCcsICcuYnRuLW1pbnVzJywge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YXNldDogZGF0YXNldCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6ICdtaW51cycsXG4gICAgICAgICAgICAgICAgICAgICAgICB0YXJnZXQ6ICRpbnB1dFxuICAgICAgICAgICAgICAgICAgICB9LCBfbW91c2VEb3duKVxuICAgICAgICAgICAgICAgICAgICAub24oJ21vdXNldXAgbW91c2VsZWF2ZSB0b3VjaGVuZCcsICcuYnRuLW1pbnVzJywge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YXNldDogZGF0YXNldCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6ICdtaW51cycsXG4gICAgICAgICAgICAgICAgICAgICAgICB0YXJnZXQ6ICRpbnB1dFxuICAgICAgICAgICAgICAgICAgICB9LCBfbW91c2VVcCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICByZXR1cm4gbW9kdWxlO1xufSk7Il19

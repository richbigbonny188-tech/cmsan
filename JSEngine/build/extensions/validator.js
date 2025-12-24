'use strict';

/* --------------------------------------------------------------
 validator.js 2016-10-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Validator Extension
 *
 * Validate form elements for common rules such as required fields, email addresses and other useful
 * pre-defined types. You can add new validation types by appending the list in the end of this file.
 *
 * ### Methods
 *
 * ```javascript
 * $parent.trigger('validator.validate'); // Trigger validation manually.
 * $parent.trigger('validator.reset'); // Reset validator state.
 * ```
 *
 * ### Example Usage
 *
 * The following element will be validated as a required field and the value must be a valid email
 * address (two validation rules).
 *
 * ```html
 * <div id="parent" data-gx-extension="validator">
 *   <input type="email" class="validate" data-validator-validate="required email" />
 * </div>
 *```
 *
 * The following script demonstrates how to check if there are currently invalid elements in the form.
 *
 * ```javascript
 * // Trigger validation manually:
 * $('#parent').trigger('validator.validate');
 *
 * // Check for invalid field values.
 * if ($('#parent .error').length > 0) {
 *      // Invalid elements have the ".error" class.
 * } else {
 *      // Valid input elements have the ".valid" class.
 * }
 * ```
 *
 * @todo Remove fallback code from this module and create a $.fn.validator API.
 *
 * @module JSE/Extensions/validator
 */
jse.extensions.module('validator', ['fallback'],

/** @lends module:Extensions/validator */

function (data) {

    'use strict';

    // ------------------------------------------------------------------------
    // VARIABLE DEFINITION
    // ------------------------------------------------------------------------

    var
    /**
     * Extension Reference
     *
     * @type {object}
     */
    $this = $(this),
        perform = {

        /**
         * Validate required fields.
         */
        required: function required($element, value, type, opt) {
            switch (type) {
                case 'select':
                    return parseInt(value, 10) === -1 ? false : true;
                case 'checkbox':
                    return parseInt(value, 10) === -1 ? false : true;
                case 'radio':
                    return false;
                default:
                    return value ? true : false;
            }
        },

        /**
         * Validate email addresses (you should also validate emails at server side before storing).
         */
        email: function email($element, value, type, opt) {
            if (value === '' && opt.validate.indexOf('required') === -1) {
                $element.removeClass('error valid');
                return null; // Do not validate empty strings (that are not required).
            }

            var match = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return match.test(value);
        },

        /**
         * Use this type along with the "required" to check if a CKEditor element is
         * empty or not. In case that it has the ".error" class you must find you own
         * way to display that the field is invalid because you cannot display a red
         * border directly to the validated textarea (CKEditor adds many HTML elements
         * to the page).
         */
        ckeditor: function ckeditor($element, value, type, opt) {
            var id = $element.attr('id');

            if (id === undefined) {
                throw 'Cannot validate CKEditor for element without id attribute.';
            }

            return CKEDITOR.instances[id].getData() !== '' ? true : false;
        }
    },


    /**
     * Default Options for Extension
     *
     * @type {object}
     */
    defaults = {},


    /**
     * Final Extension Options
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
    // FUNCTIONALITY
    // ------------------------------------------------------------------------

    /**
     * Set State
     *
     * @param {object} $element Validated element selector.
     * @param {string} state Describes current state ("valid", "error").
     */
    var _setState = function _setState($element, state) {
        switch (state) {
            case 'valid':
                $element.removeClass('error').addClass('valid');
                break;
            case 'error':
                $element.removeClass('valid').addClass('error');
                break;
            default:
                $element.removeClass('valid error');
                break;
        }
    };

    /**
     * Validate Item
     *
     * @return {boolean} Returns the validation result.
     */
    var _validateItem = function _validateItem() {
        var $self = $(this),
            settings = jse.libs.fallback._data($self, 'validator'),
            validate = settings.validate ? settings.validate.split(' ') : [],
            type = $self.prop('tagName').toLowerCase(),
            result = true;

        type = type !== 'input' ? type : $self.attr('type').toLowerCase();

        $.each(validate, function (index, validationType) {
            var isValid = perform[validationType]($self, $self.val(), type, settings);
            if (isValid !== null) {
                _setState($self, isValid ? 'valid' : 'error');
                result = !result ? false : isValid;
            }
        });

        return result;
    };

    /**
     * Validate Multiple Items
     *
     * @param {object} event Contains the event information.
     * @param {object} deferred Defines the deferred object.
     */
    var _validateItems = function _validateItems(event, deferred) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        var $self = event ? $(event.target) : $this,
            valid = true;

        $self.filter('.validate').add($self.find('.validate')).each(function () {
            var current = _validateItem.call($(this));
            valid = !valid ? false : current;
        });

        if (deferred && deferred.deferred) {
            if (valid) {
                deferred.deferred.resolve();
            } else {
                deferred.deferred.reject();
            }
        }

        return valid;
    };

    /**
     * Reset Validator Elements
     */
    var _resetValidator = function _resetValidator() {
        $this.filter('.validate').add($this.find('.validate')).each(function () {
            _setState($(this), 'reset');
        });
    };

    // ------------------------------------------------------------------------
    // INITIALIZATION
    // ------------------------------------------------------------------------

    /**
     * Init function of the extension, called by the engine.
     */
    module.init = function (done) {
        $this.on('change', '.validate:text:visible', _validateItem).on('validator.validate', _validateItems).on('validator.reset', _resetValidator).on('submit', function (event) {
            if (!_validateItems()) {
                event.preventDefault();
            }
        });

        done();
    };

    // Return data to module engine.
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInZhbGlkYXRvci5qcyJdLCJuYW1lcyI6WyJqc2UiLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsInBlcmZvcm0iLCJyZXF1aXJlZCIsIiRlbGVtZW50IiwidmFsdWUiLCJ0eXBlIiwib3B0IiwicGFyc2VJbnQiLCJlbWFpbCIsInZhbGlkYXRlIiwiaW5kZXhPZiIsInJlbW92ZUNsYXNzIiwibWF0Y2giLCJ0ZXN0IiwiY2tlZGl0b3IiLCJpZCIsImF0dHIiLCJ1bmRlZmluZWQiLCJDS0VESVRPUiIsImluc3RhbmNlcyIsImdldERhdGEiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2V0U3RhdGUiLCJzdGF0ZSIsImFkZENsYXNzIiwiX3ZhbGlkYXRlSXRlbSIsIiRzZWxmIiwic2V0dGluZ3MiLCJsaWJzIiwiZmFsbGJhY2siLCJfZGF0YSIsInNwbGl0IiwicHJvcCIsInRvTG93ZXJDYXNlIiwicmVzdWx0IiwiZWFjaCIsImluZGV4IiwidmFsaWRhdGlvblR5cGUiLCJpc1ZhbGlkIiwidmFsIiwiX3ZhbGlkYXRlSXRlbXMiLCJldmVudCIsImRlZmVycmVkIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJ0YXJnZXQiLCJ2YWxpZCIsImZpbHRlciIsImFkZCIsImZpbmQiLCJjdXJyZW50IiwiY2FsbCIsInJlc29sdmUiLCJyZWplY3QiLCJfcmVzZXRWYWxpZGF0b3IiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQTBDQUEsSUFBSUMsVUFBSixDQUFlQyxNQUFmLENBQ0ksV0FESixFQUdJLENBQUMsVUFBRCxDQUhKOztBQUtJOztBQUVBLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0k7Ozs7O0FBS0FDLFlBQVFDLEVBQUUsSUFBRixD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LHlCQUFTQSxRQUFULENBQWtCVSxPQUFsQjtBQUNILGFBRkQsTUFFTztBQUNIVix5QkFBU0EsUUFBVCxDQUFrQlcsTUFBbEI7QUFDSDtBQUNKOztBQUVELGVBQU9QLEtBQVA7QUFDSCxLQTNCRDs7QUE2QkE7OztBQUdBLFFBQUlRLGtCQUFrQixTQUFsQkEsZUFBa0IsR0FBWTtBQUM5QnpELGNBQ0trRCxNQURMLENBQ1ksV0FEWixFQUVLQyxHQUZMLENBRVNuRCxNQUFNb0QsSUFBTixDQUFXLFdBQVgsQ0FGVCxFQUdLZCxJQUhMLENBR1UsWUFBWTtBQUNkYixzQkFBVXhCLEVBQUUsSUFBRixDQUFWLEVBQW1CLE9BQW5CO0FBQ0gsU0FMTDtBQU1ILEtBUEQ7O0FBU0E7QUFDQTtBQUNBOztBQUVBOzs7QUFHQUgsV0FBTzRELElBQVAsR0FBYyxVQUFVQyxJQUFWLEVBQWdCO0FBQzFCM0QsY0FDSzRELEVBREwsQ0FDUSxRQURSLEVBQ2tCLHdCQURsQixFQUM0Q2hDLGFBRDVDLEVBRUtnQyxFQUZMLENBRVEsb0JBRlIsRUFFOEJqQixjQUY5QixFQUdLaUIsRUFITCxDQUdRLGlCQUhSLEVBRzJCSCxlQUgzQixFQUlLRyxFQUpMLENBSVEsUUFKUixFQUlrQixVQUFVaEIsS0FBVixFQUFpQjtBQUMzQixnQkFBSSxDQUFDRCxnQkFBTCxFQUF1QjtBQUNuQkMsc0JBQU1FLGNBQU47QUFDSDtBQUNKLFNBUkw7O0FBVUFhO0FBQ0gsS0FaRDs7QUFjQTtBQUNBLFdBQU83RCxNQUFQO0FBQ0gsQ0F6TkwiLCJmaWxlIjoidmFsaWRhdG9yLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB2YWxpZGF0b3IuanMgMjAxNi0xMC0xNFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgVmFsaWRhdG9yIEV4dGVuc2lvblxuICpcbiAqIFZhbGlkYXRlIGZvcm0gZWxlbWVudHMgZm9yIGNvbW1vbiBydWxlcyBzdWNoIGFzIHJlcXVpcmVkIGZpZWxkcywgZW1haWwgYWRkcmVzc2VzIGFuZCBvdGhlciB1c2VmdWxcbiAqIHByZS1kZWZpbmVkIHR5cGVzLiBZb3UgY2FuIGFkZCBuZXcgdmFsaWRhdGlvbiB0eXBlcyBieSBhcHBlbmRpbmcgdGhlIGxpc3QgaW4gdGhlIGVuZCBvZiB0aGlzIGZpbGUuXG4gKlxuICogIyMjIE1ldGhvZHNcbiAqXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiAkcGFyZW50LnRyaWdnZXIoJ3ZhbGlkYXRvci52YWxpZGF0ZScpOyAvLyBUcmlnZ2VyIHZhbGlkYXRpb24gbWFudWFsbHkuXG4gKiAkcGFyZW50LnRyaWdnZXIoJ3ZhbGlkYXRvci5yZXNldCcpOyAvLyBSZXNldCB2YWxpZGF0b3Igc3RhdGUuXG4gKiBgYGBcbiAqXG4gKiAjIyMgRXhhbXBsZSBVc2FnZVxuICpcbiAqIFRoZSBmb2xsb3dpbmcgZWxlbWVudCB3aWxsIGJlIHZhbGlkYXRlZCBhcyBhIHJlcXVpcmVkIGZpZWxkIGFuZCB0aGUgdmFsdWUgbXVzdCBiZSBhIHZhbGlkIGVtYWlsXG4gKiBhZGRyZXNzICh0d28gdmFsaWRhdGlvbiBydWxlcykuXG4gKlxuICogYGBgaHRtbFxuICogPGRpdiBpZD1cInBhcmVudFwiIGRhdGEtZ3gtZXh0ZW5zaW9uPVwidmFsaWRhdG9yXCI+XG4gKiAgIDxpbnB1dCB0eXBlPVwiZW1haWxcIiBjbGFzcz1cInZhbGlkYXRlXCIgZGF0YS12YWxpZGF0b3ItdmFsaWRhdGU9XCJyZXF1aXJlZCBlbWFpbFwiIC8+XG4gKiA8L2Rpdj5cbiAqYGBgXG4gKlxuICogVGhlIGZvbGxvd2luZyBzY3JpcHQgZGVtb25zdHJhdGVzIGhvdyB0byBjaGVjayBpZiB0aGVyZSBhcmUgY3VycmVudGx5IGludmFsaWQgZWxlbWVudHMgaW4gdGhlIGZvcm0uXG4gKlxuICogYGBgamF2YXNjcmlwdFxuICogLy8gVHJpZ2dlciB2YWxpZGF0aW9uIG1hbnVhbGx5OlxuICogJCgnI3BhcmVudCcpLnRyaWdnZXIoJ3ZhbGlkYXRvci52YWxpZGF0ZScpO1xuICpcbiAqIC8vIENoZWNrIGZvciBpbnZhbGlkIGZpZWxkIHZhbHVlcy5cbiAqIGlmICgkKCcjcGFyZW50IC5lcnJvcicpLmxlbmd0aCA+IDApIHtcbiAqICAgICAgLy8gSW52YWxpZCBlbGVtZW50cyBoYXZlIHRoZSBcIi5lcnJvclwiIGNsYXNzLlxuICogfSBlbHNlIHtcbiAqICAgICAgLy8gVmFsaWQgaW5wdXQgZWxlbWVudHMgaGF2ZSB0aGUgXCIudmFsaWRcIiBjbGFzcy5cbiAqIH1cbiAqIGBgYFxuICpcbiAqIEB0b2RvIFJlbW92ZSBmYWxsYmFjayBjb2RlIGZyb20gdGhpcyBtb2R1bGUgYW5kIGNyZWF0ZSBhICQuZm4udmFsaWRhdG9yIEFQSS5cbiAqXG4gKiBAbW9kdWxlIEpTRS9FeHRlbnNpb25zL3ZhbGlkYXRvclxuICovXG5qc2UuZXh0ZW5zaW9ucy5tb2R1bGUoXG4gICAgJ3ZhbGlkYXRvcicsXG5cbiAgICBbJ2ZhbGxiYWNrJ10sXG5cbiAgICAvKiogQGxlbmRzIG1vZHVsZTpFeHRlbnNpb25zL3ZhbGlkYXRvciAqL1xuXG4gICAgZnVuY3Rpb24gKGRhdGEpIHtcblxuICAgICAgICAndXNlIHN0cmljdCc7XG5cbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgICAgIC8vIFZBUklBQkxFIERFRklOSVRJT05cbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICAgICAgdmFyXG4gICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAqIEV4dGVuc2lvbiBSZWZlcmVuY2VcbiAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgKiBAdHlwZSB7b2JqZWN0fVxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICAkdGhpcyA9ICQodGhpcyksXG5cblxuICAgICAgICAgICAgcGVyZm9ybSA9IHtcblxuICAgICAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICAgICAqIFZhbGlkYXRlIHJlcXVpcmVkIGZpZWxkcy5cbiAgICAgICAgICAgICAgICAgKi9cbiAgICAgICAgICAgICAgICByZXF1aXJlZDogZnVuY3Rpb24gKCRlbGVtZW50LCB2YWx1ZSwgdHlwZSwgb3B0KSB7XG4gICAgICAgICAgICAgICAgICAgIHN3aXRjaCAodHlwZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2FzZSAnc2VsZWN0JzpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gKHBhcnNlSW50KHZhbHVlLCAxMCkgPT09IC0xKSA/IGZhbHNlIDogdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhc2UgJ2NoZWNrYm94JzpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gKHBhcnNlSW50KHZhbHVlLCAxMCkgPT09IC0xKSA/IGZhbHNlIDogdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNhc2UgJ3JhZGlvJzpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgICAgICBkZWZhdWx0OlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiAodmFsdWUpID8gdHJ1ZSA6IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSxcblxuICAgICAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICAgICAqIFZhbGlkYXRlIGVtYWlsIGFkZHJlc3NlcyAoeW91IHNob3VsZCBhbHNvIHZhbGlkYXRlIGVtYWlscyBhdCBzZXJ2ZXIgc2lkZSBiZWZvcmUgc3RvcmluZykuXG4gICAgICAgICAgICAgICAgICovXG4gICAgICAgICAgICAgICAgZW1haWw6IGZ1bmN0aW9uICgkZWxlbWVudCwgdmFsdWUsIHR5cGUsIG9wdCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAodmFsdWUgPT09ICcnICYmIG9wdC52YWxpZGF0ZS5pbmRleE9mKCdyZXF1aXJlZCcpID09PSAtMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGVsZW1lbnQucmVtb3ZlQ2xhc3MoJ2Vycm9yIHZhbGlkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gbnVsbDsgLy8gRG8gbm90IHZhbGlkYXRlIGVtcHR5IHN0cmluZ3MgKHRoYXQgYXJlIG5vdCByZXF1aXJlZCkuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB2YXIgbWF0Y2ggPSAvXigoW148PigpW1xcXVxcXFwuLDs6XFxzQFxcXCJdKyhcXC5bXjw+KClbXFxdXFxcXC4sOzpcXHNAXFxcIl0rKSopfChcXFwiLitcXFwiKSlAKChcXFtbMC05XXsxLDN9XFwuWzAtOV17MSwzfVxcLlswLTldezEsM31cXC5bMC05XXsxLDN9XFxdKXwoKFthLXpBLVpcXC0wLTldK1xcLikrW2EtekEtWl17Mix9KSkkLztcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIG1hdGNoLnRlc3QodmFsdWUpO1xuICAgICAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAgICAgKiBVc2UgdGhpcyB0eXBlIGFsb25nIHdpdGggdGhlIFwicmVxdWlyZWRcIiB0byBjaGVjayBpZiBhIENLRWRpdG9yIGVsZW1lbnQgaXNcbiAgICAgICAgICAgICAgICAgKiBlbXB0eSBvciBub3QuIEluIGNhc2UgdGhhdCBpdCBoYXMgdGhlIFwiLmVycm9yXCIgY2xhc3MgeW91IG11c3QgZmluZCB5b3Ugb3duXG4gICAgICAgICAgICAgICAgICogd2F5IHRvIGRpc3BsYXkgdGhhdCB0aGUgZmllbGQgaXMgaW52YWxpZCBiZWNhdXNlIHlvdSBjYW5ub3QgZGlzcGxheSBhIHJlZFxuICAgICAgICAgICAgICAgICAqIGJvcmRlciBkaXJlY3RseSB0byB0aGUgdmFsaWRhdGVkIHRleHRhcmVhIChDS0VkaXRvciBhZGRzIG1hbnkgSFRNTCBlbGVtZW50c1xuICAgICAgICAgICAgICAgICAqIHRvIHRoZSBwYWdlKS5cbiAgICAgICAgICAgICAgICAgKi9cbiAgICAgICAgICAgICAgICBja2VkaXRvcjogZnVuY3Rpb24gKCRlbGVtZW50LCB2YWx1ZSwgdHlwZSwgb3B0KSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBpZCA9ICRlbGVtZW50LmF0dHIoJ2lkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGlkID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRocm93ICdDYW5ub3QgdmFsaWRhdGUgQ0tFZGl0b3IgZm9yIGVsZW1lbnQgd2l0aG91dCBpZCBhdHRyaWJ1dGUuJztcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiAoQ0tFRElUT1IuaW5zdGFuY2VzW2lkXS5nZXREYXRhKCkgIT09ICcnKSA/IHRydWUgOiBmYWxzZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuXG4gICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAqIERlZmF1bHQgT3B0aW9ucyBmb3IgRXh0ZW5zaW9uXG4gICAgICAgICAgICAgKlxuICAgICAgICAgICAgICogQHR5cGUge29iamVjdH1cbiAgICAgICAgICAgICAqL1xuICAgICAgICAgICAgZGVmYXVsdHMgPSB7fSxcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBGaW5hbCBFeHRlbnNpb24gT3B0aW9uc1xuICAgICAgICAgICAgICpcbiAgICAgICAgICAgICAqIEB0eXBlIHtvYmplY3R9XG4gICAgICAgICAgICAgKi9cbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXG4gICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAqIE1ldGEgT2JqZWN0XG4gICAgICAgICAgICAgKlxuICAgICAgICAgICAgICogQHR5cGUge29iamVjdH1cbiAgICAgICAgICAgICAqL1xuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICAgICAgIC8vIEZVTkNUSU9OQUxJVFlcbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFNldCBTdGF0ZVxuICAgICAgICAgKlxuICAgICAgICAgKiBAcGFyYW0ge29iamVjdH0gJGVsZW1lbnQgVmFsaWRhdGVkIGVsZW1lbnQgc2VsZWN0b3IuXG4gICAgICAgICAqIEBwYXJhbSB7c3RyaW5nfSBzdGF0ZSBEZXNjcmliZXMgY3VycmVudCBzdGF0ZSAoXCJ2YWxpZFwiLCBcImVycm9yXCIpLlxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zZXRTdGF0ZSA9IGZ1bmN0aW9uICgkZWxlbWVudCwgc3RhdGUpIHtcbiAgICAgICAgICAgIHN3aXRjaCAoc3RhdGUpIHtcbiAgICAgICAgICAgICAgICBjYXNlICd2YWxpZCc6XG4gICAgICAgICAgICAgICAgICAgICRlbGVtZW50XG4gICAgICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2Vycm9yJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygndmFsaWQnKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnZXJyb3InOlxuICAgICAgICAgICAgICAgICAgICAkZWxlbWVudFxuICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCd2YWxpZCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2Vycm9yJyk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGRlZmF1bHQ6XG4gICAgICAgICAgICAgICAgICAgICRlbGVtZW50LnJlbW92ZUNsYXNzKCd2YWxpZCBlcnJvcicpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogVmFsaWRhdGUgSXRlbVxuICAgICAgICAgKlxuICAgICAgICAgKiBAcmV0dXJuIHtib29sZWFufSBSZXR1cm5zIHRoZSB2YWxpZGF0aW9uIHJlc3VsdC5cbiAgICAgICAgICovXG4gICAgICAgIHZhciBfdmFsaWRhdGVJdGVtID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICBzZXR0aW5ncyA9IGpzZS5saWJzLmZhbGxiYWNrLl9kYXRhKCRzZWxmLCAndmFsaWRhdG9yJyksXG4gICAgICAgICAgICAgICAgdmFsaWRhdGUgPSAoc2V0dGluZ3MudmFsaWRhdGUpID8gc2V0dGluZ3MudmFsaWRhdGUuc3BsaXQoJyAnKSA6IFtdLFxuICAgICAgICAgICAgICAgIHR5cGUgPSAkc2VsZi5wcm9wKCd0YWdOYW1lJykudG9Mb3dlckNhc2UoKSxcbiAgICAgICAgICAgICAgICByZXN1bHQgPSB0cnVlO1xuXG4gICAgICAgICAgICB0eXBlID0gKHR5cGUgIT09ICdpbnB1dCcpID8gdHlwZSA6ICRzZWxmLmF0dHIoJ3R5cGUnKS50b0xvd2VyQ2FzZSgpO1xuXG4gICAgICAgICAgICAkLmVhY2godmFsaWRhdGUsIGZ1bmN0aW9uIChpbmRleCwgdmFsaWRhdGlvblR5cGUpIHtcbiAgICAgICAgICAgICAgICB2YXIgaXNWYWxpZCA9IHBlcmZvcm1bdmFsaWRhdGlvblR5cGVdKCRzZWxmLCAkc2VsZi52YWwoKSwgdHlwZSwgc2V0dGluZ3MpO1xuICAgICAgICAgICAgICAgIGlmIChpc1ZhbGlkICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgIF9zZXRTdGF0ZSgkc2VsZiwgKGlzVmFsaWQpID8gJ3ZhbGlkJyA6ICdlcnJvcicpO1xuICAgICAgICAgICAgICAgICAgICByZXN1bHQgPSAoIXJlc3VsdCkgPyBmYWxzZSA6IGlzVmFsaWQ7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiByZXN1bHQ7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFZhbGlkYXRlIE11bHRpcGxlIEl0ZW1zXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBDb250YWlucyB0aGUgZXZlbnQgaW5mb3JtYXRpb24uXG4gICAgICAgICAqIEBwYXJhbSB7b2JqZWN0fSBkZWZlcnJlZCBEZWZpbmVzIHRoZSBkZWZlcnJlZCBvYmplY3QuXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3ZhbGlkYXRlSXRlbXMgPSBmdW5jdGlvbiAoZXZlbnQsIGRlZmVycmVkKSB7XG4gICAgICAgICAgICBpZiAoZXZlbnQpIHtcbiAgICAgICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgICAgfVxuXG5cbiAgICAgICAgICAgIHZhciAkc2VsZiA9IGV2ZW50ID8gJChldmVudC50YXJnZXQpIDogJHRoaXMsXG4gICAgICAgICAgICAgICAgdmFsaWQgPSB0cnVlO1xuXG4gICAgICAgICAgICAkc2VsZlxuICAgICAgICAgICAgICAgIC5maWx0ZXIoJy52YWxpZGF0ZScpXG4gICAgICAgICAgICAgICAgLmFkZCgkc2VsZi5maW5kKCcudmFsaWRhdGUnKSlcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBjdXJyZW50ID0gX3ZhbGlkYXRlSXRlbS5jYWxsKCQodGhpcykpO1xuICAgICAgICAgICAgICAgICAgICB2YWxpZCA9ICghdmFsaWQpID8gZmFsc2UgOiBjdXJyZW50O1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiAoZGVmZXJyZWQgJiYgZGVmZXJyZWQuZGVmZXJyZWQpIHtcbiAgICAgICAgICAgICAgICBpZiAodmFsaWQpIHtcbiAgICAgICAgICAgICAgICAgICAgZGVmZXJyZWQuZGVmZXJyZWQucmVzb2x2ZSgpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGRlZmVycmVkLmRlZmVycmVkLnJlamVjdCgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHZhbGlkO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBSZXNldCBWYWxpZGF0b3IgRWxlbWVudHNcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfcmVzZXRWYWxpZGF0b3IgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgIC5maWx0ZXIoJy52YWxpZGF0ZScpXG4gICAgICAgICAgICAgICAgLmFkZCgkdGhpcy5maW5kKCcudmFsaWRhdGUnKSlcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIF9zZXRTdGF0ZSgkKHRoaXMpLCAncmVzZXQnKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgICAgICAgLy8gSU5JVElBTElaQVRJT05cbiAgICAgICAgLy8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIGV4dGVuc2lvbiwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG4gICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgIC5vbignY2hhbmdlJywgJy52YWxpZGF0ZTp0ZXh0OnZpc2libGUnLCBfdmFsaWRhdGVJdGVtKVxuICAgICAgICAgICAgICAgIC5vbigndmFsaWRhdG9yLnZhbGlkYXRlJywgX3ZhbGlkYXRlSXRlbXMpXG4gICAgICAgICAgICAgICAgLm9uKCd2YWxpZGF0b3IucmVzZXQnLCBfcmVzZXRWYWxpZGF0b3IpXG4gICAgICAgICAgICAgICAgLm9uKCdzdWJtaXQnLCBmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCFfdmFsaWRhdGVJdGVtcygpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

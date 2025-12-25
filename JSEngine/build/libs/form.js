'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 form.js 2022-10-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.form = jse.libs.form || {};

/**
 * ## Form Utilities Library
 *
 * This library contains form helpers mostly required by old modules (JS Engine v1.0).
 *
 * @module JSE/Libs/forms
 * @exports jse.libs.forms
 */
(function (exports) {

    'use strict';

    /**
     * Get URL parameters.
     *
     * @param {String} url
     * @param {Boolean} deep
     *
     * @return {Object}
     */

    function _getUrlParams(url, deep) {
        url = decodeURIComponent(url || location.href);

        var splitUrl = url.split('?'),
            splitParam = splitUrl.length > 1 ? splitUrl[1].split('&') : [],
            regex = new RegExp(/\[(.*?)\]/g),
            result = {};

        $.each(splitParam, function (i, v) {
            var keyValue = v.split('='),
                regexResult = regex.exec(keyValue[0]),
                base = null,
                basename = keyValue[0].substring(0, keyValue[0].search('\\[')),
                keys = [],
                lastKey = null;

            if (!deep || regexResult === null) {
                result[keyValue[0]] = keyValue[1].split('#')[0];
            } else {

                result[basename] = result[basename] || [];
                base = result[basename];

                do {
                    keys.push(regexResult[1]);
                    regexResult = regex.exec(keyValue[0]);
                } while (regexResult !== null);

                $.each(keys, function (i, v) {
                    var next = keys[i + 1];
                    v = v || '0';

                    if (typeof next === 'string') {
                        base[v] = base[v] || [];
                        base = base[v];
                    } else {
                        base[v] = base[v] || undefined;
                        lastKey = v;
                    }
                });

                if (lastKey !== null) {
                    base[lastKey] = keyValue[1];
                } else {
                    base = keyValue[1];
                }
            }
        });

        return result;
    }

    /**
     * Create Options
     *
     * Function to add options to a select field. The full dataset for each option is added at the
     * option element.
     *
     * @param {object} $destination    jQuery-object of the select field.
     * @param {json} dataset Array that contains several objects with at least a "name" and a "value" field.
     * @param {bool} addEmpty If true, an empty select option will be generated (value = -1).
     * @param {bool} order Orders the dataset by name if true.
     *
     * @public
     */
    exports.createOptions = function ($destination, dataset, addEmpty, order) {
        var markup = [];

        // Helper for sorting the dataset
        var _optionsSorter = function _optionsSorter(a, b) {
            a = a.name.toLowerCase();
            b = b.name.toLowerCase();

            return a < b ? -1 : 1;
        };

        // Sort data
        dataset = order ? dataset.sort(_optionsSorter) : dataset;

        // Add an empty element if "addEmpty" is true
        if (addEmpty) {
            markup.push($('<option value="-1"> </option>'));
        }

        // Adding options to the markup
        $.each(dataset, function (index, value) {
            var optionValue = String(value.value).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            var optionName = String(value.name).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            var $element = $('<option value="' + optionValue + '">' + optionName + '</option>');
            $element.data('data', value);
            markup.push($element);
        });

        $destination.append(markup);
    };

    /**
     * Pre-fills a form by the given key value pairs in "options".
     *
     * @param {object} $form Element in which the form fields are searched.
     * @param {object} options A JSON with key-value pairs for the form fields.
     * @param {boolean} trigger A "change"-event gets triggered on the modified form field if true.
     *
     * @public
     */
    exports.prefillForm = function ($form, options, trigger) {
        $.each(options, function (index, value) {
            var $element = $form.find('[name="' + index + '"]'),
                type = null;

            if ($element.length) {
                type = $element.prop('tagName').toLowerCase();
                type = type !== 'input' ? type : $element.attr('type').toLowerCase();

                switch (type) {
                    case 'select':
                        if ((typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object') {
                            // Case for multi-select
                            $.each(value, function (i, value) {
                                $element.find('option[value="' + value + '"]').prop('selected', true);
                            });
                        } else {
                            // Case for single select
                            $element.find('option[value="' + value + '"]').prop('selected', true);
                        }
                        break;
                    case 'checkbox':
                        $element.prop('checked', value !== 'false' ? true : false);
                        break;
                    case 'radio':
                        $element.prop('checked', false);
                        $element.each(function () {
                            var $self = $(this);
                            if ($self.val() === value.toString()) {
                                $self.prop('checked', true);
                            }
                        });
                        break;
                    case 'textarea':
                        $element.val(value);
                        break;
                    default:
                        $element.val(value);
                        break;
                }

                if (trigger) {
                    $element.trigger('change', []);
                }
            }
        });
    };

    /**
     * Returns the data from the form fields in a jQuery advantageous JSON format
     *
     * @param {object} $form Target form selector object to be searched.
     * @param {string} ignoreSelector Selector string to be ignored.
     *
     * @return {object} Returns the data from the form elements.
     *
     * @public
     */
    exports.getData = function ($form, ignore, asJSON) {
        var $elements = $form.find('input, textarea, select'),
            result = {};

        if (ignore) {
            $elements = $elements.filter(':not(' + ignore + ')');
        }

        $elements.each(function () {
            var $self = $(this),
                type = $self.prop('tagName').toLowerCase(),
                name = $self.attr('name'),
                regex = new RegExp(/\[(.*?)\]/g),
                regexResult = regex.exec(name),
                watchdog = 5,
                $selected = null,
                res = null,
                base = null,
                lastKey = null;

            type = type !== 'input' ? type : $self.attr('type').toLowerCase();

            if (regexResult !== null) {

                var basename = name.substring(0, name.search('\\[')),
                    keys = [];

                result[basename] = result[basename] || (asJSON ? {} : []);
                base = result[basename];

                do {
                    keys.push(regexResult[1]);
                    regexResult = regex.exec(name);
                    watchdog -= 1;
                } while (regexResult !== null || watchdog <= 0);

                $.each(keys, function (i, v) {
                    var next = keys[i + 1];
                    v = v || '0';

                    if (typeof next === 'string') {
                        base[v] = base[v] || (asJSON ? {} : []);
                        base = base[v];
                    } else if (type !== 'radio') {
                        v = v && v !== '0' ? v : asJSON ? Object.keys(base).length : base.length;
                        base[v] = base[v] || undefined;
                    }

                    lastKey = v;
                });
            }

            switch (type) {
                case 'radio':
                    res = $elements.filter('input[name="' + $self.attr('name') + '"]:checked').val();
                    break;
                case 'checkbox':
                    res = $self.prop('checked') ? $self.val() : false;
                    break;
                case 'select':
                    $selected = $self.find(':selected');
                    if ($selected.length > 1) {
                        res = [];
                        $selected.each(function () {
                            res.push($(this).val());
                        });
                    } else {
                        res = $selected.val();
                    }
                    break;
                case 'button':
                    break;
                default:
                    if (name) {
                        res = $self.val();
                    }
                    break;
            }

            if (base !== null) {
                base[lastKey] = res;
            } else {
                result[name] = res;
            }
        });

        return result;
    };

    /**
     * Returns the form field type.
     *
     * @param {object} $element Element selector to be checked.
     *
     * @return {string} Returns the field type name of the element.
     *
     * @public
     */
    exports.getFieldType = function ($element) {
        var type = $element.prop('tagName').toLowerCase();
        return type !== 'input' ? type : $element.attr('type').toLowerCase();
    };

    /**
     * Adds a hidden field to the provided target.
     *
     * @param {object} $target Target element to prepend the hidden field to.
     * @param {boolean} replace Should the target element be replaced?
     */
    exports.addHiddenByUrl = function ($target, replace) {
        var urlParam = _getUrlParams(null),
            $field = null,
            hiddens = '',
            update = [];

        $.each(urlParam, function (k, v) {
            if (v) {
                $field = $target.find('[name="' + k + '"]');

                if ($field.length === 0) {
                    hiddens += '<input type="hidden" name="' + k + '" value="' + v + '" />';
                } else {
                    update.push(k, v);
                }
            }
        });

        if (replace) {
            exports.prefillForm($target, update);
        }

        $target.prepend(hiddens);
    };

    /**
     * Resets the the provided target form.
     *
     * This method will clear all textfields. All radio buttons
     * and checkboxes will be unchecked, only the first checkbox and
     * radio button will get checked.
     *
     * @param {object} $target Form to reset.
     */
    exports.reset = function ($target) {
        $target.find('select, input, textarea').each(function () {
            var $self = $(this),
                type = exports.getFieldType($self);

            switch (type) {
                case 'radio':
                    $target.find('input[name="' + $self.attr('name') + '"]:checked').prop('checked', false).first().prop('checked', true);
                    break;
                case 'checkbox':
                    $self.prop('checked', false);
                    break;
                case 'select':
                    $self.children().first().prop('selected', true);
                    break;
                case 'textarea':
                    $self.val('');
                    break;
                case 'text':
                    $self.val('');
                    break;
                default:
                    break;
            }
        });
    };
})(jse.libs.form);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImZvcm0uanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsImZvcm0iLCJleHBvcnRzIiwiX2dldFVybFBhcmFtcyIsInVybCIsImRlZXAiLCJkZWNvZGVVUklDb21wb25lbnQiLCJsb2NhdGlvbiIsImhyZWYiLCJzcGxpdFVybCIsInNwbGl0Iiwic3BsaXRQYXJhbSIsImxlbmd0aCIsInJlZ2V4IiwiUmVnRXhwIiwicmVzdWx0IiwiJCIsImVhY2giLCJpIiwidiIsImtleVZhbHVlIiwicmVnZXhSZXN1bHQiLCJleGVjIiwiYmFzZSIsImJhc2VuYW1lIiwic3Vic3RyaW5nIiwic2VhcmNoIiwia2V5cyIsImxhc3RLZXkiLCJwdXNoIiwibmV4dCIsInVuZGVmaW5lZCIsImNyZWF0ZU9wdGlvbnMiLCIkZGVzdGluYXRpb24iLCJkYXRhc2V0IiwiYWRkRW1wdHkiLCJvcmRlciIsIm1hcmt1cCIsIl9vcHRpb25zU29ydGVyIiwiYSIsImIiLCJuYW1lIiwidG9Mb3dlckNhc2UiLCJzb3J0IiwiaW5kZXgiLCJ2YWx1ZSIsIm9wdGlvblZhbHVlIiwiU3RyaW5nIiwicmVwbGFjZSIsIm9wdGlvbk5hbWUiLCIkZWxlbWVudCIsImRhdGEiLCJhcHBlbmQiLCJwcmVmaWxsRm9ybSIsIiRmb3JtIiwib3B0aW9ucyIsInRyaWdnZXIiLCJmaW5kIiwidHlwZSIsInByb3AiLCJhdHRyIiwiJHNlbGYiLCJ2YWwiLCJ0b1N0cmluZyIsImdldERhdGEiLCJpZ25vcmUiLCJhc0pTT04iLCIkZWxlbWVudHMiLCJmaWx0ZXIiLCJ3YXRjaGRvZyIsIiRzZWxlY3RlZCIsInJlcyIsIk9iamVjdCIsImdldEZpZWxkVHlwZSIsImFkZEhpZGRlbkJ5VXJsIiwiJHRhcmdldCIsInVybFBhcmFtIiwiJGZpZWxkIiwiaGlkZGVucyIsInVwZGF0ZSIsImsiLCJwcmVwZW5kIiwicmVzZXQiLCJmaXJzdCIsImNoaWxkcmVuIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMs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Q0FBZ0JILE9BQWhCO0FBQ0gsS0F2QkQ7O0FBeUJBOzs7Ozs7Ozs7QUFTQTVFLFlBQVFnRixLQUFSLEdBQWdCLFVBQVVQLE9BQVYsRUFBbUI7QUFDL0JBLGdCQUNLbEIsSUFETCxDQUNVLHlCQURWLEVBRUt4QyxJQUZMLENBRVUsWUFBWTtBQUNkLGdCQUFJNEMsUUFBUTdDLEVBQUUsSUFBRixDQUFaO0FBQUEsZ0JBQ0kwQyxPQUFPeEQsUUFBUXVFLFlBQVIsQ0FBcUJaLEtBQXJCLENBRFg7O0FBR0Esb0JBQVFILElBQVI7QUFDSSxxQkFBSyxPQUFMO0FBQ0lpQiw0QkFDS2xCLElBREwsQ0FDVSxpQkFBaUJJLE1BQU1ELElBQU4sQ0FBVyxNQUFYLENBQWpCLEdBQXNDLFlBRGhELEVBRUtELElBRkwsQ0FFVSxTQUZWLEVBRXFCLEtBRnJCLEVBR0t3QixLQUhMLEdBSUt4QixJQUpMLENBSVUsU0FKVixFQUlxQixJQUpyQjtBQUtBO0FBQ0oscUJBQUssVUFBTDtBQUNJRSwwQkFBTUYsSUFBTixDQUFXLFNBQVgsRUFBc0IsS0FBdEI7QUFDQTtBQUNKLHFCQUFLLFFBQUw7QUFDSUUsMEJBQ0t1QixRQURMLEdBRUtELEtBRkwsR0FHS3hCLElBSEwsQ0FHVSxVQUhWLEVBR3NCLElBSHRCO0FBSUE7QUFDSixxQkFBSyxVQUFMO0FBQ0lFLDBCQUFNQyxHQUFOLENBQVUsRUFBVjtBQUNBO0FBQ0oscUJBQUssTUFBTDtBQUNJRCwwQkFBTUMsR0FBTixDQUFVLEVBQVY7QUFDQTtBQUNKO0FBQ0k7QUF4QlI7QUEwQkgsU0FoQ0w7QUFpQ0gsS0FsQ0Q7QUFvQ0gsQ0F2WEQsRUF1WEcvRCxJQUFJQyxJQUFKLENBQVNDLElBdlhaIiwiZmlsZSI6ImZvcm0uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGZvcm0uanMgMjAyMi0xMC0yMFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMjIgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLmZvcm0gPSBqc2UubGlicy5mb3JtIHx8IHt9O1xuXG4vKipcbiAqICMjIEZvcm0gVXRpbGl0aWVzIExpYnJhcnlcbiAqXG4gKiBUaGlzIGxpYnJhcnkgY29udGFpbnMgZm9ybSBoZWxwZXJzIG1vc3RseSByZXF1aXJlZCBieSBvbGQgbW9kdWxlcyAoSlMgRW5naW5lIHYxLjApLlxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvZm9ybXNcbiAqIEBleHBvcnRzIGpzZS5saWJzLmZvcm1zXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgLyoqXG4gICAgICogR2V0IFVSTCBwYXJhbWV0ZXJzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IHVybFxuICAgICAqIEBwYXJhbSB7Qm9vbGVhbn0gZGVlcFxuICAgICAqXG4gICAgICogQHJldHVybiB7T2JqZWN0fVxuICAgICAqL1xuICAgIGZ1bmN0aW9uIF9nZXRVcmxQYXJhbXModXJsLCBkZWVwKSB7XG4gICAgICAgIHVybCA9IGRlY29kZVVSSUNvbXBvbmVudCh1cmwgfHwgbG9jYXRpb24uaHJlZik7XG5cbiAgICAgICAgbGV0IHNwbGl0VXJsID0gdXJsLnNwbGl0KCc/JyksXG4gICAgICAgICAgICBzcGxpdFBhcmFtID0gKHNwbGl0VXJsLmxlbmd0aCA+IDEpID8gc3BsaXRVcmxbMV0uc3BsaXQoJyYnKSA6IFtdLFxuICAgICAgICAgICAgcmVnZXggPSBuZXcgUmVnRXhwKC9cXFsoLio/KVxcXS9nKSxcbiAgICAgICAgICAgIHJlc3VsdCA9IHt9O1xuXG4gICAgICAgICQuZWFjaChzcGxpdFBhcmFtLCBmdW5jdGlvbiAoaSwgdikge1xuICAgICAgICAgICAgbGV0IGtleVZhbHVlID0gdi5zcGxpdCgnPScpLFxuICAgICAgICAgICAgICAgIHJlZ2V4UmVzdWx0ID0gcmVnZXguZXhlYyhrZXlWYWx1ZVswXSksXG4gICAgICAgICAgICAgICAgYmFzZSA9IG51bGwsXG4gICAgICAgICAgICAgICAgYmFzZW5hbWUgPSBrZXlWYWx1ZVswXS5zdWJzdHJpbmcoMCwga2V5VmFsdWVbMF0uc2VhcmNoKCdcXFxcWycpKSxcbiAgICAgICAgICAgICAgICBrZXlzID0gW10sXG4gICAgICAgICAgICAgICAgbGFzdEtleSA9IG51bGw7XG5cbiAgICAgICAgICAgIGlmICghZGVlcCB8fCByZWdleFJlc3VsdCA9PT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIHJlc3VsdFtrZXlWYWx1ZVswXV0gPSBrZXlWYWx1ZVsxXS5zcGxpdCgnIycpWzBdO1xuICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgIHJlc3VsdFtiYXNlbmFtZV0gPSByZXN1bHRbYmFzZW5hbWVdIHx8IFtdO1xuICAgICAgICAgICAgICAgIGJhc2UgPSByZXN1bHRbYmFzZW5hbWVdO1xuXG4gICAgICAgICAgICAgICAgZG8ge1xuICAgICAgICAgICAgICAgICAgICBrZXlzLnB1c2gocmVnZXhSZXN1bHRbMV0pO1xuICAgICAgICAgICAgICAgICAgICByZWdleFJlc3VsdCA9IHJlZ2V4LmV4ZWMoa2V5VmFsdWVbMF0pO1xuICAgICAgICAgICAgICAgIH0gd2hpbGUgKHJlZ2V4UmVzdWx0ICE9PSBudWxsKTtcblxuICAgICAgICAgICAgICAgICQuZWFjaChrZXlzLCBmdW5jdGlvbiAoaSwgdikge1xuICAgICAgICAgICAgICAgICAgICBsZXQgbmV4dCA9IGtleXNbaSArIDFdO1xuICAgICAgICAgICAgICAgICAgICB2ID0gdiB8fCAnMCc7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHR5cGVvZiAobmV4dCkgPT09ICdzdHJpbmcnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBiYXNlW3ZdID0gYmFzZVt2XSB8fCBbXTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJhc2UgPSBiYXNlW3ZdO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZVt2XSA9IGJhc2Vbdl0gfHwgdW5kZWZpbmVkO1xuICAgICAgICAgICAgICAgICAgICAgICAgbGFzdEtleSA9IHY7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIGlmIChsYXN0S2V5ICE9PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgICAgIGJhc2VbbGFzdEtleV0gPSBrZXlWYWx1ZVsxXTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBiYXNlID0ga2V5VmFsdWVbMV07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiByZXN1bHQ7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQ3JlYXRlIE9wdGlvbnNcbiAgICAgKlxuICAgICAqIEZ1bmN0aW9uIHRvIGFkZCBvcHRpb25zIHRvIGEgc2VsZWN0IGZpZWxkLiBUaGUgZnVsbCBkYXRhc2V0IGZvciBlYWNoIG9wdGlvbiBpcyBhZGRlZCBhdCB0aGVcbiAgICAgKiBvcHRpb24gZWxlbWVudC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSAkZGVzdGluYXRpb24gICAgalF1ZXJ5LW9iamVjdCBvZiB0aGUgc2VsZWN0IGZpZWxkLlxuICAgICAqIEBwYXJhbSB7anNvbn0gZGF0YXNldCBBcnJheSB0aGF0IGNvbnRhaW5zIHNldmVyYWwgb2JqZWN0cyB3aXRoIGF0IGxlYXN0IGEgXCJuYW1lXCIgYW5kIGEgXCJ2YWx1ZVwiIGZpZWxkLlxuICAgICAqIEBwYXJhbSB7Ym9vbH0gYWRkRW1wdHkgSWYgdHJ1ZSwgYW4gZW1wdHkgc2VsZWN0IG9wdGlvbiB3aWxsIGJlIGdlbmVyYXRlZCAodmFsdWUgPSAtMSkuXG4gICAgICogQHBhcmFtIHtib29sfSBvcmRlciBPcmRlcnMgdGhlIGRhdGFzZXQgYnkgbmFtZSBpZiB0cnVlLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGV4cG9ydHMuY3JlYXRlT3B0aW9ucyA9IGZ1bmN0aW9uICgkZGVzdGluYXRpb24sIGRhdGFzZXQsIGFkZEVtcHR5LCBvcmRlcikge1xuICAgICAgICB2YXIgbWFya3VwID0gW107XG5cbiAgICAgICAgLy8gSGVscGVyIGZvciBzb3J0aW5nIHRoZSBkYXRhc2V0XG4gICAgICAgIHZhciBfb3B0aW9uc1NvcnRlciA9IGZ1bmN0aW9uIChhLCBiKSB7XG4gICAgICAgICAgICBhID0gYS5uYW1lLnRvTG93ZXJDYXNlKCk7XG4gICAgICAgICAgICBiID0gYi5uYW1lLnRvTG93ZXJDYXNlKCk7XG5cbiAgICAgICAgICAgIHJldHVybiAoYSA8IGIpID8gLTEgOiAxO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFNvcnQgZGF0YVxuICAgICAgICBkYXRhc2V0ID0gb3JkZXIgPyBkYXRhc2V0LnNvcnQoX29wdGlvbnNTb3J0ZXIpIDogZGF0YXNldDtcblxuICAgICAgICAvLyBBZGQgYW4gZW1wdHkgZWxlbWVudCBpZiBcImFkZEVtcHR5XCIgaXMgdHJ1ZVxuICAgICAgICBpZiAoYWRkRW1wdHkpIHtcbiAgICAgICAgICAgIG1hcmt1cC5wdXNoKCQoJzxvcHRpb24gdmFsdWU9XCItMVwiPiA8L29wdGlvbj4nKSk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBBZGRpbmcgb3B0aW9ucyB0byB0aGUgbWFya3VwXG4gICAgICAgICQuZWFjaChkYXRhc2V0LCBmdW5jdGlvbiAoaW5kZXgsIHZhbHVlKSB7XG4gICAgICAgICAgICBsZXQgb3B0aW9uVmFsdWUgPSBTdHJpbmcodmFsdWUudmFsdWUpXG4gICAgICAgICAgICAgICAgLnJlcGxhY2UoLyYvZywgXCImYW1wO1wiKVxuICAgICAgICAgICAgICAgIC5yZXBsYWNlKC88L2csIFwiJmx0O1wiKVxuICAgICAgICAgICAgICAgIC5yZXBsYWNlKC8+L2csIFwiJmd0O1wiKVxuICAgICAgICAgICAgICAgIC5yZXBsYWNlKC9cIi9nLCBcIiZxdW90O1wiKVxuICAgICAgICAgICAgICAgIC5yZXBsYWNlKC8nL2csIFwiJiMwMzk7XCIpO1xuICAgICAgICAgICAgbGV0IG9wdGlvbk5hbWUgPSBTdHJpbmcodmFsdWUubmFtZSlcbiAgICAgICAgICAgICAgICAucmVwbGFjZSgvJi9nLCBcIiZhbXA7XCIpXG4gICAgICAgICAgICAgICAgLnJlcGxhY2UoLzwvZywgXCImbHQ7XCIpXG4gICAgICAgICAgICAgICAgLnJlcGxhY2UoLz4vZywgXCImZ3Q7XCIpXG4gICAgICAgICAgICAgICAgLnJlcGxhY2UoL1wiL2csIFwiJnF1b3Q7XCIpXG4gICAgICAgICAgICAgICAgLnJlcGxhY2UoLycvZywgXCImIzAzOTtcIik7XG4gICAgICAgICAgICB2YXIgJGVsZW1lbnQgPSAkKCc8b3B0aW9uIHZhbHVlPVwiJyArIG9wdGlvblZhbHVlICsgJ1wiPicgKyBvcHRpb25OYW1lICsgJzwvb3B0aW9uPicpO1xuICAgICAgICAgICAgJGVsZW1lbnQuZGF0YSgnZGF0YScsIHZhbHVlKTtcbiAgICAgICAgICAgIG1hcmt1cC5wdXNoKCRlbGVtZW50KTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgJGRlc3RpbmF0aW9uLmFwcGVuZChtYXJrdXApO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBQcmUtZmlsbHMgYSBmb3JtIGJ5IHRoZSBnaXZlbiBrZXkgdmFsdWUgcGFpcnMgaW4gXCJvcHRpb25zXCIuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gJGZvcm0gRWxlbWVudCBpbiB3aGljaCB0aGUgZm9ybSBmaWVsZHMgYXJlIHNlYXJjaGVkLlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIEEgSlNPTiB3aXRoIGtleS12YWx1ZSBwYWlycyBmb3IgdGhlIGZvcm0gZmllbGRzLlxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gdHJpZ2dlciBBIFwiY2hhbmdlXCItZXZlbnQgZ2V0cyB0cmlnZ2VyZWQgb24gdGhlIG1vZGlmaWVkIGZvcm0gZmllbGQgaWYgdHJ1ZS5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBleHBvcnRzLnByZWZpbGxGb3JtID0gZnVuY3Rpb24gKCRmb3JtLCBvcHRpb25zLCB0cmlnZ2VyKSB7XG4gICAgICAgICQuZWFjaChvcHRpb25zLCBmdW5jdGlvbiAoaW5kZXgsIHZhbHVlKSB7XG4gICAgICAgICAgICB2YXIgJGVsZW1lbnQgPSAkZm9ybS5maW5kKCdbbmFtZT1cIicgKyBpbmRleCArICdcIl0nKSxcbiAgICAgICAgICAgICAgICB0eXBlID0gbnVsbDtcblxuICAgICAgICAgICAgaWYgKCRlbGVtZW50Lmxlbmd0aCkge1xuICAgICAgICAgICAgICAgIHR5cGUgPSAkZWxlbWVudC5wcm9wKCd0YWdOYW1lJykudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgICAgICAgICB0eXBlID0gKHR5cGUgIT09ICdpbnB1dCcpID8gdHlwZSA6ICRlbGVtZW50LmF0dHIoJ3R5cGUnKS50b0xvd2VyQ2FzZSgpO1xuXG4gICAgICAgICAgICAgICAgc3dpdGNoICh0eXBlKSB7XG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJ3NlbGVjdCc6XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIHZhbHVlID09PSAnb2JqZWN0Jykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIENhc2UgZm9yIG11bHRpLXNlbGVjdFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQuZWFjaCh2YWx1ZSwgZnVuY3Rpb24gKGksIHZhbHVlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRlbGVtZW50XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnb3B0aW9uW3ZhbHVlPVwiJyArIHZhbHVlICsgJ1wiXScpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAucHJvcCgnc2VsZWN0ZWQnLCB0cnVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gQ2FzZSBmb3Igc2luZ2xlIHNlbGVjdFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRlbGVtZW50XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5maW5kKCdvcHRpb25bdmFsdWU9XCInICsgdmFsdWUgKyAnXCJdJylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLnByb3AoJ3NlbGVjdGVkJywgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAnY2hlY2tib3gnOlxuICAgICAgICAgICAgICAgICAgICAgICAgJGVsZW1lbnQucHJvcCgnY2hlY2tlZCcsICh2YWx1ZSAhPT0gJ2ZhbHNlJykgPyB0cnVlIDogZmFsc2UpO1xuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJ3JhZGlvJzpcbiAgICAgICAgICAgICAgICAgICAgICAgICRlbGVtZW50LnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkZWxlbWVudC5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkc2VsZi52YWwoKSA9PT0gdmFsdWUudG9TdHJpbmcoKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkc2VsZi5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAndGV4dGFyZWEnOlxuICAgICAgICAgICAgICAgICAgICAgICAgJGVsZW1lbnQudmFsKHZhbHVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICBkZWZhdWx0OlxuICAgICAgICAgICAgICAgICAgICAgICAgJGVsZW1lbnQudmFsKHZhbHVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIGlmICh0cmlnZ2VyKSB7XG4gICAgICAgICAgICAgICAgICAgICRlbGVtZW50LnRyaWdnZXIoJ2NoYW5nZScsIFtdKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgdGhlIGRhdGEgZnJvbSB0aGUgZm9ybSBmaWVsZHMgaW4gYSBqUXVlcnkgYWR2YW50YWdlb3VzIEpTT04gZm9ybWF0XG4gICAgICpcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gJGZvcm0gVGFyZ2V0IGZvcm0gc2VsZWN0b3Igb2JqZWN0IHRvIGJlIHNlYXJjaGVkLlxuICAgICAqIEBwYXJhbSB7c3RyaW5nfSBpZ25vcmVTZWxlY3RvciBTZWxlY3RvciBzdHJpbmcgdG8gYmUgaWdub3JlZC5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyB0aGUgZGF0YSBmcm9tIHRoZSBmb3JtIGVsZW1lbnRzLlxuICAgICAqXG4gICAgICogQHB1YmxpY1xuICAgICAqL1xuICAgIGV4cG9ydHMuZ2V0RGF0YSA9IGZ1bmN0aW9uICgkZm9ybSwgaWdub3JlLCBhc0pTT04pIHtcbiAgICAgICAgdmFyICRlbGVtZW50cyA9ICRmb3JtLmZpbmQoJ2lucHV0LCB0ZXh0YXJlYSwgc2VsZWN0JyksXG4gICAgICAgICAgICByZXN1bHQgPSB7fTtcblxuICAgICAgICBpZiAoaWdub3JlKSB7XG4gICAgICAgICAgICAkZWxlbWVudHMgPSAkZWxlbWVudHMuZmlsdGVyKCc6bm90KCcgKyBpZ25vcmUgKyAnKScpO1xuICAgICAgICB9XG5cbiAgICAgICAgJGVsZW1lbnRzLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICB0eXBlID0gJHNlbGYucHJvcCgndGFnTmFtZScpLnRvTG93ZXJDYXNlKCksXG4gICAgICAgICAgICAgICAgbmFtZSA9ICRzZWxmLmF0dHIoJ25hbWUnKSxcbiAgICAgICAgICAgICAgICByZWdleCA9IG5ldyBSZWdFeHAoL1xcWyguKj8pXFxdL2cpLFxuICAgICAgICAgICAgICAgIHJlZ2V4UmVzdWx0ID0gcmVnZXguZXhlYyhuYW1lKSxcbiAgICAgICAgICAgICAgICB3YXRjaGRvZyA9IDUsXG4gICAgICAgICAgICAgICAgJHNlbGVjdGVkID0gbnVsbCxcbiAgICAgICAgICAgICAgICByZXMgPSBudWxsLFxuICAgICAgICAgICAgICAgIGJhc2UgPSBudWxsLFxuICAgICAgICAgICAgICAgIGxhc3RLZXkgPSBudWxsO1xuXG4gICAgICAgICAgICB0eXBlID0gKHR5cGUgIT09ICdpbnB1dCcpID8gdHlwZSA6ICRzZWxmLmF0dHIoJ3R5cGUnKS50b0xvd2VyQ2FzZSgpO1xuXG4gICAgICAgICAgICBpZiAocmVnZXhSZXN1bHQgIT09IG51bGwpIHtcblxuICAgICAgICAgICAgICAgIHZhciBiYXNlbmFtZSA9IG5hbWUuc3Vic3RyaW5nKDAsIG5hbWUuc2VhcmNoKCdcXFxcWycpKSxcbiAgICAgICAgICAgICAgICAgICAga2V5cyA9IFtdO1xuXG4gICAgICAgICAgICAgICAgcmVzdWx0W2Jhc2VuYW1lXSA9IHJlc3VsdFtiYXNlbmFtZV0gfHwgKGFzSlNPTiA/IHt9IDogW10pO1xuICAgICAgICAgICAgICAgIGJhc2UgPSByZXN1bHRbYmFzZW5hbWVdO1xuXG4gICAgICAgICAgICAgICAgZG8ge1xuICAgICAgICAgICAgICAgICAgICBrZXlzLnB1c2gocmVnZXhSZXN1bHRbMV0pO1xuICAgICAgICAgICAgICAgICAgICByZWdleFJlc3VsdCA9IHJlZ2V4LmV4ZWMobmFtZSk7XG4gICAgICAgICAgICAgICAgICAgIHdhdGNoZG9nIC09IDE7XG4gICAgICAgICAgICAgICAgfSB3aGlsZSAocmVnZXhSZXN1bHQgIT09IG51bGwgfHwgd2F0Y2hkb2cgPD0gMCk7XG5cbiAgICAgICAgICAgICAgICAkLmVhY2goa2V5cywgZnVuY3Rpb24gKGksIHYpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIG5leHQgPSBrZXlzW2kgKyAxXTtcbiAgICAgICAgICAgICAgICAgICAgdiA9IHYgfHwgJzAnO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICh0eXBlb2YgKG5leHQpID09PSAnc3RyaW5nJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZVt2XSA9IGJhc2Vbdl0gfHwgKGFzSlNPTiA/IHt9IDogW10pO1xuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZSA9IGJhc2Vbdl07XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodHlwZSAhPT0gJ3JhZGlvJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgdiA9ICh2ICYmIHYgIT09ICcwJykgPyB2IDpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAoYXNKU09OKSA/IE9iamVjdC5rZXlzKGJhc2UpLmxlbmd0aCA6IGJhc2UubGVuZ3RoO1xuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZVt2XSA9IGJhc2Vbdl0gfHwgdW5kZWZpbmVkO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgbGFzdEtleSA9IHY7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgc3dpdGNoICh0eXBlKSB7XG4gICAgICAgICAgICAgICAgY2FzZSAncmFkaW8nOlxuICAgICAgICAgICAgICAgICAgICByZXMgPSAkZWxlbWVudHNcbiAgICAgICAgICAgICAgICAgICAgICAgIC5maWx0ZXIoJ2lucHV0W25hbWU9XCInICsgJHNlbGYuYXR0cignbmFtZScpICsgJ1wiXTpjaGVja2VkJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC52YWwoKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnY2hlY2tib3gnOlxuICAgICAgICAgICAgICAgICAgICByZXMgPSAoJHNlbGYucHJvcCgnY2hlY2tlZCcpKSA/ICRzZWxmLnZhbCgpIDogZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgJ3NlbGVjdCc6XG4gICAgICAgICAgICAgICAgICAgICRzZWxlY3RlZCA9ICRzZWxmLmZpbmQoJzpzZWxlY3RlZCcpO1xuICAgICAgICAgICAgICAgICAgICBpZiAoJHNlbGVjdGVkLmxlbmd0aCA+IDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlcyA9IFtdO1xuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGVjdGVkLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlcy5wdXNoKCQodGhpcykudmFsKCkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXMgPSAkc2VsZWN0ZWQudmFsKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnYnV0dG9uJzpcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgICAgICAgICAgaWYgKG5hbWUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlcyA9ICRzZWxmLnZhbCgpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoYmFzZSAhPT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIGJhc2VbbGFzdEtleV0gPSByZXM7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIHJlc3VsdFtuYW1lXSA9IHJlcztcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIHRoZSBmb3JtIGZpZWxkIHR5cGUuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge29iamVjdH0gJGVsZW1lbnQgRWxlbWVudCBzZWxlY3RvciB0byBiZSBjaGVja2VkLlxuICAgICAqXG4gICAgICogQHJldHVybiB7c3RyaW5nfSBSZXR1cm5zIHRoZSBmaWVsZCB0eXBlIG5hbWUgb2YgdGhlIGVsZW1lbnQuXG4gICAgICpcbiAgICAgKiBAcHVibGljXG4gICAgICovXG4gICAgZXhwb3J0cy5nZXRGaWVsZFR5cGUgPSBmdW5jdGlvbiAoJGVsZW1lbnQpIHtcbiAgICAgICAgdmFyIHR5cGUgPSAkZWxlbWVudC5wcm9wKCd0YWdOYW1lJykudG9Mb3dlckNhc2UoKTtcbiAgICAgICAgcmV0dXJuICh0eXBlICE9PSAnaW5wdXQnKSA/IHR5cGUgOiAkZWxlbWVudC5hdHRyKCd0eXBlJykudG9Mb3dlckNhc2UoKTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogQWRkcyBhIGhpZGRlbiBmaWVsZCB0byB0aGUgcHJvdmlkZWQgdGFyZ2V0LlxuICAgICAqXG4gICAgICogQHBhcmFtIHtvYmplY3R9ICR0YXJnZXQgVGFyZ2V0IGVsZW1lbnQgdG8gcHJlcGVuZCB0aGUgaGlkZGVuIGZpZWxkIHRvLlxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gcmVwbGFjZSBTaG91bGQgdGhlIHRhcmdldCBlbGVtZW50IGJlIHJlcGxhY2VkP1xuICAgICAqL1xuICAgIGV4cG9ydHMuYWRkSGlkZGVuQnlVcmwgPSBmdW5jdGlvbiAoJHRhcmdldCwgcmVwbGFjZSkge1xuICAgICAgICB2YXIgdXJsUGFyYW0gPSBfZ2V0VXJsUGFyYW1zKG51bGwpLFxuICAgICAgICAgICAgJGZpZWxkID0gbnVsbCxcbiAgICAgICAgICAgIGhpZGRlbnMgPSAnJyxcbiAgICAgICAgICAgIHVwZGF0ZSA9IFtdO1xuXG4gICAgICAgICQuZWFjaCh1cmxQYXJhbSwgZnVuY3Rpb24gKGssIHYpIHtcbiAgICAgICAgICAgIGlmICh2KSB7XG4gICAgICAgICAgICAgICAgJGZpZWxkID0gJHRhcmdldC5maW5kKCdbbmFtZT1cIicgKyBrICsgJ1wiXScpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCRmaWVsZC5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgaGlkZGVucyArPSAnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiJyArIGsgKyAnXCIgdmFsdWU9XCInICsgdiArICdcIiAvPic7XG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgdXBkYXRlLnB1c2goaywgdik7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICBpZiAocmVwbGFjZSkge1xuICAgICAgICAgICAgZXhwb3J0cy5wcmVmaWxsRm9ybSgkdGFyZ2V0LCB1cGRhdGUpO1xuICAgICAgICB9XG5cbiAgICAgICAgJHRhcmdldC5wcmVwZW5kKGhpZGRlbnMpO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXNldHMgdGhlIHRoZSBwcm92aWRlZCB0YXJnZXQgZm9ybS5cbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIHdpbGwgY2xlYXIgYWxsIHRleHRmaWVsZHMuIEFsbCByYWRpbyBidXR0b25zXG4gICAgICogYW5kIGNoZWNrYm94ZXMgd2lsbCBiZSB1bmNoZWNrZWQsIG9ubHkgdGhlIGZpcnN0IGNoZWNrYm94IGFuZFxuICAgICAqIHJhZGlvIGJ1dHRvbiB3aWxsIGdldCBjaGVja2VkLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtvYmplY3R9ICR0YXJnZXQgRm9ybSB0byByZXNldC5cbiAgICAgKi9cbiAgICBleHBvcnRzLnJlc2V0ID0gZnVuY3Rpb24gKCR0YXJnZXQpIHtcbiAgICAgICAgJHRhcmdldFxuICAgICAgICAgICAgLmZpbmQoJ3NlbGVjdCwgaW5wdXQsIHRleHRhcmVhJylcbiAgICAgICAgICAgIC5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICB0eXBlID0gZXhwb3J0cy5nZXRGaWVsZFR5cGUoJHNlbGYpO1xuXG4gICAgICAgICAgICAgICAgc3dpdGNoICh0eXBlKSB7XG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJ3JhZGlvJzpcbiAgICAgICAgICAgICAgICAgICAgICAgICR0YXJnZXRcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnaW5wdXRbbmFtZT1cIicgKyAkc2VsZi5hdHRyKCduYW1lJykgKyAnXCJdOmNoZWNrZWQnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5wcm9wKCdjaGVja2VkJywgZmFsc2UpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmZpcnN0KClcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAucHJvcCgnY2hlY2tlZCcsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJ2NoZWNrYm94JzpcbiAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAnc2VsZWN0JzpcbiAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmNoaWxkcmVuKClcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmlyc3QoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5wcm9wKCdzZWxlY3RlZCcsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJ3RleHRhcmVhJzpcbiAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmLnZhbCgnJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAndGV4dCc6XG4gICAgICAgICAgICAgICAgICAgICAgICAkc2VsZi52YWwoJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgICAgIGRlZmF1bHQ6XG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICB9O1xuXG59KShqc2UubGlicy5mb3JtKTtcbiJdfQ==

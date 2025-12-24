'use strict';

/* --------------------------------------------------------------
 fallback.js 2016-09-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.fallback = jse.libs.fallback || {};

/**
 * ## Fallback Library
 *
 * This library contains a set of deprecated functions that are still present for fallback support. Do not
 * use these methods in new modules.
 *
 * @module JSE/Libs/fallback
 * @exports jse.libs.fallback
 */
(function (exports) {

    'use strict';

    /**
     * Add ":attr" pseudo selector.
     *
     * This pseudo selector is normally enabled by including the JSEngine "jquery_extensions" library. Honeygrid
     * through needs this pseudo selector in this library which might be loaded prior to jquery_extensions and
     * this is why we define it once again in this file.
     */

    if ($.expr.pseudos.attr === undefined) {
        $.expr.pseudos.attr = $.expr.createPseudo(function (selector) {
            var regexp = new RegExp(selector);
            return function (elem) {
                for (var i = 0; i < elem.attributes.length; i++) {
                    var attr = elem.attributes[i];
                    if (regexp.test(attr.name)) {
                        return true;
                    }
                }
                return false;
            };
        });
    }

    /**
     * Add a fallback usage warning in the console.
     *
     * As the JS engine evolves many old features will need to be changed in order to let a finer and clearer
     * API for the JS Engine core mechanisms. Use this method to create a fallback usage warning for the functions
     * placed within this library.
     *
     * @param {String} functionName The deprecated function name.
     *
     * @private
     */
    function _warn(functionName) {
        jse.core.debug.warn('jse.libs.fallback.' + functionName + ' was called! ' + 'Avoid the use of fallback methods in new modules.');
    }

    /**
     * Get the module related data of the provided element.
     *
     * @param {jQuery} $element
     * @param {String} moduleName
     *
     * @return {Object}
     */
    exports._data = function ($element, moduleName) {
        _warn('_data');

        var initialData = $element.data(),
            filteredData = {};

        // Searches for module relevant data inside the main-data-object.
        // Data for other widgets will not get passed to this widget
        $.each(initialData, function (key, value) {
            if (key.indexOf(moduleName) === 0 || key.indexOf(moduleName.toLowerCase()) === 0) {
                var newKey = key.substr(moduleName.length);
                newKey = newKey.substr(0, 1).toLowerCase() + newKey.substr(1);
                filteredData[newKey] = value;
            }
        });

        return filteredData;
    };

    /**
     * Setup Widget Attribute
     *
     * @param {Object} $element Change the widget attribute of an element.
     */
    exports.setupWidgetAttr = function ($element) {
        _warn('setupWidgetAttr');

        $element.filter(':attr(^data-gx-_), :attr(^data-gambio-_), :attr(^data-jse-_)').add($element.find(':attr(^data-gx-_), :attr(^data-gambio-_), :attr(^data-jse-_)')).each(function () {
            var $self = $(this),
                attributes = $self[0].attributes,
                matchedAttribute = void 0,
                namespaceName = void 0;

            $.each(attributes, function (index, attribute) {
                if (attribute === undefined) {
                    return true; // wrong attribute, continue loop
                }

                matchedAttribute = attribute.name.match(/data-(gambio|gx|jse)-_.*/g);

                if (matchedAttribute !== null && matchedAttribute.length > 0) {
                    namespaceName = matchedAttribute[0].match(/(gambio|gx|jse)/g)[0];

                    $self.attr(attribute.name.replace('data-' + namespaceName + '-_', 'data-' + namespaceName + '-'), attribute.value);
                }
            });
        });
    };

    /**
     * Get URL parameters.
     *
     * @param {String} url
     * @param {Boolean} deep
     *
     * @return {Object}
     */
    exports.getUrlParams = function (url, deep) {
        _warn('getUrlParams');

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
    };

    /**
     * Fallback getData method.
     *
     * This method was included in v1.0 of JS Engine and is replaced by the
     * "jse.libs.form.getData" method.
     *
     * @param {Object} $form Selector of the form to be parsed.
     * @param {String} ignore (optional) jQuery selector string of form elements to be ignored.
     *
     * @return {Object} Returns the data of the form as an object.
     */
    exports.getData = function ($form, ignore) {
        _warn('getData');

        var $elements = $form.find('input, textarea, select'),
            result = {};

        if (ignore) {
            $elements = $elements.filter(':not(' + ignore + ')');
        }

        $elements.each(function () {
            var $self = $(this),
                type = $self.prop('tagName').toLowerCase(),
                name = $self.attr('name'),
                $selected = null;

            type = type !== 'input' ? type : $self.attr('type').toLowerCase();

            switch (type) {
                case 'radio':
                    $form.find('input[name="' + name + '"]:checked').val();
                    break;
                case 'checkbox':
                    if (name.search('\\[') !== -1) {
                        if ($self.prop('checked')) {
                            name = name.substring(0, name.search('\\['));
                            if (result[name] === undefined) {
                                result[name] = [];
                            }
                            result[name].push($(this).val());
                        }
                    } else {
                        result[name] = $self.prop('checked');
                    }
                    break;
                case 'select':
                    $selected = $self.find(':selected');
                    if ($selected.length > 1) {
                        result[name] = [];
                        $selected.each(function () {
                            result[name].push($(this).val());
                        });
                    } else {
                        result[name] = $selected.val();
                    }
                    break;
                case 'button':
                    break;
                default:
                    if (name) {
                        result[name] = $self.val();
                    }
                    break;
            }
        });
        return result;
    };
})(jse.libs.fallback);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImZhbGxiYWNrLmpzIl0sIm5hbWVzIjpbImpzZSIsImxpYnMiLCJmYWxsYmFjayIsImV4cG9ydHMiLCIkIiwiZXhwciIsInBzZXVkb3MiLCJhdHRyIiwidW5kZWZpbmVkIiwiY3JlYXRlUHNldWRvIiwic2VsZWN0b3IiLCJyZWdleHAiLCJSZWdFeHAiLCJlbGVtIiwiaSIsImF0dHJpYnV0ZXMiLCJsZW5ndGgiLCJ0ZXN0IiwibmFtZSIsIl93YXJuIiwiZnVuY3Rpb25OYW1lIiwiY29yZSIsImRlYnVnIiwid2FybiIsIl9kYXRhIiwiJGVsZW1lbnQiLCJtb2R1bGVOYW1lIiwiaW5pdGlhbERhdGEiLCJkYXRhIiwiZmlsdGVyZWREYXRhIiwiZWFjaCIsImtleSIsInZhbHVlIiwiaW5kZXhPZiIsInRvTG93ZXJDYXNlIiwibmV3S2V5Iiwic3Vic3RyIiwic2V0dXBXaWRnZXRBdHRyIiwiZmlsdGVyIiwiYWRkIiwiZmluZCIsIiRzZWxmIiwibWF0Y2hlZEF0dHJpYnV0ZSIsIm5hbWVzcGFjZU5hbWUiLCJpbmRleCIsImF0dHJpYnV0ZSIsIm1hdGNoIiwicmVwbGFjZSIsImdldFVybFBhcmFtcyIsInVybCIsImRlZXAiLCJkZWNvZGVVUklDb21wb25lbnQiLCJsb2NhdGlvbiIsImhyZWYiLCJzcGxpdFVybCIsInNwbGl0Iiwic3BsaXRQYXJhbSIsInJlZ2V4IiwicmVzdWx0IiwidiIsImtleVZhbHVlIiwicmVnZXhSZXN1bHQiLCJleGVjIiwiYmFzZSIsImJhc2VuYW1lIiwic3Vic3RyaW5nIiwic2VhcmNoIiwia2V5cyIsImxhc3RLZXkiLCJwdXNoIiwibmV4dCIsImdldERhdGEiLCIkZm9ybSIsImlnbm9yZSIsIiRlbGVtZW50cyIsInR5cGUiLCJwcm9wIiwiJHNlbGVjdGVkIiwidmFsIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsUUFBVCxHQUFvQkYsSUFBSUMsSUFBSixDQUFTQyxRQUFULElBQXFCLEVBQXpD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QUFFLElBQUYsRUFBUTBFLEdBQVIsRUFBbEI7QUFDSDtBQUNKLHFCQVJELE1BUU87QUFDSHBCLCtCQUFPeEMsSUFBUCxJQUFldUIsTUFBTW1DLElBQU4sQ0FBVyxTQUFYLENBQWY7QUFDSDtBQUNEO0FBQ0oscUJBQUssUUFBTDtBQUNJQyxnQ0FBWXBDLE1BQU1ELElBQU4sQ0FBVyxXQUFYLENBQVo7QUFDQSx3QkFBSXFDLFVBQVU3RCxNQUFWLEdBQW1CLENBQXZCLEVBQTBCO0FBQ3RCMEMsK0JBQU94QyxJQUFQLElBQWUsRUFBZjtBQUNBMkQsa0NBQVUvQyxJQUFWLENBQWUsWUFBWTtBQUN2QjRCLG1DQUFPeEMsSUFBUCxFQUFhbUQsSUFBYixDQUFrQmpFLEVBQUUsSUFBRixFQUFRMEUsR0FBUixFQUFsQjtBQUNILHlCQUZEO0FBR0gscUJBTEQsTUFLTztBQUNIcEIsK0JBQU94QyxJQUFQLElBQWUyRCxVQUFVQyxHQUFWLEVBQWY7QUFDSDtBQUNEO0FBQ0oscUJBQUssUUFBTDtBQUNJO0FBQ0o7QUFDSSx3QkFBSTVELElBQUosRUFBVTtBQUNOd0MsK0JBQU94QyxJQUFQLElBQWV1QixNQUFNcUMsR0FBTixFQUFmO0FBQ0g7QUFDRDtBQXBDUjtBQXNDSCxTQTlDRDtBQStDQSxlQUFPcEIsTUFBUDtBQUNILEtBMUREO0FBNERILENBOU9ELEVBOE9HMUQsSUFBSUMsSUFBSixDQUFTQyxRQTlPWiIsImZpbGUiOiJmYWxsYmFjay5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZmFsbGJhY2suanMgMjAxNi0wOS0wOFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLmZhbGxiYWNrID0ganNlLmxpYnMuZmFsbGJhY2sgfHwge307XG5cbi8qKlxuICogIyMgRmFsbGJhY2sgTGlicmFyeVxuICpcbiAqIFRoaXMgbGlicmFyeSBjb250YWlucyBhIHNldCBvZiBkZXByZWNhdGVkIGZ1bmN0aW9ucyB0aGF0IGFyZSBzdGlsbCBwcmVzZW50IGZvciBmYWxsYmFjayBzdXBwb3J0LiBEbyBub3RcbiAqIHVzZSB0aGVzZSBtZXRob2RzIGluIG5ldyBtb2R1bGVzLlxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvZmFsbGJhY2tcbiAqIEBleHBvcnRzIGpzZS5saWJzLmZhbGxiYWNrXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgLyoqXG4gICAgICogQWRkIFwiOmF0dHJcIiBwc2V1ZG8gc2VsZWN0b3IuXG4gICAgICpcbiAgICAgKiBUaGlzIHBzZXVkbyBzZWxlY3RvciBpcyBub3JtYWxseSBlbmFibGVkIGJ5IGluY2x1ZGluZyB0aGUgSlNFbmdpbmUgXCJqcXVlcnlfZXh0ZW5zaW9uc1wiIGxpYnJhcnkuIEhvbmV5Z3JpZFxuICAgICAqIHRocm91Z2ggbmVlZHMgdGhpcyBwc2V1ZG8gc2VsZWN0b3IgaW4gdGhpcyBsaWJyYXJ5IHdoaWNoIG1pZ2h0IGJlIGxvYWRlZCBwcmlvciB0byBqcXVlcnlfZXh0ZW5zaW9ucyBhbmRcbiAgICAgKiB0aGlzIGlzIHdoeSB3ZSBkZWZpbmUgaXQgb25jZSBhZ2FpbiBpbiB0aGlzIGZpbGUuXG4gICAgICovXG4gICAgaWYgKCQuZXhwci5wc2V1ZG9zLmF0dHIgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAkLmV4cHIucHNldWRvcy5hdHRyID0gJC5leHByLmNyZWF0ZVBzZXVkbyhmdW5jdGlvbiAoc2VsZWN0b3IpIHtcbiAgICAgICAgICAgIGxldCByZWdleHAgPSBuZXcgUmVnRXhwKHNlbGVjdG9yKTtcbiAgICAgICAgICAgIHJldHVybiBmdW5jdGlvbiAoZWxlbSkge1xuICAgICAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgZWxlbS5hdHRyaWJ1dGVzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBhdHRyID0gZWxlbS5hdHRyaWJ1dGVzW2ldO1xuICAgICAgICAgICAgICAgICAgICBpZiAocmVnZXhwLnRlc3QoYXR0ci5uYW1lKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgLyoqXG4gICAgICogQWRkIGEgZmFsbGJhY2sgdXNhZ2Ugd2FybmluZyBpbiB0aGUgY29uc29sZS5cbiAgICAgKlxuICAgICAqIEFzIHRoZSBKUyBlbmdpbmUgZXZvbHZlcyBtYW55IG9sZCBmZWF0dXJlcyB3aWxsIG5lZWQgdG8gYmUgY2hhbmdlZCBpbiBvcmRlciB0byBsZXQgYSBmaW5lciBhbmQgY2xlYXJlclxuICAgICAqIEFQSSBmb3IgdGhlIEpTIEVuZ2luZSBjb3JlIG1lY2hhbmlzbXMuIFVzZSB0aGlzIG1ldGhvZCB0byBjcmVhdGUgYSBmYWxsYmFjayB1c2FnZSB3YXJuaW5nIGZvciB0aGUgZnVuY3Rpb25zXG4gICAgICogcGxhY2VkIHdpdGhpbiB0aGlzIGxpYnJhcnkuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gZnVuY3Rpb25OYW1lIFRoZSBkZXByZWNhdGVkIGZ1bmN0aW9uIG5hbWUuXG4gICAgICpcbiAgICAgKiBAcHJpdmF0ZVxuICAgICAqL1xuICAgIGZ1bmN0aW9uIF93YXJuKGZ1bmN0aW9uTmFtZSkge1xuICAgICAgICBqc2UuY29yZS5kZWJ1Zy53YXJuKGBqc2UubGlicy5mYWxsYmFjay4ke2Z1bmN0aW9uTmFtZX0gd2FzIGNhbGxlZCEgYFxuICAgICAgICAgICAgKyBgQXZvaWQgdGhlIHVzZSBvZiBmYWxsYmFjayBtZXRob2RzIGluIG5ldyBtb2R1bGVzLmApO1xuICAgIH1cblxuICAgIC8qKlxuICAgICAqIEdldCB0aGUgbW9kdWxlIHJlbGF0ZWQgZGF0YSBvZiB0aGUgcHJvdmlkZWQgZWxlbWVudC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB7alF1ZXJ5fSAkZWxlbWVudFxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSBtb2R1bGVOYW1lXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtPYmplY3R9XG4gICAgICovXG4gICAgZXhwb3J0cy5fZGF0YSA9IGZ1bmN0aW9uICgkZWxlbWVudCwgbW9kdWxlTmFtZSkge1xuICAgICAgICBfd2FybignX2RhdGEnKTtcblxuICAgICAgICBsZXQgaW5pdGlhbERhdGEgPSAkZWxlbWVudC5kYXRhKCksXG4gICAgICAgICAgICBmaWx0ZXJlZERhdGEgPSB7fTtcblxuICAgICAgICAvLyBTZWFyY2hlcyBmb3IgbW9kdWxlIHJlbGV2YW50IGRhdGEgaW5zaWRlIHRoZSBtYWluLWRhdGEtb2JqZWN0LlxuICAgICAgICAvLyBEYXRhIGZvciBvdGhlciB3aWRnZXRzIHdpbGwgbm90IGdldCBwYXNzZWQgdG8gdGhpcyB3aWRnZXRcbiAgICAgICAgJC5lYWNoKGluaXRpYWxEYXRhLCAoa2V5LCB2YWx1ZSkgPT4ge1xuICAgICAgICAgICAgaWYgKGtleS5pbmRleE9mKG1vZHVsZU5hbWUpID09PSAwIHx8IGtleS5pbmRleE9mKG1vZHVsZU5hbWUudG9Mb3dlckNhc2UoKSkgPT09IDApIHtcbiAgICAgICAgICAgICAgICBsZXQgbmV3S2V5ID0ga2V5LnN1YnN0cihtb2R1bGVOYW1lLmxlbmd0aCk7XG4gICAgICAgICAgICAgICAgbmV3S2V5ID0gbmV3S2V5LnN1YnN0cigwLCAxKS50b0xvd2VyQ2FzZSgpICsgbmV3S2V5LnN1YnN0cigxKTtcbiAgICAgICAgICAgICAgICBmaWx0ZXJlZERhdGFbbmV3S2V5XSA9IHZhbHVlO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gZmlsdGVyZWREYXRhO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBTZXR1cCBXaWRnZXQgQXR0cmlidXRlXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge09iamVjdH0gJGVsZW1lbnQgQ2hhbmdlIHRoZSB3aWRnZXQgYXR0cmlidXRlIG9mIGFuIGVsZW1lbnQuXG4gICAgICovXG4gICAgZXhwb3J0cy5zZXR1cFdpZGdldEF0dHIgPSBmdW5jdGlvbiAoJGVsZW1lbnQpIHtcbiAgICAgICAgX3dhcm4oJ3NldHVwV2lkZ2V0QXR0cicpO1xuXG4gICAgICAgICRlbGVtZW50XG4gICAgICAgICAgICAuZmlsdGVyKCc6YXR0ciheZGF0YS1neC1fKSwgOmF0dHIoXmRhdGEtZ2FtYmlvLV8pLCA6YXR0ciheZGF0YS1qc2UtXyknKVxuICAgICAgICAgICAgLmFkZCgkZWxlbWVudC5maW5kKCc6YXR0ciheZGF0YS1neC1fKSwgOmF0dHIoXmRhdGEtZ2FtYmlvLV8pLCA6YXR0ciheZGF0YS1qc2UtXyknKSlcbiAgICAgICAgICAgIC5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBsZXQgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICBhdHRyaWJ1dGVzID0gJHNlbGZbMF0uYXR0cmlidXRlcyxcbiAgICAgICAgICAgICAgICAgICAgbWF0Y2hlZEF0dHJpYnV0ZSxcbiAgICAgICAgICAgICAgICAgICAgbmFtZXNwYWNlTmFtZTtcblxuICAgICAgICAgICAgICAgICQuZWFjaChhdHRyaWJ1dGVzLCBmdW5jdGlvbiAoaW5kZXgsIGF0dHJpYnV0ZSkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoYXR0cmlidXRlID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB0cnVlOyAvLyB3cm9uZyBhdHRyaWJ1dGUsIGNvbnRpbnVlIGxvb3BcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIG1hdGNoZWRBdHRyaWJ1dGUgPSBhdHRyaWJ1dGUubmFtZS5tYXRjaCgvZGF0YS0oZ2FtYmlvfGd4fGpzZSktXy4qL2cpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChtYXRjaGVkQXR0cmlidXRlICE9PSBudWxsICYmIG1hdGNoZWRBdHRyaWJ1dGUubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgbmFtZXNwYWNlTmFtZSA9IG1hdGNoZWRBdHRyaWJ1dGVbMF0ubWF0Y2goLyhnYW1iaW98Z3h8anNlKS9nKVswXTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGZcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXR0cihhdHRyaWJ1dGUubmFtZS5yZXBsYWNlKCdkYXRhLScgKyBuYW1lc3BhY2VOYW1lICsgJy1fJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2RhdGEtJyArIG5hbWVzcGFjZU5hbWUgKyAnLScpLCBhdHRyaWJ1dGUudmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogR2V0IFVSTCBwYXJhbWV0ZXJzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtTdHJpbmd9IHVybFxuICAgICAqIEBwYXJhbSB7Qm9vbGVhbn0gZGVlcFxuICAgICAqXG4gICAgICogQHJldHVybiB7T2JqZWN0fVxuICAgICAqL1xuICAgIGV4cG9ydHMuZ2V0VXJsUGFyYW1zID0gZnVuY3Rpb24gKHVybCwgZGVlcCkge1xuICAgICAgICBfd2FybignZ2V0VXJsUGFyYW1zJyk7XG5cbiAgICAgICAgdXJsID0gZGVjb2RlVVJJQ29tcG9uZW50KHVybCB8fCBsb2NhdGlvbi5ocmVmKTtcblxuICAgICAgICBsZXQgc3BsaXRVcmwgPSB1cmwuc3BsaXQoJz8nKSxcbiAgICAgICAgICAgIHNwbGl0UGFyYW0gPSAoc3BsaXRVcmwubGVuZ3RoID4gMSkgPyBzcGxpdFVybFsxXS5zcGxpdCgnJicpIDogW10sXG4gICAgICAgICAgICByZWdleCA9IG5ldyBSZWdFeHAoL1xcWyguKj8pXFxdL2cpLFxuICAgICAgICAgICAgcmVzdWx0ID0ge307XG5cbiAgICAgICAgJC5lYWNoKHNwbGl0UGFyYW0sIGZ1bmN0aW9uIChpLCB2KSB7XG4gICAgICAgICAgICBsZXQga2V5VmFsdWUgPSB2LnNwbGl0KCc9JyksXG4gICAgICAgICAgICAgICAgcmVnZXhSZXN1bHQgPSByZWdleC5leGVjKGtleVZhbHVlWzBdKSxcbiAgICAgICAgICAgICAgICBiYXNlID0gbnVsbCxcbiAgICAgICAgICAgICAgICBiYXNlbmFtZSA9IGtleVZhbHVlWzBdLnN1YnN0cmluZygwLCBrZXlWYWx1ZVswXS5zZWFyY2goJ1xcXFxbJykpLFxuICAgICAgICAgICAgICAgIGtleXMgPSBbXSxcbiAgICAgICAgICAgICAgICBsYXN0S2V5ID0gbnVsbDtcblxuICAgICAgICAgICAgaWYgKCFkZWVwIHx8IHJlZ2V4UmVzdWx0ID09PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgcmVzdWx0W2tleVZhbHVlWzBdXSA9IGtleVZhbHVlWzFdLnNwbGl0KCcjJylbMF07XG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgcmVzdWx0W2Jhc2VuYW1lXSA9IHJlc3VsdFtiYXNlbmFtZV0gfHwgW107XG4gICAgICAgICAgICAgICAgYmFzZSA9IHJlc3VsdFtiYXNlbmFtZV07XG5cbiAgICAgICAgICAgICAgICBkbyB7XG4gICAgICAgICAgICAgICAgICAgIGtleXMucHVzaChyZWdleFJlc3VsdFsxXSk7XG4gICAgICAgICAgICAgICAgICAgIHJlZ2V4UmVzdWx0ID0gcmVnZXguZXhlYyhrZXlWYWx1ZVswXSk7XG4gICAgICAgICAgICAgICAgfSB3aGlsZSAocmVnZXhSZXN1bHQgIT09IG51bGwpO1xuXG4gICAgICAgICAgICAgICAgJC5lYWNoKGtleXMsIGZ1bmN0aW9uIChpLCB2KSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBuZXh0ID0ga2V5c1tpICsgMV07XG4gICAgICAgICAgICAgICAgICAgIHYgPSB2IHx8ICcwJztcblxuICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIChuZXh0KSA9PT0gJ3N0cmluZycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJhc2Vbdl0gPSBiYXNlW3ZdIHx8IFtdO1xuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZSA9IGJhc2Vbdl07XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBiYXNlW3ZdID0gYmFzZVt2XSB8fCB1bmRlZmluZWQ7XG4gICAgICAgICAgICAgICAgICAgICAgICBsYXN0S2V5ID0gdjtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgaWYgKGxhc3RLZXkgIT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICAgICAgYmFzZVtsYXN0S2V5XSA9IGtleVZhbHVlWzFdO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGJhc2UgPSBrZXlWYWx1ZVsxXTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgcmV0dXJuIHJlc3VsdDtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogRmFsbGJhY2sgZ2V0RGF0YSBtZXRob2QuXG4gICAgICpcbiAgICAgKiBUaGlzIG1ldGhvZCB3YXMgaW5jbHVkZWQgaW4gdjEuMCBvZiBKUyBFbmdpbmUgYW5kIGlzIHJlcGxhY2VkIGJ5IHRoZVxuICAgICAqIFwianNlLmxpYnMuZm9ybS5nZXREYXRhXCIgbWV0aG9kLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtPYmplY3R9ICRmb3JtIFNlbGVjdG9yIG9mIHRoZSBmb3JtIHRvIGJlIHBhcnNlZC5cbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gaWdub3JlIChvcHRpb25hbCkgalF1ZXJ5IHNlbGVjdG9yIHN0cmluZyBvZiBmb3JtIGVsZW1lbnRzIHRvIGJlIGlnbm9yZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtPYmplY3R9IFJldHVybnMgdGhlIGRhdGEgb2YgdGhlIGZvcm0gYXMgYW4gb2JqZWN0LlxuICAgICAqL1xuICAgIGV4cG9ydHMuZ2V0RGF0YSA9IGZ1bmN0aW9uICgkZm9ybSwgaWdub3JlKSB7XG4gICAgICAgIF93YXJuKCdnZXREYXRhJyk7XG5cbiAgICAgICAgbGV0ICRlbGVtZW50cyA9ICRmb3JtLmZpbmQoJ2lucHV0LCB0ZXh0YXJlYSwgc2VsZWN0JyksXG4gICAgICAgICAgICByZXN1bHQgPSB7fTtcblxuICAgICAgICBpZiAoaWdub3JlKSB7XG4gICAgICAgICAgICAkZWxlbWVudHMgPSAkZWxlbWVudHMuZmlsdGVyKCc6bm90KCcgKyBpZ25vcmUgKyAnKScpO1xuICAgICAgICB9XG5cbiAgICAgICAgJGVsZW1lbnRzLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgbGV0ICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICB0eXBlID0gJHNlbGYucHJvcCgndGFnTmFtZScpLnRvTG93ZXJDYXNlKCksXG4gICAgICAgICAgICAgICAgbmFtZSA9ICRzZWxmLmF0dHIoJ25hbWUnKSxcbiAgICAgICAgICAgICAgICAkc2VsZWN0ZWQgPSBudWxsO1xuXG4gICAgICAgICAgICB0eXBlID0gKHR5cGUgIT09ICdpbnB1dCcpID8gdHlwZSA6ICRzZWxmLmF0dHIoJ3R5cGUnKS50b0xvd2VyQ2FzZSgpO1xuXG4gICAgICAgICAgICBzd2l0Y2ggKHR5cGUpIHtcbiAgICAgICAgICAgICAgICBjYXNlICdyYWRpbyc6XG4gICAgICAgICAgICAgICAgICAgICRmb3JtXG4gICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnaW5wdXRbbmFtZT1cIicgKyBuYW1lICsgJ1wiXTpjaGVja2VkJylcbiAgICAgICAgICAgICAgICAgICAgICAgIC52YWwoKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnY2hlY2tib3gnOlxuICAgICAgICAgICAgICAgICAgICBpZiAobmFtZS5zZWFyY2goJ1xcXFxbJykgIT09IC0xKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJHNlbGYucHJvcCgnY2hlY2tlZCcpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmFtZSA9IG5hbWUuc3Vic3RyaW5nKDAsIG5hbWUuc2VhcmNoKCdcXFxcWycpKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAocmVzdWx0W25hbWVdID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0W25hbWVdID0gW107XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdFtuYW1lXS5wdXNoKCQodGhpcykudmFsKCkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0W25hbWVdID0gJHNlbGYucHJvcCgnY2hlY2tlZCcpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgJ3NlbGVjdCc6XG4gICAgICAgICAgICAgICAgICAgICRzZWxlY3RlZCA9ICRzZWxmLmZpbmQoJzpzZWxlY3RlZCcpO1xuICAgICAgICAgICAgICAgICAgICBpZiAoJHNlbGVjdGVkLmxlbmd0aCA+IDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdFtuYW1lXSA9IFtdO1xuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGVjdGVkLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdFtuYW1lXS5wdXNoKCQodGhpcykudmFsKCkpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXN1bHRbbmFtZV0gPSAkc2VsZWN0ZWQudmFsKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAnYnV0dG9uJzpcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgZGVmYXVsdDpcbiAgICAgICAgICAgICAgICAgICAgaWYgKG5hbWUpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdFtuYW1lXSA9ICRzZWxmLnZhbCgpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICAgICAgcmV0dXJuIHJlc3VsdDtcbiAgICB9O1xuXG59KShqc2UubGlicy5mYWxsYmFjayk7ICJdfQ==

'use strict';

/* --------------------------------------------------------------
 theme_helpers.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.theme.helpers = jse.libs.theme.helpers || {};

/**
 * Theme Helper Methods
 *
 * This library contains some methods that are required by the theme and need to be defined prior to its
 * initialization. Include this file right after the "initialize_theme.js" and not as a module dependency.
 *
 * Important: If possible, prefer to use the methods of the core JS Engine libraries and not from this library because
 * they can lead to unexpected results or might be hard to use.
 */
(function (exports) {

    'use strict';

    /**
     * Add ":attr" pseudo selector.
     *
     * This pseudo selector is normally enabled by including the JSEngine "jquery_extensions" library. Honeygrid
     * through needs this pseudo selector in this library which might be loeaded prior to jquery_extensions and
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
     * Convert all the JS Engine module attributes to the normal state.
     *
     * This method is triggered mostly before a module initialization. Some HTML markup does not have the correct module
     * attribute set because their initialization need to be done in a later time of the page lifecycle.
     *
     * This method will perform the following conversion:
     *
     * ```
     * <!-- Before "setupWidgetAttr" (with the underscore). -->
     * <div data-gambio-_widget="some_widget"></div>
     *
     * ```
     *
     * ```
     * <!-- After "setupWidgetAttr" (the underscore is removed). -->
     * <div data-gambio-widget="some_widget"></div>
     *
     * ```
     *
     * The problem with this method is that the namespaces are hard-coded , the complexity is high and any change in the
     * core JS Engine might break the functionality. Apart from that, the creation and initialization of modules at
     * runtime should be done explicitly by JavaScript modules and HTML markup must not contain such attributes.
     */
    exports.setupWidgetAttr = function ($element) {
        $element.filter(':attr(^data-gx-_), :attr(^data-gambio-_)').add($element.find(':attr(^data-gx-_), :attr(^data-gambio-_)')).each(function () {
            var $self = $(this),
                attributes = $self[0].attributes,
                matchedAttribute,
                namespaceName;

            $.each(attributes, function (index, attribute) {
                if (attribute === undefined) {
                    return true; // wrong attribute, continue loop
                }

                matchedAttribute = attribute.name.match(/data-(gambio|gx)-_.*/g);

                if (matchedAttribute !== null && matchedAttribute.length > 0) {
                    namespaceName = matchedAttribute[0].match(/(gambio|gx)/g)[0];

                    $self.attr(attribute.name.replace('data-' + namespaceName + '-_', 'data-' + namespaceName + '-'), attribute.value);
                }
            });
        });
    };

    /**
     * Fill a form with the provided data.
     *
     * This method will try to fill a form by parsing the provided data. The data have to contain a very specific
     * structure where each value has a "selector" property that points the element to be filled.
     *
     * This method couldn't unfortunately be removed and the use of it should be avoided because it requires that the
     * data generation code must know the selectors and HTML structure of the form, which is a bad practice.
     *
     * @param {object} data Contains the data to be used when filling the form.
     * @param {object} $target jQuery selector for the form or the container of the form to be filled.
     * @param {object} selectorMapping contains the selector mappings of JSON data to the original HTML elements.
     */
    exports.fill = function (data, $target, selectorMapping) {
        $.each(data, function (i, v) {
            if (selectorMapping[v.selector] === undefined) {
                jse.core.debug.warn('The selector mapping "' + v.selector + '" doesn\'t exist.');
                return true;
            }

            var $elements = $target.find(selectorMapping[v.selector]).add($target.filter(selectorMapping[v.selector]));

            $elements.each(function () {
                var $element = $(this);

                switch (v.type) {
                    case 'html':
                        $element.html(v.value);
                        break;
                    case 'attribute':
                        $element.attr(v.key, v.value);
                        break;
                    case 'replace':
                        if (v.value) {
                            $element.replaceWith(v.value);
                        } else {
                            $element.addClass('hidden').empty();
                        }
                        break;
                    default:
                        $element.text(v.value);
                        break;
                }
            });
        });
    };

    /**
     * Get URL parameters the current location or a specific URL.
     *
     * This method was implemented to work with the theme but couldn't unfortunately be replaced with the
     * "getUrlParameters" method inside the "url_arguments" library.
     *
     * If possible, prefer to use the "url_arguments" "getUrlParameters" method instead of this one.
     *
     * @param {string} url (optional) The URL to be parsed, if not provided the current location URL will be used.
     * @param {boolean} deep (optional) Whether to perform a "deep" URL parse.
     *
     * @return {object} Returns an object that contains the parameter values.
     */
    exports.getUrlParams = function (url, deep) {
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
})(jse.libs.theme.helpers);

jse.libs.template = jse.libs.template || {};
jse.libs.template.helpers = jse.libs.theme.helpers;
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInRoZW1lX2hlbHBlcnMuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInRoZW1lIiwiaGVscGVycyIsImV4cG9ydHMiLCIkIiwiZXhwciIsInBzZXVkb3MiLCJhdHRyIiwidW5kZWZpbmVkIiwiY3JlYXRlUHNldWRvIiwic2VsZWN0b3IiLCJyZWdleHAiLCJSZWdFeHAiLCJlbGVtIiwiaSIsImF0dHJpYnV0ZXMiLCJsZW5ndGgiLCJ0ZXN0IiwibmFtZSIsInNldHVwV2lkZ2V0QXR0ciIsIiRlbGVtZW50IiwiZmlsdGVyIiwiYWRkIiwiZmluZCIsImVhY2giLCIkc2VsZiIsIm1hdGNoZWRBdHRyaWJ1dGUiLCJuYW1lc3BhY2VOYW1lIiwiaW5kZXgiLCJhdHRyaWJ1dGUiLCJtYXRjaCIsInJlcGxhY2UiLCJ2YWx1ZSIsImZpbGwiLCJkYXRhIiwiJHRhcmdldCIsInNlbGVjdG9yTWFwcGluZyIsInYiLCJjb3JlIiwiZGVidWciLCJ3YXJuIiwiJGVsZW1lbnRzIiwidHlwZSIsImh0bWwiLCJrZXkiLCJyZXBsYWNlV2l0aCIsImFkZENsYXNzIiwiZW1wdHkiLCJ0ZXh0IiwiZ2V0VXJsUGFyYW1zIiwidXJsIiwiZGVlcCIsImRlY29kZVVSSUNvbXBvbmVudCIsImxvY2F0aW9uIiwiaHJlZiIsInNwbGl0VXJsIiwic3BsaXQiLCJzcGxpdFBhcmFtIiwicmVnZXgiLCJyZXN1bHQiLCJrZXlWYWx1ZSIsInJlZ2V4UmVzdWx0IiwiZXhlYyIsImJhc2UiLCJiYXNlbmFtZSIsInN1YnN0cmluZyIsInNlYXJjaCIsImtleXMiLCJsYXN0S2V5IiwicHVzaCIsIm5leHQiLCJ0ZW1wbGF0ZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxJQUFJQyxJQUFKLENBQVNDLEtBQVQsQ0FBZUMsT0FBZixHQUF5QkgsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLE9BQWYsSUFBMEIsRUFBbkQ7O0FBRUE7Ozs7Ozs7OztBQVNBLENBQUMsVUFBVUMsT0FBVixFQUFt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QjtBQUNsQkwseUJBQUtLLE9BQUwsSUFBZ0JSLFNBQVMsQ0FBVCxDQUFoQjtBQUNILGlCQUZELE1BRU87QUFDSEcsMkJBQU9ILFNBQVMsQ0FBVCxDQUFQO0FBQ0g7QUFDSjtBQUNKLFNBdkNEOztBQXlDQSxlQUFPRCxNQUFQO0FBQ0gsS0FsREQ7QUFvREgsQ0FsTUQsRUFrTUc1RCxJQUFJQyxJQUFKLENBQVNDLEtBQVQsQ0FBZUMsT0FsTWxCOztBQW9NQUgsSUFBSUMsSUFBSixDQUFTdUUsUUFBVCxHQUFvQnhFLElBQUlDLElBQUosQ0FBU3VFLFFBQVQsSUFBcUIsRUFBekM7QUFDQXhFLElBQUlDLElBQUosQ0FBU3VFLFFBQVQsQ0FBa0JyRSxPQUFsQixHQUE0QkgsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLE9BQTNDIiwiZmlsZSI6InRoZW1lX2hlbHBlcnMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHRoZW1lX2hlbHBlcnMuanMgMjAxOC0xMS0xM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTggR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLnRoZW1lLmhlbHBlcnMgPSBqc2UubGlicy50aGVtZS5oZWxwZXJzIHx8IHt9O1xuXG4vKipcbiAqIFRoZW1lIEhlbHBlciBNZXRob2RzXG4gKlxuICogVGhpcyBsaWJyYXJ5IGNvbnRhaW5zIHNvbWUgbWV0aG9kcyB0aGF0IGFyZSByZXF1aXJlZCBieSB0aGUgdGhlbWUgYW5kIG5lZWQgdG8gYmUgZGVmaW5lZCBwcmlvciB0byBpdHNcbiAqIGluaXRpYWxpemF0aW9uLiBJbmNsdWRlIHRoaXMgZmlsZSByaWdodCBhZnRlciB0aGUgXCJpbml0aWFsaXplX3RoZW1lLmpzXCIgYW5kIG5vdCBhcyBhIG1vZHVsZSBkZXBlbmRlbmN5LlxuICpcbiAqIEltcG9ydGFudDogSWYgcG9zc2libGUsIHByZWZlciB0byB1c2UgdGhlIG1ldGhvZHMgb2YgdGhlIGNvcmUgSlMgRW5naW5lIGxpYnJhcmllcyBhbmQgbm90IGZyb20gdGhpcyBsaWJyYXJ5IGJlY2F1c2VcbiAqIHRoZXkgY2FuIGxlYWQgdG8gdW5leHBlY3RlZCByZXN1bHRzIG9yIG1pZ2h0IGJlIGhhcmQgdG8gdXNlLlxuICovXG4oZnVuY3Rpb24gKGV4cG9ydHMpIHtcblxuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIC8qKlxuICAgICAqIEFkZCBcIjphdHRyXCIgcHNldWRvIHNlbGVjdG9yLlxuICAgICAqXG4gICAgICogVGhpcyBwc2V1ZG8gc2VsZWN0b3IgaXMgbm9ybWFsbHkgZW5hYmxlZCBieSBpbmNsdWRpbmcgdGhlIEpTRW5naW5lIFwianF1ZXJ5X2V4dGVuc2lvbnNcIiBsaWJyYXJ5LiBIb25leWdyaWRcbiAgICAgKiB0aHJvdWdoIG5lZWRzIHRoaXMgcHNldWRvIHNlbGVjdG9yIGluIHRoaXMgbGlicmFyeSB3aGljaCBtaWdodCBiZSBsb2VhZGVkIHByaW9yIHRvIGpxdWVyeV9leHRlbnNpb25zIGFuZFxuICAgICAqIHRoaXMgaXMgd2h5IHdlIGRlZmluZSBpdCBvbmNlIGFnYWluIGluIHRoaXMgZmlsZS5cbiAgICAgKi9cbiAgICBpZiAoJC5leHByLnBzZXVkb3MuYXR0ciA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICQuZXhwci5wc2V1ZG9zLmF0dHIgPSAkLmV4cHIuY3JlYXRlUHNldWRvKGZ1bmN0aW9uIChzZWxlY3Rvcikge1xuICAgICAgICAgICAgbGV0IHJlZ2V4cCA9IG5ldyBSZWdFeHAoc2VsZWN0b3IpO1xuICAgICAgICAgICAgcmV0dXJuIGZ1bmN0aW9uIChlbGVtKSB7XG4gICAgICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBlbGVtLmF0dHJpYnV0ZXMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGF0dHIgPSBlbGVtLmF0dHJpYnV0ZXNbaV07XG4gICAgICAgICAgICAgICAgICAgIGlmIChyZWdleHAudGVzdChhdHRyLm5hbWUpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9O1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvKipcbiAgICAgKiBDb252ZXJ0IGFsbCB0aGUgSlMgRW5naW5lIG1vZHVsZSBhdHRyaWJ1dGVzIHRvIHRoZSBub3JtYWwgc3RhdGUuXG4gICAgICpcbiAgICAgKiBUaGlzIG1ldGhvZCBpcyB0cmlnZ2VyZWQgbW9zdGx5IGJlZm9yZSBhIG1vZHVsZSBpbml0aWFsaXphdGlvbi4gU29tZSBIVE1MIG1hcmt1cCBkb2VzIG5vdCBoYXZlIHRoZSBjb3JyZWN0IG1vZHVsZVxuICAgICAqIGF0dHJpYnV0ZSBzZXQgYmVjYXVzZSB0aGVpciBpbml0aWFsaXphdGlvbiBuZWVkIHRvIGJlIGRvbmUgaW4gYSBsYXRlciB0aW1lIG9mIHRoZSBwYWdlIGxpZmVjeWNsZS5cbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIHdpbGwgcGVyZm9ybSB0aGUgZm9sbG93aW5nIGNvbnZlcnNpb246XG4gICAgICpcbiAgICAgKiBgYGBcbiAgICAgKiA8IS0tIEJlZm9yZSBcInNldHVwV2lkZ2V0QXR0clwiICh3aXRoIHRoZSB1bmRlcnNjb3JlKS4gLS0+XG4gICAgICogPGRpdiBkYXRhLWdhbWJpby1fd2lkZ2V0PVwic29tZV93aWRnZXRcIj48L2Rpdj5cbiAgICAgKlxuICAgICAqIGBgYFxuICAgICAqXG4gICAgICogYGBgXG4gICAgICogPCEtLSBBZnRlciBcInNldHVwV2lkZ2V0QXR0clwiICh0aGUgdW5kZXJzY29yZSBpcyByZW1vdmVkKS4gLS0+XG4gICAgICogPGRpdiBkYXRhLWdhbWJpby13aWRnZXQ9XCJzb21lX3dpZGdldFwiPjwvZGl2PlxuICAgICAqXG4gICAgICogYGBgXG4gICAgICpcbiAgICAgKiBUaGUgcHJvYmxlbSB3aXRoIHRoaXMgbWV0aG9kIGlzIHRoYXQgdGhlIG5hbWVzcGFjZXMgYXJlIGhhcmQtY29kZWQgLCB0aGUgY29tcGxleGl0eSBpcyBoaWdoIGFuZCBhbnkgY2hhbmdlIGluIHRoZVxuICAgICAqIGNvcmUgSlMgRW5naW5lIG1pZ2h0IGJyZWFrIHRoZSBmdW5jdGlvbmFsaXR5LiBBcGFydCBmcm9tIHRoYXQsIHRoZSBjcmVhdGlvbiBhbmQgaW5pdGlhbGl6YXRpb24gb2YgbW9kdWxlcyBhdFxuICAgICAqIHJ1bnRpbWUgc2hvdWxkIGJlIGRvbmUgZXhwbGljaXRseSBieSBKYXZhU2NyaXB0IG1vZHVsZXMgYW5kIEhUTUwgbWFya3VwIG11c3Qgbm90IGNvbnRhaW4gc3VjaCBhdHRyaWJ1dGVzLlxuICAgICAqL1xuICAgIGV4cG9ydHMuc2V0dXBXaWRnZXRBdHRyID0gZnVuY3Rpb24gKCRlbGVtZW50KSB7XG4gICAgICAgICRlbGVtZW50XG4gICAgICAgICAgICAuZmlsdGVyKCc6YXR0ciheZGF0YS1neC1fKSwgOmF0dHIoXmRhdGEtZ2FtYmlvLV8pJylcbiAgICAgICAgICAgIC5hZGQoJGVsZW1lbnQuZmluZCgnOmF0dHIoXmRhdGEtZ3gtXyksIDphdHRyKF5kYXRhLWdhbWJpby1fKScpKVxuICAgICAgICAgICAgLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZXMgPSAkc2VsZlswXS5hdHRyaWJ1dGVzLFxuICAgICAgICAgICAgICAgICAgICBtYXRjaGVkQXR0cmlidXRlLFxuICAgICAgICAgICAgICAgICAgICBuYW1lc3BhY2VOYW1lO1xuXG4gICAgICAgICAgICAgICAgJC5lYWNoKGF0dHJpYnV0ZXMsIGZ1bmN0aW9uIChpbmRleCwgYXR0cmlidXRlKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChhdHRyaWJ1dGUgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7IC8vIHdyb25nIGF0dHJpYnV0ZSwgY29udGludWUgbG9vcFxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgbWF0Y2hlZEF0dHJpYnV0ZSA9IGF0dHJpYnV0ZS5uYW1lLm1hdGNoKC9kYXRhLShnYW1iaW98Z3gpLV8uKi9nKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAobWF0Y2hlZEF0dHJpYnV0ZSAhPT0gbnVsbCAmJiBtYXRjaGVkQXR0cmlidXRlLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5hbWVzcGFjZU5hbWUgPSBtYXRjaGVkQXR0cmlidXRlWzBdLm1hdGNoKC8oZ2FtYmlvfGd4KS9nKVswXTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJHNlbGZcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXR0cihhdHRyaWJ1dGUubmFtZS5yZXBsYWNlKCdkYXRhLScgKyBuYW1lc3BhY2VOYW1lICsgJy1fJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2RhdGEtJyArIG5hbWVzcGFjZU5hbWUgKyAnLScpLCBhdHRyaWJ1dGUudmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogRmlsbCBhIGZvcm0gd2l0aCB0aGUgcHJvdmlkZWQgZGF0YS5cbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIHdpbGwgdHJ5IHRvIGZpbGwgYSBmb3JtIGJ5IHBhcnNpbmcgdGhlIHByb3ZpZGVkIGRhdGEuIFRoZSBkYXRhIGhhdmUgdG8gY29udGFpbiBhIHZlcnkgc3BlY2lmaWNcbiAgICAgKiBzdHJ1Y3R1cmUgd2hlcmUgZWFjaCB2YWx1ZSBoYXMgYSBcInNlbGVjdG9yXCIgcHJvcGVydHkgdGhhdCBwb2ludHMgdGhlIGVsZW1lbnQgdG8gYmUgZmlsbGVkLlxuICAgICAqXG4gICAgICogVGhpcyBtZXRob2QgY291bGRuJ3QgdW5mb3J0dW5hdGVseSBiZSByZW1vdmVkIGFuZCB0aGUgdXNlIG9mIGl0IHNob3VsZCBiZSBhdm9pZGVkIGJlY2F1c2UgaXQgcmVxdWlyZXMgdGhhdCB0aGVcbiAgICAgKiBkYXRhIGdlbmVyYXRpb24gY29kZSBtdXN0IGtub3cgdGhlIHNlbGVjdG9ycyBhbmQgSFRNTCBzdHJ1Y3R1cmUgb2YgdGhlIGZvcm0sIHdoaWNoIGlzIGEgYmFkIHByYWN0aWNlLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtvYmplY3R9IGRhdGEgQ29udGFpbnMgdGhlIGRhdGEgdG8gYmUgdXNlZCB3aGVuIGZpbGxpbmcgdGhlIGZvcm0uXG4gICAgICogQHBhcmFtIHtvYmplY3R9ICR0YXJnZXQgalF1ZXJ5IHNlbGVjdG9yIGZvciB0aGUgZm9ybSBvciB0aGUgY29udGFpbmVyIG9mIHRoZSBmb3JtIHRvIGJlIGZpbGxlZC5cbiAgICAgKiBAcGFyYW0ge29iamVjdH0gc2VsZWN0b3JNYXBwaW5nIGNvbnRhaW5zIHRoZSBzZWxlY3RvciBtYXBwaW5ncyBvZiBKU09OIGRhdGEgdG8gdGhlIG9yaWdpbmFsIEhUTUwgZWxlbWVudHMuXG4gICAgICovXG4gICAgZXhwb3J0cy5maWxsID0gZnVuY3Rpb24gKGRhdGEsICR0YXJnZXQsIHNlbGVjdG9yTWFwcGluZykge1xuICAgICAgICAkLmVhY2goZGF0YSwgZnVuY3Rpb24gKGksIHYpIHtcbiAgICAgICAgICAgIGlmIChzZWxlY3Rvck1hcHBpbmdbdi5zZWxlY3Rvcl0gPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIGpzZS5jb3JlLmRlYnVnLndhcm4oJ1RoZSBzZWxlY3RvciBtYXBwaW5nIFwiJyArIHYuc2VsZWN0b3IgKyAnXCIgZG9lc25cXCd0IGV4aXN0LicpO1xuICAgICAgICAgICAgICAgIHJldHVybiB0cnVlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgJGVsZW1lbnRzID0gJHRhcmdldFxuICAgICAgICAgICAgICAgIC5maW5kKHNlbGVjdG9yTWFwcGluZ1t2LnNlbGVjdG9yXSlcbiAgICAgICAgICAgICAgICAuYWRkKCR0YXJnZXQuZmlsdGVyKHNlbGVjdG9yTWFwcGluZ1t2LnNlbGVjdG9yXSkpO1xuXG4gICAgICAgICAgICAkZWxlbWVudHMuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgdmFyICRlbGVtZW50ID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgIHN3aXRjaCAodi50eXBlKSB7XG4gICAgICAgICAgICAgICAgICAgIGNhc2UgJ2h0bWwnOlxuICAgICAgICAgICAgICAgICAgICAgICAgJGVsZW1lbnQuaHRtbCh2LnZhbHVlKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICBjYXNlICdhdHRyaWJ1dGUnOlxuICAgICAgICAgICAgICAgICAgICAgICAgJGVsZW1lbnQuYXR0cih2LmtleSwgdi52YWx1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICAgICAgY2FzZSAncmVwbGFjZSc6XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodi52YWx1ZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRlbGVtZW50LnJlcGxhY2VXaXRoKHYudmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkZWxlbWVudFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2hpZGRlbicpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5lbXB0eSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgICAgIGRlZmF1bHQ6XG4gICAgICAgICAgICAgICAgICAgICAgICAkZWxlbWVudC50ZXh0KHYudmFsdWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBHZXQgVVJMIHBhcmFtZXRlcnMgdGhlIGN1cnJlbnQgbG9jYXRpb24gb3IgYSBzcGVjaWZpYyBVUkwuXG4gICAgICpcbiAgICAgKiBUaGlzIG1ldGhvZCB3YXMgaW1wbGVtZW50ZWQgdG8gd29yayB3aXRoIHRoZSB0aGVtZSBidXQgY291bGRuJ3QgdW5mb3J0dW5hdGVseSBiZSByZXBsYWNlZCB3aXRoIHRoZVxuICAgICAqIFwiZ2V0VXJsUGFyYW1ldGVyc1wiIG1ldGhvZCBpbnNpZGUgdGhlIFwidXJsX2FyZ3VtZW50c1wiIGxpYnJhcnkuXG4gICAgICpcbiAgICAgKiBJZiBwb3NzaWJsZSwgcHJlZmVyIHRvIHVzZSB0aGUgXCJ1cmxfYXJndW1lbnRzXCIgXCJnZXRVcmxQYXJhbWV0ZXJzXCIgbWV0aG9kIGluc3RlYWQgb2YgdGhpcyBvbmUuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gdXJsIChvcHRpb25hbCkgVGhlIFVSTCB0byBiZSBwYXJzZWQsIGlmIG5vdCBwcm92aWRlZCB0aGUgY3VycmVudCBsb2NhdGlvbiBVUkwgd2lsbCBiZSB1c2VkLlxuICAgICAqIEBwYXJhbSB7Ym9vbGVhbn0gZGVlcCAob3B0aW9uYWwpIFdoZXRoZXIgdG8gcGVyZm9ybSBhIFwiZGVlcFwiIFVSTCBwYXJzZS5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhbiBvYmplY3QgdGhhdCBjb250YWlucyB0aGUgcGFyYW1ldGVyIHZhbHVlcy5cbiAgICAgKi9cbiAgICBleHBvcnRzLmdldFVybFBhcmFtcyA9IGZ1bmN0aW9uICh1cmwsIGRlZXApIHtcbiAgICAgICAgdXJsID0gZGVjb2RlVVJJQ29tcG9uZW50KHVybCB8fCBsb2NhdGlvbi5ocmVmKTtcblxuICAgICAgICB2YXIgc3BsaXRVcmwgPSB1cmwuc3BsaXQoJz8nKSxcbiAgICAgICAgICAgIHNwbGl0UGFyYW0gPSAoc3BsaXRVcmwubGVuZ3RoID4gMSkgPyBzcGxpdFVybFsxXS5zcGxpdCgnJicpIDogW10sXG4gICAgICAgICAgICByZWdleCA9IG5ldyBSZWdFeHAoL1xcWyguKj8pXFxdL2cpLFxuICAgICAgICAgICAgcmVzdWx0ID0ge307XG5cbiAgICAgICAgJC5lYWNoKHNwbGl0UGFyYW0sIGZ1bmN0aW9uIChpLCB2KSB7XG4gICAgICAgICAgICB2YXIga2V5VmFsdWUgPSB2LnNwbGl0KCc9JyksXG4gICAgICAgICAgICAgICAgcmVnZXhSZXN1bHQgPSByZWdleC5leGVjKGtleVZhbHVlWzBdKSxcbiAgICAgICAgICAgICAgICBiYXNlID0gbnVsbCxcbiAgICAgICAgICAgICAgICBiYXNlbmFtZSA9IGtleVZhbHVlWzBdLnN1YnN0cmluZygwLCBrZXlWYWx1ZVswXS5zZWFyY2goJ1xcXFxbJykpLFxuICAgICAgICAgICAgICAgIGtleXMgPSBbXSxcbiAgICAgICAgICAgICAgICBsYXN0S2V5ID0gbnVsbDtcblxuICAgICAgICAgICAgaWYgKCFkZWVwIHx8IHJlZ2V4UmVzdWx0ID09PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgcmVzdWx0W2tleVZhbHVlWzBdXSA9IGtleVZhbHVlWzFdLnNwbGl0KCcjJylbMF07XG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgcmVzdWx0W2Jhc2VuYW1lXSA9IHJlc3VsdFtiYXNlbmFtZV0gfHwgW107XG4gICAgICAgICAgICAgICAgYmFzZSA9IHJlc3VsdFtiYXNlbmFtZV07XG5cbiAgICAgICAgICAgICAgICBkbyB7XG4gICAgICAgICAgICAgICAgICAgIGtleXMucHVzaChyZWdleFJlc3VsdFsxXSk7XG4gICAgICAgICAgICAgICAgICAgIHJlZ2V4UmVzdWx0ID0gcmVnZXguZXhlYyhrZXlWYWx1ZVswXSk7XG4gICAgICAgICAgICAgICAgfSB3aGlsZSAocmVnZXhSZXN1bHQgIT09IG51bGwpO1xuXG4gICAgICAgICAgICAgICAgJC5lYWNoKGtleXMsIGZ1bmN0aW9uIChpLCB2KSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBuZXh0ID0ga2V5c1tpICsgMV07XG4gICAgICAgICAgICAgICAgICAgIHYgPSB2IHx8ICcwJztcblxuICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIChuZXh0KSA9PT0gJ3N0cmluZycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJhc2Vbdl0gPSBiYXNlW3ZdIHx8IFtdO1xuICAgICAgICAgICAgICAgICAgICAgICAgYmFzZSA9IGJhc2Vbdl07XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBiYXNlW3ZdID0gYmFzZVt2XSB8fCB1bmRlZmluZWQ7XG4gICAgICAgICAgICAgICAgICAgICAgICBsYXN0S2V5ID0gdjtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgaWYgKGxhc3RLZXkgIT09IG51bGwpIHtcbiAgICAgICAgICAgICAgICAgICAgYmFzZVtsYXN0S2V5XSA9IGtleVZhbHVlWzFdO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGJhc2UgPSBrZXlWYWx1ZVsxXTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHJldHVybiByZXN1bHQ7XG4gICAgfTtcblxufSkoanNlLmxpYnMudGhlbWUuaGVscGVycyk7XG5cbmpzZS5saWJzLnRlbXBsYXRlID0ganNlLmxpYnMudGVtcGxhdGUgfHwge307XG5qc2UubGlicy50ZW1wbGF0ZS5oZWxwZXJzID0ganNlLmxpYnMudGhlbWUuaGVscGVycztcbiJdfQ==

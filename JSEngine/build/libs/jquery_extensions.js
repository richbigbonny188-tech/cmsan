'use strict';

/* --------------------------------------------------------------
 jquery_extensions.js 2016-06-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

(function () {

    'use strict';

    /**
     * Add ":attr" pseudo selector.
     *
     * This selector enables jQuery to use regular expressions for attribute name matching. Although useful,
     * the engine will remove all dependencies to jQuery and thus it must be moved into an external library
     * or file.
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
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImpxdWVyeV9leHRlbnNpb25zLmpzIl0sIm5hbWVzIjpbIiQiLCJleHByIiwicHNldWRvcyIsImF0dHIiLCJ1bmRlZmluZWQiLCJjcmVhdGVQc2V1ZG8iLCJzZWxlY3RvciIsInJlZ2V4cCIsIlJlZ0V4cCIsImVsZW0iLCJpIiwiYXR0cmlidXRlcyIsImxlbmd0aCIsInRlc3QiLCJuYW1lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUEsQ0FBQyxZQUFZOztBQUVUOztBQUVBOzs7Ozs7OztBQU9BLFFBQUlBLEVBQUVDLElBQUYsQ0FBT0MsT0FBUCxDQUFlQyxJQUFmLEtBQXdCQyxTQUE1QixFQUF1QztBQUNuQ0osVUFBRUMsSUFBRixDQUFPQyxPQUFQLENBQWVDLElBQWYsR0FBc0JILEVBQUVDLElBQUYsQ0FBT0ksWUFBUCxDQUFvQixVQUFVQyxRQUFWLEVBQW9CO0FBQzFELGdCQUFJQyxTQUFTLElBQUlDLE1BQUosQ0FBV0YsUUFBWCxDQUFiO0FBQ0EsbUJBQU8sVUFBVUcsSUFBVixFQUFnQjtBQUNuQixxQkFBSyxJQUFJQyxJQUFJLENBQWIsRUFBZ0JBLElBQUlELEtBQUtFLFVBQUwsQ0FBZ0JDLE1BQXBDLEVBQTRDRixHQUE1QyxFQUFpRDtBQUM3Qyx3QkFBSVAsT0FBT00sS0FBS0UsVUFBTCxDQUFnQkQsQ0FBaEIsQ0FBWDtBQUNBLHdCQUFJSCxPQUFPTSxJQUFQLENBQVlWLEtBQUtXLElBQWpCLENBQUosRUFBNEI7QUFDeEIsK0JBQU8sSUFBUDtBQUNIO0FBQ0o7QUFDRCx1QkFBTyxLQUFQO0FBQ0gsYUFSRDtBQVNILFNBWHFCLENBQXRCO0FBWUg7QUFFSixDQTFCRCIsImZpbGUiOiJqcXVlcnlfZXh0ZW5zaW9ucy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4ganF1ZXJ5X2V4dGVuc2lvbnMuanMgMjAxNi0wNi0yMlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbihmdW5jdGlvbiAoKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvKipcbiAgICAgKiBBZGQgXCI6YXR0clwiIHBzZXVkbyBzZWxlY3Rvci5cbiAgICAgKlxuICAgICAqIFRoaXMgc2VsZWN0b3IgZW5hYmxlcyBqUXVlcnkgdG8gdXNlIHJlZ3VsYXIgZXhwcmVzc2lvbnMgZm9yIGF0dHJpYnV0ZSBuYW1lIG1hdGNoaW5nLiBBbHRob3VnaCB1c2VmdWwsXG4gICAgICogdGhlIGVuZ2luZSB3aWxsIHJlbW92ZSBhbGwgZGVwZW5kZW5jaWVzIHRvIGpRdWVyeSBhbmQgdGh1cyBpdCBtdXN0IGJlIG1vdmVkIGludG8gYW4gZXh0ZXJuYWwgbGlicmFyeVxuICAgICAqIG9yIGZpbGUuXG4gICAgICovXG4gICAgaWYgKCQuZXhwci5wc2V1ZG9zLmF0dHIgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAkLmV4cHIucHNldWRvcy5hdHRyID0gJC5leHByLmNyZWF0ZVBzZXVkbyhmdW5jdGlvbiAoc2VsZWN0b3IpIHtcbiAgICAgICAgICAgIGxldCByZWdleHAgPSBuZXcgUmVnRXhwKHNlbGVjdG9yKTtcbiAgICAgICAgICAgIHJldHVybiBmdW5jdGlvbiAoZWxlbSkge1xuICAgICAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgZWxlbS5hdHRyaWJ1dGVzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBhdHRyID0gZWxlbS5hdHRyaWJ1dGVzW2ldO1xuICAgICAgICAgICAgICAgICAgICBpZiAocmVnZXhwLnRlc3QoYXR0ci5uYW1lKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG59KSgpOyJdfQ==

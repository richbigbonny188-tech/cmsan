'use strict';

/* --------------------------------------------------------------
 normalize.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.normalize = jse.libs.normalize || {};

/**
 * ## Normalization Library
 *
 * This library normalizes input and output (XSS protection).
 *
 * @module JSE/Libs/normalize
 * @exports jse.libs.normalize
 */
(function (exports) {

  'use strict';

  /**
   * Returns the escaped text from a HTML string.
   *
   * {@link http://stackoverflow.com/a/25207}
   *
   * @param {String} text The text to be escaped.
   *
   * @return {String} Returns the escaped string.
   *
   * @public
   */

  exports.escapeHtml = function (text) {
    return $('<div/>').text(text).html();
  };
})(jse.libs.normalize);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vcm1hbGl6ZS5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwibm9ybWFsaXplIiwiZXhwb3J0cyIsImVzY2FwZUh0bWwiLCJ0ZXh0IiwiJCIsImh0bWwiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxTQUFULEdBQXFCRixJQUFJQyxJQUFKLENBQVNDLFNBQVQsSUFBc0IsRUFBM0M7O0FBRUE7Ozs7Ozs7O0FBUUEsQ0FBQyxVQUFVQyxPQUFWLEVBQW1COztBQUVoQjs7QUFFQTs7Ozs7Ozs7Ozs7O0FBV0FBLFVBQVFDLFVBQVIsR0FBcUIsVUFBVUMsSUFBVixFQUFnQjtBQUNqQyxXQUFPQyxFQUFFLFFBQUYsRUFBWUQsSUFBWixDQUFpQkEsSUFBakIsRUFBdUJFLElBQXZCLEVBQVA7QUFDSCxHQUZEO0FBSUgsQ0FuQkQsRUFtQkdQLElBQUlDLElBQUosQ0FBU0MsU0FuQloiLCJmaWxlIjoibm9ybWFsaXplLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBub3JtYWxpemUuanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLm5vcm1hbGl6ZSA9IGpzZS5saWJzLm5vcm1hbGl6ZSB8fCB7fTtcblxuLyoqXG4gKiAjIyBOb3JtYWxpemF0aW9uIExpYnJhcnlcbiAqXG4gKiBUaGlzIGxpYnJhcnkgbm9ybWFsaXplcyBpbnB1dCBhbmQgb3V0cHV0IChYU1MgcHJvdGVjdGlvbikuXG4gKlxuICogQG1vZHVsZSBKU0UvTGlicy9ub3JtYWxpemVcbiAqIEBleHBvcnRzIGpzZS5saWJzLm5vcm1hbGl6ZVxuICovXG4oZnVuY3Rpb24gKGV4cG9ydHMpIHtcblxuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIC8qKlxuICAgICAqIFJldHVybnMgdGhlIGVzY2FwZWQgdGV4dCBmcm9tIGEgSFRNTCBzdHJpbmcuXG4gICAgICpcbiAgICAgKiB7QGxpbmsgaHR0cDovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMjUyMDd9XG4gICAgICpcbiAgICAgKiBAcGFyYW0ge1N0cmluZ30gdGV4dCBUaGUgdGV4dCB0byBiZSBlc2NhcGVkLlxuICAgICAqXG4gICAgICogQHJldHVybiB7U3RyaW5nfSBSZXR1cm5zIHRoZSBlc2NhcGVkIHN0cmluZy5cbiAgICAgKlxuICAgICAqIEBwdWJsaWNcbiAgICAgKi9cbiAgICBleHBvcnRzLmVzY2FwZUh0bWwgPSBmdW5jdGlvbiAodGV4dCkge1xuICAgICAgICByZXR1cm4gJCgnPGRpdi8+JykudGV4dCh0ZXh0KS5odG1sKCk7XG4gICAgfTtcblxufSkoanNlLmxpYnMubm9ybWFsaXplKTtcbiJdfQ==

'use strict';

/* --------------------------------------------------------------
 configuration.js 2016-12-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.configuration = jse.libs.configuration || {};

/**
 * ## Configurations Library
 *
 * This library makes it possible to receive shop configuration values.
 *
 * @module JSE/Libs/datatable
 * @exports jse.libs.datatable
 */
(function (exports) {
  'use strict';

  /**
   * @type {String}
   */

  var pageToken = jse.core.config.get('pageToken');

  /**
   * @type {String}
   */
  var baseUrl = jse.core.config.get('appUrl') + '/shop.php?do=JsConfiguration';

  /**
   * Get the configuration value by the provided key.
   *
   * @param key Configuration key.
   *
   * @returns {Promise} The promise will be resolve with the configuration value.
   */
  exports.get = function (key) {
    return new Promise(function (resolve, reject) {
      var url = baseUrl + '/Get';
      $.ajax({ url: url, data: { key: key, pageToken: pageToken } }).done(function (response) {
        return resolve(response);
      }).fail(function (error) {
        return reject(error);
      });
    });
  };
})(jse.libs.configuration);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbmZpZ3VyYXRpb24uanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsImNvbmZpZ3VyYXRpb24iLCJleHBvcnRzIiwicGFnZVRva2VuIiwiY29yZSIsImNvbmZpZyIsImdldCIsImJhc2VVcmwiLCJQcm9taXNlIiwicmVzb2x2ZSIsInJlamVjdCIsInVybCIsIiQiLCJhamF4IiwiZGF0YSIsImtleSIsImRvbmUiLCJyZXNwb25zZSIsImZhaWwiLCJlcnJvciJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxJQUFJQyxJQUFKLENBQVNDLGFBQVQsR0FBeUJGLElBQUlDLElBQUosQ0FBU0MsYUFBVCxJQUEwQixFQUFuRDs7QUFFQTs7Ozs7Ozs7QUFRQSxDQUFDLFVBQVVDLE9BQVYsRUFBbUI7QUFDaEI7O0FBRUE7Ozs7QUFHQSxNQUFNQyxZQUFZSixJQUFJSyxJQUFKLENBQVNDLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLFdBQXBCLENBQWxCOztBQUVBOzs7QUFHQSxNQUFNQyxVQUFhUixJQUFJSyxJQUFKLENBQVNDLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLFFBQXBCLENBQWIsaUNBQU47O0FBRUE7Ozs7Ozs7QUFPQUosVUFBUUksR0FBUixHQUFjLGVBQU87QUFDakIsV0FBTyxJQUFJRSxPQUFKLENBQVksVUFBQ0MsT0FBRCxFQUFVQyxNQUFWLEVBQXFCO0FBQ3BDLFVBQU1DLE1BQVNKLE9BQVQsU0FBTjtBQUNBSyxRQUFFQyxJQUFGLENBQU8sRUFBQ0YsUUFBRCxFQUFNRyxNQUFNLEVBQUNDLFFBQUQsRUFBTVosb0JBQU4sRUFBWixFQUFQLEVBQ0thLElBREwsQ0FDVTtBQUFBLGVBQVlQLFFBQVFRLFFBQVIsQ0FBWjtBQUFBLE9BRFYsRUFFS0MsSUFGTCxDQUVVO0FBQUEsZUFBU1IsT0FBT1MsS0FBUCxDQUFUO0FBQUEsT0FGVjtBQUdILEtBTE0sQ0FBUDtBQU1ILEdBUEQ7QUFTSCxDQTdCRCxFQTZCR3BCLElBQUlDLElBQUosQ0FBU0MsYUE3QloiLCJmaWxlIjoiY29uZmlndXJhdGlvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY29uZmlndXJhdGlvbi5qcyAyMDE2LTEyLTAxXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMuY29uZmlndXJhdGlvbiA9IGpzZS5saWJzLmNvbmZpZ3VyYXRpb24gfHwge307XG5cbi8qKlxuICogIyMgQ29uZmlndXJhdGlvbnMgTGlicmFyeVxuICpcbiAqIFRoaXMgbGlicmFyeSBtYWtlcyBpdCBwb3NzaWJsZSB0byByZWNlaXZlIHNob3AgY29uZmlndXJhdGlvbiB2YWx1ZXMuXG4gKlxuICogQG1vZHVsZSBKU0UvTGlicy9kYXRhdGFibGVcbiAqIEBleHBvcnRzIGpzZS5saWJzLmRhdGF0YWJsZVxuICovXG4oZnVuY3Rpb24gKGV4cG9ydHMpIHtcbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvKipcbiAgICAgKiBAdHlwZSB7U3RyaW5nfVxuICAgICAqL1xuICAgIGNvbnN0IHBhZ2VUb2tlbiA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpO1xuXG4gICAgLyoqXG4gICAgICogQHR5cGUge1N0cmluZ31cbiAgICAgKi9cbiAgICBjb25zdCBiYXNlVXJsID0gYCR7anNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJyl9L3Nob3AucGhwP2RvPUpzQ29uZmlndXJhdGlvbmA7XG5cbiAgICAvKipcbiAgICAgKiBHZXQgdGhlIGNvbmZpZ3VyYXRpb24gdmFsdWUgYnkgdGhlIHByb3ZpZGVkIGtleS5cbiAgICAgKlxuICAgICAqIEBwYXJhbSBrZXkgQ29uZmlndXJhdGlvbiBrZXkuXG4gICAgICpcbiAgICAgKiBAcmV0dXJucyB7UHJvbWlzZX0gVGhlIHByb21pc2Ugd2lsbCBiZSByZXNvbHZlIHdpdGggdGhlIGNvbmZpZ3VyYXRpb24gdmFsdWUuXG4gICAgICovXG4gICAgZXhwb3J0cy5nZXQgPSBrZXkgPT4ge1xuICAgICAgICByZXR1cm4gbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xuICAgICAgICAgICAgY29uc3QgdXJsID0gYCR7YmFzZVVybH0vR2V0YDtcbiAgICAgICAgICAgICQuYWpheCh7dXJsLCBkYXRhOiB7a2V5LCBwYWdlVG9rZW59fSlcbiAgICAgICAgICAgICAgICAuZG9uZShyZXNwb25zZSA9PiByZXNvbHZlKHJlc3BvbnNlKSlcbiAgICAgICAgICAgICAgICAuZmFpbChlcnJvciA9PiByZWplY3QoZXJyb3IpKTtcbiAgICAgICAgfSk7XG4gICAgfTtcblxufSkoanNlLmxpYnMuY29uZmlndXJhdGlvbik7XG4iXX0=

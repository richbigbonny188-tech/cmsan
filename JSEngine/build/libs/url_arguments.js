'use strict';

/* --------------------------------------------------------------
 url_arguments.js 2016-05-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.url_arguments = jse.libs.url_arguments || {};

/**
 * ## URL Arguments Library
 *
 * This library is created to help coding when values of URL are required.
 *
 * @module JSE/Libs/url_arguments
 * @exports jse.libs.url_arguments
 */
(function (exports) {

    'use strict';

    /**
     * Returns all URL parameters from the provided URL.
     *
     * @param {string} url (optional) The URL to be parsed. If not provided the current location will be used.
     *
     * @return {object} Returns an object that contains the parameters in key-value pairs.
     *
     * @deprecated Use the $.deparam method which can better parse the GET parameters.
     */

    exports.getUrlParameters = function (url) {
        var parameters = {},
            search = url ? url.replace(/.*\?/, '') : location.search.substring(1),
            result;

        if (search === null || search === '') {
            return parameters;
        }

        result = search.split('&');

        for (var i = 0; i < result.length; i++) {
            var tmp = result[i].split('=');
            parameters[tmp[0]] = tmp[1];
        }

        return parameters;
    };

    /**
     * Returns the current filename.
     *
     * @returns string Current filename.
     */
    exports.getCurrentFile = function () {
        var urlArray = window.location.pathname.split('/');
        return urlArray[urlArray.length - 1];
    };

    /**
     * Replaces a specific parameter value inside an URL.
     *
     * @param url The URL containing the parameter.
     * @param parameter The parameter name to be replaced.
     * @param value The new value of the parameter.
     *
     * @returns {string} Returns the updated URL string.
     */
    exports.replaceParameterValue = function (url, parameter, value) {
        var regex = new RegExp('(' + parameter + '=)[^\&]+');

        url = url.replace(regex, '$1' + value);

        if (url.search(parameter + '=') === -1 && value !== undefined) {
            if (url.search(/\?/) === -1) {
                url += '?' + encodeURIComponent(parameter) + '=' + encodeURIComponent(value);
            } else if (url.substr(url.length - 1, 1) === '?') {
                url += encodeURIComponent(parameter) + '=' + encodeURIComponent(value);
            } else {
                url += '&' + encodeURIComponent(parameter) + '=' + encodeURIComponent(value);
            }
        }

        return url;
    };
})(jse.libs.url_arguments);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInVybF9hcmd1bWVudHMuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInVybF9hcmd1bWVudHMiLCJleHBvcnRzIiwiZ2V0VXJsUGFyYW1ldGVycyIsInVybCIsInBhcmFtZXRlcnMiLCJzZWFyY2giLCJyZXBsYWNlIiwibG9jYXRpb24iLCJzdWJzdHJpbmciLCJyZXN1bHQiLCJzcGxpdCIsImkiLCJsZW5ndGgiLCJ0bXAiLCJnZXRDdXJyZW50RmlsZSIsInVybEFycmF5Iiwid2luZG93IiwicGF0aG5hbWUiLCJyZXBsYWNlUGFyYW1ldGVyVmFsdWUiLCJwYXJhbWV0ZXIiLCJ2YWx1ZSIsInJlZ2V4IiwiUmVnRXhwIiwidW5kZWZpbmVkIiwiZW5jb2RlVVJJQ29tcG9uZW50Iiwic3Vic3RyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsYUFBVCxHQUF5QkYsSUFBSUMsSUFBSixDQUFTQyxhQUFULElBQTBCLEVBQW5EOztBQUVBOzs7Ozs7OztBQVFBLENBQUMsVUFBVUMsT0FBVixFQUFtQjs7QUFFaEI7O0FBRUE7Ozs7Ozs7Ozs7QUFTQUEsWUFBUUMsZ0JBQVIsR0FBMkIsVUFBVUMsR0FBVixFQUFlO0FBQ3RDLFlBQUlDLGFBQWEsRUFBakI7QUFBQSxZQUNJQyxTQUFVRixHQUFELEdBQVFBLElBQUlHLE9BQUosQ0FBWSxNQUFaLEVBQW9CLEVBQXBCLENBQVIsR0FBa0NDLFNBQVNGLE1BQVQsQ0FBZ0JHLFNBQWhCLENBQTBCLENBQTFCLENBRC9DO0FBQUEsWUFFSUMsTUFGSjs7QUFJQSxZQUFJSixXQUFXLElBQVgsSUFBbUJBLFdBQVcsRUFBbEMsRUFBc0M7QUFDbEMsbUJBQU9ELFVBQVA7QUFDSDs7QUFFREssaUJBQVNKLE9BQU9LLEtBQVAsQ0FBYSxHQUFiLENBQVQ7O0FBRUEsYUFBSyxJQUFJQyxJQUFJLENBQWIsRUFBZ0JBLElBQUlGLE9BQU9HLE1BQTNCLEVBQW1DRCxHQUFuQyxFQUF3QztBQUNwQyxnQkFBSUUsTUFBTUosT0FBT0UsQ0FBUCxFQUFVRCxLQUFWLENBQWdCLEdBQWhCLENBQVY7QUFDQU4sdUJBQVdTLElBQUksQ0FBSixDQUFYLElBQXFCQSxJQUFJLENBQUosQ0FBckI7QUFDSDs7QUFFRCxlQUFPVCxVQUFQO0FBQ0gsS0FqQkQ7O0FBbUJBOzs7OztBQUtBSCxZQUFRYSxjQUFSLEdBQXlCLFlBQVk7QUFDakMsWUFBSUMsV0FBV0MsT0FBT1QsUUFBUCxDQUFnQlUsUUFBaEIsQ0FBeUJQLEtBQXpCLENBQStCLEdBQS9CLENBQWY7QUFDQSxlQUFPSyxTQUFTQSxTQUFTSCxNQUFULEdBQWtCLENBQTNCLENBQVA7QUFDSCxLQUhEOztBQUtBOzs7Ozs7Ozs7QUFTQVgsWUFBUWlCLHFCQUFSLEdBQWdDLFVBQVVmLEdBQVYsRUFBZWdCLFNBQWYsRUFBMEJDLEtBQTFCLEVBQWlDO0FBQzdELFlBQUlDLFFBQVEsSUFBSUMsTUFBSixDQUFXLE1BQU1ILFNBQU4sR0FBa0IsVUFBN0IsQ0FBWjs7QUFFQWhCLGNBQU1BLElBQUlHLE9BQUosQ0FBWWUsS0FBWixFQUFtQixPQUFPRCxLQUExQixDQUFOOztBQUVBLFlBQUlqQixJQUFJRSxNQUFKLENBQVdjLFlBQVksR0FBdkIsTUFBZ0MsQ0FBQyxDQUFqQyxJQUFzQ0MsVUFBVUcsU0FBcEQsRUFBK0Q7QUFDM0QsZ0JBQUlwQixJQUFJRSxNQUFKLENBQVcsSUFBWCxNQUFxQixDQUFDLENBQTFCLEVBQTZCO0FBQ3pCRix1QkFBTyxNQUFNcUIsbUJBQW1CTCxTQUFuQixDQUFOLEdBQXNDLEdBQXRDLEdBQTRDSyxtQkFBbUJKLEtBQW5CLENBQW5EO0FBQ0gsYUFGRCxNQUVPLElBQUlqQixJQUFJc0IsTUFBSixDQUFXdEIsSUFBSVMsTUFBSixHQUFhLENBQXhCLEVBQTJCLENBQTNCLE1BQWtDLEdBQXRDLEVBQTJDO0FBQzlDVCx1QkFBT3FCLG1CQUFtQkwsU0FBbkIsSUFBZ0MsR0FBaEMsR0FBc0NLLG1CQUFtQkosS0FBbkIsQ0FBN0M7QUFDSCxhQUZNLE1BRUE7QUFDSGpCLHVCQUFPLE1BQU1xQixtQkFBbUJMLFNBQW5CLENBQU4sR0FBc0MsR0FBdEMsR0FBNENLLG1CQUFtQkosS0FBbkIsQ0FBbkQ7QUFDSDtBQUNKOztBQUVELGVBQU9qQixHQUFQO0FBQ0gsS0FoQkQ7QUFrQkgsQ0FyRUQsRUFxRUdMLElBQUlDLElBQUosQ0FBU0MsYUFyRVoiLCJmaWxlIjoidXJsX2FyZ3VtZW50cy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gdXJsX2FyZ3VtZW50cy5qcyAyMDE2LTA1LTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMudXJsX2FyZ3VtZW50cyA9IGpzZS5saWJzLnVybF9hcmd1bWVudHMgfHwge307XG5cbi8qKlxuICogIyMgVVJMIEFyZ3VtZW50cyBMaWJyYXJ5XG4gKlxuICogVGhpcyBsaWJyYXJ5IGlzIGNyZWF0ZWQgdG8gaGVscCBjb2Rpbmcgd2hlbiB2YWx1ZXMgb2YgVVJMIGFyZSByZXF1aXJlZC5cbiAqXG4gKiBAbW9kdWxlIEpTRS9MaWJzL3VybF9hcmd1bWVudHNcbiAqIEBleHBvcnRzIGpzZS5saWJzLnVybF9hcmd1bWVudHNcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIGFsbCBVUkwgcGFyYW1ldGVycyBmcm9tIHRoZSBwcm92aWRlZCBVUkwuXG4gICAgICpcbiAgICAgKiBAcGFyYW0ge3N0cmluZ30gdXJsIChvcHRpb25hbCkgVGhlIFVSTCB0byBiZSBwYXJzZWQuIElmIG5vdCBwcm92aWRlZCB0aGUgY3VycmVudCBsb2NhdGlvbiB3aWxsIGJlIHVzZWQuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYW4gb2JqZWN0IHRoYXQgY29udGFpbnMgdGhlIHBhcmFtZXRlcnMgaW4ga2V5LXZhbHVlIHBhaXJzLlxuICAgICAqXG4gICAgICogQGRlcHJlY2F0ZWQgVXNlIHRoZSAkLmRlcGFyYW0gbWV0aG9kIHdoaWNoIGNhbiBiZXR0ZXIgcGFyc2UgdGhlIEdFVCBwYXJhbWV0ZXJzLlxuICAgICAqL1xuICAgIGV4cG9ydHMuZ2V0VXJsUGFyYW1ldGVycyA9IGZ1bmN0aW9uICh1cmwpIHtcbiAgICAgICAgdmFyIHBhcmFtZXRlcnMgPSB7fSxcbiAgICAgICAgICAgIHNlYXJjaCA9ICh1cmwpID8gdXJsLnJlcGxhY2UoLy4qXFw/LywgJycpIDogbG9jYXRpb24uc2VhcmNoLnN1YnN0cmluZygxKSxcbiAgICAgICAgICAgIHJlc3VsdDtcblxuICAgICAgICBpZiAoc2VhcmNoID09PSBudWxsIHx8IHNlYXJjaCA9PT0gJycpIHtcbiAgICAgICAgICAgIHJldHVybiBwYXJhbWV0ZXJzO1xuICAgICAgICB9XG5cbiAgICAgICAgcmVzdWx0ID0gc2VhcmNoLnNwbGl0KCcmJyk7XG5cbiAgICAgICAgZm9yICh2YXIgaSA9IDA7IGkgPCByZXN1bHQubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgIHZhciB0bXAgPSByZXN1bHRbaV0uc3BsaXQoJz0nKTtcbiAgICAgICAgICAgIHBhcmFtZXRlcnNbdG1wWzBdXSA9IHRtcFsxXTtcbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiBwYXJhbWV0ZXJzO1xuICAgIH07XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIHRoZSBjdXJyZW50IGZpbGVuYW1lLlxuICAgICAqXG4gICAgICogQHJldHVybnMgc3RyaW5nIEN1cnJlbnQgZmlsZW5hbWUuXG4gICAgICovXG4gICAgZXhwb3J0cy5nZXRDdXJyZW50RmlsZSA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdmFyIHVybEFycmF5ID0gd2luZG93LmxvY2F0aW9uLnBhdGhuYW1lLnNwbGl0KCcvJyk7XG4gICAgICAgIHJldHVybiB1cmxBcnJheVt1cmxBcnJheS5sZW5ndGggLSAxXTtcbiAgICB9O1xuXG4gICAgLyoqXG4gICAgICogUmVwbGFjZXMgYSBzcGVjaWZpYyBwYXJhbWV0ZXIgdmFsdWUgaW5zaWRlIGFuIFVSTC5cbiAgICAgKlxuICAgICAqIEBwYXJhbSB1cmwgVGhlIFVSTCBjb250YWluaW5nIHRoZSBwYXJhbWV0ZXIuXG4gICAgICogQHBhcmFtIHBhcmFtZXRlciBUaGUgcGFyYW1ldGVyIG5hbWUgdG8gYmUgcmVwbGFjZWQuXG4gICAgICogQHBhcmFtIHZhbHVlIFRoZSBuZXcgdmFsdWUgb2YgdGhlIHBhcmFtZXRlci5cbiAgICAgKlxuICAgICAqIEByZXR1cm5zIHtzdHJpbmd9IFJldHVybnMgdGhlIHVwZGF0ZWQgVVJMIHN0cmluZy5cbiAgICAgKi9cbiAgICBleHBvcnRzLnJlcGxhY2VQYXJhbWV0ZXJWYWx1ZSA9IGZ1bmN0aW9uICh1cmwsIHBhcmFtZXRlciwgdmFsdWUpIHtcbiAgICAgICAgdmFyIHJlZ2V4ID0gbmV3IFJlZ0V4cCgnKCcgKyBwYXJhbWV0ZXIgKyAnPSlbXlxcJl0rJyk7XG5cbiAgICAgICAgdXJsID0gdXJsLnJlcGxhY2UocmVnZXgsICckMScgKyB2YWx1ZSk7XG5cbiAgICAgICAgaWYgKHVybC5zZWFyY2gocGFyYW1ldGVyICsgJz0nKSA9PT0gLTEgJiYgdmFsdWUgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgaWYgKHVybC5zZWFyY2goL1xcPy8pID09PSAtMSkge1xuICAgICAgICAgICAgICAgIHVybCArPSAnPycgKyBlbmNvZGVVUklDb21wb25lbnQocGFyYW1ldGVyKSArICc9JyArIGVuY29kZVVSSUNvbXBvbmVudCh2YWx1ZSk7XG4gICAgICAgICAgICB9IGVsc2UgaWYgKHVybC5zdWJzdHIodXJsLmxlbmd0aCAtIDEsIDEpID09PSAnPycpIHtcbiAgICAgICAgICAgICAgICB1cmwgKz0gZW5jb2RlVVJJQ29tcG9uZW50KHBhcmFtZXRlcikgKyAnPScgKyBlbmNvZGVVUklDb21wb25lbnQodmFsdWUpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB1cmwgKz0gJyYnICsgZW5jb2RlVVJJQ29tcG9uZW50KHBhcmFtZXRlcikgKyAnPScgKyBlbmNvZGVVUklDb21wb25lbnQodmFsdWUpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgcmV0dXJuIHVybDtcbiAgICB9O1xuXG59KShqc2UubGlicy51cmxfYXJndW1lbnRzKTtcbiJdfQ==

'use strict';

/* --------------------------------------------------------------
 datetimepicker.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Datetimepicker Widget
 *
 * This widget will convert itself or multiple elements into datetimepicker instances. Check the defaults object for a
 * list of available options.
 *
 * You can also set this module in a container element and provide the "data-datetimepicker-container" attribute and
 * this plugin will initialize all the child elements that have the "datetimepicker" class into datetimepicker widgets.
 *
 * jQuery Datetimepicker Website: {@link http://xdsoft.net/jqplugins/datetimepicker}
 *
 * ### Options
 *
 * In addition to the options stated below, you could also add many more options shown in the
 * jQuery Datetimepicker documentation.
 *
 * **Format | `data-datetimepicker-format` | String | Optional**
 *
 * Provide the default date format. If no value is provided, the default format will be set
 * to `'d.m.Y H:i'`.
 *
 * **Lang | `data-datetimepicker-lang` | String | Optional**
 *
 * Provide the default language code. If the current language is set to english, the default
 * language code will be set to `'en-GB'`, else the language code will be set to `'de'`.
 *
 * ### Examples
 *
 * ```html
 * <input type="text" placeholder="##.##.#### ##:##" data-gx-widget="datetimepicker" />
 * ```
 *
 * @module Admin/Widgets/datetimepicker
 * @requires jQuery-Datetimepicker-Plugin
 */
jse.widgets.module('datetimepicker', [jse.source + '/vendor/datetimepicker/jquery.datetimepicker.min.css', jse.source + '/vendor/datetimepicker/jquery.datetimepicker.full.min.js'], function (data) {

    'use strict';

    var
    /**
     * Module Selector
     *
     * @type {object}
     */
    $this = $(this),


    /**
     * Default Module Options
     *
     * @type {object}
     */
    defaults = {
        format: 'd.m.Y H:i',
        lang: jse.core.config.get('languageCode') === 'en' ? 'en-GB' : 'de'
    },


    /**
     * Final Module Options
     *
     * @type {object}
     */
    options = $.extend(true, {}, defaults, data),


    /**
     * Module Instance
     *
     * @type {object}
     */
    module = {};

    /**
     * Initialize Module
     *
     * @param {function} done Call this method once the module is initialized.
     */
    module.init = function (done) {
        // Check if the datetimepicker plugin is already loaded. 
        if ($.fn.datetimepicker === undefined) {
            throw new Error('The $.fn.datetimepicker plugin must be loaded before the module is initialized.');
        }

        // Check if the current element is a container and thus need to initialize the children elements. 
        if (options.container !== undefined) {
            $this.find('.datetimepicker').datetimepicker(options);
        } else {
            $this.datetimepicker(options);
        }

        done();
    };

    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGV0aW1lcGlja2VyLmpzIl0sIm5hbWVzIjpbImpzZSIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJmb3JtYXQiLCJsYW5nIiwiY29yZSIsImNvbmZpZyIsImdldCIsIm9wdGlvbnMiLCJleHRlbmQiLCJpbml0IiwiZG9uZSIsImZuIiwiZGF0ZXRpbWVwaWNrZXIiLCJ1bmRlZmluZWQiLCJFcnJvciIsImNvbnRhaW5lciIsImZpbmQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFtQ0FBLElBQUlDLE9BQUosQ0FBWUMsTUFBWixDQUNJLGdCQURKLEVBR0ksQ0FDT0YsSUFBSUcsTUFEWCwyREFFT0gsSUFBSUcsTUFGWCw4REFISixFQVFJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRUE7QUFDSTs7Ozs7QUFLQUMsWUFBUUMsRUFBRSxJQUFGLENBTlo7OztBQVFJOzs7OztBQUtBQyxlQUFXO0FBQ1BDLGdCQUFRLFdBREQ7QUFFUEMsY0FBTVQsSUFBSVUsSUFBSixDQUFTQyxNQUFULENBQWdCQyxHQUFoQixDQUFvQixjQUFwQixNQUF3QyxJQUF4QyxHQUErQyxPQUEvQyxHQUF5RDtBQUZ4RCxLQWJmOzs7QUFrQkk7Ozs7O0FBS0FDLGNBQVVQLEVBQUVRLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQlAsUUFBbkIsRUFBNkJILElBQTdCLENBdkJkOzs7QUF5Qkk7Ozs7O0FBS0FGLGFBQVMsRUE5QmI7O0FBZ0NBOzs7OztBQUtBQSxXQUFPYSxJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUMxQjtBQUNBLFlBQUlWLEVBQUVXLEVBQUYsQ0FBS0MsY0FBTCxLQUF3QkMsU0FBNUIsRUFBdUM7QUFDbkMsa0JBQU0sSUFBSUMsS0FBSixDQUFVLGlGQUFWLENBQU47QUFDSDs7QUFFRDtBQUNBLFlBQUlQLFFBQVFRLFNBQVIsS0FBc0JGLFNBQTFCLEVBQXFDO0FBQ2pDZCxrQkFBTWlCLElBQU4sQ0FBVyxpQkFBWCxFQUE4QkosY0FBOUIsQ0FBNkNMLE9BQTdDO0FBQ0gsU0FGRCxNQUVPO0FBQ0hSLGtCQUFNYSxjQUFOLENBQXFCTCxPQUFyQjtBQUNIOztBQUVERztBQUNILEtBZEQ7O0FBZ0JBLFdBQU9kLE1BQVA7QUFDSCxDQWxFTCIsImZpbGUiOiJkYXRldGltZXBpY2tlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZGF0ZXRpbWVwaWNrZXIuanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRGF0ZXRpbWVwaWNrZXIgV2lkZ2V0XG4gKlxuICogVGhpcyB3aWRnZXQgd2lsbCBjb252ZXJ0IGl0c2VsZiBvciBtdWx0aXBsZSBlbGVtZW50cyBpbnRvIGRhdGV0aW1lcGlja2VyIGluc3RhbmNlcy4gQ2hlY2sgdGhlIGRlZmF1bHRzIG9iamVjdCBmb3IgYVxuICogbGlzdCBvZiBhdmFpbGFibGUgb3B0aW9ucy5cbiAqXG4gKiBZb3UgY2FuIGFsc28gc2V0IHRoaXMgbW9kdWxlIGluIGEgY29udGFpbmVyIGVsZW1lbnQgYW5kIHByb3ZpZGUgdGhlIFwiZGF0YS1kYXRldGltZXBpY2tlci1jb250YWluZXJcIiBhdHRyaWJ1dGUgYW5kXG4gKiB0aGlzIHBsdWdpbiB3aWxsIGluaXRpYWxpemUgYWxsIHRoZSBjaGlsZCBlbGVtZW50cyB0aGF0IGhhdmUgdGhlIFwiZGF0ZXRpbWVwaWNrZXJcIiBjbGFzcyBpbnRvIGRhdGV0aW1lcGlja2VyIHdpZGdldHMuXG4gKlxuICogalF1ZXJ5IERhdGV0aW1lcGlja2VyIFdlYnNpdGU6IHtAbGluayBodHRwOi8veGRzb2Z0Lm5ldC9qcXBsdWdpbnMvZGF0ZXRpbWVwaWNrZXJ9XG4gKlxuICogIyMjIE9wdGlvbnNcbiAqXG4gKiBJbiBhZGRpdGlvbiB0byB0aGUgb3B0aW9ucyBzdGF0ZWQgYmVsb3csIHlvdSBjb3VsZCBhbHNvIGFkZCBtYW55IG1vcmUgb3B0aW9ucyBzaG93biBpbiB0aGVcbiAqIGpRdWVyeSBEYXRldGltZXBpY2tlciBkb2N1bWVudGF0aW9uLlxuICpcbiAqICoqRm9ybWF0IHwgYGRhdGEtZGF0ZXRpbWVwaWNrZXItZm9ybWF0YCB8IFN0cmluZyB8IE9wdGlvbmFsKipcbiAqXG4gKiBQcm92aWRlIHRoZSBkZWZhdWx0IGRhdGUgZm9ybWF0LiBJZiBubyB2YWx1ZSBpcyBwcm92aWRlZCwgdGhlIGRlZmF1bHQgZm9ybWF0IHdpbGwgYmUgc2V0XG4gKiB0byBgJ2QubS5ZIEg6aSdgLlxuICpcbiAqICoqTGFuZyB8IGBkYXRhLWRhdGV0aW1lcGlja2VyLWxhbmdgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICpcbiAqIFByb3ZpZGUgdGhlIGRlZmF1bHQgbGFuZ3VhZ2UgY29kZS4gSWYgdGhlIGN1cnJlbnQgbGFuZ3VhZ2UgaXMgc2V0IHRvIGVuZ2xpc2gsIHRoZSBkZWZhdWx0XG4gKiBsYW5ndWFnZSBjb2RlIHdpbGwgYmUgc2V0IHRvIGAnZW4tR0InYCwgZWxzZSB0aGUgbGFuZ3VhZ2UgY29kZSB3aWxsIGJlIHNldCB0byBgJ2RlJ2AuXG4gKlxuICogIyMjIEV4YW1wbGVzXG4gKlxuICogYGBgaHRtbFxuICogPGlucHV0IHR5cGU9XCJ0ZXh0XCIgcGxhY2Vob2xkZXI9XCIjIy4jIy4jIyMjICMjOiMjXCIgZGF0YS1neC13aWRnZXQ9XCJkYXRldGltZXBpY2tlclwiIC8+XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEFkbWluL1dpZGdldHMvZGF0ZXRpbWVwaWNrZXJcbiAqIEByZXF1aXJlcyBqUXVlcnktRGF0ZXRpbWVwaWNrZXItUGx1Z2luXG4gKi9cbmpzZS53aWRnZXRzLm1vZHVsZShcbiAgICAnZGF0ZXRpbWVwaWNrZXInLFxuXG4gICAgW1xuICAgICAgICBgJHtqc2Uuc291cmNlfS92ZW5kb3IvZGF0ZXRpbWVwaWNrZXIvanF1ZXJ5LmRhdGV0aW1lcGlja2VyLm1pbi5jc3NgLFxuICAgICAgICBgJHtqc2Uuc291cmNlfS92ZW5kb3IvZGF0ZXRpbWVwaWNrZXIvanF1ZXJ5LmRhdGV0aW1lcGlja2VyLmZ1bGwubWluLmpzYFxuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICB2YXJcbiAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICogTW9kdWxlIFNlbGVjdG9yXG4gICAgICAgICAgICAgKlxuICAgICAgICAgICAgICogQHR5cGUge29iamVjdH1cbiAgICAgICAgICAgICAqL1xuICAgICAgICAgICAgJHRoaXMgPSAkKHRoaXMpLFxuXG4gICAgICAgICAgICAvKipcbiAgICAgICAgICAgICAqIERlZmF1bHQgTW9kdWxlIE9wdGlvbnNcbiAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgKiBAdHlwZSB7b2JqZWN0fVxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICBmb3JtYXQ6ICdkLm0uWSBIOmknLFxuICAgICAgICAgICAgICAgIGxhbmc6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2xhbmd1YWdlQ29kZScpID09PSAnZW4nID8gJ2VuLUdCJyA6ICdkZSdcbiAgICAgICAgICAgIH0sXG5cbiAgICAgICAgICAgIC8qKlxuICAgICAgICAgICAgICogRmluYWwgTW9kdWxlIE9wdGlvbnNcbiAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgKiBAdHlwZSB7b2JqZWN0fVxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBNb2R1bGUgSW5zdGFuY2VcbiAgICAgICAgICAgICAqXG4gICAgICAgICAgICAgKiBAdHlwZSB7b2JqZWN0fVxuICAgICAgICAgICAgICovXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdGlhbGl6ZSBNb2R1bGVcbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtmdW5jdGlvbn0gZG9uZSBDYWxsIHRoaXMgbWV0aG9kIG9uY2UgdGhlIG1vZHVsZSBpcyBpbml0aWFsaXplZC5cbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcbiAgICAgICAgICAgIC8vIENoZWNrIGlmIHRoZSBkYXRldGltZXBpY2tlciBwbHVnaW4gaXMgYWxyZWFkeSBsb2FkZWQuIFxuICAgICAgICAgICAgaWYgKCQuZm4uZGF0ZXRpbWVwaWNrZXIgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcignVGhlICQuZm4uZGF0ZXRpbWVwaWNrZXIgcGx1Z2luIG11c3QgYmUgbG9hZGVkIGJlZm9yZSB0aGUgbW9kdWxlIGlzIGluaXRpYWxpemVkLicpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBDaGVjayBpZiB0aGUgY3VycmVudCBlbGVtZW50IGlzIGEgY29udGFpbmVyIGFuZCB0aHVzIG5lZWQgdG8gaW5pdGlhbGl6ZSB0aGUgY2hpbGRyZW4gZWxlbWVudHMuIFxuICAgICAgICAgICAgaWYgKG9wdGlvbnMuY29udGFpbmVyICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICAkdGhpcy5maW5kKCcuZGF0ZXRpbWVwaWNrZXInKS5kYXRldGltZXBpY2tlcihvcHRpb25zKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJHRoaXMuZGF0ZXRpbWVwaWNrZXIob3B0aW9ucyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pOyAiXX0=

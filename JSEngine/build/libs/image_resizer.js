'use strict';

/* --------------------------------------------------------------
 image_resizer.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.image_resizer = jse.libs.image_resizer || {};

/**
 * ## Image Resizer Library
 *
 * Resizes images with respective aspect ratio.
 *
 * @module JSE/Libs/image_resizer
 * @exports jse.libs.image_resizer
 */
(function (exports) {

    'use strict';

    /**
     * Resize an image element with the provided width and height values.
     *
     * @param {string} element Selector string for the image element to be resized.
     * @param {object} options (optional) This object must contain the "width" and "height" properties.
     */

    exports.resize = function (element, options) {

        var $that = $(element);
        var settings = {
            width: 150,
            height: 150
        };
        options = $.extend(settings, options);

        var maxWidth = options.width;
        var maxHeight = options.height;
        var ratio = 0;
        var width = $that.width();
        var height = $that.height();

        if (width > maxWidth) {
            ratio = maxWidth / width;
            $that.css('width', maxWidth);
            $that.css('height', height * ratio);
        }

        if (height > maxHeight) {
            ratio = maxHeight / height;
            $that.css('height', maxHeight);
            $that.css('width', width * ratio);
        }
    };
})(jse.libs.image_resizer);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImltYWdlX3Jlc2l6ZXIuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsImltYWdlX3Jlc2l6ZXIiLCJleHBvcnRzIiwicmVzaXplIiwiZWxlbWVudCIsIm9wdGlvbnMiLCIkdGhhdCIsIiQiLCJzZXR0aW5ncyIsIndpZHRoIiwiaGVpZ2h0IiwiZXh0ZW5kIiwibWF4V2lkdGgiLCJtYXhIZWlnaHQiLCJyYXRpbyIsImNzcyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxJQUFJQyxJQUFKLENBQVNDLGFBQVQsR0FBeUJGLElBQUlDLElBQUosQ0FBU0MsYUFBVCxJQUEwQixFQUFuRDs7QUFFQTs7Ozs7Ozs7QUFRQSxDQUFDLFVBQVVDLE9BQVYsRUFBbUI7O0FBRWhCOztBQUVBOzs7Ozs7O0FBTUFBLFlBQVFDLE1BQVIsR0FBaUIsVUFBVUMsT0FBVixFQUFtQkMsT0FBbkIsRUFBNEI7O0FBRXpDLFlBQUlDLFFBQVFDLEVBQUVILE9BQUYsQ0FBWjtBQUNBLFlBQUlJLFdBQVc7QUFDWEMsbUJBQU8sR0FESTtBQUVYQyxvQkFBUTtBQUZHLFNBQWY7QUFJQUwsa0JBQVVFLEVBQUVJLE1BQUYsQ0FBU0gsUUFBVCxFQUFtQkgsT0FBbkIsQ0FBVjs7QUFFQSxZQUFJTyxXQUFXUCxRQUFRSSxLQUF2QjtBQUNBLFlBQUlJLFlBQVlSLFFBQVFLLE1BQXhCO0FBQ0EsWUFBSUksUUFBUSxDQUFaO0FBQ0EsWUFBSUwsUUFBUUgsTUFBTUcsS0FBTixFQUFaO0FBQ0EsWUFBSUMsU0FBU0osTUFBTUksTUFBTixFQUFiOztBQUVBLFlBQUlELFFBQVFHLFFBQVosRUFBc0I7QUFDbEJFLG9CQUFRRixXQUFXSCxLQUFuQjtBQUNBSCxrQkFBTVMsR0FBTixDQUFVLE9BQVYsRUFBbUJILFFBQW5CO0FBQ0FOLGtCQUFNUyxHQUFOLENBQVUsUUFBVixFQUFvQkwsU0FBU0ksS0FBN0I7QUFFSDs7QUFFRCxZQUFJSixTQUFTRyxTQUFiLEVBQXdCO0FBQ3BCQyxvQkFBUUQsWUFBWUgsTUFBcEI7QUFDQUosa0JBQU1TLEdBQU4sQ0FBVSxRQUFWLEVBQW9CRixTQUFwQjtBQUNBUCxrQkFBTVMsR0FBTixDQUFVLE9BQVYsRUFBbUJOLFFBQVFLLEtBQTNCO0FBRUg7QUFFSixLQTdCRDtBQStCSCxDQXpDRCxFQXlDR2YsSUFBSUMsSUFBSixDQUFTQyxhQXpDWiIsImZpbGUiOiJpbWFnZV9yZXNpemVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbWFnZV9yZXNpemVyLmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UubGlicy5pbWFnZV9yZXNpemVyID0ganNlLmxpYnMuaW1hZ2VfcmVzaXplciB8fCB7fTtcblxuLyoqXG4gKiAjIyBJbWFnZSBSZXNpemVyIExpYnJhcnlcbiAqXG4gKiBSZXNpemVzIGltYWdlcyB3aXRoIHJlc3BlY3RpdmUgYXNwZWN0IHJhdGlvLlxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvaW1hZ2VfcmVzaXplclxuICogQGV4cG9ydHMganNlLmxpYnMuaW1hZ2VfcmVzaXplclxuICovXG4oZnVuY3Rpb24gKGV4cG9ydHMpIHtcblxuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIC8qKlxuICAgICAqIFJlc2l6ZSBhbiBpbWFnZSBlbGVtZW50IHdpdGggdGhlIHByb3ZpZGVkIHdpZHRoIGFuZCBoZWlnaHQgdmFsdWVzLlxuICAgICAqXG4gICAgICogQHBhcmFtIHtzdHJpbmd9IGVsZW1lbnQgU2VsZWN0b3Igc3RyaW5nIGZvciB0aGUgaW1hZ2UgZWxlbWVudCB0byBiZSByZXNpemVkLlxuICAgICAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIChvcHRpb25hbCkgVGhpcyBvYmplY3QgbXVzdCBjb250YWluIHRoZSBcIndpZHRoXCIgYW5kIFwiaGVpZ2h0XCIgcHJvcGVydGllcy5cbiAgICAgKi9cbiAgICBleHBvcnRzLnJlc2l6ZSA9IGZ1bmN0aW9uIChlbGVtZW50LCBvcHRpb25zKSB7XG5cbiAgICAgICAgdmFyICR0aGF0ID0gJChlbGVtZW50KTtcbiAgICAgICAgdmFyIHNldHRpbmdzID0ge1xuICAgICAgICAgICAgd2lkdGg6IDE1MCxcbiAgICAgICAgICAgIGhlaWdodDogMTUwXG4gICAgICAgIH07XG4gICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZChzZXR0aW5ncywgb3B0aW9ucyk7XG5cbiAgICAgICAgdmFyIG1heFdpZHRoID0gb3B0aW9ucy53aWR0aDtcbiAgICAgICAgdmFyIG1heEhlaWdodCA9IG9wdGlvbnMuaGVpZ2h0O1xuICAgICAgICB2YXIgcmF0aW8gPSAwO1xuICAgICAgICB2YXIgd2lkdGggPSAkdGhhdC53aWR0aCgpO1xuICAgICAgICB2YXIgaGVpZ2h0ID0gJHRoYXQuaGVpZ2h0KCk7XG5cbiAgICAgICAgaWYgKHdpZHRoID4gbWF4V2lkdGgpIHtcbiAgICAgICAgICAgIHJhdGlvID0gbWF4V2lkdGggLyB3aWR0aDtcbiAgICAgICAgICAgICR0aGF0LmNzcygnd2lkdGgnLCBtYXhXaWR0aCk7XG4gICAgICAgICAgICAkdGhhdC5jc3MoJ2hlaWdodCcsIGhlaWdodCAqIHJhdGlvKTtcblxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGhlaWdodCA+IG1heEhlaWdodCkge1xuICAgICAgICAgICAgcmF0aW8gPSBtYXhIZWlnaHQgLyBoZWlnaHQ7XG4gICAgICAgICAgICAkdGhhdC5jc3MoJ2hlaWdodCcsIG1heEhlaWdodCk7XG4gICAgICAgICAgICAkdGhhdC5jc3MoJ3dpZHRoJywgd2lkdGggKiByYXRpbyk7XG5cbiAgICAgICAgfVxuXG4gICAgfTtcblxufSkoanNlLmxpYnMuaW1hZ2VfcmVzaXplcik7XG4iXX0=

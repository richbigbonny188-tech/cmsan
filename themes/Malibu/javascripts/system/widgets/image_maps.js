'use strict';

/* --------------------------------------------------------------
 image_maps.js 2015-07-22 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that searches for images with an image map and calls
 * a plugin on them, so that the image maps getting responsive
 */
gambio.widgets.module('image_maps', [jse.source + '/vendor/jQuery-rwdImageMaps/jquery.rwdImageMaps'], function () {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        module = {};

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $this.find('img[usemap]').rwdImageMaps();

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaW1hZ2VfbWFwcy5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwianNlIiwic291cmNlIiwiJHRoaXMiLCIkIiwiaW5pdCIsImRvbmUiLCJmaW5kIiwicndkSW1hZ2VNYXBzIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7QUFJQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksWUFESixFQUVJLENBQ09DLElBQUlDLE1BRFgscURBRkosRUFLSSxZQUFZOztBQUVSOztBQUVSOztBQUVRLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUosU0FBUyxFQURiOztBQUlSOztBQUVROzs7O0FBSUFBLFdBQU9LLElBQVAsR0FBYyxVQUFVQyxJQUFWLEVBQWdCOztBQUUxQkgsY0FDS0ksSUFETCxDQUNVLGFBRFYsRUFFS0MsWUFGTDs7QUFJQUY7QUFDSCxLQVBEOztBQVNBO0FBQ0EsV0FBT04sTUFBUDtBQUNILENBaENMIiwiZmlsZSI6IndpZGdldHMvaW1hZ2VfbWFwcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gaW1hZ2VfbWFwcy5qcyAyMDE1LTA3LTIyIGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCBzZWFyY2hlcyBmb3IgaW1hZ2VzIHdpdGggYW4gaW1hZ2UgbWFwIGFuZCBjYWxsc1xuICogYSBwbHVnaW4gb24gdGhlbSwgc28gdGhhdCB0aGUgaW1hZ2UgbWFwcyBnZXR0aW5nIHJlc3BvbnNpdmVcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdpbWFnZV9tYXBzJyxcbiAgICBbXG4gICAgICAgIGAke2pzZS5zb3VyY2V9L3ZlbmRvci9qUXVlcnktcndkSW1hZ2VNYXBzL2pxdWVyeS5yd2RJbWFnZU1hcHNgXG4gICAgXSxcbiAgICBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblxuICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAuZmluZCgnaW1nW3VzZW1hcF0nKVxuICAgICAgICAgICAgICAgIC5yd2RJbWFnZU1hcHMoKTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTsiXX0=

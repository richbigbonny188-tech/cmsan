'use strict';

/* --------------------------------------------------------------
 initialize_theme.js 2018-11-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Initialize Theme JS Environment
 *
 * This script will set some parameters needed by other javascript sections. Use it to configure or override code from
 * the JS Engine.
 */

jse.core.config = jse.core.config || {};

jse.libs.theme = {}; // Create new libs object for the theme libraries.

(function (exports) {

  'use strict';

  // Backup original "init" method.

  var init = jse.core.config.init;

  exports.init = function (jsEngineConfiguration) {
    jse.core.registry.set('mainModalLayer', 'magnific');
    jse.core.registry.set('tplPath', jsEngineConfiguration.tplPath);

    // Call original config file init.
    init(jsEngineConfiguration);
  };
})(jse.core.config);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluaXRpYWxpemVfdGhlbWUuanMiXSwibmFtZXMiOlsianNlIiwiY29yZSIsImNvbmZpZyIsImxpYnMiLCJ0aGVtZSIsImV4cG9ydHMiLCJpbml0IiwianNFbmdpbmVDb25maWd1cmF0aW9uIiwicmVnaXN0cnkiLCJzZXQiLCJ0cGxQYXRoIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsSUFBSUMsSUFBSixDQUFTQyxNQUFULEdBQWtCRixJQUFJQyxJQUFKLENBQVNDLE1BQVQsSUFBbUIsRUFBckM7O0FBRUFGLElBQUlHLElBQUosQ0FBU0MsS0FBVCxHQUFpQixFQUFqQixDLENBQXFCOztBQUVyQixDQUFDLFVBQVVDLE9BQVYsRUFBbUI7O0FBRWhCOztBQUVBOztBQUNBLE1BQUlDLE9BQU9OLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkksSUFBM0I7O0FBRUFELFVBQVFDLElBQVIsR0FBZSxVQUFVQyxxQkFBVixFQUFpQztBQUM1Q1AsUUFBSUMsSUFBSixDQUFTTyxRQUFULENBQWtCQyxHQUFsQixDQUFzQixnQkFBdEIsRUFBd0MsVUFBeEM7QUFDQVQsUUFBSUMsSUFBSixDQUFTTyxRQUFULENBQWtCQyxHQUFsQixDQUFzQixTQUF0QixFQUFpQ0Ysc0JBQXNCRyxPQUF2RDs7QUFFQTtBQUNBSixTQUFLQyxxQkFBTDtBQUNILEdBTkQ7QUFRSCxDQWZELEVBZUdQLElBQUlDLElBQUosQ0FBU0MsTUFmWiIsImZpbGUiOiJpbml0aWFsaXplX3RoZW1lLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbml0aWFsaXplX3RoZW1lLmpzIDIwMTgtMTEtMTNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE4IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIEluaXRpYWxpemUgVGhlbWUgSlMgRW52aXJvbm1lbnRcbiAqXG4gKiBUaGlzIHNjcmlwdCB3aWxsIHNldCBzb21lIHBhcmFtZXRlcnMgbmVlZGVkIGJ5IG90aGVyIGphdmFzY3JpcHQgc2VjdGlvbnMuIFVzZSBpdCB0byBjb25maWd1cmUgb3Igb3ZlcnJpZGUgY29kZSBmcm9tXG4gKiB0aGUgSlMgRW5naW5lLlxuICovXG5cbmpzZS5jb3JlLmNvbmZpZyA9IGpzZS5jb3JlLmNvbmZpZyB8fCB7fTtcblxuanNlLmxpYnMudGhlbWUgPSB7fTsgLy8gQ3JlYXRlIG5ldyBsaWJzIG9iamVjdCBmb3IgdGhlIHRoZW1lIGxpYnJhcmllcy5cblxuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvLyBCYWNrdXAgb3JpZ2luYWwgXCJpbml0XCIgbWV0aG9kLlxuICAgIHZhciBpbml0ID0ganNlLmNvcmUuY29uZmlnLmluaXQ7XG5cbiAgICBleHBvcnRzLmluaXQgPSBmdW5jdGlvbiAoanNFbmdpbmVDb25maWd1cmF0aW9uKSB7XG4gICAgICAgIGpzZS5jb3JlLnJlZ2lzdHJ5LnNldCgnbWFpbk1vZGFsTGF5ZXInLCAnbWFnbmlmaWMnKTtcbiAgICAgICAganNlLmNvcmUucmVnaXN0cnkuc2V0KCd0cGxQYXRoJywganNFbmdpbmVDb25maWd1cmF0aW9uLnRwbFBhdGgpO1xuXG4gICAgICAgIC8vIENhbGwgb3JpZ2luYWwgY29uZmlnIGZpbGUgaW5pdC5cbiAgICAgICAgaW5pdChqc0VuZ2luZUNvbmZpZ3VyYXRpb24pO1xuICAgIH07XG5cbn0pKGpzZS5jb3JlLmNvbmZpZyk7XG4iXX0=

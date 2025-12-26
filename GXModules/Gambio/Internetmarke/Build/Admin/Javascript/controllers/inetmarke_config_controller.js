'use strict';

/* --------------------------------------------------------------
 inetmarke_config_controller.js 2018-04-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gxmodules.controllers.module('inetmarke_config_controller', [], function (data) {
	'use strict';

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Default Options
  *
  * @type {object}
  */
	var defaults = {};

	/**
  * Final Options
  *
  * @type {object}
  */
	var options = $.extend(true, {}, defaults, data);

	/**
  * Module Object
  *
  * @type {object}
  */
	var module = {};

	module.init = function (done) {
		$('head').append($('<link rel="stylesheet" href="' + jse.core.config.get('appUrl') + '/admin/html/assets/styles/modules/internetmarke.min.css">'));

		$('#inetmarke_config div.imgcat').on('click', function () {
			var catid = $(this).data('catid');
			$('#inetmarke_config div.imgcat').removeClass('open');
			$(this).addClass('open');
			$('#inetmarke_config div.imglist').hide('fast');
			$('div.imglist#imglist_' + catid).show('fast');
		});
		var selected_catid = $('#inetmarke_config input[name="configuration[prefs_imageid]"]:checked').data('catid');
		$('#inetmarke_config div.imgcat[data-catid="' + selected_catid + '"]').trigger('click');

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvY29udHJvbGxlcnMvaW5ldG1hcmtlX2NvbmZpZ19jb250cm9sbGVyLmpzIl0sIm5hbWVzIjpbImd4bW9kdWxlcyIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwiYXBwZW5kIiwianNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsIm9uIiwiY2F0aWQiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiaGlkZSIsInNob3ciLCJzZWxlY3RlZF9jYXRpZCIsInRyaWdnZXIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsVUFBVUMsV0FBVixDQUFzQkMsTUFBdEIsQ0FDQyw2QkFERCxFQUdDLEVBSEQsRUFLQyxVQUFVQyxJQUFWLEVBQWdCO0FBQ2Y7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1DLFdBQVcsRUFBakI7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsVUFBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FBaEI7O0FBRUE7Ozs7O0FBS0EsS0FBTUQsU0FBUyxFQUFmOztBQUVBQSxRQUFPTyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCTCxJQUFFLE1BQUYsRUFBVU0sTUFBVixDQUFpQk4sRUFBRSxrQ0FBZ0NPLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsQ0FBaEMsR0FBOEQsMkRBQWhFLENBQWpCOztBQUVBVixJQUFFLDhCQUFGLEVBQWtDVyxFQUFsQyxDQUFxQyxPQUFyQyxFQUE4QyxZQUFXO0FBQ3hELE9BQUlDLFFBQVFaLEVBQUUsSUFBRixFQUFRRixJQUFSLENBQWEsT0FBYixDQUFaO0FBQ0FFLEtBQUUsOEJBQUYsRUFBa0NhLFdBQWxDLENBQThDLE1BQTlDO0FBQ0FiLEtBQUUsSUFBRixFQUFRYyxRQUFSLENBQWlCLE1BQWpCO0FBQ0FkLEtBQUUsK0JBQUYsRUFBbUNlLElBQW5DLENBQXdDLE1BQXhDO0FBQ0FmLEtBQUUseUJBQXVCWSxLQUF6QixFQUFnQ0ksSUFBaEMsQ0FBcUMsTUFBckM7QUFDQSxHQU5EO0FBT0EsTUFBSUMsaUJBQWlCakIsRUFBRSxzRUFBRixFQUEwRUYsSUFBMUUsQ0FBK0UsT0FBL0UsQ0FBckI7QUFDQUUsSUFBRSw4Q0FBNENpQixjQUE1QyxHQUEyRCxJQUE3RCxFQUFtRUMsT0FBbkUsQ0FBMkUsT0FBM0U7O0FBRUFiO0FBQ0EsRUFkRDs7QUFnQkEsUUFBT1IsTUFBUDtBQUNBLENBckRGIiwiZmlsZSI6IkFkbWluL0phdmFzY3JpcHQvY29udHJvbGxlcnMvaW5ldG1hcmtlX2NvbmZpZ19jb250cm9sbGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbmV0bWFya2VfY29uZmlnX2NvbnRyb2xsZXIuanMgMjAxOC0wNC0wNFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmd4bW9kdWxlcy5jb250cm9sbGVycy5tb2R1bGUoXG5cdCdpbmV0bWFya2VfY29uZmlnX2NvbnRyb2xsZXInLFxuXG5cdFtdLFxuXG5cdGZ1bmN0aW9uIChkYXRhKSB7XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdCAqXG5cdFx0ICogQHR5cGUge2pRdWVyeX1cblx0XHQgKi9cblx0XHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0ICpcblx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdCAqL1xuXHRcdGNvbnN0IGRlZmF1bHRzID0ge307XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdCAqXG5cdFx0ICogQHR5cGUge29iamVjdH1cblx0XHQgKi9cblx0XHRjb25zdCBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0ICpcblx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdCAqL1xuXHRcdGNvbnN0IG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JCgnaGVhZCcpLmFwcGVuZCgkKCc8bGluayByZWw9XCJzdHlsZXNoZWV0XCIgaHJlZj1cIicranNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykrJy9hZG1pbi9odG1sL2Fzc2V0cy9zdHlsZXMvbW9kdWxlcy9pbnRlcm5ldG1hcmtlLm1pbi5jc3NcIj4nKSk7XG5cblx0XHRcdCQoJyNpbmV0bWFya2VfY29uZmlnIGRpdi5pbWdjYXQnKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyIGNhdGlkID0gJCh0aGlzKS5kYXRhKCdjYXRpZCcpO1xuXHRcdFx0XHQkKCcjaW5ldG1hcmtlX2NvbmZpZyBkaXYuaW1nY2F0JykucmVtb3ZlQ2xhc3MoJ29wZW4nKTtcblx0XHRcdFx0JCh0aGlzKS5hZGRDbGFzcygnb3BlbicpO1xuXHRcdFx0XHQkKCcjaW5ldG1hcmtlX2NvbmZpZyBkaXYuaW1nbGlzdCcpLmhpZGUoJ2Zhc3QnKTtcblx0XHRcdFx0JCgnZGl2LmltZ2xpc3QjaW1nbGlzdF8nK2NhdGlkKS5zaG93KCdmYXN0Jyk7XG5cdFx0XHR9KTtcblx0XHRcdHZhciBzZWxlY3RlZF9jYXRpZCA9ICQoJyNpbmV0bWFya2VfY29uZmlnIGlucHV0W25hbWU9XCJjb25maWd1cmF0aW9uW3ByZWZzX2ltYWdlaWRdXCJdOmNoZWNrZWQnKS5kYXRhKCdjYXRpZCcpO1xuXHRcdFx0JCgnI2luZXRtYXJrZV9jb25maWcgZGl2LmltZ2NhdFtkYXRhLWNhdGlkPVwiJytzZWxlY3RlZF9jYXRpZCsnXCJdJykudHJpZ2dlcignY2xpY2snKTtcblxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9XG4pO1xuIl19

'use strict';

/* --------------------------------------------------------------
 disable_other_order_edit_buttons.js 2017-11-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Disables the rest order edit buttons (address and payment, shipping and sums).
 */
(function () {
	'use strict';

	/**
  * Initializes the module.
  *
  * @private
  */

	var init = function init() {
		var $links = $('.page-nav-tabs a');

		$links.each(function (index, link) {
			$(link).css({
				'opacity': 0.5,
				'background': '#FFF'
			}).attr('href', '#').off('click');
		});
	};

	KlarnaHub.on('ready', function () {
		return init();
	});
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIvb3JkZXJfZWRpdC9kaXNhYmxlX290aGVyX29yZGVyX2VkaXRfYnV0dG9ucy5qcyJdLCJuYW1lcyI6WyJpbml0IiwiJGxpbmtzIiwiJCIsImVhY2giLCJpbmRleCIsImxpbmsiLCJjc3MiLCJhdHRyIiwib2ZmIiwiS2xhcm5hSHViIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0EsQ0FBQyxZQUFXO0FBQ1g7O0FBRUE7Ozs7OztBQUtBLEtBQU1BLE9BQU8sU0FBUEEsSUFBTyxHQUFNO0FBQ2xCLE1BQU1DLFNBQVNDLEVBQUUsa0JBQUYsQ0FBZjs7QUFFQUQsU0FBT0UsSUFBUCxDQUFZLFVBQUNDLEtBQUQsRUFBUUMsSUFBUixFQUFpQjtBQUM1QkgsS0FBRUcsSUFBRixFQUNFQyxHQURGLENBQ007QUFDSixlQUFXLEdBRFA7QUFFSixrQkFBYztBQUZWLElBRE4sRUFLRUMsSUFMRixDQUtPLE1BTFAsRUFLZSxHQUxmLEVBTUVDLEdBTkYsQ0FNTSxPQU5OO0FBT0EsR0FSRDtBQVNBLEVBWkQ7O0FBY0FDLFdBQVVDLEVBQVYsQ0FBYSxPQUFiLEVBQXNCO0FBQUEsU0FBTVYsTUFBTjtBQUFBLEVBQXRCO0FBQ0EsQ0F2QkQiLCJmaWxlIjoiQWRtaW4vSmF2YXNjcmlwdC9leHRlbmRlcnMva2xhcm5hX2h1Yi9vcmRlcl9lZGl0L2Rpc2FibGVfb3RoZXJfb3JkZXJfZWRpdF9idXR0b25zLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBkaXNhYmxlX290aGVyX29yZGVyX2VkaXRfYnV0dG9ucy5qcyAyMDE3LTExLTAzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNyBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBEaXNhYmxlcyB0aGUgcmVzdCBvcmRlciBlZGl0IGJ1dHRvbnMgKGFkZHJlc3MgYW5kIHBheW1lbnQsIHNoaXBwaW5nIGFuZCBzdW1zKS5cbiAqL1xuKGZ1bmN0aW9uKCkge1xuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogSW5pdGlhbGl6ZXMgdGhlIG1vZHVsZS5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdGNvbnN0IGluaXQgPSAoKSA9PiB7XG5cdFx0Y29uc3QgJGxpbmtzID0gJCgnLnBhZ2UtbmF2LXRhYnMgYScpO1xuXHRcdFxuXHRcdCRsaW5rcy5lYWNoKChpbmRleCwgbGluaykgPT4ge1xuXHRcdFx0JChsaW5rKVxuXHRcdFx0XHQuY3NzKHtcblx0XHRcdFx0XHQnb3BhY2l0eSc6IDAuNSxcblx0XHRcdFx0XHQnYmFja2dyb3VuZCc6ICcjRkZGJyxcblx0XHRcdFx0fSlcblx0XHRcdFx0LmF0dHIoJ2hyZWYnLCAnIycpXG5cdFx0XHRcdC5vZmYoJ2NsaWNrJyk7XG5cdFx0fSk7XG5cdH07XG5cdFxuXHRLbGFybmFIdWIub24oJ3JlYWR5JywgKCkgPT4gaW5pdCgpKTtcbn0pKCk7Il19

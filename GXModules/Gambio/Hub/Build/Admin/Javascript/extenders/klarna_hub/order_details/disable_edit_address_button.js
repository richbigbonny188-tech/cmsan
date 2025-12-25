'use strict';

/* --------------------------------------------------------------
 disable_edit_address_button.js 2017-11-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Disables the order address edit button.
 */
(function () {
	'use strict';

	/**
  * Initializes the module.
  * 
  * @private
  */

	var init = function init() {
		var $links = $('.frame-head .head-link a');

		$links.each(function (index, link) {
			var $link = $(link);

			if (!$link.attr('href').includes('edit_action=address')) {
				return true;
			}

			$link.parent().append($('<span/>', {
				'text': $link.text().trim()
			})).css({
				'opacity': .6,
				'color': 'gray',
				'background': 'none',
				'cursor': 'not-allowed'
			});

			$link.remove();

			return false;
		});
	};

	KlarnaHub.on('ready', function () {
		return init();
	});
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIvb3JkZXJfZGV0YWlscy9kaXNhYmxlX2VkaXRfYWRkcmVzc19idXR0b24uanMiXSwibmFtZXMiOlsiaW5pdCIsIiRsaW5rcyIsIiQiLCJlYWNoIiwiaW5kZXgiLCJsaW5rIiwiJGxpbmsiLCJhdHRyIiwiaW5jbHVkZXMiLCJwYXJlbnQiLCJhcHBlbmQiLCJ0ZXh0IiwidHJpbSIsImNzcyIsInJlbW92ZSIsIktsYXJuYUh1YiIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBLENBQUMsWUFBVztBQUNYOztBQUVBOzs7Ozs7QUFLQSxLQUFNQSxPQUFPLFNBQVBBLElBQU8sR0FBTTtBQUNsQixNQUFNQyxTQUFTQyxFQUFFLDBCQUFGLENBQWY7O0FBRUFELFNBQU9FLElBQVAsQ0FBWSxVQUFDQyxLQUFELEVBQVFDLElBQVIsRUFBaUI7QUFDNUIsT0FBTUMsUUFBUUosRUFBRUcsSUFBRixDQUFkOztBQUVBLE9BQUksQ0FBQ0MsTUFBTUMsSUFBTixDQUFXLE1BQVgsRUFBbUJDLFFBQW5CLENBQTRCLHFCQUE1QixDQUFMLEVBQXlEO0FBQ3hELFdBQU8sSUFBUDtBQUNBOztBQUVERixTQUNFRyxNQURGLEdBRUVDLE1BRkYsQ0FHRVIsRUFBRSxTQUFGLEVBQWE7QUFDWixZQUFRSSxNQUFNSyxJQUFOLEdBQWFDLElBQWI7QUFESSxJQUFiLENBSEYsRUFPRUMsR0FQRixDQU9NO0FBQ0osZUFBVyxFQURQO0FBRUosYUFBUyxNQUZMO0FBR0osa0JBQWMsTUFIVjtBQUlKLGNBQVU7QUFKTixJQVBOOztBQWNBUCxTQUFNUSxNQUFOOztBQUVBLFVBQU8sS0FBUDtBQUNBLEdBeEJEO0FBeUJBLEVBNUJEOztBQThCQUMsV0FBVUMsRUFBVixDQUFhLE9BQWIsRUFBc0I7QUFBQSxTQUFNaEIsTUFBTjtBQUFBLEVBQXRCO0FBQ0EsQ0F2Q0QiLCJmaWxlIjoiQWRtaW4vSmF2YXNjcmlwdC9leHRlbmRlcnMva2xhcm5hX2h1Yi9vcmRlcl9kZXRhaWxzL2Rpc2FibGVfZWRpdF9hZGRyZXNzX2J1dHRvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZGlzYWJsZV9lZGl0X2FkZHJlc3NfYnV0dG9uLmpzIDIwMTctMTEtMDJcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE3IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIERpc2FibGVzIHRoZSBvcmRlciBhZGRyZXNzIGVkaXQgYnV0dG9uLlxuICovXG4oZnVuY3Rpb24oKSB7XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8qKlxuXHQgKiBJbml0aWFsaXplcyB0aGUgbW9kdWxlLlxuXHQgKiBcblx0ICogQHByaXZhdGVcblx0ICovXG5cdGNvbnN0IGluaXQgPSAoKSA9PiB7XG5cdFx0Y29uc3QgJGxpbmtzID0gJCgnLmZyYW1lLWhlYWQgLmhlYWQtbGluayBhJyk7XG5cdFx0XG5cdFx0JGxpbmtzLmVhY2goKGluZGV4LCBsaW5rKSA9PiB7XG5cdFx0XHRjb25zdCAkbGluayA9ICQobGluayk7IFxuXHRcdFx0XG5cdFx0XHRpZiAoISRsaW5rLmF0dHIoJ2hyZWYnKS5pbmNsdWRlcygnZWRpdF9hY3Rpb249YWRkcmVzcycpKSB7XG5cdFx0XHRcdHJldHVybiB0cnVlO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkbGlua1xuXHRcdFx0XHQucGFyZW50KClcblx0XHRcdFx0LmFwcGVuZChcblx0XHRcdFx0XHQkKCc8c3Bhbi8+Jywge1xuXHRcdFx0XHRcdFx0J3RleHQnOiAkbGluay50ZXh0KCkudHJpbSgpXG5cdFx0XHRcdFx0fSlcblx0XHRcdFx0KVxuXHRcdFx0XHQuY3NzKHtcblx0XHRcdFx0XHQnb3BhY2l0eSc6IC42LFxuXHRcdFx0XHRcdCdjb2xvcic6ICdncmF5Jyxcblx0XHRcdFx0XHQnYmFja2dyb3VuZCc6ICdub25lJyxcblx0XHRcdFx0XHQnY3Vyc29yJzogJ25vdC1hbGxvd2VkJ1xuXHRcdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0JGxpbmsucmVtb3ZlKCk7XG5cdFx0XHRcblx0XHRcdHJldHVybiBmYWxzZTtcblx0XHR9KTtcblx0fTtcblx0XG5cdEtsYXJuYUh1Yi5vbigncmVhZHknLCAoKSA9PiBpbml0KCkpO1xufSkoKTsgIl19

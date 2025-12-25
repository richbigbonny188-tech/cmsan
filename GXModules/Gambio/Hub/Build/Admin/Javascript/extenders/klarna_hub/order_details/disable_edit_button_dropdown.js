'use strict';

/* --------------------------------------------------------------
 disable_edit_button_dropdown.js 2017-11-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Disables the order edit action button.
 */
(function () {
	'use strict';

	/**
  * Initializes the module.
  *
  * @private
  */

	var init = function init() {
		$('.js-button-dropdown li').each(function (index, li) {
			var $li = $(li);

			var onclickAttribute = $li.attr('onclick');

			if (onclickAttribute && onclickAttribute.includes('orders_edit.php')) {
				$li.remove();
			}
		});
	};

	KlarnaHub.on('ready', function () {
		return init();
	});
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIvb3JkZXJfZGV0YWlscy9kaXNhYmxlX2VkaXRfYnV0dG9uX2Ryb3Bkb3duLmpzIl0sIm5hbWVzIjpbImluaXQiLCIkIiwiZWFjaCIsImluZGV4IiwibGkiLCIkbGkiLCJvbmNsaWNrQXR0cmlidXRlIiwiYXR0ciIsImluY2x1ZGVzIiwicmVtb3ZlIiwiS2xhcm5hSHViIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0EsQ0FBQyxZQUFXO0FBQ1g7O0FBRUE7Ozs7OztBQUtBLEtBQU1BLE9BQU8sU0FBUEEsSUFBTyxHQUFNO0FBQ2xCQyxJQUFFLHdCQUFGLEVBQTRCQyxJQUE1QixDQUFpQyxVQUFDQyxLQUFELEVBQVFDLEVBQVIsRUFBZTtBQUMvQyxPQUFNQyxNQUFNSixFQUFFRyxFQUFGLENBQVo7O0FBRUEsT0FBTUUsbUJBQW1CRCxJQUFJRSxJQUFKLENBQVMsU0FBVCxDQUF6Qjs7QUFFQSxPQUFJRCxvQkFBb0JBLGlCQUFpQkUsUUFBakIsQ0FBMEIsaUJBQTFCLENBQXhCLEVBQXNFO0FBQ3JFSCxRQUFJSSxNQUFKO0FBQ0E7QUFDRCxHQVJEO0FBU0EsRUFWRDs7QUFZQUMsV0FBVUMsRUFBVixDQUFhLE9BQWIsRUFBc0I7QUFBQSxTQUFNWCxNQUFOO0FBQUEsRUFBdEI7QUFDQSxDQXJCRCIsImZpbGUiOiJBZG1pbi9KYXZhc2NyaXB0L2V4dGVuZGVycy9rbGFybmFfaHViL29yZGVyX2RldGFpbHMvZGlzYWJsZV9lZGl0X2J1dHRvbl9kcm9wZG93bi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZGlzYWJsZV9lZGl0X2J1dHRvbl9kcm9wZG93bi5qcyAyMDE3LTExLTAyXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNyBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBEaXNhYmxlcyB0aGUgb3JkZXIgZWRpdCBhY3Rpb24gYnV0dG9uLlxuICovXG4oZnVuY3Rpb24oKSB7XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8qKlxuXHQgKiBJbml0aWFsaXplcyB0aGUgbW9kdWxlLlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0Y29uc3QgaW5pdCA9ICgpID0+IHtcblx0XHQkKCcuanMtYnV0dG9uLWRyb3Bkb3duIGxpJykuZWFjaCgoaW5kZXgsIGxpKSA9PiB7XG5cdFx0XHRjb25zdCAkbGkgPSAkKGxpKTtcblx0XHRcdFxuXHRcdFx0Y29uc3Qgb25jbGlja0F0dHJpYnV0ZSA9ICRsaS5hdHRyKCdvbmNsaWNrJyk7XG5cdFx0XHRcblx0XHRcdGlmIChvbmNsaWNrQXR0cmlidXRlICYmIG9uY2xpY2tBdHRyaWJ1dGUuaW5jbHVkZXMoJ29yZGVyc19lZGl0LnBocCcpKSB7XG5cdFx0XHRcdCRsaS5yZW1vdmUoKTtcblx0XHRcdH1cblx0XHR9KTtcblx0fTtcblx0XG5cdEtsYXJuYUh1Yi5vbigncmVhZHknLCAoKSA9PiBpbml0KCkpO1xufSkoKTsgIl19

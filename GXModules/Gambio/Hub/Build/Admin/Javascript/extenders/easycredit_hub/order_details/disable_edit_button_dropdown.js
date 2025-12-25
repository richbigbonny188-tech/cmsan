'use strict';

/* --------------------------------------------------------------
 disable_edit_button_dropdown.js 2020-06-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
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

	EasyCreditHub.on('ready', function () {
		return init();
	});
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2Vhc3ljcmVkaXRfaHViL29yZGVyX2RldGFpbHMvZGlzYWJsZV9lZGl0X2J1dHRvbl9kcm9wZG93bi5qcyJdLCJuYW1lcyI6WyJpbml0IiwiJCIsImVhY2giLCJpbmRleCIsImxpIiwiJGxpIiwib25jbGlja0F0dHJpYnV0ZSIsImF0dHIiLCJpbmNsdWRlcyIsInJlbW92ZSIsIkVhc3lDcmVkaXRIdWIiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7QUFHQSxDQUFDLFlBQVc7QUFDWDs7QUFFQTs7Ozs7O0FBS0EsS0FBTUEsT0FBTyxTQUFQQSxJQUFPLEdBQU07QUFDbEJDLElBQUUsd0JBQUYsRUFBNEJDLElBQTVCLENBQWlDLFVBQUNDLEtBQUQsRUFBUUMsRUFBUixFQUFlO0FBQy9DLE9BQU1DLE1BQU1KLEVBQUVHLEVBQUYsQ0FBWjs7QUFFQSxPQUFNRSxtQkFBbUJELElBQUlFLElBQUosQ0FBUyxTQUFULENBQXpCOztBQUVBLE9BQUlELG9CQUFvQkEsaUJBQWlCRSxRQUFqQixDQUEwQixpQkFBMUIsQ0FBeEIsRUFBc0U7QUFDckVILFFBQUlJLE1BQUo7QUFDQTtBQUNELEdBUkQ7QUFTQSxFQVZEOztBQVlBQyxlQUFjQyxFQUFkLENBQWlCLE9BQWpCLEVBQTBCO0FBQUEsU0FBTVgsTUFBTjtBQUFBLEVBQTFCO0FBQ0EsQ0FyQkQiLCJmaWxlIjoiQWRtaW4vSmF2YXNjcmlwdC9leHRlbmRlcnMvZWFzeWNyZWRpdF9odWIvb3JkZXJfZGV0YWlscy9kaXNhYmxlX2VkaXRfYnV0dG9uX2Ryb3Bkb3duLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBkaXNhYmxlX2VkaXRfYnV0dG9uX2Ryb3Bkb3duLmpzIDIwMjAtMDYtMTZcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDIwIEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIERpc2FibGVzIHRoZSBvcmRlciBlZGl0IGFjdGlvbiBidXR0b24uXG4gKi9cbihmdW5jdGlvbigpIHtcblx0J3VzZSBzdHJpY3QnO1xuXHRcblx0LyoqXG5cdCAqIEluaXRpYWxpemVzIHRoZSBtb2R1bGUuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHRjb25zdCBpbml0ID0gKCkgPT4ge1xuXHRcdCQoJy5qcy1idXR0b24tZHJvcGRvd24gbGknKS5lYWNoKChpbmRleCwgbGkpID0+IHtcblx0XHRcdGNvbnN0ICRsaSA9ICQobGkpO1xuXHRcdFx0XG5cdFx0XHRjb25zdCBvbmNsaWNrQXR0cmlidXRlID0gJGxpLmF0dHIoJ29uY2xpY2snKTtcblx0XHRcdFxuXHRcdFx0aWYgKG9uY2xpY2tBdHRyaWJ1dGUgJiYgb25jbGlja0F0dHJpYnV0ZS5pbmNsdWRlcygnb3JkZXJzX2VkaXQucGhwJykpIHtcblx0XHRcdFx0JGxpLnJlbW92ZSgpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXHR9O1xuXHRcblx0RWFzeUNyZWRpdEh1Yi5vbigncmVhZHknLCAoKSA9PiBpbml0KCkpO1xufSkoKTsgXG4iXX0=

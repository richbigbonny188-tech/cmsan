'use strict';

/* --------------------------------------------------------------
 order_overview.js 2016-12-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gambio Hub Order Overview Module
 *
 * This file will make sure that the order overview page will display the correct order data. It can be included through
 * the use of an extender.
 *
 * It requires an element with the "data-gambio-hub-source-selector" which contains the selector of a script
 * tag with the payment module information. This script tag must have the type "application/json" and must contain
 * a JSON object with "orderId": "gambioHubPaymentModule" key-value pairs.
 */
(function () {

	'use strict';

	// Initialize the page modifications.

	function initialize() {
		var sourceSelector = $('[data-gambio-hub-source-selector]').data('gambioHubSourceSelector');
		var paymentModules = JSON.parse($(sourceSelector).text());

		// Iterate through the table entries and correct the payment method values. 
		var $table = $('.dataTableHeadingRow').parents('table');

		$table.find('.dataTableRow,.dataTableRowSelected').not('.dataTableHeadingRow').each(function (index, tr) {
			var $td = $(tr).find('[title="gambio_hub"]');
			var orderId = +$(tr).find('td:eq(1) a').text();

			if ($td.length && paymentModules[orderId]) {
				$td.text(paymentModules[orderId]);
			}
		});
	}

	// Initialize the module once the page is ready (without jQuery).
	if (document.readyState != 'loading') {
		initialize();
	} else {
		document.addEventListener('DOMContentLoaded', initialize);
	}
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL29yZGVyX292ZXJ2aWV3LmpzIl0sIm5hbWVzIjpbImluaXRpYWxpemUiLCJzb3VyY2VTZWxlY3RvciIsIiQiLCJkYXRhIiwicGF5bWVudE1vZHVsZXMiLCJKU09OIiwicGFyc2UiLCJ0ZXh0IiwiJHRhYmxlIiwicGFyZW50cyIsImZpbmQiLCJub3QiLCJlYWNoIiwiaW5kZXgiLCJ0ciIsIiR0ZCIsIm9yZGVySWQiLCJsZW5ndGgiLCJkb2N1bWVudCIsInJlYWR5U3RhdGUiLCJhZGRFdmVudExpc3RlbmVyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7QUFVQSxDQUFDLFlBQVc7O0FBRVg7O0FBRUE7O0FBQ0EsVUFBU0EsVUFBVCxHQUFzQjtBQUNyQixNQUFJQyxpQkFBaUJDLEVBQUUsbUNBQUYsRUFBdUNDLElBQXZDLENBQTRDLHlCQUE1QyxDQUFyQjtBQUNBLE1BQUlDLGlCQUFpQkMsS0FBS0MsS0FBTCxDQUFXSixFQUFFRCxjQUFGLEVBQWtCTSxJQUFsQixFQUFYLENBQXJCOztBQUVBO0FBQ0EsTUFBSUMsU0FBU04sRUFBRSxzQkFBRixFQUEwQk8sT0FBMUIsQ0FBa0MsT0FBbEMsQ0FBYjs7QUFFQUQsU0FBT0UsSUFBUCxDQUFZLHFDQUFaLEVBQW1EQyxHQUFuRCxDQUF1RCxzQkFBdkQsRUFBK0VDLElBQS9FLENBQW9GLFVBQVNDLEtBQVQsRUFBZ0JDLEVBQWhCLEVBQW9CO0FBQ3ZHLE9BQUlDLE1BQU1iLEVBQUVZLEVBQUYsRUFBTUosSUFBTixDQUFXLHNCQUFYLENBQVY7QUFDQSxPQUFJTSxVQUFVLENBQUNkLEVBQUVZLEVBQUYsRUFBTUosSUFBTixDQUFXLFlBQVgsRUFBeUJILElBQXpCLEVBQWY7O0FBRUEsT0FBSVEsSUFBSUUsTUFBSixJQUFjYixlQUFlWSxPQUFmLENBQWxCLEVBQTJDO0FBQzFDRCxRQUFJUixJQUFKLENBQVNILGVBQWVZLE9BQWYsQ0FBVDtBQUNBO0FBQ0QsR0FQRDtBQVFBOztBQUVEO0FBQ0EsS0FBSUUsU0FBU0MsVUFBVCxJQUF1QixTQUEzQixFQUFzQztBQUNyQ25CO0FBQ0EsRUFGRCxNQUVPO0FBQ05rQixXQUFTRSxnQkFBVCxDQUEwQixrQkFBMUIsRUFBOENwQixVQUE5QztBQUNBO0FBQ0QsQ0E1QkQiLCJmaWxlIjoiQWRtaW4vSmF2YXNjcmlwdC9leHRlbmRlcnMvb3JkZXJfb3ZlcnZpZXcuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIG9yZGVyX292ZXJ2aWV3LmpzIDIwMTYtMTItMjhcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIEdhbWJpbyBIdWIgT3JkZXIgT3ZlcnZpZXcgTW9kdWxlXG4gKlxuICogVGhpcyBmaWxlIHdpbGwgbWFrZSBzdXJlIHRoYXQgdGhlIG9yZGVyIG92ZXJ2aWV3IHBhZ2Ugd2lsbCBkaXNwbGF5IHRoZSBjb3JyZWN0IG9yZGVyIGRhdGEuIEl0IGNhbiBiZSBpbmNsdWRlZCB0aHJvdWdoXG4gKiB0aGUgdXNlIG9mIGFuIGV4dGVuZGVyLlxuICpcbiAqIEl0IHJlcXVpcmVzIGFuIGVsZW1lbnQgd2l0aCB0aGUgXCJkYXRhLWdhbWJpby1odWItc291cmNlLXNlbGVjdG9yXCIgd2hpY2ggY29udGFpbnMgdGhlIHNlbGVjdG9yIG9mIGEgc2NyaXB0XG4gKiB0YWcgd2l0aCB0aGUgcGF5bWVudCBtb2R1bGUgaW5mb3JtYXRpb24uIFRoaXMgc2NyaXB0IHRhZyBtdXN0IGhhdmUgdGhlIHR5cGUgXCJhcHBsaWNhdGlvbi9qc29uXCIgYW5kIG11c3QgY29udGFpblxuICogYSBKU09OIG9iamVjdCB3aXRoIFwib3JkZXJJZFwiOiBcImdhbWJpb0h1YlBheW1lbnRNb2R1bGVcIiBrZXktdmFsdWUgcGFpcnMuXG4gKi9cbihmdW5jdGlvbigpIHtcblx0XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8vIEluaXRpYWxpemUgdGhlIHBhZ2UgbW9kaWZpY2F0aW9ucy5cblx0ZnVuY3Rpb24gaW5pdGlhbGl6ZSgpIHtcblx0XHR2YXIgc291cmNlU2VsZWN0b3IgPSAkKCdbZGF0YS1nYW1iaW8taHViLXNvdXJjZS1zZWxlY3Rvcl0nKS5kYXRhKCdnYW1iaW9IdWJTb3VyY2VTZWxlY3RvcicpO1xuXHRcdHZhciBwYXltZW50TW9kdWxlcyA9IEpTT04ucGFyc2UoJChzb3VyY2VTZWxlY3RvcikudGV4dCgpKTtcblx0XHRcblx0XHQvLyBJdGVyYXRlIHRocm91Z2ggdGhlIHRhYmxlIGVudHJpZXMgYW5kIGNvcnJlY3QgdGhlIHBheW1lbnQgbWV0aG9kIHZhbHVlcy4gXG5cdFx0dmFyICR0YWJsZSA9ICQoJy5kYXRhVGFibGVIZWFkaW5nUm93JykucGFyZW50cygndGFibGUnKTtcblx0XHRcblx0XHQkdGFibGUuZmluZCgnLmRhdGFUYWJsZVJvdywuZGF0YVRhYmxlUm93U2VsZWN0ZWQnKS5ub3QoJy5kYXRhVGFibGVIZWFkaW5nUm93JykuZWFjaChmdW5jdGlvbihpbmRleCwgdHIpIHtcblx0XHRcdHZhciAkdGQgPSAkKHRyKS5maW5kKCdbdGl0bGU9XCJnYW1iaW9faHViXCJdJyk7XG5cdFx0XHR2YXIgb3JkZXJJZCA9ICskKHRyKS5maW5kKCd0ZDplcSgxKSBhJykudGV4dCgpO1xuXHRcdFx0XG5cdFx0XHRpZiAoJHRkLmxlbmd0aCAmJiBwYXltZW50TW9kdWxlc1tvcmRlcklkXSkge1xuXHRcdFx0XHQkdGQudGV4dChwYXltZW50TW9kdWxlc1tvcmRlcklkXSk7XG5cdFx0XHR9XG5cdFx0fSk7XG5cdH1cblx0XG5cdC8vIEluaXRpYWxpemUgdGhlIG1vZHVsZSBvbmNlIHRoZSBwYWdlIGlzIHJlYWR5ICh3aXRob3V0IGpRdWVyeSkuXG5cdGlmIChkb2N1bWVudC5yZWFkeVN0YXRlICE9ICdsb2FkaW5nJykge1xuXHRcdGluaXRpYWxpemUoKTtcblx0fSBlbHNlIHtcblx0XHRkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgaW5pdGlhbGl6ZSk7XG5cdH1cbn0pKCk7Il19

'use strict';

/* --------------------------------------------------------------
 update_order_lines.js 2017-12-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Triggers the update of the shop order lines recalculation so that both Klarna and shop share the same price amounts.
 */
$(function () {
	'use strict';

	/**
  * Initializes the module.
  *
  * @private
  */

	var init = function init() {
		var orderId = KlarnaHub.Lib.getUrlParameter('oID');

		if (!orderId) {
			return;
		}

		var $orderDetailsTable = $('#order-details-table');

		var $spinner = jse.libs.loading_spinner.show($orderDetailsTable);

		var url = 'orders_edit.php?action=save_order&oID=' + orderId;

		var data = {
			customers_status_id: '',
			oID: orderId,
			cID: '',
			recalculate: '1'
		};

		$.post(url, data).done(function (response) {
			var $html = $(response);

			$orderDetailsTable.replaceWith($html.find('#order-details-table').parent().html());

			jse.libs.loading_spinner.hide($spinner);
		});
	};

	KlarnaHub.on('ready', function () {
		return init();
	});
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIvb3JkZXJfZGV0YWlscy91cGRhdGVfb3JkZXJfbGluZXMuanMiXSwibmFtZXMiOlsiJCIsImluaXQiLCJvcmRlcklkIiwiS2xhcm5hSHViIiwiTGliIiwiZ2V0VXJsUGFyYW1ldGVyIiwiJG9yZGVyRGV0YWlsc1RhYmxlIiwiJHNwaW5uZXIiLCJqc2UiLCJsaWJzIiwibG9hZGluZ19zcGlubmVyIiwic2hvdyIsInVybCIsImRhdGEiLCJjdXN0b21lcnNfc3RhdHVzX2lkIiwib0lEIiwiY0lEIiwicmVjYWxjdWxhdGUiLCJwb3N0IiwiZG9uZSIsInJlc3BvbnNlIiwiJGh0bWwiLCJyZXBsYWNlV2l0aCIsImZpbmQiLCJwYXJlbnQiLCJodG1sIiwiaGlkZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxFQUFFLFlBQVc7QUFDWjs7QUFFQTs7Ozs7O0FBS0EsS0FBTUMsT0FBTyxTQUFQQSxJQUFPLEdBQU07QUFDbEIsTUFBTUMsVUFBVUMsVUFBVUMsR0FBVixDQUFjQyxlQUFkLENBQThCLEtBQTlCLENBQWhCOztBQUVBLE1BQUksQ0FBQ0gsT0FBTCxFQUFjO0FBQ2I7QUFDQTs7QUFFRCxNQUFNSSxxQkFBcUJOLEVBQUUsc0JBQUYsQ0FBM0I7O0FBRUEsTUFBTU8sV0FBV0MsSUFBSUMsSUFBSixDQUFTQyxlQUFULENBQXlCQyxJQUF6QixDQUE4Qkwsa0JBQTlCLENBQWpCOztBQUVBLE1BQU1NLE1BQU0sMkNBQTJDVixPQUF2RDs7QUFFQSxNQUFNVyxPQUFPO0FBQ1pDLHdCQUFxQixFQURUO0FBRVpDLFFBQUtiLE9BRk87QUFHWmMsUUFBSyxFQUhPO0FBSVpDLGdCQUFhO0FBSkQsR0FBYjs7QUFPQWpCLElBQUVrQixJQUFGLENBQU9OLEdBQVAsRUFBWUMsSUFBWixFQUNFTSxJQURGLENBQ08sVUFBQ0MsUUFBRCxFQUFjO0FBQ25CLE9BQU1DLFFBQVFyQixFQUFFb0IsUUFBRixDQUFkOztBQUVBZCxzQkFBbUJnQixXQUFuQixDQUErQkQsTUFBTUUsSUFBTixDQUFXLHNCQUFYLEVBQW1DQyxNQUFuQyxHQUE0Q0MsSUFBNUMsRUFBL0I7O0FBRUFqQixPQUFJQyxJQUFKLENBQVNDLGVBQVQsQ0FBeUJnQixJQUF6QixDQUE4Qm5CLFFBQTlCO0FBQ0EsR0FQRjtBQVFBLEVBNUJEOztBQThCQUosV0FBVXdCLEVBQVYsQ0FBYSxPQUFiLEVBQXNCO0FBQUEsU0FBTTFCLE1BQU47QUFBQSxFQUF0QjtBQUNBLENBdkNEIiwiZmlsZSI6IkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIvb3JkZXJfZGV0YWlscy91cGRhdGVfb3JkZXJfbGluZXMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHVwZGF0ZV9vcmRlcl9saW5lcy5qcyAyMDE3LTEyLTE4XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNyBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBUcmlnZ2VycyB0aGUgdXBkYXRlIG9mIHRoZSBzaG9wIG9yZGVyIGxpbmVzIHJlY2FsY3VsYXRpb24gc28gdGhhdCBib3RoIEtsYXJuYSBhbmQgc2hvcCBzaGFyZSB0aGUgc2FtZSBwcmljZSBhbW91bnRzLlxuICovXG4kKGZ1bmN0aW9uKCkge1xuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogSW5pdGlhbGl6ZXMgdGhlIG1vZHVsZS5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdGNvbnN0IGluaXQgPSAoKSA9PiB7XG5cdFx0Y29uc3Qgb3JkZXJJZCA9IEtsYXJuYUh1Yi5MaWIuZ2V0VXJsUGFyYW1ldGVyKCdvSUQnKTtcblx0XHRcblx0XHRpZiAoIW9yZGVySWQpIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cdFx0XG5cdFx0Y29uc3QgJG9yZGVyRGV0YWlsc1RhYmxlID0gJCgnI29yZGVyLWRldGFpbHMtdGFibGUnKTtcblx0XHRcblx0XHRjb25zdCAkc3Bpbm5lciA9IGpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lci5zaG93KCRvcmRlckRldGFpbHNUYWJsZSk7XG5cdFx0XG5cdFx0Y29uc3QgdXJsID0gJ29yZGVyc19lZGl0LnBocD9hY3Rpb249c2F2ZV9vcmRlciZvSUQ9JyArIG9yZGVySWQ7XG5cdFx0XG5cdFx0Y29uc3QgZGF0YSA9IHtcblx0XHRcdGN1c3RvbWVyc19zdGF0dXNfaWQ6ICcnLFxuXHRcdFx0b0lEOiBvcmRlcklkLFxuXHRcdFx0Y0lEOiAnJyxcblx0XHRcdHJlY2FsY3VsYXRlOiAnMSdcblx0XHR9O1xuXHRcdFxuXHRcdCQucG9zdCh1cmwsIGRhdGEpXG5cdFx0XHQuZG9uZSgocmVzcG9uc2UpID0+IHtcblx0XHRcdFx0Y29uc3QgJGh0bWwgPSAkKHJlc3BvbnNlKTtcblx0XHRcdFx0XG5cdFx0XHRcdCRvcmRlckRldGFpbHNUYWJsZS5yZXBsYWNlV2l0aCgkaHRtbC5maW5kKCcjb3JkZXItZGV0YWlscy10YWJsZScpLnBhcmVudCgpLmh0bWwoKSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRqc2UubGlicy5sb2FkaW5nX3NwaW5uZXIuaGlkZSgkc3Bpbm5lcik7XG5cdFx0XHR9KTtcblx0fTtcblx0XG5cdEtsYXJuYUh1Yi5vbigncmVhZHknLCAoKSA9PiBpbml0KCkpO1xufSk7ICJdfQ==

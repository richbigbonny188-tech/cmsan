'use strict';

/* --------------------------------------------------------------
 payment_navigation_tab.js 2017-02-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gambio Payment Navigation Tab
 *
 * This file generates the tab navigation for the old payment module page.
 */
(function () {

	'use strict';

	// Initialize the page modifications.

	function initialize() {
		var html = $('<table><tr><td class="dataTableHeadingContent"><a href="admin.php?do=HubConfiguration/paymentMethods">' + jse.core.lang.translate('BOX_HEADING_HUB_PAYMENT', 'admin_general') + '</a></td><td class="dataTableHeadingContent">' + jse.core.lang.translate('BOX_HEADING_HUB_PAYMENT_MISC', 'admin_general') + '</td></tr></table>');
		var $target = $('.pageHeading');
		$target.append(html);
	}

	// Initialize the module once the page is ready (without jQuery).
	if (document.readyState != 'loading') {
		initialize();
	} else {
		document.addEventListener('DOMContentLoaded', initialize);
	}
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL3BheW1lbnRfbmF2aWdhdGlvbl90YWIuanMiXSwibmFtZXMiOlsiaW5pdGlhbGl6ZSIsImh0bWwiLCIkIiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCIkdGFyZ2V0IiwiYXBwZW5kIiwiZG9jdW1lbnQiLCJyZWFkeVN0YXRlIiwiYWRkRXZlbnRMaXN0ZW5lciJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBLENBQUMsWUFBVzs7QUFFWDs7QUFFQTs7QUFDQSxVQUFTQSxVQUFULEdBQXNCO0FBQ3JCLE1BQUlDLE9BQU9DLEVBQUUsMkdBQ1ZDLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLHlCQUF4QixFQUFtRCxlQUFuRCxDQURVLEdBRVYsK0NBRlUsR0FHVkgsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsOEJBQXhCLEVBQXdELGVBQXhELENBSFUsR0FHaUUsb0JBSG5FLENBQVg7QUFJQSxNQUFJQyxVQUFVTCxFQUFFLGNBQUYsQ0FBZDtBQUNBSyxVQUFRQyxNQUFSLENBQWVQLElBQWY7QUFDQTs7QUFFRDtBQUNBLEtBQUlRLFNBQVNDLFVBQVQsSUFBdUIsU0FBM0IsRUFBc0M7QUFDckNWO0FBQ0EsRUFGRCxNQUVPO0FBQ05TLFdBQVNFLGdCQUFULENBQTBCLGtCQUExQixFQUE4Q1gsVUFBOUM7QUFDQTtBQUNELENBcEJEIiwiZmlsZSI6IkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL3BheW1lbnRfbmF2aWdhdGlvbl90YWIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHBheW1lbnRfbmF2aWdhdGlvbl90YWIuanMgMjAxNy0wMi0yN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTcgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogR2FtYmlvIFBheW1lbnQgTmF2aWdhdGlvbiBUYWJcbiAqXG4gKiBUaGlzIGZpbGUgZ2VuZXJhdGVzIHRoZSB0YWIgbmF2aWdhdGlvbiBmb3IgdGhlIG9sZCBwYXltZW50IG1vZHVsZSBwYWdlLlxuICovXG4oZnVuY3Rpb24oKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvLyBJbml0aWFsaXplIHRoZSBwYWdlIG1vZGlmaWNhdGlvbnMuXG5cdGZ1bmN0aW9uIGluaXRpYWxpemUoKSB7XG5cdFx0dmFyIGh0bWwgPSAkKCc8dGFibGU+PHRyPjx0ZCBjbGFzcz1cImRhdGFUYWJsZUhlYWRpbmdDb250ZW50XCI+PGEgaHJlZj1cImFkbWluLnBocD9kbz1IdWJDb25maWd1cmF0aW9uL3BheW1lbnRNZXRob2RzXCI+J1xuXHRcdFx0KyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQk9YX0hFQURJTkdfSFVCX1BBWU1FTlQnLCAnYWRtaW5fZ2VuZXJhbCcpXG5cdFx0XHQrICc8L2E+PC90ZD48dGQgY2xhc3M9XCJkYXRhVGFibGVIZWFkaW5nQ29udGVudFwiPidcblx0XHRcdCsganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0JPWF9IRUFESU5HX0hVQl9QQVlNRU5UX01JU0MnLCAnYWRtaW5fZ2VuZXJhbCcpICsgJzwvdGQ+PC90cj48L3RhYmxlPicpO1xuXHRcdHZhciAkdGFyZ2V0ID0gJCgnLnBhZ2VIZWFkaW5nJyk7XG5cdFx0JHRhcmdldC5hcHBlbmQoaHRtbCk7XG5cdH1cblx0XG5cdC8vIEluaXRpYWxpemUgdGhlIG1vZHVsZSBvbmNlIHRoZSBwYWdlIGlzIHJlYWR5ICh3aXRob3V0IGpRdWVyeSkuXG5cdGlmIChkb2N1bWVudC5yZWFkeVN0YXRlICE9ICdsb2FkaW5nJykge1xuXHRcdGluaXRpYWxpemUoKTtcblx0fSBlbHNlIHtcblx0XHRkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgaW5pdGlhbGl6ZSk7XG5cdH1cbn0pKCk7Il19

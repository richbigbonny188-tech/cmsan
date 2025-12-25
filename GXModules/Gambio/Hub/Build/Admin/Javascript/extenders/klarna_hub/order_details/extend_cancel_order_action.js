'use strict';

/* --------------------------------------------------------------
 extend_cancel_order_action.js 2017-11-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Extends cancel-order row action (will call the respective KlarnaHub callback).
 */
(function () {
	'use strict';

	/**
  * Initializes the module.
  *
  * @private
  */

	var init = function init() {
		var $form = $('#multi_cancel_confirm_form');

		var $sourceControlGroup = $form.find('.single-checkbox:first').closest('.control-group');

		var $controlGroup = $sourceControlGroup.clone(true);

		var $label = $controlGroup.find('label');
		$label.text(KlarnaHub.Config.lang.NOTIFY_KLARNA);

		var $singleCheckbox = $controlGroup.find('.single-checkbox');
		var $checkbox = $controlGroup.find('input:checkbox');
		$checkbox.addClass('notify-klarna');

		$controlGroup.insertBefore($sourceControlGroup.next());

		$form.on('submit', function (event) {
			event.preventDefault();

			if (!$checkbox.prop('checked')) {
				$form[0].submit();
				return;
			}

			KlarnaHub.Api.executeCancelOrder().then(function () {
				return $form[0].submit();
			});
		});

		$('.js-button-dropdown .cancel-order').on('click', function () {
			$checkbox.prop('checked', true);
			$singleCheckbox.addClass('checked');
		});
	};

	KlarnaHub.on('ready', function () {
		return init();
	});
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIvb3JkZXJfZGV0YWlscy9leHRlbmRfY2FuY2VsX29yZGVyX2FjdGlvbi5qcyJdLCJuYW1lcyI6WyJpbml0IiwiJGZvcm0iLCIkIiwiJHNvdXJjZUNvbnRyb2xHcm91cCIsImZpbmQiLCJjbG9zZXN0IiwiJGNvbnRyb2xHcm91cCIsImNsb25lIiwiJGxhYmVsIiwidGV4dCIsIktsYXJuYUh1YiIsIkNvbmZpZyIsImxhbmciLCJOT1RJRllfS0xBUk5BIiwiJHNpbmdsZUNoZWNrYm94IiwiJGNoZWNrYm94IiwiYWRkQ2xhc3MiLCJpbnNlcnRCZWZvcmUiLCJuZXh0Iiwib24iLCJldmVudCIsInByZXZlbnREZWZhdWx0IiwicHJvcCIsInN1Ym1pdCIsIkFwaSIsImV4ZWN1dGVDYW5jZWxPcmRlciIsInRoZW4iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0EsQ0FBQyxZQUFXO0FBQ1g7O0FBRUE7Ozs7OztBQUtBLEtBQU1BLE9BQU8sU0FBUEEsSUFBTyxHQUFNO0FBQ2xCLE1BQU1DLFFBQVFDLEVBQUUsNEJBQUYsQ0FBZDs7QUFFQSxNQUFNQyxzQkFBc0JGLE1BQU1HLElBQU4sQ0FBVyx3QkFBWCxFQUFxQ0MsT0FBckMsQ0FBNkMsZ0JBQTdDLENBQTVCOztBQUVBLE1BQU1DLGdCQUFnQkgsb0JBQW9CSSxLQUFwQixDQUEwQixJQUExQixDQUF0Qjs7QUFFQSxNQUFNQyxTQUFTRixjQUFjRixJQUFkLENBQW1CLE9BQW5CLENBQWY7QUFDQUksU0FBT0MsSUFBUCxDQUFZQyxVQUFVQyxNQUFWLENBQWlCQyxJQUFqQixDQUFzQkMsYUFBbEM7O0FBRUEsTUFBTUMsa0JBQWtCUixjQUFjRixJQUFkLENBQW1CLGtCQUFuQixDQUF4QjtBQUNBLE1BQU1XLFlBQVlULGNBQWNGLElBQWQsQ0FBbUIsZ0JBQW5CLENBQWxCO0FBQ0FXLFlBQVVDLFFBQVYsQ0FBbUIsZUFBbkI7O0FBRUFWLGdCQUFjVyxZQUFkLENBQTJCZCxvQkFBb0JlLElBQXBCLEVBQTNCOztBQUVBakIsUUFBTWtCLEVBQU4sQ0FBUyxRQUFULEVBQW1CLFVBQUNDLEtBQUQsRUFBVztBQUM3QkEsU0FBTUMsY0FBTjs7QUFFQSxPQUFJLENBQUNOLFVBQVVPLElBQVYsQ0FBZSxTQUFmLENBQUwsRUFBZ0M7QUFDL0JyQixVQUFNLENBQU4sRUFBU3NCLE1BQVQ7QUFDQTtBQUNBOztBQUVEYixhQUFVYyxHQUFWLENBQWNDLGtCQUFkLEdBQW1DQyxJQUFuQyxDQUF3QztBQUFBLFdBQU16QixNQUFNLENBQU4sRUFBU3NCLE1BQVQsRUFBTjtBQUFBLElBQXhDO0FBQ0EsR0FURDs7QUFXQXJCLElBQUUsbUNBQUYsRUFBdUNpQixFQUF2QyxDQUEwQyxPQUExQyxFQUFtRCxZQUFNO0FBQ3hESixhQUFVTyxJQUFWLENBQWUsU0FBZixFQUEwQixJQUExQjtBQUNBUixtQkFBZ0JFLFFBQWhCLENBQXlCLFNBQXpCO0FBQ0EsR0FIRDtBQUlBLEVBL0JEOztBQWlDQU4sV0FBVVMsRUFBVixDQUFhLE9BQWIsRUFBc0I7QUFBQSxTQUFNbkIsTUFBTjtBQUFBLEVBQXRCO0FBQ0EsQ0ExQ0QiLCJmaWxlIjoiQWRtaW4vSmF2YXNjcmlwdC9leHRlbmRlcnMva2xhcm5hX2h1Yi9vcmRlcl9kZXRhaWxzL2V4dGVuZF9jYW5jZWxfb3JkZXJfYWN0aW9uLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBleHRlbmRfY2FuY2VsX29yZGVyX2FjdGlvbi5qcyAyMDE3LTExLTAzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNyBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBFeHRlbmRzIGNhbmNlbC1vcmRlciByb3cgYWN0aW9uICh3aWxsIGNhbGwgdGhlIHJlc3BlY3RpdmUgS2xhcm5hSHViIGNhbGxiYWNrKS5cbiAqL1xuKGZ1bmN0aW9uKCkge1xuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogSW5pdGlhbGl6ZXMgdGhlIG1vZHVsZS5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdGNvbnN0IGluaXQgPSAoKSA9PiB7XG5cdFx0Y29uc3QgJGZvcm0gPSAkKCcjbXVsdGlfY2FuY2VsX2NvbmZpcm1fZm9ybScpO1xuXHRcdFxuXHRcdGNvbnN0ICRzb3VyY2VDb250cm9sR3JvdXAgPSAkZm9ybS5maW5kKCcuc2luZ2xlLWNoZWNrYm94OmZpcnN0JykuY2xvc2VzdCgnLmNvbnRyb2wtZ3JvdXAnKTtcblx0XHRcblx0XHRjb25zdCAkY29udHJvbEdyb3VwID0gJHNvdXJjZUNvbnRyb2xHcm91cC5jbG9uZSh0cnVlKTtcblx0XHRcblx0XHRjb25zdCAkbGFiZWwgPSAkY29udHJvbEdyb3VwLmZpbmQoJ2xhYmVsJyk7XG5cdFx0JGxhYmVsLnRleHQoS2xhcm5hSHViLkNvbmZpZy5sYW5nLk5PVElGWV9LTEFSTkEpO1xuXHRcdFxuXHRcdGNvbnN0ICRzaW5nbGVDaGVja2JveCA9ICRjb250cm9sR3JvdXAuZmluZCgnLnNpbmdsZS1jaGVja2JveCcpO1xuXHRcdGNvbnN0ICRjaGVja2JveCA9ICRjb250cm9sR3JvdXAuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKTtcblx0XHQkY2hlY2tib3guYWRkQ2xhc3MoJ25vdGlmeS1rbGFybmEnKTtcblx0XHRcblx0XHQkY29udHJvbEdyb3VwLmluc2VydEJlZm9yZSgkc291cmNlQ29udHJvbEdyb3VwLm5leHQoKSk7XG5cdFx0XG5cdFx0JGZvcm0ub24oJ3N1Ym1pdCcsIChldmVudCkgPT4ge1xuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFxuXHRcdFx0aWYgKCEkY2hlY2tib3gucHJvcCgnY2hlY2tlZCcpKSB7XG5cdFx0XHRcdCRmb3JtWzBdLnN1Ym1pdCgpOyBcblx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRLbGFybmFIdWIuQXBpLmV4ZWN1dGVDYW5jZWxPcmRlcigpLnRoZW4oKCkgPT4gJGZvcm1bMF0uc3VibWl0KCkpO1xuXHRcdH0pO1xuXHRcdFxuXHRcdCQoJy5qcy1idXR0b24tZHJvcGRvd24gLmNhbmNlbC1vcmRlcicpLm9uKCdjbGljaycsICgpID0+IHtcblx0XHRcdCRjaGVja2JveC5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7IFxuXHRcdFx0JHNpbmdsZUNoZWNrYm94LmFkZENsYXNzKCdjaGVja2VkJyk7XG5cdFx0fSk7IFxuXHR9O1xuXHRcblx0S2xhcm5hSHViLm9uKCdyZWFkeScsICgpID0+IGluaXQoKSk7XG59KSgpOyBcbiJdfQ==

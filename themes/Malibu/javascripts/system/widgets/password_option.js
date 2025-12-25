'use strict';

/* --------------------------------------------------------------
   password_option.js 2020-02-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

gambio.widgets.module('password_option', [], function (data) {

	'use strict';

	var $this = $(this),
	    $body = $('body'),
	    $password_inputs = $('div.password-inputs'),
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	// (none)

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		var $passwordOption = $('#password-option');

		$passwordOption.on('change', function () {
			var isChecked = $(this).get(0).checked,
			    $passwordInputs = $('div.password-inputs input[type="password"]');
			if (isChecked) {
				$password_inputs.show();
				$passwordInputs.each(function () {
					$(this).removeAttr('disabled');
				});
				$('button[type="submit"]', $this.closest('form')).attr('disabled', 'disabled');
			} else {
				$password_inputs.hide();
				$passwordInputs.each(function () {
					$(this).attr('disabled', 'disabled');
					$(this).val('');
				});
				$('button[type="submit"]', $this.closest('form')).removeAttr('disabled');
			}
		});

		var passwordChange = function passwordChange() {
			var isChecked = $passwordOption.get(0) !== undefined ? $passwordOption.get(0).checked : true;
			if (isChecked) {
				var inputsMatch = $('#password').val().length >= 0 && $('#password').val() === $('#confirmation').val(),
				    $noMatchNote = $('span.password-no-match');

				if (inputsMatch) {
					$('button[type="submit"]', $this.closest('form')).removeAttr('disabled');
					$password_inputs.removeClass('has-error');
					$noMatchNote.hide();
				} else {
					$('button[type="submit"]', $this.closest('form')).attr('disabled', 'disabled');
					$password_inputs.addClass('has-error');
					$noMatchNote.show();
				}
			}
		};

		$('#password, #confirmation').on('input', passwordChange);
		$passwordOption.trigger('change');

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGFzc3dvcmRfb3B0aW9uLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkcGFzc3dvcmRfaW5wdXRzIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCIkcGFzc3dvcmRPcHRpb24iLCJvbiIsImlzQ2hlY2tlZCIsImdldCIsImNoZWNrZWQiLCIkcGFzc3dvcmRJbnB1dHMiLCJzaG93IiwiZWFjaCIsInJlbW92ZUF0dHIiLCJjbG9zZXN0IiwiYXR0ciIsImhpZGUiLCJ2YWwiLCJwYXNzd29yZENoYW5nZSIsInVuZGVmaW5lZCIsImlucHV0c01hdGNoIiwibGVuZ3RoIiwiJG5vTWF0Y2hOb3RlIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsInRyaWdnZXIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLGlCQUF0QixFQUF5QyxFQUF6QyxFQUE2QyxVQUFTQyxJQUFULEVBQWU7O0FBRTNEOztBQUVBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsUUFBUUQsRUFBRSxNQUFGLENBRFQ7QUFBQSxLQUVDRSxtQkFBbUJGLEVBQUUscUJBQUYsQ0FGcEI7QUFBQSxLQUdDRyxXQUFXLEVBSFo7QUFBQSxLQUtDQyxVQUFVSixFQUFFSyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCTCxJQUE3QixDQUxYO0FBQUEsS0FNQ0QsU0FBUyxFQU5WOztBQVFBOztBQUVBOztBQUVBOztBQUVBOzs7O0FBSUFBLFFBQU9TLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUIsTUFBSUMsa0JBQWtCUixFQUFFLGtCQUFGLENBQXRCOztBQUVBUSxrQkFBZ0JDLEVBQWhCLENBQW1CLFFBQW5CLEVBQTZCLFlBQVc7QUFDdkMsT0FBSUMsWUFBWVYsRUFBRSxJQUFGLEVBQVFXLEdBQVIsQ0FBWSxDQUFaLEVBQWVDLE9BQS9CO0FBQUEsT0FDQ0Msa0JBQWtCYixFQUFFLDRDQUFGLENBRG5CO0FBRUEsT0FBR1UsU0FBSCxFQUFjO0FBQ2JSLHFCQUFpQlksSUFBakI7QUFDQUQsb0JBQWdCRSxJQUFoQixDQUFxQixZQUFXO0FBQy9CZixPQUFFLElBQUYsRUFBUWdCLFVBQVIsQ0FBbUIsVUFBbkI7QUFDQSxLQUZEO0FBR0FoQixNQUFFLHVCQUFGLEVBQTJCRCxNQUFNa0IsT0FBTixDQUFjLE1BQWQsQ0FBM0IsRUFBa0RDLElBQWxELENBQXVELFVBQXZELEVBQW1FLFVBQW5FO0FBQ0EsSUFORCxNQU1PO0FBQ05oQixxQkFBaUJpQixJQUFqQjtBQUNBTixvQkFBZ0JFLElBQWhCLENBQXFCLFlBQVc7QUFDL0JmLE9BQUUsSUFBRixFQUFRa0IsSUFBUixDQUFhLFVBQWIsRUFBeUIsVUFBekI7QUFDQWxCLE9BQUUsSUFBRixFQUFRb0IsR0FBUixDQUFZLEVBQVo7QUFDQSxLQUhEO0FBSUFwQixNQUFFLHVCQUFGLEVBQTJCRCxNQUFNa0IsT0FBTixDQUFjLE1BQWQsQ0FBM0IsRUFBa0RELFVBQWxELENBQTZELFVBQTdEO0FBQ0E7QUFDRCxHQWpCRDs7QUFtQkEsTUFBSUssaUJBQWlCLFNBQWpCQSxjQUFpQixHQUFXO0FBQy9CLE9BQUlYLFlBQVlGLGdCQUFnQkcsR0FBaEIsQ0FBb0IsQ0FBcEIsTUFBMkJXLFNBQTNCLEdBQXVDZCxnQkFBZ0JHLEdBQWhCLENBQW9CLENBQXBCLEVBQXVCQyxPQUE5RCxHQUF3RSxJQUF4RjtBQUNBLE9BQUdGLFNBQUgsRUFBYztBQUNiLFFBQUlhLGNBQWN2QixFQUFFLFdBQUYsRUFBZW9CLEdBQWYsR0FBcUJJLE1BQXJCLElBQStCLENBQS9CLElBQXFDeEIsRUFBRSxXQUFGLEVBQWVvQixHQUFmLE9BQXlCcEIsRUFBRSxlQUFGLEVBQW1Cb0IsR0FBbkIsRUFBaEY7QUFBQSxRQUNDSyxlQUFlekIsRUFBRSx3QkFBRixDQURoQjs7QUFHQSxRQUFHdUIsV0FBSCxFQUFnQjtBQUNmdkIsT0FBRSx1QkFBRixFQUEyQkQsTUFBTWtCLE9BQU4sQ0FBYyxNQUFkLENBQTNCLEVBQWtERCxVQUFsRCxDQUE2RCxVQUE3RDtBQUNBZCxzQkFBaUJ3QixXQUFqQixDQUE2QixXQUE3QjtBQUNBRCxrQkFBYU4sSUFBYjtBQUNBLEtBSkQsTUFJTztBQUNObkIsT0FBRSx1QkFBRixFQUEyQkQsTUFBTWtCLE9BQU4sQ0FBYyxNQUFkLENBQTNCLEVBQWtEQyxJQUFsRCxDQUF1RCxVQUF2RCxFQUFtRSxVQUFuRTtBQUNBaEIsc0JBQWlCeUIsUUFBakIsQ0FBMEIsV0FBMUI7QUFDQUYsa0JBQWFYLElBQWI7QUFDQTtBQUNEO0FBQ0QsR0FoQkQ7O0FBa0JBZCxJQUFFLDBCQUFGLEVBQThCUyxFQUE5QixDQUFpQyxPQUFqQyxFQUEwQ1ksY0FBMUM7QUFDQWIsa0JBQWdCb0IsT0FBaEIsQ0FBd0IsUUFBeEI7O0FBRUFyQjtBQUNBLEVBNUNEOztBQThDQTtBQUNBLFFBQU9WLE1BQVA7QUFDQSxDQXRFRCIsImZpbGUiOiJ3aWRnZXRzL3Bhc3N3b3JkX29wdGlvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gICBwYXNzd29yZF9vcHRpb24uanMgMjAyMC0wMi0yNlxuICAgR2FtYmlvIEdtYkhcbiAgIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gICBDb3B5cmlnaHQgKGMpIDIwMTkgR2FtYmlvIEdtYkhcbiAgIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuICAgW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gICAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuKi9cblxuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdwYXNzd29yZF9vcHRpb24nLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xuXHRcblx0J3VzZSBzdHJpY3QnO1xuXHRcblx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHQkYm9keSA9ICQoJ2JvZHknKSxcblx0XHQkcGFzc3dvcmRfaW5wdXRzID0gJCgnZGl2LnBhc3N3b3JkLWlucHV0cycpLFxuXHRcdGRlZmF1bHRzID0ge1xuXHRcdH0sXG5cdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0bW9kdWxlID0ge307XG5cdFxuXHQvLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXHRcblx0Ly8gKG5vbmUpXG5cdFxuXHQvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblx0XG5cdC8qKlxuXHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0ICogQGNvbnN0cnVjdG9yXG5cdCAqL1xuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRsZXQgJHBhc3N3b3JkT3B0aW9uID0gJCgnI3Bhc3N3b3JkLW9wdGlvbicpO1xuXHRcdFxuXHRcdCRwYXNzd29yZE9wdGlvbi5vbignY2hhbmdlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRsZXQgaXNDaGVja2VkID0gJCh0aGlzKS5nZXQoMCkuY2hlY2tlZCxcblx0XHRcdFx0JHBhc3N3b3JkSW5wdXRzID0gJCgnZGl2LnBhc3N3b3JkLWlucHV0cyBpbnB1dFt0eXBlPVwicGFzc3dvcmRcIl0nKTtcblx0XHRcdGlmKGlzQ2hlY2tlZCkge1xuXHRcdFx0XHQkcGFzc3dvcmRfaW5wdXRzLnNob3coKTtcblx0XHRcdFx0JHBhc3N3b3JkSW5wdXRzLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JCh0aGlzKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuXHRcdFx0XHR9KTtcblx0XHRcdFx0JCgnYnV0dG9uW3R5cGU9XCJzdWJtaXRcIl0nLCAkdGhpcy5jbG9zZXN0KCdmb3JtJykpLmF0dHIoJ2Rpc2FibGVkJywgJ2Rpc2FibGVkJyk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQkcGFzc3dvcmRfaW5wdXRzLmhpZGUoKTtcblx0XHRcdFx0JHBhc3N3b3JkSW5wdXRzLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JCh0aGlzKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXHRcdFx0XHRcdCQodGhpcykudmFsKCcnKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdCQoJ2J1dHRvblt0eXBlPVwic3VibWl0XCJdJywgJHRoaXMuY2xvc2VzdCgnZm9ybScpKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXG5cdFx0bGV0IHBhc3N3b3JkQ2hhbmdlID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRsZXQgaXNDaGVja2VkID0gJHBhc3N3b3JkT3B0aW9uLmdldCgwKSAhPT0gdW5kZWZpbmVkID8gJHBhc3N3b3JkT3B0aW9uLmdldCgwKS5jaGVja2VkIDogdHJ1ZTtcblx0XHRcdGlmKGlzQ2hlY2tlZCkge1xuXHRcdFx0XHRsZXQgaW5wdXRzTWF0Y2ggPSAkKCcjcGFzc3dvcmQnKS52YWwoKS5sZW5ndGggPj0gMCAmJiAoJCgnI3Bhc3N3b3JkJykudmFsKCkgPT09ICQoJyNjb25maXJtYXRpb24nKS52YWwoKSksXG5cdFx0XHRcdFx0JG5vTWF0Y2hOb3RlID0gJCgnc3Bhbi5wYXNzd29yZC1uby1tYXRjaCcpO1xuXHRcdFx0XHRcblx0XHRcdFx0aWYoaW5wdXRzTWF0Y2gpIHtcblx0XHRcdFx0XHQkKCdidXR0b25bdHlwZT1cInN1Ym1pdFwiXScsICR0aGlzLmNsb3Nlc3QoJ2Zvcm0nKSkucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcblx0XHRcdFx0XHQkcGFzc3dvcmRfaW5wdXRzLnJlbW92ZUNsYXNzKCdoYXMtZXJyb3InKTtcblx0XHRcdFx0XHQkbm9NYXRjaE5vdGUuaGlkZSgpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCQoJ2J1dHRvblt0eXBlPVwic3VibWl0XCJdJywgJHRoaXMuY2xvc2VzdCgnZm9ybScpKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXHRcdFx0XHRcdCRwYXNzd29yZF9pbnB1dHMuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuXHRcdFx0XHRcdCRub01hdGNoTm90ZS5zaG93KCk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0JCgnI3Bhc3N3b3JkLCAjY29uZmlybWF0aW9uJykub24oJ2lucHV0JywgcGFzc3dvcmRDaGFuZ2UpO1xuXHRcdCRwYXNzd29yZE9wdGlvbi50cmlnZ2VyKCdjaGFuZ2UnKTtcblx0XHRcblx0XHRkb25lKCk7XG5cdH07XG5cdFxuXHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdHJldHVybiBtb2R1bGU7XG59KTtcbiJdfQ==

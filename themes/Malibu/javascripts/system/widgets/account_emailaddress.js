'use strict';

/* --------------------------------------------------------------
   account_emailaddress.js 2020-02-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

gambio.widgets.module('account_emailaddress', [], function (data) {
	'use strict';

	var $this = $(this),
	    $body = $('body'),
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
		var $emailAddress = $('#email_address'),
		    $emailConfirm = $('#email_address_confirm'),
		    $emailInputs = $('#email_address, #email_address_confirm'),
		    $noMatchNote = $('.email-no-match');

		$emailInputs.on('input', function () {
			var emailValue = $emailAddress.val(),
			    confirmValue = $emailConfirm.val();
			console.log('*************************************************************************************************************************');
			console.log('********************************************************* input has changed *********************************************');
			console.log('*************************************************************************************************************************');
			if ($emailConfirm.length && (emailValue.length > 0 || confirmValue.length > 0) && emailValue !== confirmValue) {
				$noMatchNote.show();
				$emailAddress.closest('div.form-group').addClass('has-error');
				$emailConfirm.closest('div.form-group').addClass('has-error');
			} else {
				$noMatchNote.hide();
				$emailAddress.closest('div.form-group').find('.help-block').remove();
				$emailAddress.closest('div.form-group').removeClass('has-error');

				if ($emailConfirm.length) {
					// remove help text indicating email address already exists
					$emailConfirm.closest('div.form-group').find('.help-block').remove();
					$emailConfirm.closest('div.form-group').removeClass('has-error');
				}

				// check if password is okay and enable submit button upon a valid password
				if (!$('#password-option').hasClass('has-error')) {
					$('button[type="submit"]', $this.closest('form')).removeAttr('disabled');
				}
			}
		});

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvYWNjb3VudF9lbWFpbGFkZHJlc3MuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkYm9keSIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwiJGVtYWlsQWRkcmVzcyIsIiRlbWFpbENvbmZpcm0iLCIkZW1haWxJbnB1dHMiLCIkbm9NYXRjaE5vdGUiLCJvbiIsImVtYWlsVmFsdWUiLCJ2YWwiLCJjb25maXJtVmFsdWUiLCJjb25zb2xlIiwibG9nIiwibGVuZ3RoIiwic2hvdyIsImNsb3Nlc3QiLCJhZGRDbGFzcyIsImhpZGUiLCJmaW5kIiwicmVtb3ZlIiwicmVtb3ZlQ2xhc3MiLCJoYXNDbGFzcyIsInJlbW92ZUF0dHIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLHNCQUF0QixFQUE4QyxFQUE5QyxFQUFrRCxVQUFTQyxJQUFULEVBQWU7QUFDaEU7O0FBRUEsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxRQUFRRCxFQUFFLE1BQUYsQ0FEVDtBQUFBLEtBRUNFLFdBQVcsRUFGWjtBQUFBLEtBSUNDLFVBQVVILEVBQUVJLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJKLElBQTdCLENBSlg7QUFBQSxLQUtDRCxTQUFTLEVBTFY7O0FBT0E7O0FBRUE7O0FBRUE7O0FBRUE7Ozs7QUFJQUEsUUFBT1EsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QixNQUFJQyxnQkFBZ0JQLEVBQUUsZ0JBQUYsQ0FBcEI7QUFBQSxNQUNDUSxnQkFBZ0JSLEVBQUUsd0JBQUYsQ0FEakI7QUFBQSxNQUVDUyxlQUFnQlQsRUFBRSx3Q0FBRixDQUZqQjtBQUFBLE1BR0NVLGVBQWdCVixFQUFFLGlCQUFGLENBSGpCOztBQUtBUyxlQUFhRSxFQUFiLENBQWdCLE9BQWhCLEVBQXlCLFlBQVc7QUFDbkMsT0FBSUMsYUFBYUwsY0FBY00sR0FBZCxFQUFqQjtBQUFBLE9BQ0NDLGVBQWVOLGNBQWNLLEdBQWQsRUFEaEI7QUFFQUUsV0FBUUMsR0FBUixDQUFZLDJIQUFaO0FBQ0FELFdBQVFDLEdBQVIsQ0FBWSwySEFBWjtBQUNBRCxXQUFRQyxHQUFSLENBQVksMkhBQVo7QUFDQSxPQUFJUixjQUFjUyxNQUFkLElBQ1UsQ0FBQ0wsV0FBV0ssTUFBWCxHQUFvQixDQUFwQixJQUF5QkgsYUFBYUcsTUFBYixHQUFzQixDQUFoRCxLQUFzREwsZUFBZUUsWUFEbkYsRUFFVztBQUNWSixpQkFBYVEsSUFBYjtBQUNBWCxrQkFBY1ksT0FBZCxDQUFzQixnQkFBdEIsRUFBd0NDLFFBQXhDLENBQWlELFdBQWpEO0FBQ0FaLGtCQUFjVyxPQUFkLENBQXNCLGdCQUF0QixFQUF3Q0MsUUFBeEMsQ0FBaUQsV0FBakQ7QUFDQSxJQU5ELE1BTU87QUFDTlYsaUJBQWFXLElBQWI7QUFDQWQsa0JBQWNZLE9BQWQsQ0FBc0IsZ0JBQXRCLEVBQXdDRyxJQUF4QyxDQUE2QyxhQUE3QyxFQUE0REMsTUFBNUQ7QUFDQWhCLGtCQUFjWSxPQUFkLENBQXNCLGdCQUF0QixFQUF3Q0ssV0FBeEMsQ0FBb0QsV0FBcEQ7O0FBRVksUUFBSWhCLGNBQWNTLE1BQWxCLEVBQTBCO0FBQ3RCO0FBQ0FULG1CQUFjVyxPQUFkLENBQXNCLGdCQUF0QixFQUF3Q0csSUFBeEMsQ0FBNkMsYUFBN0MsRUFBNERDLE1BQTVEO0FBQ0FmLG1CQUFjVyxPQUFkLENBQXNCLGdCQUF0QixFQUF3Q0ssV0FBeEMsQ0FBb0QsV0FBcEQ7QUFDSDs7QUFFYjtBQUNBLFFBQUcsQ0FBQ3hCLEVBQUUsa0JBQUYsRUFBc0J5QixRQUF0QixDQUErQixXQUEvQixDQUFKLEVBQWlEO0FBQ2hEekIsT0FBRSx1QkFBRixFQUEyQkQsTUFBTW9CLE9BQU4sQ0FBYyxNQUFkLENBQTNCLEVBQWtETyxVQUFsRCxDQUE2RCxVQUE3RDtBQUNBO0FBQ0Q7QUFDRCxHQTVCRDs7QUE4QkFwQjtBQUNBLEVBckNEOztBQXVDQTtBQUNBLFFBQU9ULE1BQVA7QUFDQSxDQTdERCIsImZpbGUiOiJ3aWRnZXRzL2FjY291bnRfZW1haWxhZGRyZXNzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgIGFjY291bnRfZW1haWxhZGRyZXNzLmpzIDIwMjAtMDItMjZcbiAgIEdhbWJpbyBHbWJIXG4gICBodHRwOi8vd3d3LmdhbWJpby5kZVxuICAgQ29weXJpZ2h0IChjKSAyMDE5IEdhbWJpbyBHbWJIXG4gICBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiAgIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuICAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiovXG5cbmdhbWJpby53aWRnZXRzLm1vZHVsZSgnYWNjb3VudF9lbWFpbGFkZHJlc3MnLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xuXHQndXNlIHN0cmljdCc7XG5cdFxuXHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdCRib2R5ID0gJCgnYm9keScpLFxuXHRcdGRlZmF1bHRzID0ge1xuXHRcdH0sXG5cdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0bW9kdWxlID0ge307XG5cdFxuXHQvLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXHRcblx0Ly8gKG5vbmUpXG5cdFxuXHQvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblx0XG5cdC8qKlxuXHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0ICogQGNvbnN0cnVjdG9yXG5cdCAqL1xuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRsZXQgJGVtYWlsQWRkcmVzcyA9ICQoJyNlbWFpbF9hZGRyZXNzJyksXG5cdFx0XHQkZW1haWxDb25maXJtID0gJCgnI2VtYWlsX2FkZHJlc3NfY29uZmlybScpLFxuXHRcdFx0JGVtYWlsSW5wdXRzICA9ICQoJyNlbWFpbF9hZGRyZXNzLCAjZW1haWxfYWRkcmVzc19jb25maXJtJyksXG5cdFx0XHQkbm9NYXRjaE5vdGUgID0gJCgnLmVtYWlsLW5vLW1hdGNoJyk7XG5cdFx0XG5cdFx0JGVtYWlsSW5wdXRzLm9uKCdpbnB1dCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0bGV0IGVtYWlsVmFsdWUgPSAkZW1haWxBZGRyZXNzLnZhbCgpLFxuXHRcdFx0XHRjb25maXJtVmFsdWUgPSAkZW1haWxDb25maXJtLnZhbCgpO1xuXHRcdFx0Y29uc29sZS5sb2coJyoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKionKTtcblx0XHRcdGNvbnNvbGUubG9nKCcqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiogaW5wdXQgaGFzIGNoYW5nZWQgKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqJyk7XG5cdFx0XHRjb25zb2xlLmxvZygnKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKicpO1xuXHRcdFx0aWYgKCRlbWFpbENvbmZpcm0ubGVuZ3RoICYmXG4gICAgICAgICAgICAgICAgKChlbWFpbFZhbHVlLmxlbmd0aCA+IDAgfHwgY29uZmlybVZhbHVlLmxlbmd0aCA+IDApICYmIGVtYWlsVmFsdWUgIT09IGNvbmZpcm1WYWx1ZSlcbiAgICAgICAgICAgICkge1xuXHRcdFx0XHQkbm9NYXRjaE5vdGUuc2hvdygpO1xuXHRcdFx0XHQkZW1haWxBZGRyZXNzLmNsb3Nlc3QoJ2Rpdi5mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuXHRcdFx0XHQkZW1haWxDb25maXJtLmNsb3Nlc3QoJ2Rpdi5mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JG5vTWF0Y2hOb3RlLmhpZGUoKTtcblx0XHRcdFx0JGVtYWlsQWRkcmVzcy5jbG9zZXN0KCdkaXYuZm9ybS1ncm91cCcpLmZpbmQoJy5oZWxwLWJsb2NrJykucmVtb3ZlKCk7XG5cdFx0XHRcdCRlbWFpbEFkZHJlc3MuY2xvc2VzdCgnZGl2LmZvcm0tZ3JvdXAnKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgaWYgKCRlbWFpbENvbmZpcm0ubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgIC8vIHJlbW92ZSBoZWxwIHRleHQgaW5kaWNhdGluZyBlbWFpbCBhZGRyZXNzIGFscmVhZHkgZXhpc3RzXG4gICAgICAgICAgICAgICAgICAgICRlbWFpbENvbmZpcm0uY2xvc2VzdCgnZGl2LmZvcm0tZ3JvdXAnKS5maW5kKCcuaGVscC1ibG9jaycpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAkZW1haWxDb25maXJtLmNsb3Nlc3QoJ2Rpdi5mb3JtLWdyb3VwJykucmVtb3ZlQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBcblx0XHRcdFx0Ly8gY2hlY2sgaWYgcGFzc3dvcmQgaXMgb2theSBhbmQgZW5hYmxlIHN1Ym1pdCBidXR0b24gdXBvbiBhIHZhbGlkIHBhc3N3b3JkXG5cdFx0XHRcdGlmKCEkKCcjcGFzc3dvcmQtb3B0aW9uJykuaGFzQ2xhc3MoJ2hhcy1lcnJvcicpKSB7XG5cdFx0XHRcdFx0JCgnYnV0dG9uW3R5cGU9XCJzdWJtaXRcIl0nLCAkdGhpcy5jbG9zZXN0KCdmb3JtJykpLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHRkb25lKCk7XG5cdH07XG5cdFxuXHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdHJldHVybiBtb2R1bGU7XG59KTtcbiJdfQ==

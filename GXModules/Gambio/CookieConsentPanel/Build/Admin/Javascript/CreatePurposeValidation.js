'use strict';

/* --------------------------------------------------------------
  CreatePurposeValidation.js 2020-01-29
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

$(document).ready(function () {

	var errorMessageSelector = '#purpose-missing-title-msg';
	var newPurposeTitleInputsAreValid = function newPurposeTitleInputsAreValid() {

		var everyTitleHasAValue = true;

		$('.new-purpose-title').each(function () {

			if ($(this).val().length === 0) {

				everyTitleHasAValue = false;
			}
		});

		return everyTitleHasAValue;
	};

	var checkIfTitlesAreNotEmptyInterval = function checkIfTitlesAreNotEmptyInterval() {

		var $errorMsg = $(errorMessageSelector);

		if ($errorMsg.is(":visible") && newPurposeTitleInputsAreValid()) {

			$errorMsg.slideUp();
			clearInterval(checkInterval);
		}
	};

	var checkInterval = void 0;

	$('.create-purpose-submit').click(function (event) {

		if (!newPurposeTitleInputsAreValid()) {

			$(errorMessageSelector).slideDown();

			checkInterval = setInterval(checkIfTitlesAreNotEmptyInterval, 1000);

			event.stopPropagation();
			event.preventDefault();

			return false;
		}

		return true;
	});
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvQ3JlYXRlUHVycG9zZVZhbGlkYXRpb24uanMiXSwibmFtZXMiOlsiJCIsImRvY3VtZW50IiwicmVhZHkiLCJlcnJvck1lc3NhZ2VTZWxlY3RvciIsIm5ld1B1cnBvc2VUaXRsZUlucHV0c0FyZVZhbGlkIiwiZXZlcnlUaXRsZUhhc0FWYWx1ZSIsImVhY2giLCJ2YWwiLCJsZW5ndGgiLCJjaGVja0lmVGl0bGVzQXJlTm90RW1wdHlJbnRlcnZhbCIsIiRlcnJvck1zZyIsImlzIiwic2xpZGVVcCIsImNsZWFySW50ZXJ2YWwiLCJjaGVja0ludGVydmFsIiwiY2xpY2siLCJldmVudCIsInNsaWRlRG93biIsInNldEludGVydmFsIiwic3RvcFByb3BhZ2F0aW9uIiwicHJldmVudERlZmF1bHQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7OztBQVNBQSxFQUFFQyxRQUFGLEVBQVlDLEtBQVosQ0FBa0IsWUFBVzs7QUFFNUIsS0FBTUMsdUJBQXVCLDRCQUE3QjtBQUNBLEtBQU1DLGdDQUFnQyxTQUFoQ0EsNkJBQWdDLEdBQVc7O0FBRWhELE1BQUlDLHNCQUFzQixJQUExQjs7QUFFQUwsSUFBRSxvQkFBRixFQUF3Qk0sSUFBeEIsQ0FBNkIsWUFBVzs7QUFFdkMsT0FBSU4sRUFBRSxJQUFGLEVBQVFPLEdBQVIsR0FBY0MsTUFBZCxLQUF5QixDQUE3QixFQUFnQzs7QUFFL0JILDBCQUFzQixLQUF0QjtBQUNBO0FBQ0QsR0FORDs7QUFRQSxTQUFPQSxtQkFBUDtBQUNBLEVBYkQ7O0FBZUEsS0FBSUksbUNBQW1DLFNBQW5DQSxnQ0FBbUMsR0FBVzs7QUFFakQsTUFBSUMsWUFBWVYsRUFBRUcsb0JBQUYsQ0FBaEI7O0FBRUEsTUFBSU8sVUFBVUMsRUFBVixDQUFhLFVBQWIsS0FBNEJQLCtCQUFoQyxFQUFpRTs7QUFFaEVNLGFBQVVFLE9BQVY7QUFDQUMsaUJBQWNDLGFBQWQ7QUFDQTtBQUNELEVBVEQ7O0FBV0EsS0FBSUEsc0JBQUo7O0FBRUFkLEdBQUUsd0JBQUYsRUFBNEJlLEtBQTVCLENBQWtDLFVBQVNDLEtBQVQsRUFBZ0I7O0FBRWpELE1BQUksQ0FBQ1osK0JBQUwsRUFBc0M7O0FBRXJDSixLQUFFRyxvQkFBRixFQUF3QmMsU0FBeEI7O0FBRUFILG1CQUFnQkksWUFBWVQsZ0NBQVosRUFBOEMsSUFBOUMsQ0FBaEI7O0FBRUFPLFNBQU1HLGVBQU47QUFDQUgsU0FBTUksY0FBTjs7QUFFQSxVQUFPLEtBQVA7QUFDQTs7QUFFRCxTQUFPLElBQVA7QUFDQSxFQWZEO0FBZ0JBLENBL0NEIiwiZmlsZSI6IkFkbWluL0phdmFzY3JpcHQvQ3JlYXRlUHVycG9zZVZhbGlkYXRpb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICBDcmVhdGVQdXJwb3NlVmFsaWRhdGlvbi5qcyAyMDIwLTAxLTI5XG4gIEdhbWJpbyBHbWJIXG4gIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gIENvcHlyaWdodCAoYykgMjAyMCBHYW1iaW8gR21iSFxuICBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiAgW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tKi9cblxuJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XG5cblx0Y29uc3QgZXJyb3JNZXNzYWdlU2VsZWN0b3IgPSAnI3B1cnBvc2UtbWlzc2luZy10aXRsZS1tc2cnO1xuXHRjb25zdCBuZXdQdXJwb3NlVGl0bGVJbnB1dHNBcmVWYWxpZCA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0bGV0IGV2ZXJ5VGl0bGVIYXNBVmFsdWUgPSB0cnVlO1xuXG5cdFx0JCgnLm5ldy1wdXJwb3NlLXRpdGxlJykuZWFjaChmdW5jdGlvbigpIHtcblxuXHRcdFx0aWYgKCQodGhpcykudmFsKCkubGVuZ3RoID09PSAwKSB7XG5cblx0XHRcdFx0ZXZlcnlUaXRsZUhhc0FWYWx1ZSA9IGZhbHNlO1xuXHRcdFx0fVxuXHRcdH0pO1xuXG5cdFx0cmV0dXJuIGV2ZXJ5VGl0bGVIYXNBVmFsdWU7XG5cdH07XG5cblx0bGV0IGNoZWNrSWZUaXRsZXNBcmVOb3RFbXB0eUludGVydmFsID0gZnVuY3Rpb24oKSB7XG5cdFx0XG5cdFx0bGV0ICRlcnJvck1zZyA9ICQoZXJyb3JNZXNzYWdlU2VsZWN0b3IpO1xuXHRcdFxuXHRcdGlmICgkZXJyb3JNc2cuaXMoXCI6dmlzaWJsZVwiKSAmJiBuZXdQdXJwb3NlVGl0bGVJbnB1dHNBcmVWYWxpZCgpKSB7XG5cblx0XHRcdCRlcnJvck1zZy5zbGlkZVVwKCk7XG5cdFx0XHRjbGVhckludGVydmFsKGNoZWNrSW50ZXJ2YWwpO1xuXHRcdH1cblx0fTtcblxuXHRsZXQgY2hlY2tJbnRlcnZhbDtcblxuXHQkKCcuY3JlYXRlLXB1cnBvc2Utc3VibWl0JykuY2xpY2soZnVuY3Rpb24oZXZlbnQpIHtcblxuXHRcdGlmICghbmV3UHVycG9zZVRpdGxlSW5wdXRzQXJlVmFsaWQoKSkge1xuXG5cdFx0XHQkKGVycm9yTWVzc2FnZVNlbGVjdG9yKS5zbGlkZURvd24oKTtcblxuXHRcdFx0Y2hlY2tJbnRlcnZhbCA9IHNldEludGVydmFsKGNoZWNrSWZUaXRsZXNBcmVOb3RFbXB0eUludGVydmFsLCAxMDAwKTtcblxuXHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0fVxuXG5cdFx0cmV0dXJuIHRydWU7XG5cdH0pO1xufSk7XG4iXX0=

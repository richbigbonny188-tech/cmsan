'use strict';

/* --------------------------------------------------------------
 forgot_password.js 2021-01-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('forgot_password', [], function (data) {

    'use strict';

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        var _$this$data = $this.data(),
            minPasswordLength = _$this$data.minPasswordLength;

        var passwordChange = function passwordChange() {
            var newPassword = document.getElementById('newPassword'),
                confirmedPassword = document.getElementById('confirmedPassword'),
                submitButton = document.getElementById('submit-button'),
                passwordLengthElement = document.getElementById('password-length'),
                passwordValidationElement = document.getElementById('password-validation');

            var hasMinimumLength = newPassword.value.length >= minPasswordLength,
                isConfirmed = newPassword.value === confirmedPassword.value;

            if (!hasMinimumLength) {
                passwordLengthElement.classList.remove('hidden');
                submitButton.disabled = true;
            } else if (hasMinimumLength) {
                passwordLengthElement.classList.add('hidden');
            }

            if (!isConfirmed) {
                if (confirmedPassword.value.length > 0) {
                    passwordValidationElement.classList.remove('hidden');
                }
                submitButton.disabled = true;
            } else if (isConfirmed) {
                passwordValidationElement.classList.add('hidden');
            }

            if (hasMinimumLength && isConfirmed) {
                submitButton.disabled = false;
            }
        };

        document.getElementById('newPassword').addEventListener('input', passwordChange);
        document.getElementById('confirmedPassword').addEventListener('input', passwordChange);

        done();
    };

    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvZm9yZ290X3Bhc3N3b3JkLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCJtaW5QYXNzd29yZExlbmd0aCIsInBhc3N3b3JkQ2hhbmdlIiwibmV3UGFzc3dvcmQiLCJkb2N1bWVudCIsImdldEVsZW1lbnRCeUlkIiwiY29uZmlybWVkUGFzc3dvcmQiLCJzdWJtaXRCdXR0b24iLCJwYXNzd29yZExlbmd0aEVsZW1lbnQiLCJwYXNzd29yZFZhbGlkYXRpb25FbGVtZW50IiwiaGFzTWluaW11bUxlbmd0aCIsInZhbHVlIiwibGVuZ3RoIiwiaXNDb25maXJtZWQiLCJjbGFzc0xpc3QiLCJyZW1vdmUiLCJkaXNhYmxlZCIsImFkZCIsImFkZEV2ZW50TGlzdGVuZXIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLGlCQUF0QixFQUF5QyxFQUF6QyxFQUE2QyxVQUFVQyxJQUFWLEVBQWdCOztBQUV6RDs7QUFFQSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFdBQVcsRUFEZjtBQUFBLFFBRUlDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBRmQ7QUFBQSxRQUdJRCxTQUFTLEVBSGI7O0FBS0E7Ozs7QUFJQUEsV0FBT08sSUFBUCxHQUFjLFVBQVVDLElBQVYsRUFBZ0I7QUFBQSwwQkFDSU4sTUFBTUQsSUFBTixFQURKO0FBQUEsWUFDbEJRLGlCQURrQixlQUNsQkEsaUJBRGtCOztBQUcxQixZQUFNQyxpQkFBaUIsU0FBakJBLGNBQWlCLEdBQU07QUFDekIsZ0JBQU1DLGNBQWNDLFNBQVNDLGNBQVQsQ0FBd0IsYUFBeEIsQ0FBcEI7QUFBQSxnQkFDTUMsb0JBQW9CRixTQUFTQyxjQUFULENBQXdCLG1CQUF4QixDQUQxQjtBQUFBLGdCQUVNRSxlQUFlSCxTQUFTQyxjQUFULENBQXdCLGVBQXhCLENBRnJCO0FBQUEsZ0JBR01HLHdCQUF3QkosU0FBU0MsY0FBVCxDQUF3QixpQkFBeEIsQ0FIOUI7QUFBQSxnQkFJTUksNEJBQTRCTCxTQUFTQyxjQUFULENBQXdCLHFCQUF4QixDQUpsQzs7QUFNQSxnQkFBTUssbUJBQW1CUCxZQUFZUSxLQUFaLENBQWtCQyxNQUFsQixJQUE0QlgsaUJBQXJEO0FBQUEsZ0JBQ01ZLGNBQWNWLFlBQVlRLEtBQVosS0FBc0JMLGtCQUFrQkssS0FENUQ7O0FBR0EsZ0JBQUksQ0FBQ0QsZ0JBQUwsRUFBdUI7QUFDbkJGLHNDQUFzQk0sU0FBdEIsQ0FBZ0NDLE1BQWhDLENBQXVDLFFBQXZDO0FBQ0FSLDZCQUFhUyxRQUFiLEdBQXdCLElBQXhCO0FBQ0gsYUFIRCxNQUdPLElBQUlOLGdCQUFKLEVBQXNCO0FBQ3pCRixzQ0FBc0JNLFNBQXRCLENBQWdDRyxHQUFoQyxDQUFvQyxRQUFwQztBQUNIOztBQUVELGdCQUFJLENBQUNKLFdBQUwsRUFBa0I7QUFDZCxvQkFBSVAsa0JBQWtCSyxLQUFsQixDQUF3QkMsTUFBeEIsR0FBaUMsQ0FBckMsRUFBeUM7QUFDckNILDhDQUEwQkssU0FBMUIsQ0FBb0NDLE1BQXBDLENBQTJDLFFBQTNDO0FBQ0g7QUFDRFIsNkJBQWFTLFFBQWIsR0FBd0IsSUFBeEI7QUFDSCxhQUxELE1BS08sSUFBSUgsV0FBSixFQUFpQjtBQUNwQkosMENBQTBCSyxTQUExQixDQUFvQ0csR0FBcEMsQ0FBd0MsUUFBeEM7QUFDSDs7QUFFRCxnQkFBSVAsb0JBQW9CRyxXQUF4QixFQUFxQztBQUNqQ04sNkJBQWFTLFFBQWIsR0FBd0IsS0FBeEI7QUFDSDtBQUNKLFNBN0JEOztBQStCQVosaUJBQVNDLGNBQVQsQ0FBd0IsYUFBeEIsRUFBdUNhLGdCQUF2QyxDQUF3RCxPQUF4RCxFQUFpRWhCLGNBQWpFO0FBQ0FFLGlCQUFTQyxjQUFULENBQXdCLG1CQUF4QixFQUE2Q2EsZ0JBQTdDLENBQThELE9BQTlELEVBQXVFaEIsY0FBdkU7O0FBRUFGO0FBQ0gsS0F0Q0Q7O0FBd0NBLFdBQU9SLE1BQVA7QUFDSCxDQXRERCIsImZpbGUiOiJ3aWRnZXRzL2ZvcmdvdF9wYXNzd29yZC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZm9yZ290X3Bhc3N3b3JkLmpzIDIwMjEtMDEtMjBcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDIxIEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoJ2ZvcmdvdF9wYXNzd29yZCcsIFtdLCBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgbGV0ICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgZGVmYXVsdHMgPSB7fSxcbiAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgIG1vZHVsZSA9IHt9O1xuXG4gICAgLyoqXG4gICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICogQGNvbnN0cnVjdG9yXG4gICAgICovXG4gICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICBjb25zdCB7IG1pblBhc3N3b3JkTGVuZ3RoIH0gPSAkdGhpcy5kYXRhKCk7XG5cbiAgICAgICAgY29uc3QgcGFzc3dvcmRDaGFuZ2UgPSAoKSA9PiB7XG4gICAgICAgICAgICBjb25zdCBuZXdQYXNzd29yZCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCduZXdQYXNzd29yZCcpLFxuICAgICAgICAgICAgICAgICAgY29uZmlybWVkUGFzc3dvcmQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnY29uZmlybWVkUGFzc3dvcmQnKSxcbiAgICAgICAgICAgICAgICAgIHN1Ym1pdEJ1dHRvbiA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzdWJtaXQtYnV0dG9uJyksXG4gICAgICAgICAgICAgICAgICBwYXNzd29yZExlbmd0aEVsZW1lbnQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFzc3dvcmQtbGVuZ3RoJyksXG4gICAgICAgICAgICAgICAgICBwYXNzd29yZFZhbGlkYXRpb25FbGVtZW50ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3Bhc3N3b3JkLXZhbGlkYXRpb24nKTtcblxuICAgICAgICAgICAgY29uc3QgaGFzTWluaW11bUxlbmd0aCA9IG5ld1Bhc3N3b3JkLnZhbHVlLmxlbmd0aCA+PSBtaW5QYXNzd29yZExlbmd0aCxcbiAgICAgICAgICAgICAgICAgIGlzQ29uZmlybWVkID0gbmV3UGFzc3dvcmQudmFsdWUgPT09IGNvbmZpcm1lZFBhc3N3b3JkLnZhbHVlO1xuXG4gICAgICAgICAgICBpZiAoIWhhc01pbmltdW1MZW5ndGgpIHtcbiAgICAgICAgICAgICAgICBwYXNzd29yZExlbmd0aEVsZW1lbnQuY2xhc3NMaXN0LnJlbW92ZSgnaGlkZGVuJyk7XG4gICAgICAgICAgICAgICAgc3VibWl0QnV0dG9uLmRpc2FibGVkID0gdHJ1ZTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoaGFzTWluaW11bUxlbmd0aCkge1xuICAgICAgICAgICAgICAgIHBhc3N3b3JkTGVuZ3RoRWxlbWVudC5jbGFzc0xpc3QuYWRkKCdoaWRkZW4nKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKCFpc0NvbmZpcm1lZCkge1xuICAgICAgICAgICAgICAgIGlmIChjb25maXJtZWRQYXNzd29yZC52YWx1ZS5sZW5ndGggPiAwICkge1xuICAgICAgICAgICAgICAgICAgICBwYXNzd29yZFZhbGlkYXRpb25FbGVtZW50LmNsYXNzTGlzdC5yZW1vdmUoJ2hpZGRlbicpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBzdWJtaXRCdXR0b24uZGlzYWJsZWQgPSB0cnVlO1xuICAgICAgICAgICAgfSBlbHNlIGlmIChpc0NvbmZpcm1lZCkge1xuICAgICAgICAgICAgICAgIHBhc3N3b3JkVmFsaWRhdGlvbkVsZW1lbnQuY2xhc3NMaXN0LmFkZCgnaGlkZGVuJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChoYXNNaW5pbXVtTGVuZ3RoICYmIGlzQ29uZmlybWVkKSB7XG4gICAgICAgICAgICAgICAgc3VibWl0QnV0dG9uLmRpc2FibGVkID0gZmFsc2U7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbmV3UGFzc3dvcmQnKS5hZGRFdmVudExpc3RlbmVyKCdpbnB1dCcsIHBhc3N3b3JkQ2hhbmdlKTtcbiAgICAgICAgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2NvbmZpcm1lZFBhc3N3b3JkJykuYWRkRXZlbnRMaXN0ZW5lcignaW5wdXQnLCBwYXNzd29yZENoYW5nZSk7XG5cbiAgICAgICAgZG9uZSgpO1xuICAgIH07XG5cbiAgICByZXR1cm4gbW9kdWxlO1xufSk7XG4iXX0=

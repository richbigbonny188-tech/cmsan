'use strict';

/* --------------------------------------------------------------
 callback_service.js 2016-02-01 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Checks the input values of the callback form and shows messages on error or success.
 */
gambio.widgets.module('callback_service', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {
        'successSelector': '#callback-service .alert-success',
        'errorSelector': '#callback-service .alert-danger',
        'vvCodeSelector': '#callback-service #vvcode',
        'vvCodeImageSelector': '#callback-service #vvcode_image'
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Validates the form data. If an error occurs it will show the error message, otherwise the messages will be hidden.
     *
     * @return {boolean}
     * @private
     */
    var _onSubmit = function _onSubmit() {

        var deferred = new $.Deferred();
        $(options.successSelector).addClass('hidden');
        $(options.errorSelector).addClass('hidden');

        $.ajax({
            data: $this.serialize(),
            url: 'request_port.php?module=CallbackService&action=check',
            type: 'GET',
            dataType: 'html',
            success: function success(error_message) {
                if (error_message.length > 0) {
                    $(options.errorSelector).html(error_message).removeClass('hidden');

                    try {
                        Recaptcha.reload();
                    } catch (e) {
                        $(options.vvCodeSelector).val('');
                        $(options.vvCodeImageSelector).attr('src', 'request_port.php?rand=' + Math.random() + '&module=CreateVVCode');
                    }

                    deferred.reject();
                } else {
                    deferred.resolve();
                }
            }
        });
        deferred.done(_submitForm);
        return false;
    };

    /**
     * Submits the form data and shows a success message on success.
     *
     * @private
     */
    var _submitForm = function _submitForm() {

        $.ajax({
            data: $this.serialize(),
            url: 'request_port.php?module=CallbackService&action=send',
            type: 'POST',
            dataType: 'html',
            success: function success(message) {
                if (message.length > 0) {
                    $(options.successSelector).html(message).removeClass('hidden');

                    try {
                        Recaptcha.reload();
                    } catch (e) {
                        $(options.vvCodeSelector).val('');
                        $(options.vvCodeImageSelector).attr('src', 'request_port.php?rand=' + Math.random() + '&module=CreateVVCode');
                    }
                }
            }
        });
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $this.on('submit', _onSubmit);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY2FsbGJhY2tfc2VydmljZS5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9vblN1Ym1pdCIsImRlZmVycmVkIiwiRGVmZXJyZWQiLCJzdWNjZXNzU2VsZWN0b3IiLCJhZGRDbGFzcyIsImVycm9yU2VsZWN0b3IiLCJhamF4Iiwic2VyaWFsaXplIiwidXJsIiwidHlwZSIsImRhdGFUeXBlIiwic3VjY2VzcyIsImVycm9yX21lc3NhZ2UiLCJsZW5ndGgiLCJodG1sIiwicmVtb3ZlQ2xhc3MiLCJSZWNhcHRjaGEiLCJyZWxvYWQiLCJlIiwidnZDb2RlU2VsZWN0b3IiLCJ2YWwiLCJ2dkNvZGVJbWFnZVNlbGVjdG9yIiwiYXR0ciIsIk1hdGgiLCJyYW5kb20iLCJyZWplY3QiLCJyZXNvbHZlIiwiZG9uZSIsIl9zdWJtaXRGb3JtIiwibWVzc2FnZSIsImluaXQiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVdBOzs7QUFHQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksa0JBREosRUFHSSxFQUhKLEVBS0ksVUFBVUMsSUFBVixFQUFnQjs7QUFFWjs7QUFFQTs7QUFFQSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFdBQVc7QUFDUCwyQkFBbUIsa0NBRFo7QUFFUCx5QkFBaUIsaUNBRlY7QUFHUCwwQkFBa0IsMkJBSFg7QUFJUCwrQkFBdUI7QUFKaEIsS0FEZjtBQUFBLFFBT0lDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBUGQ7QUFBQSxRQVFJRCxTQUFTLEVBUmI7O0FBV0E7O0FBRUE7Ozs7OztBQU1BLFFBQUlPLFlBQVksU0FBWkEsU0FBWSxHQUFZOztBQUV4QixZQUFJQyxXQUFXLElBQUlMLEVBQUVNLFFBQU4sRUFBZjtBQUNBTixVQUFFRSxRQUFRSyxlQUFWLEVBQTJCQyxRQUEzQixDQUFvQyxRQUFwQztBQUNBUixVQUFFRSxRQUFRTyxhQUFWLEVBQXlCRCxRQUF6QixDQUFrQyxRQUFsQzs7QUFFQVIsVUFBRVUsSUFBRixDQUFPO0FBQ0haLGtCQUFNQyxNQUFNWSxTQUFOLEVBREg7QUFFSEMsaUJBQUssc0RBRkY7QUFHSEMsa0JBQU0sS0FISDtBQUlIQyxzQkFBVSxNQUpQO0FBS0hDLHFCQUFTLGlCQUFVQyxhQUFWLEVBQXlCO0FBQzlCLG9CQUFJQSxjQUFjQyxNQUFkLEdBQXVCLENBQTNCLEVBQThCO0FBQzFCakIsc0JBQUVFLFFBQVFPLGFBQVYsRUFBeUJTLElBQXpCLENBQThCRixhQUE5QixFQUE2Q0csV0FBN0MsQ0FBeUQsUUFBekQ7O0FBRUEsd0JBQUk7QUFDQUMsa0NBQVVDLE1BQVY7QUFDSCxxQkFGRCxDQUVFLE9BQU9DLENBQVAsRUFBVTtBQUNSdEIsMEJBQUVFLFFBQVFxQixjQUFWLEVBQTBCQyxHQUExQixDQUE4QixFQUE5QjtBQUNBeEIsMEJBQUVFLFFBQVF1QixtQkFBVixFQUErQkMsSUFBL0IsQ0FBb0MsS0FBcEMsRUFBMkMsMkJBQTJCQyxLQUFLQyxNQUFMLEVBQTNCLEdBQTJDLHNCQUF0RjtBQUNIOztBQUVEdkIsNkJBQVN3QixNQUFUO0FBRUgsaUJBWkQsTUFZTztBQUNIeEIsNkJBQVN5QixPQUFUO0FBQ0g7QUFDSjtBQXJCRSxTQUFQO0FBdUJBekIsaUJBQVMwQixJQUFULENBQWNDLFdBQWQ7QUFDQSxlQUFPLEtBQVA7QUFDSCxLQS9CRDs7QUFrQ0E7Ozs7O0FBS0EsUUFBSUEsY0FBYyxTQUFkQSxXQUFjLEdBQVk7O0FBRTFCaEMsVUFBRVUsSUFBRixDQUFPO0FBQ0haLGtCQUFNQyxNQUFNWSxTQUFOLEVBREg7QUFFSEMsaUJBQUsscURBRkY7QUFHSEMsa0JBQU0sTUFISDtBQUlIQyxzQkFBVSxNQUpQO0FBS0hDLHFCQUFTLGlCQUFVa0IsT0FBVixFQUFtQjtBQUN4QixvQkFBSUEsUUFBUWhCLE1BQVIsR0FBaUIsQ0FBckIsRUFBd0I7QUFDcEJqQixzQkFBRUUsUUFBUUssZUFBVixFQUEyQlcsSUFBM0IsQ0FBZ0NlLE9BQWhDLEVBQXlDZCxXQUF6QyxDQUFxRCxRQUFyRDs7QUFFQSx3QkFBSTtBQUNBQyxrQ0FBVUMsTUFBVjtBQUNILHFCQUZELENBRUUsT0FBT0MsQ0FBUCxFQUFVO0FBQ1J0QiwwQkFBRUUsUUFBUXFCLGNBQVYsRUFBMEJDLEdBQTFCLENBQThCLEVBQTlCO0FBQ0F4QiwwQkFBRUUsUUFBUXVCLG1CQUFWLEVBQStCQyxJQUEvQixDQUFvQyxLQUFwQyxFQUEyQywyQkFBMkJDLEtBQUtDLE1BQUwsRUFBM0IsR0FBMkMsc0JBQXRGO0FBQ0g7QUFDSjtBQUNKO0FBaEJFLFNBQVA7QUFrQkgsS0FwQkQ7O0FBc0JBOztBQUVBOzs7O0FBSUEvQixXQUFPcUMsSUFBUCxHQUFjLFVBQVVILElBQVYsRUFBZ0I7O0FBRTFCaEMsY0FBTW9DLEVBQU4sQ0FBUyxRQUFULEVBQW1CL0IsU0FBbkI7O0FBRUEyQjtBQUNILEtBTEQ7O0FBT0E7QUFDQSxXQUFPbEMsTUFBUDtBQUNILENBMUdMIiwiZmlsZSI6IndpZGdldHMvY2FsbGJhY2tfc2VydmljZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY2FsbGJhY2tfc2VydmljZS5qcyAyMDE2LTAyLTAxIGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuXG4vKipcbiAqIENoZWNrcyB0aGUgaW5wdXQgdmFsdWVzIG9mIHRoZSBjYWxsYmFjayBmb3JtIGFuZCBzaG93cyBtZXNzYWdlcyBvbiBlcnJvciBvciBzdWNjZXNzLlxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ2NhbGxiYWNrX3NlcnZpY2UnLFxuXG4gICAgW10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAgICAgJ3N1Y2Nlc3NTZWxlY3Rvcic6ICcjY2FsbGJhY2stc2VydmljZSAuYWxlcnQtc3VjY2VzcycsXG4gICAgICAgICAgICAgICAgJ2Vycm9yU2VsZWN0b3InOiAnI2NhbGxiYWNrLXNlcnZpY2UgLmFsZXJ0LWRhbmdlcicsXG4gICAgICAgICAgICAgICAgJ3Z2Q29kZVNlbGVjdG9yJzogJyNjYWxsYmFjay1zZXJ2aWNlICN2dmNvZGUnLFxuICAgICAgICAgICAgICAgICd2dkNvZGVJbWFnZVNlbGVjdG9yJzogJyNjYWxsYmFjay1zZXJ2aWNlICN2dmNvZGVfaW1hZ2UnXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4gICAgICAgIC8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFZhbGlkYXRlcyB0aGUgZm9ybSBkYXRhLiBJZiBhbiBlcnJvciBvY2N1cnMgaXQgd2lsbCBzaG93IHRoZSBlcnJvciBtZXNzYWdlLCBvdGhlcndpc2UgdGhlIG1lc3NhZ2VzIHdpbGwgYmUgaGlkZGVuLlxuICAgICAgICAgKlxuICAgICAgICAgKiBAcmV0dXJuIHtib29sZWFufVxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9vblN1Ym1pdCA9IGZ1bmN0aW9uICgpIHtcblxuICAgICAgICAgICAgdmFyIGRlZmVycmVkID0gbmV3ICQuRGVmZXJyZWQoKTtcbiAgICAgICAgICAgICQob3B0aW9ucy5zdWNjZXNzU2VsZWN0b3IpLmFkZENsYXNzKCdoaWRkZW4nKTtcbiAgICAgICAgICAgICQob3B0aW9ucy5lcnJvclNlbGVjdG9yKS5hZGRDbGFzcygnaGlkZGVuJyk7XG5cbiAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgZGF0YTogJHRoaXMuc2VyaWFsaXplKCksXG4gICAgICAgICAgICAgICAgdXJsOiAncmVxdWVzdF9wb3J0LnBocD9tb2R1bGU9Q2FsbGJhY2tTZXJ2aWNlJmFjdGlvbj1jaGVjaycsXG4gICAgICAgICAgICAgICAgdHlwZTogJ0dFVCcsXG4gICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdodG1sJyxcbiAgICAgICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAoZXJyb3JfbWVzc2FnZSkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoZXJyb3JfbWVzc2FnZS5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKG9wdGlvbnMuZXJyb3JTZWxlY3RvcikuaHRtbChlcnJvcl9tZXNzYWdlKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRyeSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgUmVjYXB0Y2hhLnJlbG9hZCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQob3B0aW9ucy52dkNvZGVTZWxlY3RvcikudmFsKCcnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKG9wdGlvbnMudnZDb2RlSW1hZ2VTZWxlY3RvcikuYXR0cignc3JjJywgJ3JlcXVlc3RfcG9ydC5waHA/cmFuZD0nICsgTWF0aC5yYW5kb20oKSArICcmbW9kdWxlPUNyZWF0ZVZWQ29kZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBkZWZlcnJlZC5yZWplY3QoKTtcblxuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgZGVmZXJyZWQucmVzb2x2ZSgpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBkZWZlcnJlZC5kb25lKF9zdWJtaXRGb3JtKTtcbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfTtcblxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBTdWJtaXRzIHRoZSBmb3JtIGRhdGEgYW5kIHNob3dzIGEgc3VjY2VzcyBtZXNzYWdlIG9uIHN1Y2Nlc3MuXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3N1Ym1pdEZvcm0gPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgZGF0YTogJHRoaXMuc2VyaWFsaXplKCksXG4gICAgICAgICAgICAgICAgdXJsOiAncmVxdWVzdF9wb3J0LnBocD9tb2R1bGU9Q2FsbGJhY2tTZXJ2aWNlJmFjdGlvbj1zZW5kJyxcbiAgICAgICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdodG1sJyxcbiAgICAgICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAobWVzc2FnZSkge1xuICAgICAgICAgICAgICAgICAgICBpZiAobWVzc2FnZS5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKG9wdGlvbnMuc3VjY2Vzc1NlbGVjdG9yKS5odG1sKG1lc3NhZ2UpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdHJ5IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBSZWNhcHRjaGEucmVsb2FkKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGNhdGNoIChlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJChvcHRpb25zLnZ2Q29kZVNlbGVjdG9yKS52YWwoJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQob3B0aW9ucy52dkNvZGVJbWFnZVNlbGVjdG9yKS5hdHRyKCdzcmMnLCAncmVxdWVzdF9wb3J0LnBocD9yYW5kPScgKyBNYXRoLnJhbmRvbSgpICsgJyZtb2R1bGU9Q3JlYXRlVlZDb2RlJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICAgICAkdGhpcy5vbignc3VibWl0JywgX29uU3VibWl0KTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTsiXX0=

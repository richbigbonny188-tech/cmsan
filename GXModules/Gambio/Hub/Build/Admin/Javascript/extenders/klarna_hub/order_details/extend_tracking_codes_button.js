'use strict';

/* --------------------------------------------------------------
 extend_tracking_codes_button.js 2022-03-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Extends the add tracking code modal with a "notify-klarna" checkbox.
 *
 */
(function () {
    'use strict';

    /**
     * Initializes the module.
     *
     * @private
     */

    var init = function init() {
        // Tracking code selectors.
        var $shippingCompanySelect = $('#delivery-service');
        var $trackingCodeText = $('#tracking-number');

        $('.notify-klarna-container').show();

        $trackingCodeText.one('change', function () {
            var $submitButton = $trackingCodeText.closest('.ui-dialog').find('.btn-primary');

            $submitButton.hide();

            $submitButton.after('<button type="button" class="btn btn-primary ui-button ui-corner-all ui-widget" id="btn-add-tracking-number">' + $submitButton.html() + '</button>');

            var $addTrackingCodeButton = $('#btn-add-tracking-number');

            $addTrackingCodeButton.off('click.notifyklarna').on('click.notifyklarna', function () {
                if ($shippingCompanySelect.val() === '') {
                    return;
                }

                if ($trackingCodeText.val() === '') {
                    return;
                }

                var shippingCompany = $shippingCompanySelect.find('option:selected').text();
                var trackingNumber = $trackingCodeText.val();

                if ($('#notify-klarna').is(':checked')) {
                    KlarnaHub.Api.executeAddTrackingCode(shippingCompany, trackingNumber).then($submitButton.trigger('click'));
                } else {
                    $submitButton.trigger('click');
                }
            });
        });
    };

    KlarnaHub.on('ready', function () {
        return init();
    });
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZXh0ZW5kZXJzL2tsYXJuYV9odWIvb3JkZXJfZGV0YWlscy9leHRlbmRfdHJhY2tpbmdfY29kZXNfYnV0dG9uLmpzIl0sIm5hbWVzIjpbImluaXQiLCIkc2hpcHBpbmdDb21wYW55U2VsZWN0IiwiJCIsIiR0cmFja2luZ0NvZGVUZXh0Iiwic2hvdyIsIm9uZSIsIiRzdWJtaXRCdXR0b24iLCJjbG9zZXN0IiwiZmluZCIsImhpZGUiLCJhZnRlciIsImh0bWwiLCIkYWRkVHJhY2tpbmdDb2RlQnV0dG9uIiwib2ZmIiwib24iLCJ2YWwiLCJzaGlwcGluZ0NvbXBhbnkiLCJ0ZXh0IiwidHJhY2tpbmdOdW1iZXIiLCJpcyIsIktsYXJuYUh1YiIsIkFwaSIsImV4ZWN1dGVBZGRUcmFja2luZ0NvZGUiLCJ0aGVuIiwidHJpZ2dlciJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUEsQ0FBQyxZQUFXO0FBQ1g7O0FBRUE7Ozs7OztBQUtBLFFBQU1BLE9BQU8sU0FBUEEsSUFBTyxHQUFNO0FBQ2xCO0FBQ00sWUFBSUMseUJBQXlCQyxFQUFFLG1CQUFGLENBQTdCO0FBQ0EsWUFBSUMsb0JBQXlCRCxFQUFFLGtCQUFGLENBQTdCOztBQUVBQSxVQUFFLDBCQUFGLEVBQThCRSxJQUE5Qjs7QUFFQUQsMEJBQWtCRSxHQUFsQixDQUFzQixRQUF0QixFQUFnQyxZQUFNO0FBQ2xDLGdCQUFNQyxnQkFBZ0JILGtCQUFrQkksT0FBbEIsQ0FBMEIsWUFBMUIsRUFBd0NDLElBQXhDLENBQTZDLGNBQTdDLENBQXRCOztBQUVBRiwwQkFBY0csSUFBZDs7QUFFQUgsMEJBQWNJLEtBQWQsQ0FBb0Isa0hBQ2RKLGNBQWNLLElBQWQsRUFEYyxHQUNTLFdBRDdCOztBQUdBLGdCQUFNQyx5QkFBeUJWLEVBQUUsMEJBQUYsQ0FBL0I7O0FBRUFVLG1DQUF1QkMsR0FBdkIsQ0FBMkIsb0JBQTNCLEVBQWlEQyxFQUFqRCxDQUFvRCxvQkFBcEQsRUFBMEUsWUFBTTtBQUM1RSxvQkFBSWIsdUJBQXVCYyxHQUF2QixPQUFpQyxFQUFyQyxFQUF5QztBQUNyQztBQUNIOztBQUVELG9CQUFJWixrQkFBa0JZLEdBQWxCLE9BQTRCLEVBQWhDLEVBQW9DO0FBQ2hDO0FBQ0g7O0FBRUQsb0JBQU1DLGtCQUFrQmYsdUJBQXVCTyxJQUF2QixDQUE0QixpQkFBNUIsRUFBK0NTLElBQS9DLEVBQXhCO0FBQ0Esb0JBQU1DLGlCQUFpQmYsa0JBQWtCWSxHQUFsQixFQUF2Qjs7QUFFQSxvQkFBSWIsRUFBRSxnQkFBRixFQUFvQmlCLEVBQXBCLENBQXVCLFVBQXZCLENBQUosRUFBd0M7QUFDcENDLDhCQUFVQyxHQUFWLENBQWNDLHNCQUFkLENBQXFDTixlQUFyQyxFQUFzREUsY0FBdEQsRUFBc0VLLElBQXRFLENBQTJFakIsY0FBY2tCLE9BQWQsQ0FBc0IsT0FBdEIsQ0FBM0U7QUFDSCxpQkFGRCxNQUVPO0FBQ0hsQixrQ0FBY2tCLE9BQWQsQ0FBc0IsT0FBdEI7QUFDSDtBQUVKLGFBbEJEO0FBbUJILFNBN0JEO0FBOEJOLEtBckNEOztBQXVDQUosY0FBVU4sRUFBVixDQUFhLE9BQWIsRUFBc0I7QUFBQSxlQUFNZCxNQUFOO0FBQUEsS0FBdEI7QUFDQSxDQWhERCIsImZpbGUiOiJBZG1pbi9KYXZhc2NyaXB0L2V4dGVuZGVycy9rbGFybmFfaHViL29yZGVyX2RldGFpbHMvZXh0ZW5kX3RyYWNraW5nX2NvZGVzX2J1dHRvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZXh0ZW5kX3RyYWNraW5nX2NvZGVzX2J1dHRvbi5qcyAyMDIyLTAzLTE4XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAyMiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBFeHRlbmRzIHRoZSBhZGQgdHJhY2tpbmcgY29kZSBtb2RhbCB3aXRoIGEgXCJub3RpZnkta2xhcm5hXCIgY2hlY2tib3guXG4gKlxuICovXG4oZnVuY3Rpb24oKSB7XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8qKlxuXHQgKiBJbml0aWFsaXplcyB0aGUgbW9kdWxlLlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0Y29uc3QgaW5pdCA9ICgpID0+IHtcblx0XHQvLyBUcmFja2luZyBjb2RlIHNlbGVjdG9ycy5cbiAgICAgICAgbGV0ICRzaGlwcGluZ0NvbXBhbnlTZWxlY3QgPSAkKCcjZGVsaXZlcnktc2VydmljZScpO1xuICAgICAgICBsZXQgJHRyYWNraW5nQ29kZVRleHQgICAgICA9ICQoJyN0cmFja2luZy1udW1iZXInKTtcbiAgICAgICAgXG4gICAgICAgICQoJy5ub3RpZnkta2xhcm5hLWNvbnRhaW5lcicpLnNob3coKTtcbiAgICAgICAgXG4gICAgICAgICR0cmFja2luZ0NvZGVUZXh0Lm9uZSgnY2hhbmdlJywgKCkgPT4ge1xuICAgICAgICAgICAgY29uc3QgJHN1Ym1pdEJ1dHRvbiA9ICR0cmFja2luZ0NvZGVUZXh0LmNsb3Nlc3QoJy51aS1kaWFsb2cnKS5maW5kKCcuYnRuLXByaW1hcnknKTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgJHN1Ym1pdEJ1dHRvbi5oaWRlKCk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgICRzdWJtaXRCdXR0b24uYWZ0ZXIoJzxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwiYnRuIGJ0bi1wcmltYXJ5IHVpLWJ1dHRvbiB1aS1jb3JuZXItYWxsIHVpLXdpZGdldFwiIGlkPVwiYnRuLWFkZC10cmFja2luZy1udW1iZXJcIj4nXG4gICAgICAgICAgICAgICAgKyAkc3VibWl0QnV0dG9uLmh0bWwoKSArICc8L2J1dHRvbj4nKTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgY29uc3QgJGFkZFRyYWNraW5nQ29kZUJ1dHRvbiA9ICQoJyNidG4tYWRkLXRyYWNraW5nLW51bWJlcicpO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAkYWRkVHJhY2tpbmdDb2RlQnV0dG9uLm9mZignY2xpY2subm90aWZ5a2xhcm5hJykub24oJ2NsaWNrLm5vdGlmeWtsYXJuYScsICgpID0+IHtcbiAgICAgICAgICAgICAgICBpZiAoJHNoaXBwaW5nQ29tcGFueVNlbGVjdC52YWwoKSA9PT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgICAgICBpZiAoJHRyYWNraW5nQ29kZVRleHQudmFsKCkgPT09ICcnKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgY29uc3Qgc2hpcHBpbmdDb21wYW55ID0gJHNoaXBwaW5nQ29tcGFueVNlbGVjdC5maW5kKCdvcHRpb246c2VsZWN0ZWQnKS50ZXh0KCk7XG4gICAgICAgICAgICAgICAgY29uc3QgdHJhY2tpbmdOdW1iZXIgPSAkdHJhY2tpbmdDb2RlVGV4dC52YWwoKTtcbiAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgICAgICBpZiAoJCgnI25vdGlmeS1rbGFybmEnKS5pcygnOmNoZWNrZWQnKSkge1xuICAgICAgICAgICAgICAgICAgICBLbGFybmFIdWIuQXBpLmV4ZWN1dGVBZGRUcmFja2luZ0NvZGUoc2hpcHBpbmdDb21wYW55LCB0cmFja2luZ051bWJlcikudGhlbigkc3VibWl0QnV0dG9uLnRyaWdnZXIoJ2NsaWNrJykpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICRzdWJtaXRCdXR0b24udHJpZ2dlcignY2xpY2snKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG5cdH07XG5cdFxuXHRLbGFybmFIdWIub24oJ3JlYWR5JywgKCkgPT4gaW5pdCgpKTtcbn0pKCk7ICJdfQ==

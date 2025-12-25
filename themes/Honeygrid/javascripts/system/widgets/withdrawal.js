'use strict';

/* --------------------------------------------------------------
 withdrawals.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Switches the text input field on click on a radio button.
 */
gambio.widgets.module('withdrawal', [jse.source + '/vendor/jquery-ui-dist/jquery-ui.min.css', jse.source + '/vendor/jquery-ui-dist/jquery-ui.min.js'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = { lang: 'de' },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########


    var _toggleInputField = function _toggleInputField() {
        $('.withdrawal-date').toggleClass('active').prop('disabled', function (i, v) {
            return !v;
        });
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $this.on('change', '.withdrawal_form_switcher', _toggleInputField);

        if (options.lang === 'de') {
            $('.withdrawal-date').datepicker({
                dayNamesMin: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
                monthNames: ['Januar', 'Februar', 'M&auml;rz', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
                firstDay: 1,
                dateFormat: 'dd.mm.yy',
                changeMonth: false
            });
        } else {
            $('.withdrawal-date').datepicker({
                firstDay: 1,
                dateFormat: 'dd.mm.yy',
                changeMonth: false
            });
        }

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvd2l0aGRyYXdhbC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwianNlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwibGFuZyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfdG9nZ2xlSW5wdXRGaWVsZCIsInRvZ2dsZUNsYXNzIiwicHJvcCIsImkiLCJ2IiwiaW5pdCIsImRvbmUiLCJvbiIsImRhdGVwaWNrZXIiLCJkYXlOYW1lc01pbiIsIm1vbnRoTmFtZXMiLCJmaXJzdERheSIsImRhdGVGb3JtYXQiLCJjaGFuZ2VNb250aCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7QUFHQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksWUFESixFQUdJLENBQ09DLElBQUlDLE1BRFgsK0NBRU9ELElBQUlDLE1BRlgsNkNBSEosRUFRSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVSOztBQUVRLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsV0FBVyxFQUFDQyxNQUFNLElBQVAsRUFEZjtBQUFBLFFBRUlDLFVBQVVILEVBQUVJLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkgsUUFBbkIsRUFBNkJILElBQTdCLENBRmQ7QUFBQSxRQUdJSCxTQUFTLEVBSGI7O0FBTVI7OztBQUdRLFFBQUlVLG9CQUFvQixTQUFwQkEsaUJBQW9CLEdBQVk7QUFDaENMLFVBQUUsa0JBQUYsRUFBc0JNLFdBQXRCLENBQWtDLFFBQWxDLEVBQTRDQyxJQUE1QyxDQUFpRCxVQUFqRCxFQUE2RCxVQUFVQyxDQUFWLEVBQWFDLENBQWIsRUFBZ0I7QUFDekUsbUJBQU8sQ0FBQ0EsQ0FBUjtBQUNILFNBRkQ7QUFHSCxLQUpEOztBQU9SOztBQUVROzs7O0FBSUFkLFdBQU9lLElBQVAsR0FBYyxVQUFVQyxJQUFWLEVBQWdCOztBQUUxQlosY0FBTWEsRUFBTixDQUFTLFFBQVQsRUFBbUIsMkJBQW5CLEVBQWdEUCxpQkFBaEQ7O0FBRUEsWUFBSUYsUUFBUUQsSUFBUixLQUFpQixJQUFyQixFQUEyQjtBQUN2QkYsY0FBRSxrQkFBRixFQUFzQmEsVUFBdEIsQ0FBaUM7QUFDN0JDLDZCQUFhLENBQUMsSUFBRCxFQUFPLElBQVAsRUFBYSxJQUFiLEVBQW1CLElBQW5CLEVBQXlCLElBQXpCLEVBQStCLElBQS9CLEVBQXFDLElBQXJDLENBRGdCO0FBRTdCQyw0QkFBWSxDQUNSLFFBRFEsRUFDRSxTQURGLEVBQ2EsV0FEYixFQUMwQixPQUQxQixFQUNtQyxLQURuQyxFQUMwQyxNQUQxQyxFQUVSLE1BRlEsRUFFQSxRQUZBLEVBRVUsV0FGVixFQUV1QixTQUZ2QixFQUVrQyxVQUZsQyxFQUdSLFVBSFEsQ0FGaUI7QUFPN0JDLDBCQUFVLENBUG1CO0FBUTdCQyw0QkFBWSxVQVJpQjtBQVM3QkMsNkJBQWE7QUFUZ0IsYUFBakM7QUFXSCxTQVpELE1BWU87QUFDSGxCLGNBQUUsa0JBQUYsRUFBc0JhLFVBQXRCLENBQWlDO0FBQzdCRywwQkFBVSxDQURtQjtBQUU3QkMsNEJBQVksVUFGaUI7QUFHN0JDLDZCQUFhO0FBSGdCLGFBQWpDO0FBS0g7O0FBRURQO0FBQ0gsS0F6QkQ7O0FBMkJBO0FBQ0EsV0FBT2hCLE1BQVA7QUFDSCxDQWpFTCIsImZpbGUiOiJ3aWRnZXRzL3dpdGhkcmF3YWwuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHdpdGhkcmF3YWxzLmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFN3aXRjaGVzIHRoZSB0ZXh0IGlucHV0IGZpZWxkIG9uIGNsaWNrIG9uIGEgcmFkaW8gYnV0dG9uLlxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ3dpdGhkcmF3YWwnLFxuXG4gICAgW1xuICAgICAgICBgJHtqc2Uuc291cmNlfS92ZW5kb3IvanF1ZXJ5LXVpLWRpc3QvanF1ZXJ5LXVpLm1pbi5jc3NgLFxuICAgICAgICBgJHtqc2Uuc291cmNlfS92ZW5kb3IvanF1ZXJ5LXVpLWRpc3QvanF1ZXJ5LXVpLm1pbi5qc2BcbiAgICBdLFxuXG4gICAgZnVuY3Rpb24gKGRhdGEpIHtcblxuICAgICAgICAndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtsYW5nOiAnZGUnfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuXG4gICAgICAgIHZhciBfdG9nZ2xlSW5wdXRGaWVsZCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICQoJy53aXRoZHJhd2FsLWRhdGUnKS50b2dnbGVDbGFzcygnYWN0aXZlJykucHJvcCgnZGlzYWJsZWQnLCBmdW5jdGlvbiAoaSwgdikge1xuICAgICAgICAgICAgICAgIHJldHVybiAhdjtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgICAgICogQGNvbnN0cnVjdG9yXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cbiAgICAgICAgICAgICR0aGlzLm9uKCdjaGFuZ2UnLCAnLndpdGhkcmF3YWxfZm9ybV9zd2l0Y2hlcicsIF90b2dnbGVJbnB1dEZpZWxkKTtcblxuICAgICAgICAgICAgaWYgKG9wdGlvbnMubGFuZyA9PT0gJ2RlJykge1xuICAgICAgICAgICAgICAgICQoJy53aXRoZHJhd2FsLWRhdGUnKS5kYXRlcGlja2VyKHtcbiAgICAgICAgICAgICAgICAgICAgZGF5TmFtZXNNaW46IFsnU28nLCAnTW8nLCAnRGknLCAnTWknLCAnRG8nLCAnRnInLCAnU2EnXSxcbiAgICAgICAgICAgICAgICAgICAgbW9udGhOYW1lczogW1xuICAgICAgICAgICAgICAgICAgICAgICAgJ0phbnVhcicsICdGZWJydWFyJywgJ00mYXVtbDtyeicsICdBcHJpbCcsICdNYWknLCAnSnVuaScsXG4gICAgICAgICAgICAgICAgICAgICAgICAnSnVsaScsICdBdWd1c3QnLCAnU2VwdGVtYmVyJywgJ09rdG9iZXInLCAnTm92ZW1iZXInLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ0RlemVtYmVyJ1xuICAgICAgICAgICAgICAgICAgICBdLFxuICAgICAgICAgICAgICAgICAgICBmaXJzdERheTogMSxcbiAgICAgICAgICAgICAgICAgICAgZGF0ZUZvcm1hdDogJ2RkLm1tLnl5JyxcbiAgICAgICAgICAgICAgICAgICAgY2hhbmdlTW9udGg6IGZhbHNlXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJy53aXRoZHJhd2FsLWRhdGUnKS5kYXRlcGlja2VyKHtcbiAgICAgICAgICAgICAgICAgICAgZmlyc3REYXk6IDEsXG4gICAgICAgICAgICAgICAgICAgIGRhdGVGb3JtYXQ6ICdkZC5tbS55eScsXG4gICAgICAgICAgICAgICAgICAgIGNoYW5nZU1vbnRoOiBmYWxzZVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

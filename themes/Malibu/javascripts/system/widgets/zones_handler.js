'use strict';

/* --------------------------------------------------------------
 zones_handler.js 2017-06-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * The Component for handling the federal state dropdown depending on the country.
 * The field will be blacked out if there are no federal states for the selected
 * country.
 */
gambio.widgets.module('zones_handler', ['form', 'xhr'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $states = $('select#state.form-control'),
        $selectedState = $('input[name=selected_zone_id]'),
        $statesFormGroup = $('select#state.form-control').closest('div.form-group'),
        defaults = {
        loadStates: 'shop.php?do=Zones',
        country: 'select#country.form-control'
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    var _changeHandler = function _changeHandler() {

        var dataset = jse.libs.form.getData($this);

        jse.libs.xhr.ajax({ url: options.loadStates, data: dataset }, true).done(function (result) {

            if (result.success) {

                $states.children('option').remove();
                $selectedState.prop("disabled", false);
                $states.prop("disabled", false);

                $.each(result.data, function (key, value) {

                    if (value.selected) {
                        $states.append($("<option selected/>").val(value.id).text(value.name));
                    } else {
                        $states.append($("<option />").val(value.id).text(value.name));
                    }
                });

                $statesFormGroup.removeClass('hidden').show();
            } else {

                $statesFormGroup.hide();
                $selectedState.prop("disabled", true);
                $states.prop("disabled", true);
            }
        }).always(function () {
            setDisabledAttributeForSubmitButton(false);
        });
    };

    var setDisabledAttributeForSubmitButton = function setDisabledAttributeForSubmitButton(newValue) {
        var submitButton = $('form#account_edit button[type="submit"].btn');

        if (submitButton.length) {
            submitButton.get(0).disabled = newValue;
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        _changeHandler();

        $this.on('change', options.country, _changeHandler);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvem9uZXNfaGFuZGxlci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRzdGF0ZXMiLCIkc2VsZWN0ZWRTdGF0ZSIsIiRzdGF0ZXNGb3JtR3JvdXAiLCJjbG9zZXN0IiwiZGVmYXVsdHMiLCJsb2FkU3RhdGVzIiwiY291bnRyeSIsIm9wdGlvbnMiLCJleHRlbmQiLCJfY2hhbmdlSGFuZGxlciIsImRhdGFzZXQiLCJqc2UiLCJsaWJzIiwiZm9ybSIsImdldERhdGEiLCJ4aHIiLCJhamF4IiwidXJsIiwiZG9uZSIsInJlc3VsdCIsInN1Y2Nlc3MiLCJjaGlsZHJlbiIsInJlbW92ZSIsInByb3AiLCJlYWNoIiwia2V5IiwidmFsdWUiLCJzZWxlY3RlZCIsImFwcGVuZCIsInZhbCIsImlkIiwidGV4dCIsIm5hbWUiLCJyZW1vdmVDbGFzcyIsInNob3ciLCJoaWRlIiwiYWx3YXlzIiwic2V0RGlzYWJsZWRBdHRyaWJ1dGVGb3JTdWJtaXRCdXR0b24iLCJuZXdWYWx1ZSIsInN1Ym1pdEJ1dHRvbiIsImxlbmd0aCIsImdldCIsImRpc2FibGVkIiwiaW5pdCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLGVBREosRUFHSSxDQUNJLE1BREosRUFFSSxLQUZKLENBSEosRUFRSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVBOztBQUVBLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsVUFBVUQsRUFBRSwyQkFBRixDQURkO0FBQUEsUUFFSUUsaUJBQWlCRixFQUFFLDhCQUFGLENBRnJCO0FBQUEsUUFHSUcsbUJBQW1CSCxFQUFFLDJCQUFGLEVBQStCSSxPQUEvQixDQUF1QyxnQkFBdkMsQ0FIdkI7QUFBQSxRQUtJQyxXQUFXO0FBQ1BDLG9CQUFZLG1CQURMO0FBRVBDLGlCQUFTO0FBRkYsS0FMZjtBQUFBLFFBUU9DLFVBQVVSLEVBQUVTLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkosUUFBbkIsRUFBNkJQLElBQTdCLENBUmpCO0FBQUEsUUFTSUQsU0FBUyxFQVRiOztBQVlBLFFBQUlhLGlCQUFpQixTQUFqQkEsY0FBaUIsR0FBWTs7QUFFN0IsWUFBSUMsVUFBVUMsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLE9BQWQsQ0FBc0JoQixLQUF0QixDQUFkOztBQUVBYSxZQUFJQyxJQUFKLENBQVNHLEdBQVQsQ0FBYUMsSUFBYixDQUFrQixFQUFDQyxLQUFLVixRQUFRRixVQUFkLEVBQTBCUixNQUFNYSxPQUFoQyxFQUFsQixFQUE0RCxJQUE1RCxFQUFrRVEsSUFBbEUsQ0FBdUUsVUFBVUMsTUFBVixFQUFrQjs7QUFFckYsZ0JBQUlBLE9BQU9DLE9BQVgsRUFBb0I7O0FBRWhCcEIsd0JBQVFxQixRQUFSLENBQWlCLFFBQWpCLEVBQTJCQyxNQUEzQjtBQUNBckIsK0JBQWVzQixJQUFmLENBQW9CLFVBQXBCLEVBQWdDLEtBQWhDO0FBQ0F2Qix3QkFBUXVCLElBQVIsQ0FBYSxVQUFiLEVBQXlCLEtBQXpCOztBQUVBeEIsa0JBQUV5QixJQUFGLENBQU9MLE9BQU90QixJQUFkLEVBQW9CLFVBQVU0QixHQUFWLEVBQWVDLEtBQWYsRUFBc0I7O0FBRXRDLHdCQUFJQSxNQUFNQyxRQUFWLEVBQW9CO0FBQ2hCM0IsZ0NBQVE0QixNQUFSLENBQWU3QixFQUFFLG9CQUFGLEVBQXdCOEIsR0FBeEIsQ0FBNEJILE1BQU1JLEVBQWxDLEVBQXNDQyxJQUF0QyxDQUEyQ0wsTUFBTU0sSUFBakQsQ0FBZjtBQUNILHFCQUZELE1BRU87QUFDSGhDLGdDQUFRNEIsTUFBUixDQUFlN0IsRUFBRSxZQUFGLEVBQWdCOEIsR0FBaEIsQ0FBb0JILE1BQU1JLEVBQTFCLEVBQThCQyxJQUE5QixDQUFtQ0wsTUFBTU0sSUFBekMsQ0FBZjtBQUNIO0FBQ0osaUJBUEQ7O0FBU0E5QixpQ0FBaUIrQixXQUFqQixDQUE2QixRQUE3QixFQUF1Q0MsSUFBdkM7QUFFSCxhQWpCRCxNQWlCTzs7QUFFSGhDLGlDQUFpQmlDLElBQWpCO0FBQ0FsQywrQkFBZXNCLElBQWYsQ0FBb0IsVUFBcEIsRUFBZ0MsSUFBaEM7QUFDQXZCLHdCQUFRdUIsSUFBUixDQUFhLFVBQWIsRUFBeUIsSUFBekI7QUFFSDtBQUNKLFNBMUJELEVBMEJHYSxNQTFCSCxDQTBCVSxZQUFXO0FBQ3BCQyxnREFBb0MsS0FBcEM7QUFDQSxTQTVCRDtBQThCSCxLQWxDRDs7QUFvQ0gsUUFBSUEsc0NBQXNDLFNBQXRDQSxtQ0FBc0MsQ0FBU0MsUUFBVCxFQUFtQjtBQUM1RCxZQUFJQyxlQUFleEMsRUFBRSw2Q0FBRixDQUFuQjs7QUFFQSxZQUFHd0MsYUFBYUMsTUFBaEIsRUFBdUI7QUFDdEJELHlCQUFhRSxHQUFiLENBQWlCLENBQWpCLEVBQW9CQyxRQUFwQixHQUErQkosUUFBL0I7QUFDQTtBQUNELEtBTkQ7O0FBUUc7O0FBRUE7Ozs7QUFJQTFDLFdBQU8rQyxJQUFQLEdBQWMsVUFBVXpCLElBQVYsRUFBZ0I7O0FBRTFCVDs7QUFFQVgsY0FBTThDLEVBQU4sQ0FBUyxRQUFULEVBQW1CckMsUUFBUUQsT0FBM0IsRUFBb0NHLGNBQXBDOztBQUVBUztBQUNILEtBUEQ7O0FBU0E7QUFDQSxXQUFPdEIsTUFBUDtBQUNILENBdkZMIiwiZmlsZSI6IndpZGdldHMvem9uZXNfaGFuZGxlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gem9uZXNfaGFuZGxlci5qcyAyMDE3LTA2LTAxXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNyBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBUaGUgQ29tcG9uZW50IGZvciBoYW5kbGluZyB0aGUgZmVkZXJhbCBzdGF0ZSBkcm9wZG93biBkZXBlbmRpbmcgb24gdGhlIGNvdW50cnkuXG4gKiBUaGUgZmllbGQgd2lsbCBiZSBibGFja2VkIG91dCBpZiB0aGVyZSBhcmUgbm8gZmVkZXJhbCBzdGF0ZXMgZm9yIHRoZSBzZWxlY3RlZFxuICogY291bnRyeS5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICd6b25lc19oYW5kbGVyJyxcblxuICAgIFtcbiAgICAgICAgJ2Zvcm0nLFxuICAgICAgICAneGhyJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgJHN0YXRlcyA9ICQoJ3NlbGVjdCNzdGF0ZS5mb3JtLWNvbnRyb2wnKSxcbiAgICAgICAgICAgICRzZWxlY3RlZFN0YXRlID0gJCgnaW5wdXRbbmFtZT1zZWxlY3RlZF96b25lX2lkXScpLFxuICAgICAgICAgICAgJHN0YXRlc0Zvcm1Hcm91cCA9ICQoJ3NlbGVjdCNzdGF0ZS5mb3JtLWNvbnRyb2wnKS5jbG9zZXN0KCdkaXYuZm9ybS1ncm91cCcpLFxuXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICBsb2FkU3RhdGVzOiAnc2hvcC5waHA/ZG89Wm9uZXMnLFxuICAgICAgICAgICAgICAgIGNvdW50cnk6ICdzZWxlY3QjY291bnRyeS5mb3JtLWNvbnRyb2wnLFxuICAgICAgICAgICAgfSwgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4gICAgICAgIHZhciBfY2hhbmdlSGFuZGxlciA9IGZ1bmN0aW9uICgpIHtcblxuICAgICAgICAgICAgdmFyIGRhdGFzZXQgPSBqc2UubGlicy5mb3JtLmdldERhdGEoJHRoaXMpO1xuXG4gICAgICAgICAgICBqc2UubGlicy54aHIuYWpheCh7dXJsOiBvcHRpb25zLmxvYWRTdGF0ZXMsIGRhdGE6IGRhdGFzZXR9LCB0cnVlKS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcblxuICAgICAgICAgICAgICAgIGlmIChyZXN1bHQuc3VjY2Vzcykge1xuXG4gICAgICAgICAgICAgICAgICAgICRzdGF0ZXMuY2hpbGRyZW4oJ29wdGlvbicpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAkc2VsZWN0ZWRTdGF0ZS5wcm9wKFwiZGlzYWJsZWRcIiwgZmFsc2UpO1xuICAgICAgICAgICAgICAgICAgICAkc3RhdGVzLnByb3AoXCJkaXNhYmxlZFwiLCBmYWxzZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgJC5lYWNoKHJlc3VsdC5kYXRhLCBmdW5jdGlvbiAoa2V5LCB2YWx1ZSkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodmFsdWUuc2VsZWN0ZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkc3RhdGVzLmFwcGVuZCgkKFwiPG9wdGlvbiBzZWxlY3RlZC8+XCIpLnZhbCh2YWx1ZS5pZCkudGV4dCh2YWx1ZS5uYW1lKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzdGF0ZXMuYXBwZW5kKCQoXCI8b3B0aW9uIC8+XCIpLnZhbCh2YWx1ZS5pZCkudGV4dCh2YWx1ZS5uYW1lKSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICRzdGF0ZXNGb3JtR3JvdXAucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpLnNob3coKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJHN0YXRlc0Zvcm1Hcm91cC5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICRzZWxlY3RlZFN0YXRlLnByb3AoXCJkaXNhYmxlZFwiLCB0cnVlKTtcbiAgICAgICAgICAgICAgICAgICAgJHN0YXRlcy5wcm9wKFwiZGlzYWJsZWRcIiwgdHJ1ZSk7XG5cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KS5hbHdheXMoZnVuY3Rpb24oKSB7XG5cdCAgICAgICAgICAgIHNldERpc2FibGVkQXR0cmlidXRlRm9yU3VibWl0QnV0dG9uKGZhbHNlKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH07XG5cblx0ICAgIGxldCBzZXREaXNhYmxlZEF0dHJpYnV0ZUZvclN1Ym1pdEJ1dHRvbiA9IGZ1bmN0aW9uKG5ld1ZhbHVlKSB7XG5cdFx0ICAgIGxldCBzdWJtaXRCdXR0b24gPSAkKCdmb3JtI2FjY291bnRfZWRpdCBidXR0b25bdHlwZT1cInN1Ym1pdFwiXS5idG4nKTtcblx0XHRcblx0XHQgICAgaWYoc3VibWl0QnV0dG9uLmxlbmd0aCl7XG5cdFx0ICAgIFx0c3VibWl0QnV0dG9uLmdldCgwKS5kaXNhYmxlZCA9IG5ld1ZhbHVlO1xuXHRcdCAgICB9XG5cdCAgICB9O1xuICAgICAgICBcbiAgICAgICAgLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblxuICAgICAgICAgICAgX2NoYW5nZUhhbmRsZXIoKTtcblxuICAgICAgICAgICAgJHRoaXMub24oJ2NoYW5nZScsIG9wdGlvbnMuY291bnRyeSwgX2NoYW5nZUhhbmRsZXIpO1xuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

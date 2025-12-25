'use strict';

/* --------------------------------------------------------------
 shipping_calculator.js 2016-05-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that updates the shipping cost box at the
 * shopping cart page
 */
gambio.widgets.module('shipping_calculator', ['form', 'xhr'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        defaults = {
        // URL at which the request is send.
        url: 'shop.php?do=CartShippingCosts',
        selectorMapping: {
            gambioUltraCosts: '.cart_shipping_costs_gambio_ultra_dropdown, .order-total-shipping-info-gambioultra-costs',
            shippingWeight: '.shipping-calculator-shipping-weight-unit, .shipping-weight-value',
            shippingCost: '.shipping-calculator-shipping-costs, .order-total-shipping-info, .shipping-cost-value',
            shippingCalculator: '.shipping-calculator-shipping-modules',
            invalidCombinationError: '#cart_shipping_costs_invalid_combination_error'
        }
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Function that requests the given URL and
     * fills the page with the delivered data
     * @private
     */
    var _updateShippingCosts = function _updateShippingCosts() {
        var formdata = jse.libs.form.getData($this);

        jse.libs.xhr.ajax({ url: options.url, data: formdata }).done(function (result) {
            jse.libs.theme.helpers.fill(result.content, $body, options.selectorMapping);
        });

        // update modal content source
        var value = $this.find('select[name="cart_shipping_country"]').val();
        $('#shipping-information-layer.hidden select[name="cart_shipping_country"] option').attr('selected', false);
        $('#shipping-information-layer.hidden select[name="cart_shipping_country"] option[value="' + value + '"]').attr('selected', true);
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $this.on('change update', _updateShippingCosts);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2hpcHBpbmdfY2FsY3VsYXRvci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRib2R5IiwiZGVmYXVsdHMiLCJ1cmwiLCJzZWxlY3Rvck1hcHBpbmciLCJnYW1iaW9VbHRyYUNvc3RzIiwic2hpcHBpbmdXZWlnaHQiLCJzaGlwcGluZ0Nvc3QiLCJzaGlwcGluZ0NhbGN1bGF0b3IiLCJpbnZhbGlkQ29tYmluYXRpb25FcnJvciIsIm9wdGlvbnMiLCJleHRlbmQiLCJfdXBkYXRlU2hpcHBpbmdDb3N0cyIsImZvcm1kYXRhIiwianNlIiwibGlicyIsImZvcm0iLCJnZXREYXRhIiwieGhyIiwiYWpheCIsImRvbmUiLCJyZXN1bHQiLCJ0aGVtZSIsImhlbHBlcnMiLCJmaWxsIiwiY29udGVudCIsInZhbHVlIiwiZmluZCIsInZhbCIsImF0dHIiLCJpbml0Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7OztBQUlBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSxxQkFESixFQUdJLENBQUMsTUFBRCxFQUFTLEtBQVQsQ0FISixFQUtJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRVI7O0FBRVEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxRQUFRRCxFQUFFLE1BQUYsQ0FEWjtBQUFBLFFBRUlFLFdBQVc7QUFDUDtBQUNBQyxhQUFLLCtCQUZFO0FBR1BDLHlCQUFpQjtBQUNiQyw4QkFBa0IsMEZBREw7QUFFYkMsNEJBQWdCLG1FQUZIO0FBR2JDLDBCQUFjLHVGQUhEO0FBSWJDLGdDQUFvQix1Q0FKUDtBQUtiQyxxQ0FBeUI7QUFMWjtBQUhWLEtBRmY7QUFBQSxRQWFJQyxVQUFVVixFQUFFVyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJULFFBQW5CLEVBQTZCSixJQUE3QixDQWJkO0FBQUEsUUFjSUQsU0FBUyxFQWRiOztBQWlCUjs7QUFFUTs7Ozs7QUFLQSxRQUFJZSx1QkFBdUIsU0FBdkJBLG9CQUF1QixHQUFZO0FBQ25DLFlBQUlDLFdBQVdDLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxPQUFkLENBQXNCbEIsS0FBdEIsQ0FBZjs7QUFFQWUsWUFBSUMsSUFBSixDQUFTRyxHQUFULENBQWFDLElBQWIsQ0FBa0IsRUFBQ2hCLEtBQUtPLFFBQVFQLEdBQWQsRUFBbUJMLE1BQU1lLFFBQXpCLEVBQWxCLEVBQXNETyxJQUF0RCxDQUEyRCxVQUFVQyxNQUFWLEVBQWtCO0FBQ3pFUCxnQkFBSUMsSUFBSixDQUFTTyxLQUFULENBQWVDLE9BQWYsQ0FBdUJDLElBQXZCLENBQTRCSCxPQUFPSSxPQUFuQyxFQUE0Q3hCLEtBQTVDLEVBQW1EUyxRQUFRTixlQUEzRDtBQUNILFNBRkQ7O0FBSUE7QUFDQSxZQUFJc0IsUUFBUTNCLE1BQU00QixJQUFOLENBQVcsc0NBQVgsRUFBbURDLEdBQW5ELEVBQVo7QUFDQTVCLFVBQUUsZ0ZBQUYsRUFBb0Y2QixJQUFwRixDQUF5RixVQUF6RixFQUFxRyxLQUFyRztBQUNBN0IsVUFBRSwyRkFBMkYwQixLQUEzRixHQUFtRyxJQUFyRyxFQUNLRyxJQURMLENBQ1UsVUFEVixFQUNzQixJQUR0QjtBQUVILEtBWkQ7O0FBZVI7O0FBRVE7Ozs7QUFJQWhDLFdBQU9pQyxJQUFQLEdBQWMsVUFBVVYsSUFBVixFQUFnQjs7QUFFMUJyQixjQUFNZ0MsRUFBTixDQUFTLGVBQVQsRUFBMEJuQixvQkFBMUI7O0FBRUFRO0FBRUgsS0FORDs7QUFRQTtBQUNBLFdBQU92QixNQUFQO0FBQ0gsQ0FsRUwiLCJmaWxlIjoid2lkZ2V0cy9zaGlwcGluZ19jYWxjdWxhdG9yLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzaGlwcGluZ19jYWxjdWxhdG9yLmpzIDIwMTYtMDUtMTlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IHVwZGF0ZXMgdGhlIHNoaXBwaW5nIGNvc3QgYm94IGF0IHRoZVxuICogc2hvcHBpbmcgY2FydCBwYWdlXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnc2hpcHBpbmdfY2FsY3VsYXRvcicsXG5cbiAgICBbJ2Zvcm0nLCAneGhyJ10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICRib2R5ID0gJCgnYm9keScpLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAgICAgLy8gVVJMIGF0IHdoaWNoIHRoZSByZXF1ZXN0IGlzIHNlbmQuXG4gICAgICAgICAgICAgICAgdXJsOiAnc2hvcC5waHA/ZG89Q2FydFNoaXBwaW5nQ29zdHMnLFxuICAgICAgICAgICAgICAgIHNlbGVjdG9yTWFwcGluZzoge1xuICAgICAgICAgICAgICAgICAgICBnYW1iaW9VbHRyYUNvc3RzOiAnLmNhcnRfc2hpcHBpbmdfY29zdHNfZ2FtYmlvX3VsdHJhX2Ryb3Bkb3duLCAub3JkZXItdG90YWwtc2hpcHBpbmctaW5mby1nYW1iaW91bHRyYS1jb3N0cycsXG4gICAgICAgICAgICAgICAgICAgIHNoaXBwaW5nV2VpZ2h0OiAnLnNoaXBwaW5nLWNhbGN1bGF0b3Itc2hpcHBpbmctd2VpZ2h0LXVuaXQsIC5zaGlwcGluZy13ZWlnaHQtdmFsdWUnLFxuICAgICAgICAgICAgICAgICAgICBzaGlwcGluZ0Nvc3Q6ICcuc2hpcHBpbmctY2FsY3VsYXRvci1zaGlwcGluZy1jb3N0cywgLm9yZGVyLXRvdGFsLXNoaXBwaW5nLWluZm8sIC5zaGlwcGluZy1jb3N0LXZhbHVlJyxcbiAgICAgICAgICAgICAgICAgICAgc2hpcHBpbmdDYWxjdWxhdG9yOiAnLnNoaXBwaW5nLWNhbGN1bGF0b3Itc2hpcHBpbmctbW9kdWxlcycsXG4gICAgICAgICAgICAgICAgICAgIGludmFsaWRDb21iaW5hdGlvbkVycm9yOiAnI2NhcnRfc2hpcHBpbmdfY29zdHNfaW52YWxpZF9jb21iaW5hdGlvbl9lcnJvcidcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBGdW5jdGlvbiB0aGF0IHJlcXVlc3RzIHRoZSBnaXZlbiBVUkwgYW5kXG4gICAgICAgICAqIGZpbGxzIHRoZSBwYWdlIHdpdGggdGhlIGRlbGl2ZXJlZCBkYXRhXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3VwZGF0ZVNoaXBwaW5nQ29zdHMgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgZm9ybWRhdGEgPSBqc2UubGlicy5mb3JtLmdldERhdGEoJHRoaXMpO1xuXG4gICAgICAgICAgICBqc2UubGlicy54aHIuYWpheCh7dXJsOiBvcHRpb25zLnVybCwgZGF0YTogZm9ybWRhdGF9KS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcbiAgICAgICAgICAgICAgICBqc2UubGlicy50aGVtZS5oZWxwZXJzLmZpbGwocmVzdWx0LmNvbnRlbnQsICRib2R5LCBvcHRpb25zLnNlbGVjdG9yTWFwcGluZyk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy8gdXBkYXRlIG1vZGFsIGNvbnRlbnQgc291cmNlXG4gICAgICAgICAgICB2YXIgdmFsdWUgPSAkdGhpcy5maW5kKCdzZWxlY3RbbmFtZT1cImNhcnRfc2hpcHBpbmdfY291bnRyeVwiXScpLnZhbCgpO1xuICAgICAgICAgICAgJCgnI3NoaXBwaW5nLWluZm9ybWF0aW9uLWxheWVyLmhpZGRlbiBzZWxlY3RbbmFtZT1cImNhcnRfc2hpcHBpbmdfY291bnRyeVwiXSBvcHRpb24nKS5hdHRyKCdzZWxlY3RlZCcsIGZhbHNlKTtcbiAgICAgICAgICAgICQoJyNzaGlwcGluZy1pbmZvcm1hdGlvbi1sYXllci5oaWRkZW4gc2VsZWN0W25hbWU9XCJjYXJ0X3NoaXBwaW5nX2NvdW50cnlcIl0gb3B0aW9uW3ZhbHVlPVwiJyArIHZhbHVlICsgJ1wiXScpXG4gICAgICAgICAgICAgICAgLmF0dHIoJ3NlbGVjdGVkJywgdHJ1ZSk7XG4gICAgICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblxuICAgICAgICAgICAgJHRoaXMub24oJ2NoYW5nZSB1cGRhdGUnLCBfdXBkYXRlU2hpcHBpbmdDb3N0cyk7XG5cbiAgICAgICAgICAgIGRvbmUoKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTsiXX0=

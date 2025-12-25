'use strict';

/* --------------------------------------------------------------
 share_cart_button_handler.js 2016-04-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('share_cart_button_handler', ['xhr', gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {
        url: 'shop.php?do=SharedShoppingCart/StoreShoppingCart'
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    var _shareCartHandler = function _shareCartHandler() {
        jse.libs.xhr.ajax({ url: options.url }, true).done(function (result) {
            $('.shared_cart_url').val($("<div/>").html(result.link).text());
        });
        $('.share-cart-response-wrapper').find('p').first().empty();
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        $('body').on(jse.libs.theme.events.SHARE_CART_MODAL_READY(), _shareCartHandler);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2hhcmVfY2FydF9idXR0b25faGFuZGxlci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwidXJsIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zaGFyZUNhcnRIYW5kbGVyIiwianNlIiwibGlicyIsInhociIsImFqYXgiLCJkb25lIiwicmVzdWx0IiwidmFsIiwiaHRtbCIsImxpbmsiLCJ0ZXh0IiwiZmluZCIsImZpcnN0IiwiZW1wdHkiLCJpbml0Iiwib24iLCJ0aGVtZSIsImV2ZW50cyIsIlNIQVJFX0NBUlRfTU9EQUxfUkVBRFkiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksMkJBREosRUFHSSxDQUNJLEtBREosRUFFSUYsT0FBT0csTUFBUCxHQUFnQixjQUZwQixDQUhKLEVBUUksVUFBVUMsSUFBVixFQUFnQjs7QUFFWjs7QUFFUjs7QUFFUSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFdBQVc7QUFDUEMsYUFBSztBQURFLEtBRGY7QUFBQSxRQUlJQyxVQUFVSCxFQUFFSSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJILFFBQW5CLEVBQTZCSCxJQUE3QixDQUpkO0FBQUEsUUFLSUYsU0FBUyxFQUxiOztBQU9BLFFBQUlTLG9CQUFvQixTQUFwQkEsaUJBQW9CLEdBQVk7QUFDaENDLFlBQUlDLElBQUosQ0FBU0MsR0FBVCxDQUFhQyxJQUFiLENBQWtCLEVBQUNQLEtBQUtDLFFBQVFELEdBQWQsRUFBbEIsRUFBc0MsSUFBdEMsRUFBNENRLElBQTVDLENBQWlELFVBQVVDLE1BQVYsRUFBa0I7QUFDL0RYLGNBQUUsa0JBQUYsRUFBc0JZLEdBQXRCLENBQTBCWixFQUFFLFFBQUYsRUFBWWEsSUFBWixDQUFpQkYsT0FBT0csSUFBeEIsRUFBOEJDLElBQTlCLEVBQTFCO0FBQ0gsU0FGRDtBQUdBZixVQUFFLDhCQUFGLEVBQWtDZ0IsSUFBbEMsQ0FBdUMsR0FBdkMsRUFBNENDLEtBQTVDLEdBQW9EQyxLQUFwRDtBQUNILEtBTEQ7O0FBUVI7O0FBRVE7Ozs7QUFJQXRCLFdBQU91QixJQUFQLEdBQWMsVUFBVVQsSUFBVixFQUFnQjtBQUMxQlYsVUFBRSxNQUFGLEVBQVVvQixFQUFWLENBQWFkLElBQUlDLElBQUosQ0FBU2MsS0FBVCxDQUFlQyxNQUFmLENBQXNCQyxzQkFBdEIsRUFBYixFQUE2RGxCLGlCQUE3RDs7QUFFQUs7QUFDSCxLQUpEOztBQU1BO0FBQ0EsV0FBT2QsTUFBUDtBQUNILENBM0NMIiwiZmlsZSI6IndpZGdldHMvc2hhcmVfY2FydF9idXR0b25faGFuZGxlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc2hhcmVfY2FydF9idXR0b25faGFuZGxlci5qcyAyMDE2LTA0LTA4XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdzaGFyZV9jYXJ0X2J1dHRvbl9oYW5kbGVyJyxcblxuICAgIFtcbiAgICAgICAgJ3hocicsXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIHVybDogJ3Nob3AucGhwP2RvPVNoYXJlZFNob3BwaW5nQ2FydC9TdG9yZVNob3BwaW5nQ2FydCdcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgICAgIG1vZHVsZSA9IHt9O1xuXG4gICAgICAgIHZhciBfc2hhcmVDYXJ0SGFuZGxlciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGpzZS5saWJzLnhoci5hamF4KHt1cmw6IG9wdGlvbnMudXJsfSwgdHJ1ZSkuZG9uZShmdW5jdGlvbiAocmVzdWx0KSB7XG4gICAgICAgICAgICAgICAgJCgnLnNoYXJlZF9jYXJ0X3VybCcpLnZhbCgkKFwiPGRpdi8+XCIpLmh0bWwocmVzdWx0LmxpbmspLnRleHQoKSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICQoJy5zaGFyZS1jYXJ0LXJlc3BvbnNlLXdyYXBwZXInKS5maW5kKCdwJykuZmlyc3QoKS5lbXB0eSgpO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgICAgICogQGNvbnN0cnVjdG9yXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG4gICAgICAgICAgICAkKCdib2R5Jykub24oanNlLmxpYnMudGhlbWUuZXZlbnRzLlNIQVJFX0NBUlRfTU9EQUxfUkVBRFkoKSwgX3NoYXJlQ2FydEhhbmRsZXIpO1xuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pOyJdfQ==

'use strict';

/* --------------------------------------------------------------
 product_min_height_fix.js 2016-05-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that fixes min height of product info content element
 */
gambio.widgets.module('product_min_height_fix', [gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $window = $(window),
        defaults = {
        productInfoContent: '.product-info-content' // Selector to apply min height to
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########		

    /**
     * Fix for problem that box overlaps content like cross selling products if product content is too short
     *
     * @private
     */
    var _setProductInfoContentMinHeight = function _setProductInfoContentMinHeight() {
        $(options.productInfoContent).css('min-height', $this.outerHeight() + parseFloat($this.css('top')) + 'px');
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        _setProductInfoContentMinHeight();

        $window.on(jse.libs.theme.events.STICKYBOX_CONTENT_CHANGE(), _setProductInfoContentMinHeight);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9taW5faGVpZ2h0X2ZpeC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR3aW5kb3ciLCJ3aW5kb3ciLCJkZWZhdWx0cyIsInByb2R1Y3RJbmZvQ29udGVudCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2V0UHJvZHVjdEluZm9Db250ZW50TWluSGVpZ2h0IiwiY3NzIiwib3V0ZXJIZWlnaHQiLCJwYXJzZUZsb2F0IiwiaW5pdCIsImRvbmUiLCJvbiIsImpzZSIsImxpYnMiLCJ0aGVtZSIsImV2ZW50cyIsIlNUSUNLWUJPWF9DT05URU5UX0NIQU5HRSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7QUFHQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksd0JBREosRUFHSSxDQUNJRixPQUFPRyxNQUFQLEdBQWdCLGNBRHBCLENBSEosRUFPSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVBOztBQUVBLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsVUFBVUQsRUFBRUUsTUFBRixDQURkO0FBQUEsUUFFSUMsV0FBVztBQUNQQyw0QkFBb0IsdUJBRGIsQ0FDcUM7QUFEckMsS0FGZjtBQUFBLFFBS0lDLFVBQVVMLEVBQUVNLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkgsUUFBbkIsRUFBNkJMLElBQTdCLENBTGQ7QUFBQSxRQU1JRixTQUFTLEVBTmI7O0FBUUE7O0FBRUE7Ozs7O0FBS0EsUUFBSVcsa0NBQWtDLFNBQWxDQSwrQkFBa0MsR0FBWTtBQUM5Q1AsVUFBRUssUUFBUUQsa0JBQVYsRUFBOEJJLEdBQTlCLENBQWtDLFlBQWxDLEVBQWlEVCxNQUFNVSxXQUFOLEtBQXNCQyxXQUFXWCxNQUFNUyxHQUFOLENBQVUsS0FBVixDQUFYLENBQXZCLEdBQXVELElBQXZHO0FBQ0gsS0FGRDs7QUFJQTs7QUFFQTs7OztBQUlBWixXQUFPZSxJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUMxQkw7O0FBRUFOLGdCQUFRWSxFQUFSLENBQVdDLElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFlQyxNQUFmLENBQXNCQyx3QkFBdEIsRUFBWCxFQUE2RFgsK0JBQTdEOztBQUVBSztBQUNILEtBTkQ7O0FBUUE7QUFDQSxXQUFPaEIsTUFBUDtBQUNILENBaERMIiwiZmlsZSI6IndpZGdldHMvcHJvZHVjdF9taW5faGVpZ2h0X2ZpeC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcHJvZHVjdF9taW5faGVpZ2h0X2ZpeC5qcyAyMDE2LTA1LTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCBmaXhlcyBtaW4gaGVpZ2h0IG9mIHByb2R1Y3QgaW5mbyBjb250ZW50IGVsZW1lbnRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdwcm9kdWN0X21pbl9oZWlnaHRfZml4JyxcblxuICAgIFtcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnLFxuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgJHdpbmRvdyA9ICQod2luZG93KSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIHByb2R1Y3RJbmZvQ29udGVudDogJy5wcm9kdWN0LWluZm8tY29udGVudCcgLy8gU2VsZWN0b3IgdG8gYXBwbHkgbWluIGhlaWdodCB0b1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcdFx0XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEZpeCBmb3IgcHJvYmxlbSB0aGF0IGJveCBvdmVybGFwcyBjb250ZW50IGxpa2UgY3Jvc3Mgc2VsbGluZyBwcm9kdWN0cyBpZiBwcm9kdWN0IGNvbnRlbnQgaXMgdG9vIHNob3J0XG4gICAgICAgICAqXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3NldFByb2R1Y3RJbmZvQ29udGVudE1pbkhlaWdodCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICQob3B0aW9ucy5wcm9kdWN0SW5mb0NvbnRlbnQpLmNzcygnbWluLWhlaWdodCcsICgkdGhpcy5vdXRlckhlaWdodCgpICsgcGFyc2VGbG9hdCgkdGhpcy5jc3MoJ3RvcCcpKSkgKyAncHgnKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAgICAgX3NldFByb2R1Y3RJbmZvQ29udGVudE1pbkhlaWdodCgpO1xuXG4gICAgICAgICAgICAkd2luZG93Lm9uKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5TVElDS1lCT1hfQ09OVEVOVF9DSEFOR0UoKSwgX3NldFByb2R1Y3RJbmZvQ29udGVudE1pbkhlaWdodCk7XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7XG4iXX0=

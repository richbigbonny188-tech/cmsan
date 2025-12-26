'use strict';

/* --------------------------------------------------------------
 notifications.js 2016-06-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Used for hiding the Top-Bar- and the Pop-Up-Notification
 */
gambio.widgets.module('notifications', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        initialMarginTop = '0',
        defaults = {
        outerWrapperSelector: '#outer-wrapper',
        headerSelector: '#header'
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    var _topBarPositioning = function _topBarPositioning() {
        var topBarHeight = $('.topbar-notification').outerHeight();

        topBarHeight += parseInt(initialMarginTop.replace('px', ''));

        $(options.outerWrapperSelector).css('margin-top', topBarHeight + 'px');
    };

    var _hideTopbarNotification = function _hideTopbarNotification(event) {
        event.stopPropagation();

        $.ajax({
            type: 'POST',
            url: 'request_port.php?module=Notification&action=hide_topbar',
            timeout: 5000,
            dataType: 'json',
            context: this,
            data: {},
            success: function success(p_response) {
                $('.topbar-notification').remove();
                $(options.outerWrapperSelector).removeClass('topbar-active');

                if ($(options.headerSelector).css('position') !== 'fixed') {
                    $(options.outerWrapperSelector).css('margin-top', initialMarginTop);
                }
            }
        });

        return false;
    };

    var _hidePopUpNotification = function _hidePopUpNotification(event) {
        event.stopPropagation();

        $.ajax({
            type: 'POST',
            url: 'request_port.php?module=Notification&action=hide_popup_notification',
            timeout: 5000,
            dataType: 'json',
            context: this,
            data: {},
            success: function success(p_response) {
                $('.popup-notification').remove();
            }
        });

        return false;
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        initialMarginTop = $(options.outerWrapperSelector).css('margin-top');

        if ($(options.headerSelector).css('position') !== 'fixed') {
            _topBarPositioning();
        }

        $this.on('click', '.hide-topbar-notification', _hideTopbarNotification);
        $this.on('click', '.hide-popup-notification', _hidePopUpNotification);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbm90aWZpY2F0aW9ucy5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImluaXRpYWxNYXJnaW5Ub3AiLCJkZWZhdWx0cyIsIm91dGVyV3JhcHBlclNlbGVjdG9yIiwiaGVhZGVyU2VsZWN0b3IiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3RvcEJhclBvc2l0aW9uaW5nIiwidG9wQmFySGVpZ2h0Iiwib3V0ZXJIZWlnaHQiLCJwYXJzZUludCIsInJlcGxhY2UiLCJjc3MiLCJfaGlkZVRvcGJhck5vdGlmaWNhdGlvbiIsImV2ZW50Iiwic3RvcFByb3BhZ2F0aW9uIiwiYWpheCIsInR5cGUiLCJ1cmwiLCJ0aW1lb3V0IiwiZGF0YVR5cGUiLCJjb250ZXh0Iiwic3VjY2VzcyIsInBfcmVzcG9uc2UiLCJyZW1vdmUiLCJyZW1vdmVDbGFzcyIsIl9oaWRlUG9wVXBOb3RpZmljYXRpb24iLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSxlQURKLEVBR0ksRUFISixFQUtJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRVI7O0FBRVEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxtQkFBbUIsR0FEdkI7QUFBQSxRQUVJQyxXQUFXO0FBQ1BDLDhCQUFzQixnQkFEZjtBQUVQQyx3QkFBZ0I7QUFGVCxLQUZmO0FBQUEsUUFNSUMsVUFBVUwsRUFBRU0sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkosSUFBN0IsQ0FOZDtBQUFBLFFBT0lELFNBQVMsRUFQYjs7QUFVUjs7QUFFUSxRQUFJVSxxQkFBcUIsU0FBckJBLGtCQUFxQixHQUFZO0FBQ2pDLFlBQUlDLGVBQWVSLEVBQUUsc0JBQUYsRUFBMEJTLFdBQTFCLEVBQW5COztBQUVBRCx3QkFBZ0JFLFNBQVNULGlCQUFpQlUsT0FBakIsQ0FBeUIsSUFBekIsRUFBK0IsRUFBL0IsQ0FBVCxDQUFoQjs7QUFFQVgsVUFBRUssUUFBUUYsb0JBQVYsRUFBZ0NTLEdBQWhDLENBQW9DLFlBQXBDLEVBQWtESixlQUFlLElBQWpFO0FBQ0gsS0FORDs7QUFRQSxRQUFJSywwQkFBMEIsU0FBMUJBLHVCQUEwQixDQUFVQyxLQUFWLEVBQWlCO0FBQzNDQSxjQUFNQyxlQUFOOztBQUVBZixVQUFFZ0IsSUFBRixDQUFPO0FBQ0hDLGtCQUFNLE1BREg7QUFFSEMsaUJBQUsseURBRkY7QUFHSEMscUJBQVMsSUFITjtBQUlIQyxzQkFBVSxNQUpQO0FBS0hDLHFCQUFTLElBTE47QUFNSHZCLGtCQUFNLEVBTkg7QUFPSHdCLHFCQUFTLGlCQUFVQyxVQUFWLEVBQXNCO0FBQzNCdkIsa0JBQUUsc0JBQUYsRUFBMEJ3QixNQUExQjtBQUNBeEIsa0JBQUVLLFFBQVFGLG9CQUFWLEVBQWdDc0IsV0FBaEMsQ0FBNEMsZUFBNUM7O0FBRUEsb0JBQUl6QixFQUFFSyxRQUFRRCxjQUFWLEVBQTBCUSxHQUExQixDQUE4QixVQUE5QixNQUE4QyxPQUFsRCxFQUEyRDtBQUN2RFosc0JBQUVLLFFBQVFGLG9CQUFWLEVBQWdDUyxHQUFoQyxDQUFvQyxZQUFwQyxFQUFrRFgsZ0JBQWxEO0FBQ0g7QUFDSjtBQWRFLFNBQVA7O0FBaUJBLGVBQU8sS0FBUDtBQUNILEtBckJEOztBQXVCQSxRQUFJeUIseUJBQXlCLFNBQXpCQSxzQkFBeUIsQ0FBVVosS0FBVixFQUFpQjtBQUMxQ0EsY0FBTUMsZUFBTjs7QUFFQWYsVUFBRWdCLElBQUYsQ0FBTztBQUNIQyxrQkFBTSxNQURIO0FBRUhDLGlCQUFLLHFFQUZGO0FBR0hDLHFCQUFTLElBSE47QUFJSEMsc0JBQVUsTUFKUDtBQUtIQyxxQkFBUyxJQUxOO0FBTUh2QixrQkFBTSxFQU5IO0FBT0h3QixxQkFBUyxpQkFBVUMsVUFBVixFQUFzQjtBQUMzQnZCLGtCQUFFLHFCQUFGLEVBQXlCd0IsTUFBekI7QUFDSDtBQVRFLFNBQVA7O0FBWUEsZUFBTyxLQUFQO0FBQ0gsS0FoQkQ7O0FBbUJSOztBQUVROzs7O0FBSUEzQixXQUFPOEIsSUFBUCxHQUFjLFVBQVVDLElBQVYsRUFBZ0I7O0FBRTFCM0IsMkJBQW1CRCxFQUFFSyxRQUFRRixvQkFBVixFQUFnQ1MsR0FBaEMsQ0FBb0MsWUFBcEMsQ0FBbkI7O0FBRUEsWUFBSVosRUFBRUssUUFBUUQsY0FBVixFQUEwQlEsR0FBMUIsQ0FBOEIsVUFBOUIsTUFBOEMsT0FBbEQsRUFBMkQ7QUFDdkRMO0FBQ0g7O0FBRURSLGNBQU04QixFQUFOLENBQVMsT0FBVCxFQUFrQiwyQkFBbEIsRUFBK0NoQix1QkFBL0M7QUFDQWQsY0FBTThCLEVBQU4sQ0FBUyxPQUFULEVBQWtCLDBCQUFsQixFQUE4Q0gsc0JBQTlDOztBQUVBRTtBQUNILEtBWkQ7O0FBY0E7QUFDQSxXQUFPL0IsTUFBUDtBQUNILENBL0ZMIiwiZmlsZSI6IndpZGdldHMvbm90aWZpY2F0aW9ucy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbm90aWZpY2F0aW9ucy5qcyAyMDE2LTA2LTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBVc2VkIGZvciBoaWRpbmcgdGhlIFRvcC1CYXItIGFuZCB0aGUgUG9wLVVwLU5vdGlmaWNhdGlvblxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ25vdGlmaWNhdGlvbnMnLFxuXG4gICAgW10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgIGluaXRpYWxNYXJnaW5Ub3AgPSAnMCcsXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICBvdXRlcldyYXBwZXJTZWxlY3RvcjogJyNvdXRlci13cmFwcGVyJyxcbiAgICAgICAgICAgICAgICBoZWFkZXJTZWxlY3RvcjogJyNoZWFkZXInXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIHZhciBfdG9wQmFyUG9zaXRpb25pbmcgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgdG9wQmFySGVpZ2h0ID0gJCgnLnRvcGJhci1ub3RpZmljYXRpb24nKS5vdXRlckhlaWdodCgpO1xuXG4gICAgICAgICAgICB0b3BCYXJIZWlnaHQgKz0gcGFyc2VJbnQoaW5pdGlhbE1hcmdpblRvcC5yZXBsYWNlKCdweCcsICcnKSk7XG5cbiAgICAgICAgICAgICQob3B0aW9ucy5vdXRlcldyYXBwZXJTZWxlY3RvcikuY3NzKCdtYXJnaW4tdG9wJywgdG9wQmFySGVpZ2h0ICsgJ3B4Jyk7XG4gICAgICAgIH07XG5cbiAgICAgICAgdmFyIF9oaWRlVG9wYmFyTm90aWZpY2F0aW9uID0gZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgICAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICB0eXBlOiAnUE9TVCcsXG4gICAgICAgICAgICAgICAgdXJsOiAncmVxdWVzdF9wb3J0LnBocD9tb2R1bGU9Tm90aWZpY2F0aW9uJmFjdGlvbj1oaWRlX3RvcGJhcicsXG4gICAgICAgICAgICAgICAgdGltZW91dDogNTAwMCxcbiAgICAgICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgICAgIGNvbnRleHQ6IHRoaXMsXG4gICAgICAgICAgICAgICAgZGF0YToge30sXG4gICAgICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHBfcmVzcG9uc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgJCgnLnRvcGJhci1ub3RpZmljYXRpb24nKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICAgICAgJChvcHRpb25zLm91dGVyV3JhcHBlclNlbGVjdG9yKS5yZW1vdmVDbGFzcygndG9wYmFyLWFjdGl2ZScpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICgkKG9wdGlvbnMuaGVhZGVyU2VsZWN0b3IpLmNzcygncG9zaXRpb24nKSAhPT0gJ2ZpeGVkJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgJChvcHRpb25zLm91dGVyV3JhcHBlclNlbGVjdG9yKS5jc3MoJ21hcmdpbi10b3AnLCBpbml0aWFsTWFyZ2luVG9wKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgIH07XG5cbiAgICAgICAgdmFyIF9oaWRlUG9wVXBOb3RpZmljYXRpb24gPSBmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcbiAgICAgICAgICAgICAgICB1cmw6ICdyZXF1ZXN0X3BvcnQucGhwP21vZHVsZT1Ob3RpZmljYXRpb24mYWN0aW9uPWhpZGVfcG9wdXBfbm90aWZpY2F0aW9uJyxcbiAgICAgICAgICAgICAgICB0aW1lb3V0OiA1MDAwLFxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgICAgICAgICAgY29udGV4dDogdGhpcyxcbiAgICAgICAgICAgICAgICBkYXRhOiB7fSxcbiAgICAgICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocF9yZXNwb25zZSkge1xuICAgICAgICAgICAgICAgICAgICAkKCcucG9wdXAtbm90aWZpY2F0aW9uJykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgfTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICAgICBpbml0aWFsTWFyZ2luVG9wID0gJChvcHRpb25zLm91dGVyV3JhcHBlclNlbGVjdG9yKS5jc3MoJ21hcmdpbi10b3AnKTtcblxuICAgICAgICAgICAgaWYgKCQob3B0aW9ucy5oZWFkZXJTZWxlY3RvcikuY3NzKCdwb3NpdGlvbicpICE9PSAnZml4ZWQnKSB7XG4gICAgICAgICAgICAgICAgX3RvcEJhclBvc2l0aW9uaW5nKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICR0aGlzLm9uKCdjbGljaycsICcuaGlkZS10b3BiYXItbm90aWZpY2F0aW9uJywgX2hpZGVUb3BiYXJOb3RpZmljYXRpb24pO1xuICAgICAgICAgICAgJHRoaXMub24oJ2NsaWNrJywgJy5oaWRlLXBvcHVwLW5vdGlmaWNhdGlvbicsIF9oaWRlUG9wVXBOb3RpZmljYXRpb24pO1xuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pOyJdfQ==

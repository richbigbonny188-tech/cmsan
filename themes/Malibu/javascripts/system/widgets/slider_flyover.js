'use strict';

/* --------------------------------------------------------------
 slider_flyover.js 2016-02-04 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gets the size of the biggest image from the applied element and puts the previous and next buttons to the right
 * position, if the screen-width is bigger than 1920px.
 */
gambio.widgets.module('slider_flyover', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {},
        flyover_container = '#slider_flyover_container',
        mouse_pos_x,
        mouse_pos_y,
        actual_area_id,
        request;

    // ########## PRIVATE FUNCTIONS ##########

    var _remove_flyover = function _remove_flyover() {
        if (actual_area_id == 0) {
            if (request) {
                request.abort();
            }
            $(flyover_container).remove();
        }
    };

    var _create_container = function _create_container() {
        if ($(flyover_container).length == 0) {
            $('body').append('<div id="slider_flyover_container"></div>');
        }
    };

    var _box_position = function _box_position(self) {
        self.off("mousemove");

        self.on("mousemove", function (e) {
            mouse_pos_x = e.pageX;
            mouse_pos_y = e.pageY;
        });
    };

    var _show_flyover = function _show_flyover(self, response) {
        var id = self.attr("id").split("_");
        if (id[1] == actual_area_id && $.trim(response) != "" && $.trim(response.replace(/<br \/>/g, "")) != "") {
            $(flyover_container).addClass(actual_area_id);
            $(flyover_container).html(response);
            $(flyover_container).css("left", mouse_pos_x + 5);
            $(flyover_container).css("top", mouse_pos_y);
            if (mouse_pos_x - $(document).scrollLeft() + $(flyover_container).width() + 30 >= $(window).width()) {

                $(flyover_container).css("left", mouse_pos_x - $(flyover_container).width() - 25);
            }
            if (mouse_pos_y - $(document).scrollTop() + $(flyover_container).height() + 30 >= $(window).height()) {
                $(flyover_container).css("top", mouse_pos_y - $(flyover_container).height() - 25);
            }
            $(flyover_container).show();
        }
    };

    var _get_flyover_info = function _get_flyover_info(self) {
        var id = self.attr("id").split("_");
        actual_area_id = id[1];

        if (actual_area_id != $(flyover_container).attr("class")) {
            if (request) {
                request.abort();
            }

            request = $.ajax({
                type: "POST",
                url: "request_port.php?module=Slider",
                async: true,
                data: { "action": "get_flyover_content", "slider_image_area_id": actual_area_id },
                success: function success(response) {
                    _show_flyover(self, response);
                }
            });
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        var sliderAreaSelectorString = '.swiper-slide area';

        // @TODO Implement flyover content.
        done();
        return;

        $this.on('mouseenter', sliderAreaSelectorString, function () {
            _create_container();
            _box_position($(this));
            _get_flyover_info($(this));
        }).on('mouseleave', sliderAreaSelectorString, function () {
            actual_area_id = 0;
            _remove_flyover();
        });

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2xpZGVyX2ZseW92ZXIuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJmbHlvdmVyX2NvbnRhaW5lciIsIm1vdXNlX3Bvc194IiwibW91c2VfcG9zX3kiLCJhY3R1YWxfYXJlYV9pZCIsInJlcXVlc3QiLCJfcmVtb3ZlX2ZseW92ZXIiLCJhYm9ydCIsInJlbW92ZSIsIl9jcmVhdGVfY29udGFpbmVyIiwibGVuZ3RoIiwiYXBwZW5kIiwiX2JveF9wb3NpdGlvbiIsInNlbGYiLCJvZmYiLCJvbiIsImUiLCJwYWdlWCIsInBhZ2VZIiwiX3Nob3dfZmx5b3ZlciIsInJlc3BvbnNlIiwiaWQiLCJhdHRyIiwic3BsaXQiLCJ0cmltIiwicmVwbGFjZSIsImFkZENsYXNzIiwiaHRtbCIsImNzcyIsImRvY3VtZW50Iiwic2Nyb2xsTGVmdCIsIndpZHRoIiwid2luZG93Iiwic2Nyb2xsVG9wIiwiaGVpZ2h0Iiwic2hvdyIsIl9nZXRfZmx5b3Zlcl9pbmZvIiwiYWpheCIsInR5cGUiLCJ1cmwiLCJhc3luYyIsInN1Y2Nlc3MiLCJpbml0IiwiZG9uZSIsInNsaWRlckFyZWFTZWxlY3RvclN0cmluZyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLGdCQURKLEVBR0ksRUFISixFQUtJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRUE7O0FBRUEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxXQUFXLEVBRGY7QUFBQSxRQUVJQyxVQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQUZkO0FBQUEsUUFHSUQsU0FBUyxFQUhiO0FBQUEsUUFLSU8sb0JBQW9CLDJCQUx4QjtBQUFB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QXFCcUIsSUFBckIsQ0FBMEIsT0FBMUIsQ0FBdEIsRUFBMEQ7QUFDdEQsZ0JBQUlqQixPQUFKLEVBQWE7QUFDVEEsd0JBQVFFLEtBQVI7QUFDSDs7QUFFREYsc0JBQVVSLEVBQUV3QyxJQUFGLENBQU87QUFDYkMsc0JBQU0sTUFETztBQUViQyxxQkFBSyxnQ0FGUTtBQUdiQyx1QkFBTyxJQUhNO0FBSWI3QyxzQkFBTSxFQUFDLFVBQVUscUJBQVgsRUFBa0Msd0JBQXdCUyxjQUExRCxFQUpPO0FBS2JxQyx5QkFBUyxpQkFBVXJCLFFBQVYsRUFBb0I7QUFDekJELGtDQUFjTixJQUFkLEVBQW9CTyxRQUFwQjtBQUNIO0FBUFksYUFBUCxDQUFWO0FBU0g7QUFDSixLQW5CRDs7QUFxQkE7O0FBRUE7Ozs7QUFJQTFCLFdBQU9nRCxJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUMxQixZQUFJQywyQkFBMkIsb0JBQS9COztBQUVBO0FBQ0FEO0FBQ0E7O0FBRUEvQyxjQUNLbUIsRUFETCxDQUNRLFlBRFIsRUFDc0I2Qix3QkFEdEIsRUFDZ0QsWUFBWTtBQUNwRG5DO0FBQ0FHLDBCQUFjZixFQUFFLElBQUYsQ0FBZDtBQUNBdUMsOEJBQWtCdkMsRUFBRSxJQUFGLENBQWxCO0FBQ0gsU0FMTCxFQU1La0IsRUFOTCxDQU1RLFlBTlIsRUFNc0I2Qix3QkFOdEIsRUFNZ0QsWUFBWTtBQUNwRHhDLDZCQUFpQixDQUFqQjtBQUNBRTtBQUNILFNBVEw7O0FBV0FxQztBQUNILEtBbkJEOztBQXFCQTtBQUNBLFdBQU9qRCxNQUFQO0FBQ0gsQ0ExSEwiLCJmaWxlIjoid2lkZ2V0cy9zbGlkZXJfZmx5b3Zlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc2xpZGVyX2ZseW92ZXIuanMgMjAxNi0wMi0wNCBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogR2V0cyB0aGUgc2l6ZSBvZiB0aGUgYmlnZ2VzdCBpbWFnZSBmcm9tIHRoZSBhcHBsaWVkIGVsZW1lbnQgYW5kIHB1dHMgdGhlIHByZXZpb3VzIGFuZCBuZXh0IGJ1dHRvbnMgdG8gdGhlIHJpZ2h0XG4gKiBwb3NpdGlvbiwgaWYgdGhlIHNjcmVlbi13aWR0aCBpcyBiaWdnZXIgdGhhbiAxOTIwcHguXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnc2xpZGVyX2ZseW92ZXInLFxuXG4gICAgW10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7fSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge30sXG5cbiAgICAgICAgICAgIGZseW92ZXJfY29udGFpbmVyID0gJyNzbGlkZXJfZmx5b3Zlcl9jb250YWluZXInLFxuICAgICAgICAgICAgbW91c2VfcG9zX3gsXG4gICAgICAgICAgICBtb3VzZV9wb3NfeSxcbiAgICAgICAgICAgIGFjdHVhbF9hcmVhX2lkLFxuICAgICAgICAgICAgcmVxdWVzdDtcblxuXG4gICAgICAgIC8vICMjIyMjIyMjIyMgUFJJVkFURSBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG4gICAgICAgIHZhciBfcmVtb3ZlX2ZseW92ZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBpZiAoYWN0dWFsX2FyZWFfaWQgPT0gMCkge1xuICAgICAgICAgICAgICAgIGlmIChyZXF1ZXN0KSB7XG4gICAgICAgICAgICAgICAgICAgIHJlcXVlc3QuYWJvcnQoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgJChmbHlvdmVyX2NvbnRhaW5lcikucmVtb3ZlKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgdmFyIF9jcmVhdGVfY29udGFpbmVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgaWYgKCQoZmx5b3Zlcl9jb250YWluZXIpLmxlbmd0aCA9PSAwKSB7XG4gICAgICAgICAgICAgICAgJCgnYm9keScpLmFwcGVuZCgnPGRpdiBpZD1cInNsaWRlcl9mbHlvdmVyX2NvbnRhaW5lclwiPjwvZGl2PicpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIHZhciBfYm94X3Bvc2l0aW9uID0gZnVuY3Rpb24gKHNlbGYpIHtcbiAgICAgICAgICAgIHNlbGYub2ZmKFwibW91c2Vtb3ZlXCIpO1xuXG4gICAgICAgICAgICBzZWxmLm9uKFwibW91c2Vtb3ZlXCIsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgbW91c2VfcG9zX3ggPSBlLnBhZ2VYO1xuICAgICAgICAgICAgICAgIG1vdXNlX3Bvc195ID0gZS5wYWdlWTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgIHZhciBfc2hvd19mbHlvdmVyID0gZnVuY3Rpb24gKHNlbGYsIHJlc3BvbnNlKSB7XG4gICAgICAgICAgICB2YXIgaWQgPSBzZWxmLmF0dHIoXCJpZFwiKS5zcGxpdChcIl9cIik7XG4gICAgICAgICAgICBpZiAoaWRbMV0gPT0gYWN0dWFsX2FyZWFfaWQgJiYgJC50cmltKHJlc3BvbnNlKSAhPSBcIlwiXG4gICAgICAgICAgICAgICAgJiYgJC50cmltKHJlc3BvbnNlLnJlcGxhY2UoLzxiciBcXC8+L2csIFwiXCIpKSAhPSBcIlwiKSB7XG4gICAgICAgICAgICAgICAgJChmbHlvdmVyX2NvbnRhaW5lcikuYWRkQ2xhc3MoYWN0dWFsX2FyZWFfaWQpO1xuICAgICAgICAgICAgICAgICQoZmx5b3Zlcl9jb250YWluZXIpLmh0bWwocmVzcG9uc2UpO1xuICAgICAgICAgICAgICAgICQoZmx5b3Zlcl9jb250YWluZXIpLmNzcyhcImxlZnRcIiwgbW91c2VfcG9zX3ggKyA1KTtcbiAgICAgICAgICAgICAgICAkKGZseW92ZXJfY29udGFpbmVyKS5jc3MoXCJ0b3BcIiwgbW91c2VfcG9zX3kpO1xuICAgICAgICAgICAgICAgIGlmICgobW91c2VfcG9zX3ggLSAkKGRvY3VtZW50KS5zY3JvbGxMZWZ0KCkgKyAkKGZseW92ZXJfY29udGFpbmVyKS53aWR0aCgpXG4gICAgICAgICAgICAgICAgICAgICsgMzApID49ICQod2luZG93KS53aWR0aCgpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJChmbHlvdmVyX2NvbnRhaW5lcilcbiAgICAgICAgICAgICAgICAgICAgICAgIC5jc3MoXCJsZWZ0XCIsIG1vdXNlX3Bvc194IC0gJChmbHlvdmVyX2NvbnRhaW5lcikud2lkdGgoKSAtIDI1KTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaWYgKChtb3VzZV9wb3NfeSAtICQoZG9jdW1lbnQpLnNjcm9sbFRvcCgpICsgJChmbHlvdmVyX2NvbnRhaW5lcikuaGVpZ2h0KClcbiAgICAgICAgICAgICAgICAgICAgKyAzMCkgPj0gJCh3aW5kb3cpLmhlaWdodCgpKSB7XG4gICAgICAgICAgICAgICAgICAgICQoZmx5b3Zlcl9jb250YWluZXIpXG4gICAgICAgICAgICAgICAgICAgICAgICAuY3NzKFwidG9wXCIsIG1vdXNlX3Bvc195IC0gJChmbHlvdmVyX2NvbnRhaW5lcikuaGVpZ2h0KCkgLSAyNSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICQoZmx5b3Zlcl9jb250YWluZXIpLnNob3coKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICB2YXIgX2dldF9mbHlvdmVyX2luZm8gPSBmdW5jdGlvbiAoc2VsZikge1xuICAgICAgICAgICAgdmFyIGlkID0gc2VsZi5hdHRyKFwiaWRcIikuc3BsaXQoXCJfXCIpO1xuICAgICAgICAgICAgYWN0dWFsX2FyZWFfaWQgPSBpZFsxXTtcblxuICAgICAgICAgICAgaWYgKGFjdHVhbF9hcmVhX2lkICE9ICQoZmx5b3Zlcl9jb250YWluZXIpLmF0dHIoXCJjbGFzc1wiKSkge1xuICAgICAgICAgICAgICAgIGlmIChyZXF1ZXN0KSB7XG4gICAgICAgICAgICAgICAgICAgIHJlcXVlc3QuYWJvcnQoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICByZXF1ZXN0ID0gJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogXCJQT1NUXCIsXG4gICAgICAgICAgICAgICAgICAgIHVybDogXCJyZXF1ZXN0X3BvcnQucGhwP21vZHVsZT1TbGlkZXJcIixcbiAgICAgICAgICAgICAgICAgICAgYXN5bmM6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6IHtcImFjdGlvblwiOiBcImdldF9mbHlvdmVyX2NvbnRlbnRcIiwgXCJzbGlkZXJfaW1hZ2VfYXJlYV9pZFwiOiBhY3R1YWxfYXJlYV9pZH0sXG4gICAgICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChyZXNwb25zZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgX3Nob3dfZmx5b3ZlcihzZWxmLCByZXNwb25zZSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAgICAgdmFyIHNsaWRlckFyZWFTZWxlY3RvclN0cmluZyA9ICcuc3dpcGVyLXNsaWRlIGFyZWEnO1xuXG4gICAgICAgICAgICAvLyBAVE9ETyBJbXBsZW1lbnQgZmx5b3ZlciBjb250ZW50LlxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICAgICAgcmV0dXJuO1xuXG4gICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgIC5vbignbW91c2VlbnRlcicsIHNsaWRlckFyZWFTZWxlY3RvclN0cmluZywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICBfY3JlYXRlX2NvbnRhaW5lcigpO1xuICAgICAgICAgICAgICAgICAgICBfYm94X3Bvc2l0aW9uKCQodGhpcykpO1xuICAgICAgICAgICAgICAgICAgICBfZ2V0X2ZseW92ZXJfaW5mbygkKHRoaXMpKTtcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIC5vbignbW91c2VsZWF2ZScsIHNsaWRlckFyZWFTZWxlY3RvclN0cmluZywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICBhY3R1YWxfYXJlYV9pZCA9IDA7XG4gICAgICAgICAgICAgICAgICAgIF9yZW1vdmVfZmx5b3ZlcigpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pOyJdfQ==

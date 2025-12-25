'use strict';

/* --------------------------------------------------------------
 slider_size.js 2016-02-04 gm
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
gambio.widgets.module('slider_size', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {},
        maxWidth = 0,
        nextButton = $('.js-teaser-slider-next.swiper-button-next'),
        prevButton = $('.js-teaser-slider-prev.swiper-button-prev');

    // ########## PRIVATE FUNCTIONS ##########


    /**
     * Gets the biggest image from the applied element and calls the positioning method.
     *
     * @private
     */
    var _getBiggestImageWidth = function _getBiggestImageWidth() {

        var windowWidth = $(window).width();

        $(window).load(function () {
            $('#slider').each(function () {

                $this.find('.swiper-container .swiper-wrapper .swiper-slide img').each(function () {

                    var w = $(this).get(0).naturalWidth;
                    if (w > maxWidth) {
                        maxWidth = w;
                    }
                });
                if (maxWidth && windowWidth > 1920) {
                    _positionButtons(maxWidth);
                }
            });
        });
    };

    /**
     * Puts the previous and next buttons of the swiper to the correct position, if the screen-width is bigger than
     * 1920px
     *
     * @param maxWidth int
     * @private
     */
    var _positionButtons = function _positionButtons(maxWidth) {

        var marginVal = Math.ceil(-(maxWidth / 2) + 30);

        nextButton.css({
            'right': '50%',
            'margin-right': marginVal + 'px'
        });

        prevButton.css({
            'left': '50%',
            'margin-left': marginVal + 'px'
        });
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        _getBiggestImageWidth();

        $(window).resize(function () {
            if ($(window).width() <= 1920 && nextButton.attr('style') && prevButton.attr('style')) {
                nextButton.removeAttr('style');
                prevButton.removeAttr('style');
            } else if ($(window).width() > 1920 && !nextButton.attr('style') && !prevButton.attr('style')) {
                _positionButtons(maxWidth);
            }
        });

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2xpZGVyX3NpemUuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJtYXhXaWR0aCIsIm5leHRCdXR0b24iLCJwcmV2QnV0dG9uIiwiX2dldEJpZ2dlc3RJbWFnZVdpZHRoIiwid2luZG93V2lkdGgiLCJ3aW5kb3ciLCJ3aWR0aCIsImxvYWQiLCJlYWNoIiwiZmluZCIsInciLCJnZXQiLCJuYXR1cmFsV2lkdGgiLCJfcG9zaXRpb25CdXR0b25zIiwibWFyZ2luVmFsIiwiTWF0aCIsImNlaWwiLCJjc3MiLCJpbml0IiwiZG9uZSIsInJlc2l6ZSIsImF0dHIiLCJyZW1vdmVBdHRyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7QUFJQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksYUFESixFQUdJLEVBSEosRUFLSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVBOztBQUVBLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsV0FBVyxFQURmO0FBQUEsUUFFSUMsVUFBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FGZDtBQUFBLFFBR0lELFNBQVMsRUFIYjtBQUFBLFFBS0lPLFdBQVcsQ0FMZjtBQUFBLFFBTUlDLGFBQWFMLEVBQUUsMkNBQUYsQ0FOakI7QUFBQSxRQU9JTSxhQUFhTixFQUFFLDJDQUFGLENBUGpCOztBQVVBOzs7QUFHQTs7Ozs7QUFLQSxRQUFJTyx3QkFBd0IsU0FBeEJBLHFCQUF3QixHQUFZOztBQUVwQyxZQUFJQyxjQUFjUixFQUFFUyxNQUFGLEVBQVVDLEtBQVYsRUFBbEI7O0FBRUFWLFVBQUVTLE1BQUYsRUFBVUUsSUFBVixDQUFlLFlBQVk7QUFDdkJYLGNBQUUsU0FBRixFQUFhWSxJQUFiLENBQWtCLFlBQVk7O0FBRTFCYixzQkFBTWMsSUFBTixDQUFXLHFEQUFYLEVBQWtFRCxJQUFsRSxDQUF1RSxZQUFZOztBQUUvRSx3QkFBSUUsSUFBSWQsRUFBRSxJQUFGLEVBQVFlLEdBQVIsQ0FBWSxDQUFaLEVBQWVDLFlBQXZCO0FBQ0Esd0JBQUlGLElBQUlWLFFBQVIsRUFBa0I7QUFDZEEsbUNBQVdVLENBQVg7QUFDSDtBQUNKLGlCQU5EO0FBT0Esb0JBQUlWLFlBQVlJLGNBQWMsSUFBOUIsRUFBb0M7QUFDaENTLHFDQUFpQmIsUUFBakI7QUFDSDtBQUNKLGFBWkQ7QUFhSCxTQWREO0FBZUgsS0FuQkQ7O0FBcUJBOzs7Ozs7O0FBT0EsUUFBSWEsbUJBQW1CLFNBQW5CQSxnQkFBbUIsQ0FBVWIsUUFBVixFQUFvQjs7QUFFdkMsWUFBSWMsWUFBWUMsS0FBS0MsSUFBTCxDQUFVLEVBQUVoQixXQUFXLENBQWIsSUFBa0IsRUFBNUIsQ0FBaEI7O0FBRUFDLG1CQUFXZ0IsR0FBWCxDQUFlO0FBQ1gscUJBQVMsS0FERTtBQUVYLDRCQUFnQkgsWUFBWTtBQUZqQixTQUFmOztBQUtBWixtQkFBV2UsR0FBWCxDQUFlO0FBQ1gsb0JBQVEsS0FERztBQUVYLDJCQUFlSCxZQUFZO0FBRmhCLFNBQWY7QUFJSCxLQWJEOztBQWdCQTs7QUFFQTs7OztBQUlBckIsV0FBT3lCLElBQVAsR0FBYyxVQUFVQyxJQUFWLEVBQWdCOztBQUUxQmhCOztBQUVBUCxVQUFFUyxNQUFGLEVBQVVlLE1BQVYsQ0FBaUIsWUFBWTtBQUN6QixnQkFBSXhCLEVBQUVTLE1BQUYsRUFBVUMsS0FBVixNQUFxQixJQUFyQixJQUE2QkwsV0FBV29CLElBQVgsQ0FBZ0IsT0FBaEIsQ0FBN0IsSUFBeURuQixXQUFXbUIsSUFBWCxDQUFnQixPQUFoQixDQUE3RCxFQUF1RjtBQUNuRnBCLDJCQUFXcUIsVUFBWCxDQUFzQixPQUF0QjtBQUNBcEIsMkJBQVdvQixVQUFYLENBQXNCLE9BQXRCO0FBQ0gsYUFIRCxNQUdPLElBQUkxQixFQUFFUyxNQUFGLEVBQVVDLEtBQVYsS0FBb0IsSUFBcEIsSUFBNEIsQ0FBQ0wsV0FBV29CLElBQVgsQ0FBZ0IsT0FBaEIsQ0FBN0IsSUFBeUQsQ0FBQ25CLFdBQVdtQixJQUFYLENBQWdCLE9BQWhCLENBQTlELEVBQXdGO0FBQzNGUixpQ0FBaUJiLFFBQWpCO0FBQ0g7QUFDSixTQVBEOztBQVNBbUI7QUFDSCxLQWREOztBQWdCQTtBQUNBLFdBQU8xQixNQUFQO0FBQ0gsQ0FqR0wiLCJmaWxlIjoid2lkZ2V0cy9zbGlkZXJfc2l6ZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc2xpZGVyX3NpemUuanMgMjAxNi0wMi0wNCBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogR2V0cyB0aGUgc2l6ZSBvZiB0aGUgYmlnZ2VzdCBpbWFnZSBmcm9tIHRoZSBhcHBsaWVkIGVsZW1lbnQgYW5kIHB1dHMgdGhlIHByZXZpb3VzIGFuZCBuZXh0IGJ1dHRvbnMgdG8gdGhlIHJpZ2h0XG4gKiBwb3NpdGlvbiwgaWYgdGhlIHNjcmVlbi13aWR0aCBpcyBiaWdnZXIgdGhhbiAxOTIwcHguXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnc2xpZGVyX3NpemUnLFxuXG4gICAgW10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7fSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge30sXG5cbiAgICAgICAgICAgIG1heFdpZHRoID0gMCxcbiAgICAgICAgICAgIG5leHRCdXR0b24gPSAkKCcuanMtdGVhc2VyLXNsaWRlci1uZXh0LnN3aXBlci1idXR0b24tbmV4dCcpLFxuICAgICAgICAgICAgcHJldkJ1dHRvbiA9ICQoJy5qcy10ZWFzZXItc2xpZGVyLXByZXYuc3dpcGVyLWJ1dHRvbi1wcmV2Jyk7XG5cblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFBSSVZBVEUgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBHZXRzIHRoZSBiaWdnZXN0IGltYWdlIGZyb20gdGhlIGFwcGxpZWQgZWxlbWVudCBhbmQgY2FsbHMgdGhlIHBvc2l0aW9uaW5nIG1ldGhvZC5cbiAgICAgICAgICpcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfZ2V0QmlnZ2VzdEltYWdlV2lkdGggPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgIHZhciB3aW5kb3dXaWR0aCA9ICQod2luZG93KS53aWR0aCgpO1xuXG4gICAgICAgICAgICAkKHdpbmRvdykubG9hZChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgJCgnI3NsaWRlcicpLmVhY2goZnVuY3Rpb24gKCkge1xuXG4gICAgICAgICAgICAgICAgICAgICR0aGlzLmZpbmQoJy5zd2lwZXItY29udGFpbmVyIC5zd2lwZXItd3JhcHBlciAuc3dpcGVyLXNsaWRlIGltZycpLmVhY2goZnVuY3Rpb24gKCkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgdyA9ICQodGhpcykuZ2V0KDApLm5hdHVyYWxXaWR0aDtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICh3ID4gbWF4V2lkdGgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBtYXhXaWR0aCA9IHc7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICBpZiAobWF4V2lkdGggJiYgd2luZG93V2lkdGggPiAxOTIwKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBfcG9zaXRpb25CdXR0b25zKG1heFdpZHRoKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFB1dHMgdGhlIHByZXZpb3VzIGFuZCBuZXh0IGJ1dHRvbnMgb2YgdGhlIHN3aXBlciB0byB0aGUgY29ycmVjdCBwb3NpdGlvbiwgaWYgdGhlIHNjcmVlbi13aWR0aCBpcyBiaWdnZXIgdGhhblxuICAgICAgICAgKiAxOTIwcHhcbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIG1heFdpZHRoIGludFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9wb3NpdGlvbkJ1dHRvbnMgPSBmdW5jdGlvbiAobWF4V2lkdGgpIHtcblxuICAgICAgICAgICAgdmFyIG1hcmdpblZhbCA9IE1hdGguY2VpbCgtKG1heFdpZHRoIC8gMikgKyAzMCk7XG5cbiAgICAgICAgICAgIG5leHRCdXR0b24uY3NzKHtcbiAgICAgICAgICAgICAgICAncmlnaHQnOiAnNTAlJyxcbiAgICAgICAgICAgICAgICAnbWFyZ2luLXJpZ2h0JzogbWFyZ2luVmFsICsgJ3B4J1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHByZXZCdXR0b24uY3NzKHtcbiAgICAgICAgICAgICAgICAnbGVmdCc6ICc1MCUnLFxuICAgICAgICAgICAgICAgICdtYXJnaW4tbGVmdCc6IG1hcmdpblZhbCArICdweCdcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblxuICAgICAgICAgICAgX2dldEJpZ2dlc3RJbWFnZVdpZHRoKCk7XG5cbiAgICAgICAgICAgICQod2luZG93KS5yZXNpemUoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGlmICgkKHdpbmRvdykud2lkdGgoKSA8PSAxOTIwICYmIG5leHRCdXR0b24uYXR0cignc3R5bGUnKSAmJiBwcmV2QnV0dG9uLmF0dHIoJ3N0eWxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgbmV4dEJ1dHRvbi5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgICAgICAgICBwcmV2QnV0dG9uLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmICgkKHdpbmRvdykud2lkdGgoKSA+IDE5MjAgJiYgIW5leHRCdXR0b24uYXR0cignc3R5bGUnKSAmJiAhcHJldkJ1dHRvbi5hdHRyKCdzdHlsZScpKSB7XG4gICAgICAgICAgICAgICAgICAgIF9wb3NpdGlvbkJ1dHRvbnMobWF4V2lkdGgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pOyJdfQ==

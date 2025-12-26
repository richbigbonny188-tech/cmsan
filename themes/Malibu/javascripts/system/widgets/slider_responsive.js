'use strict';

/* --------------------------------------------------------------
 slider_responsive.js 2017-05-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Slider Responsive Module
 *
 * This module will handle the image replacement whenever the viewport breakpoint changes.
 */
gambio.widgets.module('slider_responsive', [gambio.source + '/libs/responsive', gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {},
        slider;

    // ########## PRIVATE FUNCTIONS ##########

    var _breakpointHandler = function _breakpointHandler(event, currentBreakpoint) {
        var $swiperContainer = $this.find('.swiper-container'),
            $swiperWrapper = $swiperContainer.find('.swiper-wrapper'),
            previousSwiperInstance = $swiperContainer.get(0).swiper;

        // Reset the existing swiper instance (if any).  
        if (previousSwiperInstance) {
            try {
                previousSwiperInstance.destroy(true, true);
            } catch (exception) {
                // Sometime the breakpoint handler is called many times from various events which leads
                // to errors while destroying previous Swiper instances, thus the try-catch block. 
            }
        }
        $swiperWrapper.empty();
        $this.find('.swiper-pagination').empty();

        // Update the slider HTML markup with the breakpoint-respective image.
        slider.slides.forEach(function (slide) {
            slide.images.forEach(function (image) {
                if ((image.breakpoint === currentBreakpoint.name || image.breakpoint === 'xs' && currentBreakpoint.name === 'too small') && image.languageId === parseInt(jse.core.registry.get('languageId')) && image.image !== '') {

                    var $swiperSlide = $('<div class="swiper-slide"></div>');

                    // Are there image areas?
                    var hasAreas = image.areas && image.areas.length;

                    // Randomly generated string.
                    var imageMapId = Math.random().toString(36).substr(2, 5);

                    if (slide.thumbnail !== '') {
                        $swiperSlide.attr({
                            'data-thumb-image': jse.core.config.get('appUrl') + '/images/slider_images/thumbnails/' + slide.thumbnail,
                            'data-thumb-text': slide.title
                        });
                    }

                    var $slideImage = $('<img />');

                    // Use image map resizer plugin to adjust image map area sizes.
                    $slideImage.rwdImageMaps();

                    // Assign image map, if there are image areas.
                    if (hasAreas) {
                        $slideImage.attr('usemap', '#' + imageMapId);
                    }

                    $slideImage.attr({
                        class: 'img-responsive center-block',
                        src: jse.core.config.get('appUrl') + '/images/slider_images/' + image.image,
                        alt: slide.altText,
                        title: slide.title
                    }).appendTo($swiperSlide);

                    if (slide.url) {
                        $slideImage.wrap('<a />').parent().attr({
                            href: slide.url,
                            target: slide.urlTarget
                        });
                    }

                    // Check for image areas and iterate over them.
                    if (hasAreas) {
                        // Create image map element.
                        var $map = $('<map name="' + imageMapId + '">');

                        /**
                         * Iterator function which processes every image area data.
                         * @param {Object} area Image area data.
                         */
                        var imageAreaIterator = function imageAreaIterator(area) {
                            var areaElementOptions = {
                                shape: 'poly',
                                coords: area.coordinates,
                                href: area.linkUrl,
                                title: area.linkTitle,
                                target: area.linkTarget,
                                'data-id': area.id
                            };

                            // Create image area element.
                            var $area = $('<area>', areaElementOptions);

                            // Put area into image map element.
                            $map.append($area);
                        };

                        // Process every image area.
                        image.areas.forEach(imageAreaIterator);

                        // Append image map to slide element.
                        $swiperSlide.append($map);
                    }

                    $swiperSlide.appendTo($swiperWrapper);
                }
            });
        });

        if ($swiperWrapper.children().length === 0) {
            return; // There is no slide set for this breakpoint. 
        }

        $swiperContainer.attr({
            'data-gambio-widget': 'swiper',
            'data-swiper-disable-translucence-fix': 'true'
        });

        $swiperContainer.data('swiper-breakpoints', [{
            breakpoint: 100,
            usePreviewBullets: true,
            slidesPerView: 1
        }]);

        $swiperContainer.data('swiper-slider-options', {
            effect: options.effect,
            speed: options.speed,
            nextButton: '.js-teaser-slider-next',
            prevButton: '.js-teaser-slider-prev',
            autoplay: slider.speed
        });

        // Initialize the new swiper instance and trigger the widget ready event. 
        gambio.widgets.init($swiperContainer);
        $('body').trigger(jse.libs.theme.events.SLIDER_RESPONSIVE_READY());

        $('img[usemap]').rwdImageMaps();
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget.
     */
    module.init = function (done) {
        if ($(options.source).length === 0) {
            return; // There is no JSON source for the slider data. 
        }

        slider = JSON.parse($(options.source).text());

        $(document).on('JSENGINE_INIT_FINISHED', function () {
            $('body').on(jse.libs.theme.events.BREAKPOINT(), _breakpointHandler);
            _breakpointHandler({}, jse.libs.theme.responsive.breakpoint());
            $('img[usemap]').rwdImageMaps();
        });

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2xpZGVyX3Jlc3BvbnNpdmUuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJzbGlkZXIiLCJfYnJlYWtwb2ludEhhbmRsZXIiLCJldmVudCIsImN1cnJlbnRCcmVha3BvaW50IiwiJHN3aXBlckNvbnRhaW5lciIsImZpbmQiLCIkc3dpcGVyV3JhcHBlciIsInByZXZpb3VzU3dpcGVySW5zdGFuY2UiLCJnZXQiLCJzd2lwZXIiLCJkZXN0cm95IiwiZXhjZXB0aW9uIiwiZW1wdHkiLCJzbGlkZXMiLCJmb3JFYWNoIiwic2xpZGUiLCJpbWFnZXMiLCJpbWFnZSIsImJyZWFrcG9pbnQiLCJuYW1lIiwibGFuZ3VhZ2VJZCIsInBhcnNlSW50IiwianNlIiwiY29yZSIsInJlZ2lzdHJ5IiwiJHN3aXBlclNsaWRlIiwiaGFzQXJlYXMiLCJhcmVhcyIsImxlbmd0aCIsImltYWdlTWFwSWQiLCJNYXRoIiwicmFuZG9tIiwidG9TdHJpbmciLCJzdWJzdHIiLCJ0aHVtYm5haWwiLCJhdHRyIiwiY29uZmlnIiwidGl0bGUiLCIkc2xpZGVJbWFnZSIsInJ3ZEltYWdlTWFwcyIsImNsYXNzIiwic3JjIiwiYWx0IiwiYWx0VGV4dCIsImFwcGVuZFRvIiwidXJsIiwid3JhcCIsInBhcmVudCIsImhyZWYiLCJ0YXJnZXQiLCJ1cmxUYXJnZXQiLCIkbWFwIiwiaW1hZ2VBcmVhSXRlcmF0b3IiLCJhcmVhRWxlbWVudE9wdGlvbnMiLCJzaGFwZSIsImNvb3JkcyIsImFyZWEiLCJjb29yZGluYXRlcyIsImxpbmtVcmwiLCJsaW5rVGl0bGUiLCJsaW5rVGFyZ2V0IiwiaWQiLCIkYXJlYSIsImFwcGVuZCIsImNoaWxkcmVuIiwidXNlUHJldmlld0J1bGxldHMiLCJzbGlkZXNQZXJWaWV3IiwiZWZmZWN0Iiwic3BlZWQiLCJuZXh0QnV0dG9uIiwicHJldkJ1dHRvbiIsImF1dG9wbGF5IiwiaW5pdCIsInRyaWdnZXIiLCJsaWJzIiwidGhlbWUiLCJldmVudHMiLCJTTElERVJfUkVTUE9OU0lWRV9SRUFEWSIsImRvbmUiLCJKU09OIiwicGFyc2UiLCJ0ZXh0IiwiZG9jdW1lbnQiLCJvbiIsIkJSRUFLUE9JTlQiLCJyZXNwb25zaXZlIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLG1CQURKLEVBR0ksQ0FBQ0YsT0FBT0csTUFBUCxHQUFnQixrQkFBakIsRUFBcUNILE9BQU9HLE1BQVAsR0FBZ0IsY0FBckQsQ0FISixFQUtJLFVBQVVDLElBQVYsRUFBZ0I7O0FBRVo7O0FBRUE7O0FBRUEsUUFBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxRQUNJQyxXQUFXLEVBRGY7QUFBQSxRQUVJQyxVQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQUZkO0FBQUEsUUFHSUYsU0FBUyxFQUhiO0FBQUEsUUFJSVEsTUFKSjs7QUFNQTs7QUFFQSxRQUFJQyxxQkFBcUIsU0FBckJBLGtCQUFxQixDQUFVQyxLQUFWLEVBQWlCQyxpQkFBakIsRUFBb0M7QUFDekQsWUFBSUMsbUJBQW1CVCxNQUFNVSxJQUFOLENBQVcsbUJBQVgsQ0FBdkI7QUFBQSxZQUNJQyxpQkFBaUJGLGlCQUFpQkMsSUFBakIsQ0FBc0IsaUJBQXRCLENBRHJCO0FBQUEsWUFFSUUseUJBQXlCSCxpQkFBaUJJLEdBQWpCLENBQXFCLENBQXJCLEVBQXdCQyxNQUZyRDs7QUFJQTtBQUNBLFlBQUlGLHNCQUFKLEVBQTRCO0FBQ3hCLGdCQUFJO0FBQ0FBLHVDQUF1QkcsT0FBdkIsQ0FBK0IsSUFBL0IsRUFBcUMsSUFBckM7QUFDSCxhQUZELENBRUUsT0FBT0MsU0FBUCxFQUFrQjtBQUNoQjtBQUNBO0FBQ0g7QUFDSjtBQUNETCx1QkFBZU0sS0FBZjtB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QUFZQyxFQUFaLENBQWUsd0JBQWYsRUFBeUMsWUFBWTtBQUNqRHZGLGNBQUUsTUFBRixFQUFVdUYsRUFBVixDQUFhN0QsSUFBSW9ELElBQUosQ0FBU0MsS0FBVCxDQUFlQyxNQUFmLENBQXNCUSxVQUF0QixFQUFiLEVBQWlEbkYsa0JBQWpEO0FBQ0FBLCtCQUFtQixFQUFuQixFQUF1QnFCLElBQUlvRCxJQUFKLENBQVNDLEtBQVQsQ0FBZVUsVUFBZixDQUEwQm5FLFVBQTFCLEVBQXZCO0FBQ0F0QixjQUFFLGFBQUYsRUFBaUIyQyxZQUFqQjtBQUNILFNBSkQ7O0FBTUF1QztBQUNILEtBZEQ7O0FBZ0JBO0FBQ0EsV0FBT3RGLE1BQVA7QUFDSCxDQTNMTCIsImZpbGUiOiJ3aWRnZXRzL3NsaWRlcl9yZXNwb25zaXZlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBzbGlkZXJfcmVzcG9uc2l2ZS5qcyAyMDE3LTA1LTEyXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNyBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBTbGlkZXIgUmVzcG9uc2l2ZSBNb2R1bGVcbiAqXG4gKiBUaGlzIG1vZHVsZSB3aWxsIGhhbmRsZSB0aGUgaW1hZ2UgcmVwbGFjZW1lbnQgd2hlbmV2ZXIgdGhlIHZpZXdwb3J0IGJyZWFrcG9pbnQgY2hhbmdlcy5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdzbGlkZXJfcmVzcG9uc2l2ZScsXG5cbiAgICBbZ2FtYmlvLnNvdXJjZSArICcvbGlicy9yZXNwb25zaXZlJywgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgICAgIC8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHt9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fSxcbiAgICAgICAgICAgIHNsaWRlcjtcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFBSSVZBVEUgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgX2JyZWFrcG9pbnRIYW5kbGVyID0gZnVuY3Rpb24gKGV2ZW50LCBjdXJyZW50QnJlYWtwb2ludCkge1xuICAgICAgICAgICAgdmFyICRzd2lwZXJDb250YWluZXIgPSAkdGhpcy5maW5kKCcuc3dpcGVyLWNvbnRhaW5lcicpLFxuICAgICAgICAgICAgICAgICRzd2lwZXJXcmFwcGVyID0gJHN3aXBlckNvbnRhaW5lci5maW5kKCcuc3dpcGVyLXdyYXBwZXInKSxcbiAgICAgICAgICAgICAgICBwcmV2aW91c1N3aXBlckluc3RhbmNlID0gJHN3aXBlckNvbnRhaW5lci5nZXQoMCkuc3dpcGVyO1xuXG4gICAgICAgICAgICAvLyBSZXNldCB0aGUgZXhpc3Rpbmcgc3dpcGVyIGluc3RhbmNlIChpZiBhbnkpLiAgXG4gICAgICAgICAgICBpZiAocHJldmlvdXNTd2lwZXJJbnN0YW5jZSkge1xuICAgICAgICAgICAgICAgIHRyeSB7XG4gICAgICAgICAgICAgICAgICAgIHByZXZpb3VzU3dpcGVySW5zdGFuY2UuZGVzdHJveSh0cnVlLCB0cnVlKTtcbiAgICAgICAgICAgICAgICB9IGNhdGNoIChleGNlcHRpb24pIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gU29tZXRpbWUgdGhlIGJyZWFrcG9pbnQgaGFuZGxlciBpcyBjYWxsZWQgbWFueSB0aW1lcyBmcm9tIHZhcmlvdXMgZXZlbnRzIHdoaWNoIGxlYWRzXG4gICAgICAgICAgICAgICAgICAgIC8vIHRvIGVycm9ycyB3aGlsZSBkZXN0cm95aW5nIHByZXZpb3VzIFN3aXBlciBpbnN0YW5jZXMsIHRodXMgdGhlIHRyeS1jYXRjaCBibG9jay4gXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgJHN3aXBlcldyYXBwZXIuZW1wdHkoKTtcbiAgICAgICAgICAgICR0aGlzLmZpbmQoJy5zd2lwZXItcGFnaW5hdGlvbicpLmVtcHR5KCk7XG5cbiAgICAgICAgICAgIC8vIFVwZGF0ZSB0aGUgc2xpZGVyIEhUTUwgbWFya3VwIHdpdGggdGhlIGJyZWFrcG9pbnQtcmVzcGVjdGl2ZSBpbWFnZS5cbiAgICAgICAgICAgIHNsaWRlci5zbGlkZXMuZm9yRWFjaChmdW5jdGlvbiAoc2xpZGUpIHtcbiAgICAgICAgICAgICAgICBzbGlkZS5pbWFnZXMuZm9yRWFjaChmdW5jdGlvbiAoaW1hZ2UpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKChpbWFnZS5icmVha3BvaW50ID09PSBjdXJyZW50QnJlYWtwb2ludC5uYW1lIHx8IChpbWFnZS5icmVha3BvaW50ID09PSAneHMnXG4gICAgICAgICAgICAgICAgICAgICAgICAmJiBjdXJyZW50QnJlYWtwb2ludC5uYW1lID09PSAndG9vIHNtYWxsJykpXG4gICAgICAgICAgICAgICAgICAgICAgICAmJiBpbWFnZS5sYW5ndWFnZUlkID09PSBwYXJzZUludChqc2UuY29yZS5yZWdpc3RyeS5nZXQoJ2xhbmd1YWdlSWQnKSlcbiAgICAgICAgICAgICAgICAgICAgICAgICYmIGltYWdlLmltYWdlICE9PSAnJykge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgJHN3aXBlclNsaWRlID0gJCgnPGRpdiBjbGFzcz1cInN3aXBlci1zbGlkZVwiPjwvZGl2PicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBBcmUgdGhlcmUgaW1hZ2UgYXJlYXM/XG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBoYXNBcmVhcyA9IChpbWFnZS5hcmVhcyAmJiBpbWFnZS5hcmVhcy5sZW5ndGgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBSYW5kb21seSBnZW5lcmF0ZWQgc3RyaW5nLlxuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgaW1hZ2VNYXBJZCA9IE1hdGgucmFuZG9tKCkudG9TdHJpbmcoMzYpLnN1YnN0cigyLCA1KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHNsaWRlLnRodW1ibmFpbCAhPT0gJycpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkc3dpcGVyU2xpZGUuYXR0cih7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdkYXRhLXRodW1iLWltYWdlJzoganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICsgJy9pbWFnZXMvc2xpZGVyX2ltYWdlcy90aHVtYm5haWxzLydcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICsgc2xpZGUudGh1bWJuYWlsLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAnZGF0YS10aHVtYi10ZXh0Jzogc2xpZGUudGl0bGVcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyICRzbGlkZUltYWdlID0gJCgnPGltZyAvPicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBVc2UgaW1hZ2UgbWFwIHJlc2l6ZXIgcGx1Z2luIHRvIGFkanVzdCBpbWFnZSBtYXAgYXJlYSBzaXplcy5cbiAgICAgICAgICAgICAgICAgICAgICAgICRzbGlkZUltYWdlLnJ3ZEltYWdlTWFwcygpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBBc3NpZ24gaW1hZ2UgbWFwLCBpZiB0aGVyZSBhcmUgaW1hZ2UgYXJlYXMuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoaGFzQXJlYXMpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkc2xpZGVJbWFnZS5hdHRyKCd1c2VtYXAnLCBgIyR7aW1hZ2VNYXBJZH1gKVxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAkc2xpZGVJbWFnZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5hdHRyKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY2xhc3M6ICdpbWctcmVzcG9uc2l2ZSBjZW50ZXItYmxvY2snLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzcmM6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9pbWFnZXMvc2xpZGVyX2ltYWdlcy8nICsgaW1hZ2UuaW1hZ2UsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFsdDogc2xpZGUuYWx0VGV4dCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGl0bGU6IHNsaWRlLnRpdGxlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXBwZW5kVG8oJHN3aXBlclNsaWRlKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHNsaWRlLnVybCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzbGlkZUltYWdlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC53cmFwKCc8YSAvPicpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5wYXJlbnQoKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYXR0cih7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBocmVmOiBzbGlkZS51cmwsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0YXJnZXQ6IHNsaWRlLnVybFRhcmdldFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gQ2hlY2sgZm9yIGltYWdlIGFyZWFzIGFuZCBpdGVyYXRlIG92ZXIgdGhlbS5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChoYXNBcmVhcykge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIENyZWF0ZSBpbWFnZSBtYXAgZWxlbWVudC5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zdCAkbWFwID0gJChgPG1hcCBuYW1lPVwiJHtpbWFnZU1hcElkfVwiPmApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICogSXRlcmF0b3IgZnVuY3Rpb24gd2hpY2ggcHJvY2Vzc2VzIGV2ZXJ5IGltYWdlIGFyZWEgZGF0YS5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKiBAcGFyYW0ge09iamVjdH0gYXJlYSBJbWFnZSBhcmVhIGRhdGEuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICovXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgaW1hZ2VBcmVhSXRlcmF0b3IgPSBhcmVhID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgYXJlYUVsZW1lbnRPcHRpb25zID0ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hhcGU6ICdwb2x5JyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvb3JkczogYXJlYS5jb29yZGluYXRlcyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGhyZWY6IGFyZWEubGlua1VybCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRpdGxlOiBhcmVhLmxpbmtUaXRsZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRhcmdldDogYXJlYS5saW5rVGFyZ2V0LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ2RhdGEtaWQnOiBhcmVhLmlkXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH07XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gQ3JlYXRlIGltYWdlIGFyZWEgZWxlbWVudC5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgJGFyZWEgPSAkKCc8YXJlYT4nLCBhcmVhRWxlbWVudE9wdGlvbnMpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vIFB1dCBhcmVhIGludG8gaW1hZ2UgbWFwIGVsZW1lbnQuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRtYXAuYXBwZW5kKCRhcmVhKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gUHJvY2VzcyBldmVyeSBpbWFnZSBhcmVhLlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGltYWdlLmFyZWFzLmZvckVhY2goaW1hZ2VBcmVhSXRlcmF0b3IpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gQXBwZW5kIGltYWdlIG1hcCB0byBzbGlkZSBlbGVtZW50LlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzd2lwZXJTbGlkZS5hcHBlbmQoJG1hcCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICRzd2lwZXJTbGlkZS5hcHBlbmRUbygkc3dpcGVyV3JhcHBlcik7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiAoJHN3aXBlcldyYXBwZXIuY2hpbGRyZW4oKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICByZXR1cm47IC8vIFRoZXJlIGlzIG5vIHNsaWRlIHNldCBmb3IgdGhpcyBicmVha3BvaW50LiBcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJHN3aXBlckNvbnRhaW5lci5hdHRyKHtcbiAgICAgICAgICAgICAgICAnZGF0YS1nYW1iaW8td2lkZ2V0JzogJ3N3aXBlcicsXG4gICAgICAgICAgICAgICAgJ2RhdGEtc3dpcGVyLWRpc2FibGUtdHJhbnNsdWNlbmNlLWZpeCc6ICd0cnVlJyxcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkc3dpcGVyQ29udGFpbmVyLmRhdGEoXG4gICAgICAgICAgICAgICAgJ3N3aXBlci1icmVha3BvaW50cycsIFtcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWtwb2ludDogMTAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgdXNlUHJldmlld0J1bGxldHM6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXNQZXJWaWV3OiAxXG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBdXG4gICAgICAgICAgICApO1xuXG4gICAgICAgICAgICAkc3dpcGVyQ29udGFpbmVyLmRhdGEoXG4gICAgICAgICAgICAgICAgJ3N3aXBlci1zbGlkZXItb3B0aW9ucycsIHtcbiAgICAgICAgICAgICAgICAgICAgZWZmZWN0OiBvcHRpb25zLmVmZmVjdCxcbiAgICAgICAgICAgICAgICAgICAgc3BlZWQ6IG9wdGlvbnMuc3BlZWQsXG4gICAgICAgICAgICAgICAgICAgIG5leHRCdXR0b246ICcuanMtdGVhc2VyLXNsaWRlci1uZXh0JyxcbiAgICAgICAgICAgICAgICAgICAgcHJldkJ1dHRvbjogJy5qcy10ZWFzZXItc2xpZGVyLXByZXYnLFxuICAgICAgICAgICAgICAgICAgICBhdXRvcGxheTogc2xpZGVyLnNwZWVkXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgKTtcblxuICAgICAgICAgICAgLy8gSW5pdGlhbGl6ZSB0aGUgbmV3IHN3aXBlciBpbnN0YW5jZSBhbmQgdHJpZ2dlciB0aGUgd2lkZ2V0IHJlYWR5IGV2ZW50LiBcbiAgICAgICAgICAgIGdhbWJpby53aWRnZXRzLmluaXQoJHN3aXBlckNvbnRhaW5lcik7XG4gICAgICAgICAgICAkKCdib2R5JykudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuU0xJREVSX1JFU1BPTlNJVkVfUkVBRFkoKSk7XG5cbiAgICAgICAgICAgICQoJ2ltZ1t1c2VtYXBdJykucndkSW1hZ2VNYXBzKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldC5cbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcbiAgICAgICAgICAgIGlmICgkKG9wdGlvbnMuc291cmNlKS5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgICAgICByZXR1cm47IC8vIFRoZXJlIGlzIG5vIEpTT04gc291cmNlIGZvciB0aGUgc2xpZGVyIGRhdGEuIFxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzbGlkZXIgPSBKU09OLnBhcnNlKCQob3B0aW9ucy5zb3VyY2UpLnRleHQoKSk7XG5cbiAgICAgICAgICAgICQoZG9jdW1lbnQpLm9uKCdKU0VOR0lORV9JTklUX0ZJTklTSEVEJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICQoJ2JvZHknKS5vbihqc2UubGlicy50aGVtZS5ldmVudHMuQlJFQUtQT0lOVCgpLCBfYnJlYWtwb2ludEhhbmRsZXIpO1xuICAgICAgICAgICAgICAgIF9icmVha3BvaW50SGFuZGxlcih7fSwganNlLmxpYnMudGhlbWUucmVzcG9uc2l2ZS5icmVha3BvaW50KCkpO1xuICAgICAgICAgICAgICAgICQoJ2ltZ1t1c2VtYXBdJykucndkSW1hZ2VNYXBzKCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTsiXX0=

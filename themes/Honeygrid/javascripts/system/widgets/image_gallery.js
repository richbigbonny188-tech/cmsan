'use strict';

/* --------------------------------------------------------------
 image_gallery.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that opens the gallery modal layer (which is
 * used for the article pictures)
 */
gambio.widgets.module('image_gallery', [gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal', gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $theme = null,
        $body = $('body'),
        layer = null,
        configuration = { // Modal layer configuration
        notheme: false,
        preloader: true,
        closeOnOuter: true,
        dialogClass: 'product_images',
        gallery: {
            enabled: true
        }
    },
        defaults = {
        target: '.swiper-slide', // Selector for the click event listener
        theme: '#product_image_layer', // theme that is used for the layer
        breakpoint: 40 // Maximum breakpoint for mobile view mode
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Click event handler that configures the swiper(s)
     * inside the layer and opens it afterwards
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _clickHandler = function _clickHandler(e) {
        e.preventDefault();

        // Only open in desktop mode
        if (jse.libs.theme.responsive.breakpoint().id > options.breakpoint) {
            var $self = $(this),
                $swiper = $theme.find('[data-swiper-slider-options]'),
                dataset = $self.data(),
                index = dataset.index || dataset.swiperSlideIndex || 0;

            // Loop that replaces the initial slide of
            // each swiper inside the layer
            $swiper.each(function () {
                $(this).attr('data-swiper-init-slide', index);
            });

            // Opens the modal layer
            layer = jse.libs.theme.modal.custom(configuration);
        }
    };

    /**
     * Handler which closes an opened gallery if the
     * screen width gets under the size of an desktop mode
     * @private
     */
    var _breakpointHandler = function _breakpointHandler() {
        if (jse.libs.theme.responsive.breakpoint().id <= options.breakpoint && layer) {
            layer.close(true);
        }
    };

    /**
     * Event handler to append / remove slides from the
     * gallery layer swipers
     * @param       {object}        e           jQuery event object
     * @param       {object}        d           JSON data of the images
     * @private
     */
    var _addSlides = function _addSlides(e, d) {

        // Loops through all swipers inside the layer
        $theme.find('.swiper-container theme').each(function () {
            var $tpl = $(this),
                $slideContainer = $tpl.siblings('.swiper-wrapper');

            // Loops through each category inside the images array
            $.each(d, function (category, dataset) {
                var catName = category + '-category',
                    add = '',
                    markup = $tpl.html();

                // Generate the markup for the new slides
                // and replace the old images of that category
                // eith the new ones
                $.each(dataset || [], function (i, v) {
                    v.className = catName;
                    add += Mustache.render(markup, v);
                });

                $slideContainer.find('.' + catName).remove();

                $slideContainer.append(add);
            });
        });
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     *
     * @constructor
     */
    module.init = function (done) {
        configuration.theme = options.theme;
        $theme = $(options.theme);

        $this.on('click', options.target, _clickHandler).on(jse.libs.theme.events.SLIDES_UPDATE(), _addSlides);

        $body.on(jse.libs.theme.events.BREAKPOINT(), _breakpointHandler);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaW1hZ2VfZ2FsbGVyeS5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR0aGVtZSIsIiRib2R5IiwibGF5ZXIiLCJjb25maWd1cmF0aW9uIiwibm90aGVtZSIsInByZWxvYWRlciIsImNsb3NlT25PdXRlciIsImRpYWxvZ0NsYXNzIiwiZ2FsbGVyeSIsImVuYWJsZWQiLCJkZWZhdWx0cyIsInRhcmdldCIsInRoZW1lIiwiYnJlYWtwb2ludCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfY2xpY2tIYW5kbGVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwianNlIiwibGlicyIsInJlc3BvbnNpdmUiLCJpZCIsIiRzZWxmIiwiJHN3aXBlciIsImZpbmQiLCJkYXRhc2V0IiwiaW5kZXgiLCJzd2lwZXJTbGlkZUluZGV4IiwiZWFjaCIsImF0dHIiLCJtb2RhbCIsImN1c3RvbSIsIl9icmVha3BvaW50SGFuZGxlciIsImNsb3NlIiwiX2FkZFNsaWRlcyIsImQiLCIkdHBsIiwiJHNsaWRlQ29udGFpbmVyIiwic2libGluZ3MiLCJjYXRlZ29yeSIsImNhdE5hbWUiLCJhZGQiLCJtYXJrdXAiLCJodG1sIiwiaSIsInYiLCJjbGFzc05hbWUiLCJNdXN0YWNoZSIsInJlbmRlciIsInJlbW92ZSIsImFwcGVuZCIsImluaXQiLCJkb25lIiwib24iLCJldmVudHMiLCJTTElERVNfVVBEQVRFIiwiQlJFQUtQT0lOVCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLGVBREosRUFHSSxDQUNJRixPQUFPRyxNQUFQLEdBQWdCLDBCQURwQixFQUVJSCxPQUFPRyxNQUFQLEdBQWdCLGFBRnBCLEVBR0lILE9BQU9HLE1BQVAsR0FBZ0IsY0FIcEIsRUFJSUgsT0FBT0csTUFBUCxHQUFnQixrQkFKcEIsQ0FISixFQVVJLFVB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UixnQ0FDS2IsSUFETCxDQUNVLE1BQU1nQixPQURoQixFQUVLUyxNQUZMOztBQUlBWixnQ0FBZ0JhLE1BQWhCLENBQXVCVCxHQUF2QjtBQUNILGFBbEJEO0FBbUJILFNBMUJMO0FBMkJILEtBOUJEOztBQWlDUjs7QUFFUTs7Ozs7QUFLQS9DLFdBQU95RCxJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUMxQmxELHNCQUFjUyxLQUFkLEdBQXNCRSxRQUFRRixLQUE5QjtBQUNBWixpQkFBU0QsRUFBRWUsUUFBUUYsS0FBVixDQUFUOztBQUVBZCxjQUNLd0QsRUFETCxDQUNRLE9BRFIsRUFDaUJ4QyxRQUFRSCxNQUR6QixFQUNpQ0ssYUFEakMsRUFFS3NDLEVBRkwsQ0FFUW5DLElBQUlDLElBQUosQ0FBU1IsS0FBVCxDQUFlMkMsTUFBZixDQUFzQkMsYUFBdEIsRUFGUixFQUUrQ3JCLFVBRi9DOztBQUlBbEMsY0FDS3FELEVBREwsQ0FDUW5DLElBQUlDLElBQUosQ0FBU1IsS0FBVCxDQUFlMkMsTUFBZixDQUFzQkUsVUFBdEIsRUFEUixFQUM0Q3hCLGtCQUQ1Qzs7QUFHQW9CO0FBQ0gsS0FaRDs7QUFjQTtBQUNBLFdBQU8xRCxNQUFQO0FBQ0gsQ0E3SUwiLCJmaWxlIjoid2lkZ2V0cy9pbWFnZV9nYWxsZXJ5LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbWFnZV9nYWxsZXJ5LmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IG9wZW5zIHRoZSBnYWxsZXJ5IG1vZGFsIGxheWVyICh3aGljaCBpc1xuICogdXNlZCBmb3IgdGhlIGFydGljbGUgcGljdHVyZXMpXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnaW1hZ2VfZ2FsbGVyeScsXG5cbiAgICBbXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvbW9kYWwuZXh0LW1hZ25pZmljJyxcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9tb2RhbCcsXG4gICAgICAgIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJyxcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9yZXNwb25zaXZlJ1xuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgICAgICR0aGVtZSA9IG51bGwsXG4gICAgICAgICAgICAkYm9keSA9ICQoJ2JvZHknKSxcbiAgICAgICAgICAgIGxheWVyID0gbnVsbCxcbiAgICAgICAgICAgIGNvbmZpZ3VyYXRpb24gPSB7ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gTW9kYWwgbGF5ZXIgY29uZmlndXJhdGlvblxuICAgICAgICAgICAgICAgIG5vdGhlbWU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIHByZWxvYWRlcjogdHJ1ZSxcbiAgICAgICAgICAgICAgICBjbG9zZU9uT3V0ZXI6IHRydWUsXG4gICAgICAgICAgICAgICAgZGlhbG9nQ2xhc3M6ICdwcm9kdWN0X2ltYWdlcycsXG4gICAgICAgICAgICAgICAgZ2FsbGVyeToge1xuICAgICAgICAgICAgICAgICAgICBlbmFibGVkOiB0cnVlXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIHRhcmdldDogJy5zd2lwZXItc2xpZGUnLCAvLyBTZWxlY3RvciBmb3IgdGhlIGNsaWNrIGV2ZW50IGxpc3RlbmVyXG4gICAgICAgICAgICAgICAgdGhlbWU6ICcjcHJvZHVjdF9pbWFnZV9sYXllcicsIC8vIHRoZW1lIHRoYXQgaXMgdXNlZCBmb3IgdGhlIGxheWVyXG4gICAgICAgICAgICAgICAgYnJlYWtwb2ludDogNDAgLy8gTWF4aW11bSBicmVha3BvaW50IGZvciBtb2JpbGUgdmlldyBtb2RlXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogQ2xpY2sgZXZlbnQgaGFuZGxlciB0aGF0IGNvbmZpZ3VyZXMgdGhlIHN3aXBlcihzKVxuICAgICAgICAgKiBpbnNpZGUgdGhlIGxheWVyIGFuZCBvcGVucyBpdCBhZnRlcndhcmRzXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2xpY2tIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgLy8gT25seSBvcGVuIGluIGRlc2t0b3AgbW9kZVxuICAgICAgICAgICAgaWYgKGpzZS5saWJzLnRoZW1lLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkID4gb3B0aW9ucy5icmVha3BvaW50KSB7XG4gICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgJHN3aXBlciA9ICR0aGVtZS5maW5kKCdbZGF0YS1zd2lwZXItc2xpZGVyLW9wdGlvbnNdJyksXG4gICAgICAgICAgICAgICAgICAgIGRhdGFzZXQgPSAkc2VsZi5kYXRhKCksXG4gICAgICAgICAgICAgICAgICAgIGluZGV4ID0gZGF0YXNldC5pbmRleCB8fCBkYXRhc2V0LnN3aXBlclNsaWRlSW5kZXggfHwgMDtcblxuICAgICAgICAgICAgICAgIC8vIExvb3AgdGhhdCByZXBsYWNlcyB0aGUgaW5pdGlhbCBzbGlkZSBvZlxuICAgICAgICAgICAgICAgIC8vIGVhY2ggc3dpcGVyIGluc2lkZSB0aGUgbGF5ZXJcbiAgICAgICAgICAgICAgICAkc3dpcGVyLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ2RhdGEtc3dpcGVyLWluaXQtc2xpZGUnLCBpbmRleCk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyBPcGVucyB0aGUgbW9kYWwgbGF5ZXJcbiAgICAgICAgICAgICAgICBsYXllciA9IGpzZS5saWJzLnRoZW1lLm1vZGFsLmN1c3RvbShjb25maWd1cmF0aW9uKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIYW5kbGVyIHdoaWNoIGNsb3NlcyBhbiBvcGVuZWQgZ2FsbGVyeSBpZiB0aGVcbiAgICAgICAgICogc2NyZWVuIHdpZHRoIGdldHMgdW5kZXIgdGhlIHNpemUgb2YgYW4gZGVza3RvcCBtb2RlXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2JyZWFrcG9pbnRIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgaWYgKGpzZS5saWJzLnRoZW1lLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludCAmJiBsYXllcikge1xuICAgICAgICAgICAgICAgIGxheWVyLmNsb3NlKHRydWUpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIHRvIGFwcGVuZCAvIHJlbW92ZSBzbGlkZXMgZnJvbSB0aGVcbiAgICAgICAgICogZ2FsbGVyeSBsYXllciBzd2lwZXJzXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGQgICAgICAgICAgIEpTT04gZGF0YSBvZiB0aGUgaW1hZ2VzXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2FkZFNsaWRlcyA9IGZ1bmN0aW9uIChlLCBkKSB7XG5cbiAgICAgICAgICAgIC8vIExvb3BzIHRocm91Z2ggYWxsIHN3aXBlcnMgaW5zaWRlIHRoZSBsYXllclxuICAgICAgICAgICAgJHRoZW1lXG4gICAgICAgICAgICAgICAgLmZpbmQoJy5zd2lwZXItY29udGFpbmVyIHRoZW1lJylcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciAkdHBsID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICRzbGlkZUNvbnRhaW5lciA9ICR0cGwuc2libGluZ3MoJy5zd2lwZXItd3JhcHBlcicpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIExvb3BzIHRocm91Z2ggZWFjaCBjYXRlZ29yeSBpbnNpZGUgdGhlIGltYWdlcyBhcnJheVxuICAgICAgICAgICAgICAgICAgICAkLmVhY2goZCwgZnVuY3Rpb24gKGNhdGVnb3J5LCBkYXRhc2V0KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgY2F0TmFtZSA9IGNhdGVnb3J5ICsgJy1jYXRlZ29yeScsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYWRkID0gJycsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbWFya3VwID0gJHRwbC5odG1sKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEdlbmVyYXRlIHRoZSBtYXJrdXAgZm9yIHRoZSBuZXcgc2xpZGVzXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBhbmQgcmVwbGFjZSB0aGUgb2xkIGltYWdlcyBvZiB0aGF0IGNhdGVnb3J5XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBlaXRoIHRoZSBuZXcgb25lc1xuICAgICAgICAgICAgICAgICAgICAgICAgJC5lYWNoKGRhdGFzZXQgfHwgW10sIGZ1bmN0aW9uIChpLCB2KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdi5jbGFzc05hbWUgPSBjYXROYW1lO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFkZCArPSBNdXN0YWNoZS5yZW5kZXIobWFya3VwLCB2KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkc2xpZGVDb250YWluZXJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmluZCgnLicgKyBjYXROYW1lKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJHNsaWRlQ29udGFpbmVyLmFwcGVuZChhZGQpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqXG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAgICAgY29uZmlndXJhdGlvbi50aGVtZSA9IG9wdGlvbnMudGhlbWU7XG4gICAgICAgICAgICAkdGhlbWUgPSAkKG9wdGlvbnMudGhlbWUpO1xuXG4gICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgIC5vbignY2xpY2snLCBvcHRpb25zLnRhcmdldCwgX2NsaWNrSGFuZGxlcilcbiAgICAgICAgICAgICAgICAub24oanNlLmxpYnMudGhlbWUuZXZlbnRzLlNMSURFU19VUERBVEUoKSwgX2FkZFNsaWRlcyk7XG5cbiAgICAgICAgICAgICRib2R5XG4gICAgICAgICAgICAgICAgLm9uKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5CUkVBS1BPSU5UKCksIF9icmVha3BvaW50SGFuZGxlcik7XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7XG4iXX0=

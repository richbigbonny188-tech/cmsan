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
gambio.widgets.module('image_gallery_lightbox', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $theme = null,
        module = {};

    // ########## EVENT HANDLER ##########

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     *
     * @constructor
     */
    module.init = function (done) {

        // Delegate lightbox links with Magnific Popup
        // http://dimsemenov.com/plugins/magnific-popup/
        $this.magnificPopup({
            delegate: '.swiper-slide:not(.swiper-slide-duplicate) a',
            type: 'image', gallery: {
                enabled: true
            }
        });

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaW1hZ2VfZ2FsbGVyeV9saWdodGJveC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR0aGVtZSIsImluaXQiLCJkb25lIiwibWFnbmlmaWNQb3B1cCIsImRlbGVnYXRlIiwidHlwZSIsImdhbGxlcnkiLCJlbmFibGVkIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7QUFJQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksd0JBREosRUFHSSxFQUhKLEVBS0ksVUFBVUMsSUFBVixFQUFnQjs7QUFFWjs7QUFFUjs7QUFFUSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFNBQVMsSUFEYjtBQUFBLFFBRUlKLFNBQVMsRUFGYjs7QUFJUjs7QUFFQTs7QUFFUTs7Ozs7QUFLQUEsV0FBT0ssSUFBUCxHQUFjLFVBQVVDLElBQVYsRUFBZ0I7O0FBRTFCO0FBQ0E7QUFDQUosY0FBTUssYUFBTixDQUFvQjtBQUNoQkMsc0JBQVUsOENBRE07QUFFaEJDLGtCQUFNLE9BRlUsRUFFREMsU0FBUztBQUNwQkMseUJBQVM7QUFEVztBQUZSLFNBQXBCOztBQU9BTDtBQUNILEtBWkQ7O0FBY0E7QUFDQSxXQUFPTixNQUFQO0FBQ0gsQ0F4Q0wiLCJmaWxlIjoid2lkZ2V0cy9pbWFnZV9nYWxsZXJ5X2xpZ2h0Ym94LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbWFnZV9nYWxsZXJ5LmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IG9wZW5zIHRoZSBnYWxsZXJ5IG1vZGFsIGxheWVyICh3aGljaCBpc1xuICogdXNlZCBmb3IgdGhlIGFydGljbGUgcGljdHVyZXMpXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnaW1hZ2VfZ2FsbGVyeV9saWdodGJveCcsXG5cbiAgICBbXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgJHRoZW1lID0gbnVsbCxcbiAgICAgICAgICAgIG1vZHVsZSA9IHt9O1xuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqXG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICAgICAvLyBEZWxlZ2F0ZSBsaWdodGJveCBsaW5rcyB3aXRoIE1hZ25pZmljIFBvcHVwXG4gICAgICAgICAgICAvLyBodHRwOi8vZGltc2VtZW5vdi5jb20vcGx1Z2lucy9tYWduaWZpYy1wb3B1cC9cbiAgICAgICAgICAgICR0aGlzLm1hZ25pZmljUG9wdXAoe1xuICAgICAgICAgICAgICAgIGRlbGVnYXRlOiAnLnN3aXBlci1zbGlkZTpub3QoLnN3aXBlci1zbGlkZS1kdXBsaWNhdGUpIGEnLFxuICAgICAgICAgICAgICAgIHR5cGU6ICdpbWFnZScsIGdhbGxlcnk6IHtcbiAgICAgICAgICAgICAgICAgICAgZW5hYmxlZDogdHJ1ZVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

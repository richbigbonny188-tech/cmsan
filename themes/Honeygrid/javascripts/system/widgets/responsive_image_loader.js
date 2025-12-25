'use strict';

/* --------------------------------------------------------------
 responsive_image_loader.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Example:
 *
 * <img src="img/testbild4_320.jpg" title="Testbild" alt="Testbild" data-image-xs="img/testbild4_320.jpg"
 *      data-image-sm="img/testbild4_640.jpg" data-image-md="img/testbild4_1024.jpg"
 *      data-image-lg="img/testbild4_1600.jpg"/>
 */
gambio.widgets.module('responsive_image_loader', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {
        breakpoints: ['xs', 'sm', 'md', 'lg']
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ######### HELPER FUNCTIONS ##########

    /**
     * Helper function that registers the ":attr"
     * selector to jQuery. With this one it's possible
     * to select elements with an regular expression
     * @private
     */
    var _registerSelector = function _registerSelector() {
        if ($.expr.pseudos.attr === undefined) {
            $.expr.pseudos.attr = $.expr.createPseudo(function (arg) {
                var regexp = new RegExp(arg);
                return function (elem) {
                    for (var i = 0; i < elem.attributes.length; i++) {
                        var attr = elem.attributes[i];
                        if (regexp.test(attr.name)) {
                            return true;
                        }
                    }
                    return false;
                };
            });
        }
    };

    // ########## MAIN FUNCTIONALITY ##########

    /**
     * Function that searches for the best fitting image
     * for the parent container, so that it can set the src-attribute
     * inside the img-tag
     * @param       {object}    $target     jQuery selection that contains the image to set (optional)
     * @private
     */
    var _resizeImages = function _resizeImages($target) {
        var $self = $(this),
            breakpoint = jse.libs.theme.responsive.breakpoint(),
            $elements = $target && $target.length ? $target : $self.filter(':attr(^data-image)').add($self.find(':attr(^data-image)'));

        // Iterate trough every image element
        // and check if there is a new image
        // size to set
        $elements.not('.lazyLoading').each(function () {

            var $element = $(this),
                breakpoint = jse.libs.theme.responsive.breakpoint(),
                bp = options.breakpoints.indexOf(breakpoint.name),
                bpCount = options.breakpoints.length,
                img = null;

            for (bp; bp < bpCount; bp += 1) {
                var attrName = 'data-image-' + options.breakpoints[bp],
                    value = $element.attr(attrName);

                if (value) {
                    img = value;
                    break;
                }
            }

            if (!img) {
                img = $element.attr('data-image');
            }

            // If an image was found and the target element has a
            // different value inside it's src-attribute set the
            // new value
            if (img && $element.attr('src') !== img) {
                $element.attr('src', img);
            }
        });
    };

    /**
     * Function that initializes the lazy loading
     * capability of images
     * @private
     */
    var _registerLazyLoading = function _registerLazyLoading() {
        var $elements = $this.filter('.lazyLoading:attr(^data-image)').add($this.find('.lazyLoading:attr(^data-image)'));

        /**
         * Function that scans the given elements for images
         * that are in the viewport and set the source attribute
         * @private
         */
        var _lazyLoadingScrollHandler = function _lazyLoadingScrollHandler() {

            var windowWidth = $(window).width(),
                windowHeight = $(window).height(),
                top = $(window).scrollTop(),
                left = $(window).scrollLeft();

            $elements.each(function () {
                var $self = $(this),
                    offset = $self.offset();

                if (offset.top < top + windowHeight || offset.left < left + windowWidth) {
                    $elements = $elements.not($self);
                    $self.trigger('lazyLoadImage');
                }
            });
        };

        /**
         * Removes the class "lazyLoading" from the image
         * so that the "_resizeImages" is able to select it
         * Afterwards execute this function to set the
         * correct image source
         * @param       {object}    e       jQuery event object
         * @private
         */
        var _loadImage = function _loadImage(e) {
            e.stopPropagation();

            var $self = $(this);
            $self.removeClass('lazyLoading');
            _resizeImages($self);
        };

        // Add an event handler for loading the first real image
        // to every image element that is only executed once
        $elements.each(function () {
            $(this).one('lazyLoadImage', _loadImage);
        });

        // Add event handler to every event that changes the dimension / viewport
        $(window).on('scroll windowWasResized', _lazyLoadingScrollHandler);

        // Load images that are in view on load
        _lazyLoadingScrollHandler();
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        _registerSelector();
        _registerLazyLoading();

        $(window).on(jse.libs.theme.events.BREAKPOINT(), function () {
            _resizeImages.call($this);
        });

        _resizeImages.call($this);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcmVzcG9uc2l2ZV9pbWFnZV9sb2FkZXIuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsImJyZWFrcG9pbnRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9yZWdpc3RlclNlbGVjdG9yIiwiZXhwciIsInBzZXVkb3MiLCJhdHRyIiwidW5kZWZpbmVkIiwiY3JlYXRlUHNldWRvIiwiYXJnIiwicmVnZXhwIiwiUmVnRXhwIiwiZWxlbSIsImkiLCJhdHRyaWJ1dGVzIiwibGVuZ3RoIiwidGVzdCIsIm5hbWUiLCJfcmVzaXplSW1hZ2VzIiwiJHRhcmdldCIsIiRzZWxmIiwiYnJlYWtwb2ludCIsImpzZSIsImxpYnMiLCJ0aGVtZSIsInJlc3BvbnNpdmUiLCIkZWxlbWVudHMiLCJmaWx0ZXIiLCJhZGQiLCJmaW5kIiwibm90IiwiZWFjaCIsIiRlbGVtZW50IiwiYnAiLCJpbmRleE9mIiwiYnBDb3VudCIsImltZyIsImF0dHJOYW1lIiwidmFsdWUiLCJfcmVnaXN0ZXJMYXp5TG9hZGluZyIsIl9sYXp5TG9hZGluZ1Njcm9sbEhhbmRsZXIiLCJ3aW5kb3dXaWR0aCIsIndpbmRvdyIsIndpZHRoIiwid2luZG93SGVpZ2h0IiwiaGVpZ2h0IiwidG9wIiwic2Nyb2xsVG9wIiwibGVmdCIsInNjcm9sbExlZnQiLCJvZmZzZXQiLCJ0cmlnZ2VyIiwiX2xvYWRJbWFnZSIsImUiLCJzdG9wUHJvcGFnYXRpb24iLCJyZW1vdmVDbGFzcyIsIm9uZSIsIm9uIiwiaW5pdCIsImRvbmUiLCJldmVudHMiLCJCUkVBS1BPSU5UIiwiY2FsbCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLHlCQURKLEVBR0ksQ0FDSUYsT0FBT0csTUFBUCxHQUFnQixjQURwQixFQUVJSCxPQUFPRyxNQUFQLEdB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QU5EOztBQVFBO0FBQ0E7QUFDQU0sa0JBQVVLLElBQVYsQ0FBZSxZQUFZO0FBQ3ZCakMsY0FBRSxJQUFGLEVBQVEwRCxHQUFSLENBQVksZUFBWixFQUE2QkosVUFBN0I7QUFDSCxTQUZEOztBQUlBO0FBQ0F0RCxVQUFFNEMsTUFBRixFQUFVZSxFQUFWLENBQWEseUJBQWIsRUFBd0NqQix5QkFBeEM7O0FBRUE7QUFDQUE7QUFDSCxLQXZERDs7QUEwRFI7O0FBRVE7Ozs7QUFJQTlDLFdBQU9nRSxJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjs7QUFFMUJ4RDtBQUNBb0M7O0FBRUF6QyxVQUFFNEMsTUFBRixFQUFVZSxFQUFWLENBQWFuQyxJQUFJQyxJQUFKLENBQVNDLEtBQVQsQ0FBZW9DLE1BQWYsQ0FBc0JDLFVBQXRCLEVBQWIsRUFBaUQsWUFBWTtBQUN6RDNDLDBCQUFjNEMsSUFBZCxDQUFtQmpFLEtBQW5CO0FBQ0gsU0FGRDs7QUFJQXFCLHNCQUFjNEMsSUFBZCxDQUFtQmpFLEtBQW5COztBQUVBOEQ7QUFDSCxLQVpEOztBQWNBO0FBQ0EsV0FBT2pFLE1BQVA7QUFDSCxDQXpMTCIsImZpbGUiOiJ3aWRnZXRzL3Jlc3BvbnNpdmVfaW1hZ2VfbG9hZGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiByZXNwb25zaXZlX2ltYWdlX2xvYWRlci5qcyAyMDE2LTAzLTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBFeGFtcGxlOlxuICpcbiAqIDxpbWcgc3JjPVwiaW1nL3Rlc3RiaWxkNF8zMjAuanBnXCIgdGl0bGU9XCJUZXN0YmlsZFwiIGFsdD1cIlRlc3RiaWxkXCIgZGF0YS1pbWFnZS14cz1cImltZy90ZXN0YmlsZDRfMzIwLmpwZ1wiXG4gKiAgICAgIGRhdGEtaW1hZ2Utc209XCJpbWcvdGVzdGJpbGQ0XzY0MC5qcGdcIiBkYXRhLWltYWdlLW1kPVwiaW1nL3Rlc3RiaWxkNF8xMDI0LmpwZ1wiXG4gKiAgICAgIGRhdGEtaW1hZ2UtbGc9XCJpbWcvdGVzdGJpbGQ0XzE2MDAuanBnXCIvPlxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ3Jlc3BvbnNpdmVfaW1hZ2VfbG9hZGVyJyxcblxuICAgIFtcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnLFxuICAgICAgICBnYW1iaW8uc291cmNlICsgJy9saWJzL3Jlc3BvbnNpdmUnXG4gICAgXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAgICAgYnJlYWtwb2ludHM6IFsneHMnLCAnc20nLCAnbWQnLCAnbGcnXVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cbi8vICMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgcmVnaXN0ZXJzIHRoZSBcIjphdHRyXCJcbiAgICAgICAgICogc2VsZWN0b3IgdG8galF1ZXJ5LiBXaXRoIHRoaXMgb25lIGl0J3MgcG9zc2libGVcbiAgICAgICAgICogdG8gc2VsZWN0IGVsZW1lbnRzIHdpdGggYW4gcmVndWxhciBleHByZXNzaW9uXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3JlZ2lzdGVyU2VsZWN0b3IgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBpZiAoJC5leHByLnBzZXVkb3MuYXR0ciA9PT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICAgICAgJC5leHByLnBzZXVkb3MuYXR0ciA9ICQuZXhwci5jcmVhdGVQc2V1ZG8oZnVuY3Rpb24gKGFyZykge1xuICAgICAgICAgICAgICAgICAgICB2YXIgcmVnZXhwID0gbmV3IFJlZ0V4cChhcmcpO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZnVuY3Rpb24gKGVsZW0pIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgZWxlbS5hdHRyaWJ1dGVzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGF0dHIgPSBlbGVtLmF0dHJpYnV0ZXNbaV07XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHJlZ2V4cC50ZXN0KGF0dHIubmFtZSkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRydWU7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgTUFJTiBGVU5DVElPTkFMSVRZICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogRnVuY3Rpb24gdGhhdCBzZWFyY2hlcyBmb3IgdGhlIGJlc3QgZml0dGluZyBpbWFnZVxuICAgICAgICAgKiBmb3IgdGhlIHBhcmVudCBjb250YWluZXIsIHNvIHRoYXQgaXQgY2FuIHNldCB0aGUgc3JjLWF0dHJpYnV0ZVxuICAgICAgICAgKiBpbnNpZGUgdGhlIGltZy10YWdcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICR0YXJnZXQgICAgIGpRdWVyeSBzZWxlY3Rpb24gdGhhdCBjb250YWlucyB0aGUgaW1hZ2UgdG8gc2V0IChvcHRpb25hbClcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfcmVzaXplSW1hZ2VzID0gZnVuY3Rpb24gKCR0YXJnZXQpIHtcbiAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgYnJlYWtwb2ludCA9IGpzZS5saWJzLnRoZW1lLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLFxuICAgICAgICAgICAgICAgICRlbGVtZW50cyA9ICgkdGFyZ2V0ICYmICR0YXJnZXQubGVuZ3RoKSA/ICR0YXJnZXQgOiAkc2VsZlxuICAgICAgICAgICAgICAgICAgICAuZmlsdGVyKCc6YXR0ciheZGF0YS1pbWFnZSknKVxuICAgICAgICAgICAgICAgICAgICAuYWRkKCRzZWxmLmZpbmQoJzphdHRyKF5kYXRhLWltYWdlKScpKTtcblxuICAgICAgICAgICAgLy8gSXRlcmF0ZSB0cm91Z2ggZXZlcnkgaW1hZ2UgZWxlbWVudFxuICAgICAgICAgICAgLy8gYW5kIGNoZWNrIGlmIHRoZXJlIGlzIGEgbmV3IGltYWdlXG4gICAgICAgICAgICAvLyBzaXplIHRvIHNldFxuICAgICAgICAgICAgJGVsZW1lbnRzXG4gICAgICAgICAgICAgICAgLm5vdCgnLmxhenlMb2FkaW5nJylcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICRlbGVtZW50ID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrcG9pbnQgPSBqc2UubGlicy50aGVtZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGJwID0gb3B0aW9ucy5icmVha3BvaW50cy5pbmRleE9mKGJyZWFrcG9pbnQubmFtZSksXG4gICAgICAgICAgICAgICAgICAgICAgICBicENvdW50ID0gb3B0aW9ucy5icmVha3BvaW50cy5sZW5ndGgsXG4gICAgICAgICAgICAgICAgICAgICAgICBpbWcgPSBudWxsO1xuXG4gICAgICAgICAgICAgICAgICAgIGZvciAoYnA7IGJwIDwgYnBDb3VudDsgYnAgKz0gMSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGF0dHJOYW1lID0gJ2RhdGEtaW1hZ2UtJyArIG9wdGlvbnMuYnJlYWtwb2ludHNbYnBdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlID0gJGVsZW1lbnQuYXR0cihhdHRyTmFtZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICh2YWx1ZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGltZyA9IHZhbHVlO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCFpbWcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGltZyA9ICRlbGVtZW50LmF0dHIoJ2RhdGEtaW1hZ2UnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vIElmIGFuIGltYWdlIHdhcyBmb3VuZCBhbmQgdGhlIHRhcmdldCBlbGVtZW50IGhhcyBhXG4gICAgICAgICAgICAgICAgICAgIC8vIGRpZmZlcmVudCB2YWx1ZSBpbnNpZGUgaXQncyBzcmMtYXR0cmlidXRlIHNldCB0aGVcbiAgICAgICAgICAgICAgICAgICAgLy8gbmV3IHZhbHVlXG4gICAgICAgICAgICAgICAgICAgIGlmIChpbWcgJiYgJGVsZW1lbnQuYXR0cignc3JjJykgIT09IGltZykge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGVsZW1lbnQuYXR0cignc3JjJywgaW1nKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEZ1bmN0aW9uIHRoYXQgaW5pdGlhbGl6ZXMgdGhlIGxhenkgbG9hZGluZ1xuICAgICAgICAgKiBjYXBhYmlsaXR5IG9mIGltYWdlc1xuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9yZWdpc3RlckxhenlMb2FkaW5nID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyICRlbGVtZW50cyA9ICR0aGlzXG4gICAgICAgICAgICAgICAgLmZpbHRlcignLmxhenlMb2FkaW5nOmF0dHIoXmRhdGEtaW1hZ2UpJylcbiAgICAgICAgICAgICAgICAuYWRkKCR0aGlzLmZpbmQoJy5sYXp5TG9hZGluZzphdHRyKF5kYXRhLWltYWdlKScpKTtcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBGdW5jdGlvbiB0aGF0IHNjYW5zIHRoZSBnaXZlbiBlbGVtZW50cyBmb3IgaW1hZ2VzXG4gICAgICAgICAgICAgKiB0aGF0IGFyZSBpbiB0aGUgdmlld3BvcnQgYW5kIHNldCB0aGUgc291cmNlIGF0dHJpYnV0ZVxuICAgICAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICAgICAqL1xuICAgICAgICAgICAgdmFyIF9sYXp5TG9hZGluZ1Njcm9sbEhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgd2luZG93V2lkdGggPSAkKHdpbmRvdykud2lkdGgoKSxcbiAgICAgICAgICAgICAgICAgICAgd2luZG93SGVpZ2h0ID0gJCh3aW5kb3cpLmhlaWdodCgpLFxuICAgICAgICAgICAgICAgICAgICB0b3AgPSAkKHdpbmRvdykuc2Nyb2xsVG9wKCksXG4gICAgICAgICAgICAgICAgICAgIGxlZnQgPSAkKHdpbmRvdykuc2Nyb2xsTGVmdCgpO1xuXG4gICAgICAgICAgICAgICAgJGVsZW1lbnRzLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICAgICAgb2Zmc2V0ID0gJHNlbGYub2Zmc2V0KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKG9mZnNldC50b3AgPCAodG9wICsgd2luZG93SGVpZ2h0KSB8fCBvZmZzZXQubGVmdCA8IChsZWZ0ICsgd2luZG93V2lkdGgpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkZWxlbWVudHMgPSAkZWxlbWVudHMubm90KCRzZWxmKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRzZWxmLnRyaWdnZXIoJ2xhenlMb2FkSW1hZ2UnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLyoqXG4gICAgICAgICAgICAgKiBSZW1vdmVzIHRoZSBjbGFzcyBcImxhenlMb2FkaW5nXCIgZnJvbSB0aGUgaW1hZ2VcbiAgICAgICAgICAgICAqIHNvIHRoYXQgdGhlIFwiX3Jlc2l6ZUltYWdlc1wiIGlzIGFibGUgdG8gc2VsZWN0IGl0XG4gICAgICAgICAgICAgKiBBZnRlcndhcmRzIGV4ZWN1dGUgdGhpcyBmdW5jdGlvbiB0byBzZXQgdGhlXG4gICAgICAgICAgICAgKiBjb3JyZWN0IGltYWdlIHNvdXJjZVxuICAgICAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICAgICAqL1xuICAgICAgICAgICAgdmFyIF9sb2FkSW1hZ2UgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgICRzZWxmLnJlbW92ZUNsYXNzKCdsYXp5TG9hZGluZycpO1xuICAgICAgICAgICAgICAgIF9yZXNpemVJbWFnZXMoJHNlbGYpO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy8gQWRkIGFuIGV2ZW50IGhhbmRsZXIgZm9yIGxvYWRpbmcgdGhlIGZpcnN0IHJlYWwgaW1hZ2VcbiAgICAgICAgICAgIC8vIHRvIGV2ZXJ5IGltYWdlIGVsZW1lbnQgdGhhdCBpcyBvbmx5IGV4ZWN1dGVkIG9uY2VcbiAgICAgICAgICAgICRlbGVtZW50cy5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLm9uZSgnbGF6eUxvYWRJbWFnZScsIF9sb2FkSW1hZ2UpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vIEFkZCBldmVudCBoYW5kbGVyIHRvIGV2ZXJ5IGV2ZW50IHRoYXQgY2hhbmdlcyB0aGUgZGltZW5zaW9uIC8gdmlld3BvcnRcbiAgICAgICAgICAgICQod2luZG93KS5vbignc2Nyb2xsIHdpbmRvd1dhc1Jlc2l6ZWQnLCBfbGF6eUxvYWRpbmdTY3JvbGxIYW5kbGVyKTtcblxuICAgICAgICAgICAgLy8gTG9hZCBpbWFnZXMgdGhhdCBhcmUgaW4gdmlldyBvbiBsb2FkXG4gICAgICAgICAgICBfbGF6eUxvYWRpbmdTY3JvbGxIYW5kbGVyKCk7XG4gICAgICAgIH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblxuICAgICAgICAgICAgX3JlZ2lzdGVyU2VsZWN0b3IoKTtcbiAgICAgICAgICAgIF9yZWdpc3RlckxhenlMb2FkaW5nKCk7XG5cbiAgICAgICAgICAgICQod2luZG93KS5vbihqc2UubGlicy50aGVtZS5ldmVudHMuQlJFQUtQT0lOVCgpLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgX3Jlc2l6ZUltYWdlcy5jYWxsKCR0aGlzKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBfcmVzaXplSW1hZ2VzLmNhbGwoJHRoaXMpO1xuXG4gICAgICAgICAgICBkb25lKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgICAgICByZXR1cm4gbW9kdWxlO1xuICAgIH0pO1xuIl19

'use strict';

/* --------------------------------------------------------------
 product_hover.js 2022-03-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that is used for the hover functionality
 * of the product tiles. It includes the functionality
 * for the image gallery inside the tile
 */
gambio.widgets.module('product_hover', [gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $window = $(window),
        $body = $('body'),
        $container = null,
        timer = null,
        componentId = null,
        clickTimer = 0,
        defaults = {
        delay: 50, // Delay in ms after which a hovered element gets closed after mouseleave
        flyoverClass: 'flyover', // Class that gets added to every flyover
        scope: '', // Sets the scope selector for the mouseover events
        container: '#wrapper', // Container selector which is the boundary for the cloned element
        productUrlSelector: '.product-url' // a tag selector of product's url
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Helper function to remove the opened flyovers that
     * were appended to the body by this component
     * @private
     */
    var _removeFlyover = function _removeFlyover(all) {
        var $flyover = $body.children('.' + options.flyoverClass);
        $flyover = all ? $flyover : $flyover.filter('.product-hover-' + componentId);

        $flyover.remove();
    };

    /**
     * Helper function that replaces the preloader
     * images with the real thumbnail images on
     * layer creation. This is needed to save
     * bandwidth
     * @param       {object}    $clone      jQuery selection of the layer
     * @private
     */
    var _loadImages = function _loadImages($clone) {
        $clone.find('.thumbnails img').each(function () {

            var $self = $(this),
                $img = $('<img />'),
                dataset = $self.data(),
                src = dataset.thumbSrc || dataset.src,
                $parentListItem = null;

            $img.on('load', function () {
                $parentListItem = $self.closest('li');
                $parentListItem.addClass('loaded').css({
                    'background': '#FFFFFF url("' + src + '") no-repeat center',
                    'background-size': 'contain'
                }).find('img, .align-helper').remove();
            }).attr('src', src);
        });
    };

    // ########## EVENT HANDLER ##########

    /**
     * Handler for the click event on the thumbnail
     * images. After a click on such an image the
     * main image of the hover element gets replaced
     * with the bigger version of the thumbnail image
     * @param       {object}        e       jQuery event object
     * @private
     */
    var _mouseEnterThumbHandler = function _mouseEnterThumbHandler(e) {
        e.preventDefault();

        var $img = $(this),
            $container = $img.closest('.' + options.flyoverClass),
            dataSrc = $img.css('background-image');

        // Change path to big images and remove quotes
        dataSrc = dataSrc.replace('/thumbnail_images/', '/info_images/').replace('/gallery_images/', '/thumbnail_images/').replace(/["']/gm, '');

        // Remove "url()"
        var matches = dataSrc.match(/url\((.+)\)/);
        if (matches && matches[1]) {
            dataSrc = matches[1];
        }

        if (dataSrc) {
            $container.find('.product-hover-main-image img').attr('src', dataSrc);
        }
    };

    /**
     * Event handler for the mouse leave event of the
     * hovered element. It sets a timer to remove the
     * hover element after a certain time
     * @param       {object}    e       jQuery event object
     * @private
     */
    var _mouseLeaveHandler = function _mouseLeaveHandler(e) {
        e.stopPropagation();
        timer = timer ? clearTimeout(timer) : null;
        timer = window.setTimeout(_removeFlyover, options.delay);
    };

    /**
     * Event handler for the mouse enter event on both
     * elements (initial & hovered element).
     * It clones the initial element and adds the clone
     * to the body. It additionally adds functionality
     * for the image gallery inside the hovered element
     * @param       {object}        e       jQuery event object
     * @private
     */
    var _mouseEnterHandler = function _mouseEnterHandler(e) {
        e.stopPropagation();

        var $self = $(this),
            $clone = null,
            $target = $body,
            uid = $self.data().uid || parseInt(Math.random() * 10000, 10),
            $flyover = $target.children('.' + options.flyoverClass + '.product-hover-' + componentId + '[data-product_hover-uid="' + uid + '"]'),
            offset = $self.offset();

        timer = timer ? clearTimeout(timer) : null;

        // Check if flyover needs to be created
        if (!$self.hasClass(options.flyoverClass) && !$flyover.length) {
            // Remove old opened flyovers
            _removeFlyover(true);
            $this.trigger(jse.libs.theme.events.OPEN_FLYOUT(), $this);

            // Add a UID for identification to th hovered object
            $self.attr('data-product_hover-uid', uid).data('uid', uid);

            // Generate the markup
            $clone = $self.clone(true);

            // Replace the preloader images with the thumbnail images
            _loadImages($clone);

            // Set the positioning of the layer
            $clone.addClass(options.flyoverClass + ' product-hover-' + componentId).css({
                'position': 'absolute',
                'left': offset.left,
                'top': offset.top,
                'width': $self[0].getBoundingClientRect().width,
                'height': $self[0].getBoundingClientRect().height
            });

            // Add event listener to the hover elements
            $clone.on('mouseenter', _mouseEnterHandler).on('mouseleave', _mouseLeaveHandler).on('mouseenter', '.thumbnails', _mouseEnterThumbHandler).on('click', _clickHandler);

            // Add the element to the body element
            $body.append($clone);

            if ($container.offset().left > $clone.offset().left) {
                $clone.addClass('gallery-right');
            }
        }
    };

    /**
     * Handler for the window resize event. It
     * recalculates the position of the overlays
     * @private
     */
    var _resizeHandler = function _resizeHandler() {

        var $flyover = $body.children('.' + options.flyoverClass + '.product-hover-' + componentId);

        $flyover.each(function () {
            var $self = $(this),
                uid = $self.data().uid,
                $source = $this.find('[data-product_hover-uid="' + uid + '"]'),
                offset = $source.offset();

            $self.css({
                left: offset.left,
                top: offset.top,
                width: 2 * $source.outerWidth()
            });
        });
    };

    /**
     * Event handler that closes the flyovers
     * if another flyover opens on the page
     * @param       {object}        e           jQuery event object
     * @param       {object}        d           jQuery selection of the event emitter
     * @private
     */
    var _closeLayers = function _closeLayers(e, d) {
        if ($this !== d) {
            _removeFlyover();
        }
    };

    /**
     * Event handler that makes the flyover and product image clickable linking to the product details page
     *
     * @param       {object}        e           jQuery event object
     * @private
     */
    var _clickHandler = function _clickHandler(e) {
        var $container = $(this);

        if ($(this).hasClass('product-container') === false) {
            $container = $(this).closest('.product-container');
        }

        var $link = $container.find(options.productUrlSelector).first();

        function callback() {
            if ($link.length) {
                var url = $link.attr('href');

                if (url !== undefined) {
                    e.stopPropagation();
                    e.preventDefault();

                    // prevent double _clickHandler actions
                    if (new Date().getTime() - clickTimer < 100) {
                        return;
                    } else {
                        clickTimer = new Date().getTime();
                    }

                    switch (e.which) {
                        // left click
                        case 1:
                            if (e.ctrlKey) {
                                window.open(url, '_blank');
                                return;
                            }
                            break;

                        // middle click
                        case 2:
                            window.open(url, '_blank');
                            return;
                            break;

                        // right click
                        case 3:
                            return;
                    }

                    location.href = url;
                }
            }
        }

        jse.libs.hooks.execute(jse.libs.hooks.keys.shop.product.listing.hover, { container: $container }, 500).then(callback).catch(callback);
    };

    /**
     * Real links in the product container need no execution of the _clickHandler logic.
     *
     * @param       {object}        e           jQuery event object
     * @private
     */
    var _ignoreClickHandler = function _ignoreClickHandler(e) {
        e.stopPropagation();
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        componentId = parseInt(Math.random() * 10000, 10);
        $container = $(options.container);

        $this.on('touchstart', function () {
            // Workaround for tablet navigation problem
            $this.off('mouseenter mouseleave');
        }).on('touchend', function () {
            $this.off('mouseenter', options.scope + ' .product-container', _mouseEnterHandler).off('mouseleave', options.scope + ' .product-container', _mouseLeaveHandler);
        }).on('mouseenter', options.scope + ' .product-container', _mouseEnterHandler).on('mouseleave', options.scope + ' .product-container', _mouseLeaveHandler);

        $this.find('.product-container .product-image').on('click mouseup', _clickHandler);
        $this.find('.product-container .product-image a').on('click mouseup', _ignoreClickHandler);

        $body.on(jse.libs.theme.events.OPEN_FLYOUT(), _closeLayers);

        $window.on('resize', _resizeHandler);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9ob3Zlci5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR3aW5kb3ciLCJ3aW5kb3ciLCIkYm9keSIsIiRjb250YWluZXIiLCJ0aW1lciIsImNvbXBvbmVudElkIiwiY2xpY2tUaW1lciIsImRlZmF1bHRzIiwiZGVsYXkiLCJmbHlvdmVyQ2xhc3MiLCJzY29wZSIsImNvbnRhaW5lciIsInByb2R1Y3RVcmxTZWxlY3RvciIsIm9wdGlvbnMiLCJleHRlbmQiLCJfcmVtb3ZlRmx5b3ZlciIsImFsbCIsIiRmbHlvdmVyIiwiY2hpbGRyZW4iLCJmaWx0ZXIiLCJyZW1vdmUiLCJfbG9hZEltYWdlcyIsIiRjbG9uZSIsImZpbmQiLCJlYWNoIiwiJHNlbGYiLCIkaW1nIiwiZGF0YXNldCIsInNyYyIsInRodW1iU3JjIiwiJHBhcmVudExpc3RJdGVtIiwib24iLCJjbG9zZXN0IiwiYWRkQ2xhc3MiLCJjc3MiLCJhdHRyIiwiX21vdXNlRW50ZXJUaHVtYkhhbmRsZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJkYXRhU3JjIiwicmVwbGFjZSIsIm1hdGNoZXMiLCJtYXRjaCIsIl9tb3VzZUxlYXZlSGFuZGxlciIsInN0b3BQcm9wYWdhdGlvbiIsImNsZWFyVGltZW91dCIsInNldFRpbWVvdXQiLCJfbW91c2VFbnRlckhhbmRsZXIiLCIkdGFyZ2V0IiwidWlkIiwicGFyc2VJbnQiLCJNYXRoIiwicmFuZG9tIiwib2Zmc2V0IiwiaGFzQ2xhc3MiLCJsZW5ndGgiLCJ0cmlnZ2VyIiwianNlIiwibGlicyIsInRoZW1lIiwiZXZlbnRzIiwiT1BFTl9GTFlPVVQiLCJjbG9uZSIsImxlZnQiLCJ0b3AiLCJnZXRCb3VuZGluZ0NsaWVudFJlY3QiLCJ3aWR0aCIsImhlaWdodCIsIl9jbGlja0hhbmRsZXIiLCJhcHBlbmQiLCJfcmVzaXplSGFuZGxlciIsIiRzb3VyY2UiLCJvdXRlcldpZHRoIiwiX2Nsb3NlTGF5ZXJzIiwiZCIsIiRsaW5rIiwiZmlyc3QiLCJjYWxsYmFjayIsInVybCIsInVuZGVmaW5lZCIsIkRhdGUiLCJnZXRUaW1lIiwid2hpY2giLCJjdHJsS2V5Iiwib3BlbiIsImxvY2F0aW9uIiwiaHJlZiIsImhvb2tzIiwiZXhlY3V0ZSIsImtleXMiLCJzaG9wIiwicHJvZHVjdCIsImxpc3RpbmciLCJob3ZlciIsInRoZW4iLCJjYXRjaCIsIl9pZ25vcmVDbGlja0hhbmRsZXIiLCJpbml0IiwiZG9uZSIsIm9mZiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSxlQURKLEVBR0ksQ0FDSUYsT0FBT0csTUFBUCxHQUFnQixjQURwQixDQUhKLEVBT0ksVUFBVUMsSUFBVixFQUFnQjs7QUFFWjs7QUFFUjs7QUFFUSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFVBQVVELEVBQUVFLE1BQUYsQ0FEZDtBQUFBLFFBRUlDLFFBQVFILEVBQUUsTUFBRixDQUZaO0FBQUEsUUFHSUksYUFBYSxJQUhqQjtBQUFBLFFBSUlDLFFBQVEsSUFKWjtBQUFBLFFBS0lDLGNBQWMsSUFMbEI7QUFBQSxRQU1JQyxhQUFhLENBTmpCO0FBQUEsUUFPSUMsV0FBVztBQUNQQyxlQUFPLEVBREEsRUFDVTtBQUNqQkMsc0JBQWMsU0FGUCxFQUVtQjtBQUMxQkMsZUFBTyxFQUhBLEVBR2E7QUFDcEJDLG1CQUFXLFVBSkosRUFJZ0I7QUFDdkJDLDRCQUFvQixjQUxiLENBSzRCO0FBTDVCLEtBUGY7QUFBQSxRQWNJQyxVQUFVZCxFQUFFZSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJQLFFBQW5CLEVBQTZCVixJQUE3QixDQWRkO0FBQUEsUUFlSUYsU0FBUyxFQWZiOztBQWlCUjs7QUFFUTs7Ozs7QUFLQSxRQUFJb0IsaUJBQWlCLFNBQWpCQSxjQUFpQixDQUFVQyxHQUFWLEVBQWU7QUFDaEMs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QUFYLEVBQWtEUSxFQUFsRCxDQUFxRCxlQUFyRCxFQUFzRWlFLG1CQUF0RTs7QUFFQTlGLGNBQ0s2QixFQURMLENBQ1EwQixJQUFJQyxJQUFKLENBQVNDLEtBQVQsQ0FBZUMsTUFBZixDQUFzQkMsV0FBdEIsRUFEUixFQUM2Q1ksWUFEN0M7O0FBR0F6RSxnQkFDSytCLEVBREwsQ0FDUSxRQURSLEVBQ2tCdUMsY0FEbEI7O0FBR0E0QjtBQUNILEtBNUJEOztBQThCQTtBQUNBLFdBQU92RyxNQUFQO0FBQ0gsQ0FwVkwiLCJmaWxlIjoid2lkZ2V0cy9wcm9kdWN0X2hvdmVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBwcm9kdWN0X2hvdmVyLmpzIDIwMjItMDMtMjJcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDIyIEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IGlzIHVzZWQgZm9yIHRoZSBob3ZlciBmdW5jdGlvbmFsaXR5XG4gKiBvZiB0aGUgcHJvZHVjdCB0aWxlcy4gSXQgaW5jbHVkZXMgdGhlIGZ1bmN0aW9uYWxpdHlcbiAqIGZvciB0aGUgaW1hZ2UgZ2FsbGVyeSBpbnNpZGUgdGhlIHRpbGVcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdwcm9kdWN0X2hvdmVyJyxcblxuICAgIFtcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnXG4gICAgXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgJHdpbmRvdyA9ICQod2luZG93KSxcbiAgICAgICAgICAgICRib2R5ID0gJCgnYm9keScpLFxuICAgICAgICAgICAgJGNvbnRhaW5lciA9IG51bGwsXG4gICAgICAgICAgICB0aW1lciA9IG51bGwsXG4gICAgICAgICAgICBjb21wb25lbnRJZCA9IG51bGwsXG4gICAgICAgICAgICBjbGlja1RpbWVyID0gMCxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIGRlbGF5OiA1MCwgICAgICAgLy8gRGVsYXkgaW4gbXMgYWZ0ZXIgd2hpY2ggYSBob3ZlcmVkIGVsZW1lbnQgZ2V0cyBjbG9zZWQgYWZ0ZXIgbW91c2VsZWF2ZVxuICAgICAgICAgICAgICAgIGZseW92ZXJDbGFzczogJ2ZseW92ZXInLCAgLy8gQ2xhc3MgdGhhdCBnZXRzIGFkZGVkIHRvIGV2ZXJ5IGZseW92ZXJcbiAgICAgICAgICAgICAgICBzY29wZTogJycsICAgICAgICAgIC8vIFNldHMgdGhlIHNjb3BlIHNlbGVjdG9yIGZvciB0aGUgbW91c2VvdmVyIGV2ZW50c1xuICAgICAgICAgICAgICAgIGNvbnRhaW5lcjogJyN3cmFwcGVyJywgLy8gQ29udGFpbmVyIHNlbGVjdG9yIHdoaWNoIGlzIHRoZSBib3VuZGFyeSBmb3IgdGhlIGNsb25lZCBlbGVtZW50XG4gICAgICAgICAgICAgICAgcHJvZHVjdFVybFNlbGVjdG9yOiAnLnByb2R1Y3QtdXJsJyAvLyBhIHRhZyBzZWxlY3RvciBvZiBwcm9kdWN0J3MgdXJsXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRvIHJlbW92ZSB0aGUgb3BlbmVkIGZseW92ZXJzIHRoYXRcbiAgICAgICAgICogd2VyZSBhcHBlbmRlZCB0byB0aGUgYm9keSBieSB0aGlzIGNvbXBvbmVudFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9yZW1vdmVGbHlvdmVyID0gZnVuY3Rpb24gKGFsbCkge1xuICAgICAgICAgICAgdmFyICRmbHlvdmVyID0gJGJvZHkuY2hpbGRyZW4oJy4nICsgb3B0aW9ucy5mbHlvdmVyQ2xhc3MpO1xuICAgICAgICAgICAgJGZseW92ZXIgPSBhbGwgPyAkZmx5b3ZlciA6ICRmbHlvdmVyLmZpbHRlcignLnByb2R1Y3QtaG92ZXItJyArIGNvbXBvbmVudElkKTtcblxuICAgICAgICAgICAgJGZseW92ZXIucmVtb3ZlKCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IHJlcGxhY2VzIHRoZSBwcmVsb2FkZXJcbiAgICAgICAgICogaW1hZ2VzIHdpdGggdGhlIHJlYWwgdGh1bWJuYWlsIGltYWdlcyBvblxuICAgICAgICAgKiBsYXllciBjcmVhdGlvbi4gVGhpcyBpcyBuZWVkZWQgdG8gc2F2ZVxuICAgICAgICAgKiBiYW5kd2lkdGhcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICRjbG9uZSAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGxheWVyXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2xvYWRJbWFnZXMgPSBmdW5jdGlvbiAoJGNsb25lKSB7XG4gICAgICAgICAgICAkY2xvbmVcbiAgICAgICAgICAgICAgICAuZmluZCgnLnRodW1ibmFpbHMgaW1nJylcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICRpbWcgPSAkKCc8aW1nIC8+JyksXG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRhc2V0ID0gJHNlbGYuZGF0YSgpLFxuICAgICAgICAgICAgICAgICAgICAgICAgc3JjID0gZGF0YXNldC50aHVtYlNyYyB8fCBkYXRhc2V0LnNyYyxcbiAgICAgICAgICAgICAgICAgICAgICAgICRwYXJlbnRMaXN0SXRlbSA9IG51bGw7XG5cbiAgICAgICAgICAgICAgICAgICAgJGltZy5vbignbG9hZCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICRwYXJlbnRMaXN0SXRlbSA9ICRzZWxmLmNsb3Nlc3QoJ2xpJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkcGFyZW50TGlzdEl0ZW1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2xvYWRlZCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmNzcyh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdiYWNrZ3JvdW5kJzogJyNGRkZGRkYgdXJsKFwiJyArIHNyYyArICdcIikgbm8tcmVwZWF0IGNlbnRlcicsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICdiYWNrZ3JvdW5kLXNpemUnOiAnY29udGFpbidcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5maW5kKCdpbWcsIC5hbGlnbi1oZWxwZXInKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICAgICAgfSkuYXR0cignc3JjJywgc3JjKTtcblxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhhbmRsZXIgZm9yIHRoZSBjbGljayBldmVudCBvbiB0aGUgdGh1bWJuYWlsXG4gICAgICAgICAqIGltYWdlcy4gQWZ0ZXIgYSBjbGljayBvbiBzdWNoIGFuIGltYWdlIHRoZVxuICAgICAgICAgKiBtYWluIGltYWdlIG9mIHRoZSBob3ZlciBlbGVtZW50IGdldHMgcmVwbGFjZWRcbiAgICAgICAgICogd2l0aCB0aGUgYmlnZ2VyIHZlcnNpb24gb2YgdGhlIHRodW1ibmFpbCBpbWFnZVxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9tb3VzZUVudGVyVGh1bWJIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgdmFyICRpbWcgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICRjb250YWluZXIgPSAkaW1nLmNsb3Nlc3QoJy4nICsgb3B0aW9ucy5mbHlvdmVyQ2xhc3MpLFxuICAgICAgICAgICAgICAgIGRhdGFTcmMgPSAkaW1nLmNzcygnYmFja2dyb3VuZC1pbWFnZScpO1xuXG4gICAgICAgICAgICAvLyBDaGFuZ2UgcGF0aCB0byBiaWcgaW1hZ2VzIGFuZCByZW1vdmUgcXVvdGVzXG4gICAgICAgICAgICBkYXRhU3JjID0gZGF0YVNyY1xuICAgICAgICAgICAgICAgIC5yZXBsYWNlKCcvdGh1bWJuYWlsX2ltYWdlcy8nLCAnL2luZm9faW1hZ2VzLycpXG4gICAgICAgICAgICAgICAgLnJlcGxhY2UoJy9nYWxsZXJ5X2ltYWdlcy8nLCAnL3RodW1ibmFpbF9pbWFnZXMvJylcbiAgICAgICAgICAgICAgICAucmVwbGFjZSgvW1wiJ10vZ20sICcnKTtcblxuICAgICAgICAgICAgLy8gUmVtb3ZlIFwidXJsKClcIlxuICAgICAgICAgICAgdmFyIG1hdGNoZXMgPSBkYXRhU3JjLm1hdGNoKC91cmxcXCgoLispXFwpLyk7XG4gICAgICAgICAgICBpZiAobWF0Y2hlcyAmJiBtYXRjaGVzWzFdKSB7XG4gICAgICAgICAgICAgICAgZGF0YVNyYyA9IG1hdGNoZXNbMV07XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChkYXRhU3JjKSB7XG4gICAgICAgICAgICAgICAgJGNvbnRhaW5lclxuICAgICAgICAgICAgICAgICAgICAuZmluZCgnLnByb2R1Y3QtaG92ZXItbWFpbi1pbWFnZSBpbWcnKVxuICAgICAgICAgICAgICAgICAgICAuYXR0cignc3JjJywgZGF0YVNyYyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBtb3VzZSBsZWF2ZSBldmVudCBvZiB0aGVcbiAgICAgICAgICogaG92ZXJlZCBlbGVtZW50LiBJdCBzZXRzIGEgdGltZXIgdG8gcmVtb3ZlIHRoZVxuICAgICAgICAgKiBob3ZlciBlbGVtZW50IGFmdGVyIGEgY2VydGFpbiB0aW1lXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfbW91c2VMZWF2ZUhhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcbiAgICAgICAgICAgIHRpbWVyID0gdGltZXIgPyBjbGVhclRpbWVvdXQodGltZXIpIDogbnVsbDtcbiAgICAgICAgICAgIHRpbWVyID0gd2luZG93LnNldFRpbWVvdXQoX3JlbW92ZUZseW92ZXIsIG9wdGlvbnMuZGVsYXkpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIGZvciB0aGUgbW91c2UgZW50ZXIgZXZlbnQgb24gYm90aFxuICAgICAgICAgKiBlbGVtZW50cyAoaW5pdGlhbCAmIGhvdmVyZWQgZWxlbWVudCkuXG4gICAgICAgICAqIEl0IGNsb25lcyB0aGUgaW5pdGlhbCBlbGVtZW50IGFuZCBhZGRzIHRoZSBjbG9uZVxuICAgICAgICAgKiB0byB0aGUgYm9keS4gSXQgYWRkaXRpb25hbGx5IGFkZHMgZnVuY3Rpb25hbGl0eVxuICAgICAgICAgKiBmb3IgdGhlIGltYWdlIGdhbGxlcnkgaW5zaWRlIHRoZSBob3ZlcmVkIGVsZW1lbnRcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfbW91c2VFbnRlckhhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAkY2xvbmUgPSBudWxsLFxuICAgICAgICAgICAgICAgICR0YXJnZXQgPSAkYm9keSxcbiAgICAgICAgICAgICAgICB1aWQgPSAkc2VsZi5kYXRhKCkudWlkIHx8IHBhcnNlSW50KE1hdGgucmFuZG9tKCkgKiAxMDAwMCwgMTApLFxuICAgICAgICAgICAgICAgICRmbHlvdmVyID0gJHRhcmdldC5jaGlsZHJlbignLicgKyBvcHRpb25zLmZseW92ZXJDbGFzcyArICcucHJvZHVjdC1ob3Zlci0nICsgY29tcG9uZW50SWRcbiAgICAgICAgICAgICAgICAgICAgKyAnW2RhdGEtcHJvZHVjdF9ob3Zlci11aWQ9XCInICsgdWlkICsgJ1wiXScpLFxuICAgICAgICAgICAgICAgIG9mZnNldCA9ICRzZWxmLm9mZnNldCgpO1xuXG4gICAgICAgICAgICB0aW1lciA9IHRpbWVyID8gY2xlYXJUaW1lb3V0KHRpbWVyKSA6IG51bGw7XG5cbiAgICAgICAgICAgIC8vIENoZWNrIGlmIGZseW92ZXIgbmVlZHMgdG8gYmUgY3JlYXRlZFxuICAgICAgICAgICAgaWYgKCEkc2VsZi5oYXNDbGFzcyhvcHRpb25zLmZseW92ZXJDbGFzcykgJiYgISRmbHlvdmVyLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgIC8vIFJlbW92ZSBvbGQgb3BlbmVkIGZseW92ZXJzXG4gICAgICAgICAgICAgICAgX3JlbW92ZUZseW92ZXIodHJ1ZSk7XG4gICAgICAgICAgICAgICAgJHRoaXMudHJpZ2dlcihqc2UubGlicy50aGVtZS5ldmVudHMuT1BFTl9GTFlPVVQoKSwgJHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgLy8gQWRkIGEgVUlEIGZvciBpZGVudGlmaWNhdGlvbiB0byB0aCBob3ZlcmVkIG9iamVjdFxuICAgICAgICAgICAgICAgICRzZWxmXG4gICAgICAgICAgICAgICAgICAgIC5hdHRyKCdkYXRhLXByb2R1Y3RfaG92ZXItdWlkJywgdWlkKVxuICAgICAgICAgICAgICAgICAgICAuZGF0YSgndWlkJywgdWlkKTtcblxuICAgICAgICAgICAgICAgIC8vIEdlbmVyYXRlIHRoZSBtYXJrdXBcbiAgICAgICAgICAgICAgICAkY2xvbmUgPSAkc2VsZi5jbG9uZSh0cnVlKTtcblxuICAgICAgICAgICAgICAgIC8vIFJlcGxhY2UgdGhlIHByZWxvYWRlciBpbWFnZXMgd2l0aCB0aGUgdGh1bWJuYWlsIGltYWdlc1xuICAgICAgICAgICAgICAgIF9sb2FkSW1hZ2VzKCRjbG9uZSk7XG5cbiAgICAgICAgICAgICAgICAvLyBTZXQgdGhlIHBvc2l0aW9uaW5nIG9mIHRoZSBsYXllclxuICAgICAgICAgICAgICAgICRjbG9uZVxuICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3Mob3B0aW9ucy5mbHlvdmVyQ2xhc3MgKyAnIHByb2R1Y3QtaG92ZXItJyArIGNvbXBvbmVudElkKVxuICAgICAgICAgICAgICAgICAgICAuY3NzKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICdwb3NpdGlvbic6ICdhYnNvbHV0ZScsXG4gICAgICAgICAgICAgICAgICAgICAgICAnbGVmdCc6IG9mZnNldC5sZWZ0LFxuICAgICAgICAgICAgICAgICAgICAgICAgJ3RvcCc6IG9mZnNldC50b3AsXG4gICAgICAgICAgICAgICAgICAgICAgICAnd2lkdGgnOiAkc2VsZlswXS5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKS53aWR0aCxcbiAgICAgICAgICAgICAgICAgICAgICAgICdoZWlnaHQnOiAkc2VsZlswXS5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKS5oZWlnaHRcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyBBZGQgZXZlbnQgbGlzdGVuZXIgdG8gdGhlIGhvdmVyIGVsZW1lbnRzXG4gICAgICAgICAgICAgICAgJGNsb25lXG4gICAgICAgICAgICAgICAgICAgIC5vbignbW91c2VlbnRlcicsIF9tb3VzZUVudGVySGFuZGxlcilcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdtb3VzZWxlYXZlJywgX21vdXNlTGVhdmVIYW5kbGVyKVxuICAgICAgICAgICAgICAgICAgICAub24oJ21vdXNlZW50ZXInLCAnLnRodW1ibmFpbHMnLCBfbW91c2VFbnRlclRodW1iSGFuZGxlcilcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdjbGljaycsIF9jbGlja0hhbmRsZXIpO1xuXG4gICAgICAgICAgICAgICAgLy8gQWRkIHRoZSBlbGVtZW50IHRvIHRoZSBib2R5IGVsZW1lbnRcbiAgICAgICAgICAgICAgICAkYm9keS5hcHBlbmQoJGNsb25lKTtcblxuICAgICAgICAgICAgICAgIGlmICgkY29udGFpbmVyLm9mZnNldCgpLmxlZnQgPiAkY2xvbmUub2Zmc2V0KCkubGVmdCkge1xuICAgICAgICAgICAgICAgICAgICAkY2xvbmUuYWRkQ2xhc3MoJ2dhbGxlcnktcmlnaHQnKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhhbmRsZXIgZm9yIHRoZSB3aW5kb3cgcmVzaXplIGV2ZW50LiBJdFxuICAgICAgICAgKiByZWNhbGN1bGF0ZXMgdGhlIHBvc2l0aW9uIG9mIHRoZSBvdmVybGF5c1xuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9yZXNpemVIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuXG4gICAgICAgICAgICB2YXIgJGZseW92ZXIgPSAkYm9keS5jaGlsZHJlbignLicgKyBvcHRpb25zLmZseW92ZXJDbGFzcyArICcucHJvZHVjdC1ob3Zlci0nICsgY29tcG9uZW50SWQpO1xuXG4gICAgICAgICAgICAkZmx5b3Zlci5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgICAgICB1aWQgPSAkc2VsZi5kYXRhKCkudWlkLFxuICAgICAgICAgICAgICAgICAgICAkc291cmNlID0gJHRoaXMuZmluZCgnW2RhdGEtcHJvZHVjdF9ob3Zlci11aWQ9XCInICsgdWlkICsgJ1wiXScpLFxuICAgICAgICAgICAgICAgICAgICBvZmZzZXQgPSAkc291cmNlLm9mZnNldCgpO1xuXG4gICAgICAgICAgICAgICAgJHNlbGYuY3NzKHtcbiAgICAgICAgICAgICAgICAgICAgbGVmdDogb2Zmc2V0LmxlZnQsXG4gICAgICAgICAgICAgICAgICAgIHRvcDogb2Zmc2V0LnRvcCxcbiAgICAgICAgICAgICAgICAgICAgd2lkdGg6IDIgKiAkc291cmNlLm91dGVyV2lkdGgoKVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogRXZlbnQgaGFuZGxlciB0aGF0IGNsb3NlcyB0aGUgZmx5b3ZlcnNcbiAgICAgICAgICogaWYgYW5vdGhlciBmbHlvdmVyIG9wZW5zIG9uIHRoZSBwYWdlXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGQgICAgICAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGV2ZW50IGVtaXR0ZXJcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfY2xvc2VMYXllcnMgPSBmdW5jdGlvbiAoZSwgZCkge1xuICAgICAgICAgICAgaWYgKCR0aGlzICE9PSBkKSB7XG4gICAgICAgICAgICAgICAgX3JlbW92ZUZseW92ZXIoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIHRoYXQgbWFrZXMgdGhlIGZseW92ZXIgYW5kIHByb2R1Y3QgaW1hZ2UgY2xpY2thYmxlIGxpbmtpbmcgdG8gdGhlIHByb2R1Y3QgZGV0YWlscyBwYWdlXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9jbGlja0hhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgdmFyICRjb250YWluZXIgPSAkKHRoaXMpO1xuXG4gICAgICAgICAgICBpZiAoJCh0aGlzKS5oYXNDbGFzcygncHJvZHVjdC1jb250YWluZXInKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAkY29udGFpbmVyID0gJCh0aGlzKS5jbG9zZXN0KCcucHJvZHVjdC1jb250YWluZXInKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyICRsaW5rID0gJGNvbnRhaW5lci5maW5kKG9wdGlvbnMucHJvZHVjdFVybFNlbGVjdG9yKS5maXJzdCgpO1xuXG4gICAgICAgICAgICBmdW5jdGlvbiBjYWxsYmFjaygpIHtcbiAgICAgICAgICAgICAgICBpZiAoJGxpbmsubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciB1cmwgPSAkbGluay5hdHRyKCdocmVmJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHVybCAhPT0gdW5kZWZpbmVkKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuICAgICAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBwcmV2ZW50IGRvdWJsZSBfY2xpY2tIYW5kbGVyIGFjdGlvbnNcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChuZXcgRGF0ZSgpLmdldFRpbWUoKSAtIGNsaWNrVGltZXIgPCAxMDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNsaWNrVGltZXIgPSBuZXcgRGF0ZSgpLmdldFRpbWUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgc3dpdGNoIChlLndoaWNoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gbGVmdCBjbGlja1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNhc2UgMTpcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGUuY3RybEtleSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgd2luZG93Lm9wZW4odXJsLCAnX2JsYW5rJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBtaWRkbGUgY2xpY2tcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjYXNlIDI6XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy5vcGVuKHVybCwgJ19ibGFuaycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gcmlnaHQgY2xpY2tcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjYXNlIDM6XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgbG9jYXRpb24uaHJlZiA9IHVybDtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAganNlLmxpYnMuaG9va3MuZXhlY3V0ZShqc2UubGlicy5ob29rcy5rZXlzLnNob3AucHJvZHVjdC5saXN0aW5nLmhvdmVyLCB7Y29udGFpbmVyOiAkY29udGFpbmVyfSwgNTAwKVxuICAgICAgICAgICAgICAgIC50aGVuKGNhbGxiYWNrKVxuICAgICAgICAgICAgICAgIC5jYXRjaChjYWxsYmFjayk7XG4gICAgICAgIH07XG4gICAgICAgIFxuICAgICAgICAvKipcbiAgICAgICAgICogUmVhbCBsaW5rcyBpbiB0aGUgcHJvZHVjdCBjb250YWluZXIgbmVlZCBubyBleGVjdXRpb24gb2YgdGhlIF9jbGlja0hhbmRsZXIgbG9naWMuXG4gICAgICAgICAqXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9pZ25vcmVDbGlja0hhbmRsZXIgPSBmdW5jdGlvbiBfaWdub3JlQ2xpY2tIYW5kbGVyKGUpIHtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgIH07XG4gICAgICAgIFxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAgICAgKiBAY29uc3RydWN0b3JcbiAgICAgICAgICovXG4gICAgICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcblxuICAgICAgICAgICAgY29tcG9uZW50SWQgPSBwYXJzZUludChNYXRoLnJhbmRvbSgpICogMTAwMDAsIDEwKTtcbiAgICAgICAgICAgICRjb250YWluZXIgPSAkKG9wdGlvbnMuY29udGFpbmVyKTtcblxuICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAub24oJ3RvdWNoc3RhcnQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIC8vIFdvcmthcm91bmQgZm9yIHRhYmxldCBuYXZpZ2F0aW9uIHByb2JsZW1cbiAgICAgICAgICAgICAgICAgICAgJHRoaXMub2ZmKCdtb3VzZWVudGVyIG1vdXNlbGVhdmUnKTtcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIC5vbigndG91Y2hlbmQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICR0aGlzXG4gICAgICAgICAgICAgICAgICAgICAgICAub2ZmKCdtb3VzZWVudGVyJywgb3B0aW9ucy5zY29wZSArICcgLnByb2R1Y3QtY29udGFpbmVyJywgX21vdXNlRW50ZXJIYW5kbGVyKVxuICAgICAgICAgICAgICAgICAgICAgICAgLm9mZignbW91c2VsZWF2ZScsIG9wdGlvbnMuc2NvcGUgKyAnIC5wcm9kdWN0LWNvbnRhaW5lcicsIF9tb3VzZUxlYXZlSGFuZGxlcik7XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAub24oJ21vdXNlZW50ZXInLCBvcHRpb25zLnNjb3BlICsgJyAucHJvZHVjdC1jb250YWluZXInLCBfbW91c2VFbnRlckhhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKCdtb3VzZWxlYXZlJywgb3B0aW9ucy5zY29wZSArICcgLnByb2R1Y3QtY29udGFpbmVyJywgX21vdXNlTGVhdmVIYW5kbGVyKTtcblxuICAgICAgICAgICAgJHRoaXMuZmluZCgnLnByb2R1Y3QtY29udGFpbmVyIC5wcm9kdWN0LWltYWdlJykub24oJ2NsaWNrIG1vdXNldXAnLCBfY2xpY2tIYW5kbGVyKTtcbiAgICAgICAgICAgICR0aGlzLmZpbmQoJy5wcm9kdWN0LWNvbnRhaW5lciAucHJvZHVjdC1pbWFnZSBhJykub24oJ2NsaWNrIG1vdXNldXAnLCBfaWdub3JlQ2xpY2tIYW5kbGVyKTtcblxuICAgICAgICAgICAgJGJvZHlcbiAgICAgICAgICAgICAgICAub24oanNlLmxpYnMudGhlbWUuZXZlbnRzLk9QRU5fRkxZT1VUKCksIF9jbG9zZUxheWVycyk7XG5cbiAgICAgICAgICAgICR3aW5kb3dcbiAgICAgICAgICAgICAgICAub24oJ3Jlc2l6ZScsIF9yZXNpemVIYW5kbGVyKTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTtcbiJdfQ==

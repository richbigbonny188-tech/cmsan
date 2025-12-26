'use strict';

/* --------------------------------------------------------------
 swiper.js 2020-06-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Swiper */

/**
 * Widget that binds the swiper plugin (third party) to a DOM element
 *
 * @todo Remove the try - catch blocks and and correct the swiper issues.
 */
gambio.widgets.module('swiper', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        $slides = null,
        $controls = null,
        $target = null,
        $theme = null,
        init = true,
        swiper = null,
        sliderOptions = null,
        hasThumbnails = true,
        mode = null,
        breakpointDataset = null,
        duplicates = false,
        preventSlideStart = false,
        sliderDefaults = {
        observer: true,
        // Default configuration for the swiper
        pagination: '.swiper-pagination',
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        paginationClickable: true,
        loop: true,
        autoplay: 3,
        autoplayDisableOnInteraction: false
    },
        defaults = {
        observer: true,
        // JSON that gets merged with the sliderDefaults and is passed to "swiper" directly.
        sliderOptions: null,
        // If this instance is a "main" swiper, the given selector selects the "control" swiper.
        controls: null,
        // If this instance is a "control" swiper, the given selector selects the "main" swiper.
        target: null,
        // Sets the initial slide (needed to prevent different init slides in main/controller slider).
        initSlide: null,
        // Detect if a swiper is needed for the breakpoint. If not, turn it off
        autoOff: false,
        // The translucence fix enables support for a fade effect between images with different aspect ratio,
        // but causing a delay between the change
        disableTranslucenceFix: false,
        breakpoints: []
    },
        options = $.extend({}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Function that generates the markup for
     * the preview bullets
     * @param       {Swiper}        swiper          Swiper object
     * @param       {integer}       index           Index of the slide
     * @param       {string}        className       The classname that must be add to the markup
     * @return      {string}                        The preview image html string
     * @private
     */
    var _generatePreviewButtons = function _generatePreviewButtons(swiper, index, className) {
        var $currentSlide = $slides.eq(index),
            $image = $currentSlide.find('img'),
            altTxt = $image.attr('alt'),
            thumbImage = $currentSlide.data('thumbImage');

        if (thumbImage) {
            return '<img src="' + thumbImage + '" alt="' + altTxt + '" class="' + className + '" />';
        }

        return '';
    };

    /**
     * Helper function to get the index of the
     * active slide
     * @return     {integer}                       The index of the active slide
     * @private
     */
    var _getIndex = function _getIndex() {
        return $this.find('.swiper-slide-active').data("swiper-slide-index") || 0;
    };

    /**
     * Helper function to add the active
     * class to the active slide
     * @param       {integer}           index       The index of the active slide
     * @private
     */
    var _setActive = function _setActive(index) {
        $slides = $this.find('.swiper-slide:not(.swiper-slide-duplicate)');
        index = duplicates ? index + 1 : index;
        $slides.removeClass('active').eq(index).addClass('active');
    };

    // ########## EVENT HANDLER ##########

    /**
     * Event handler for the mouseenter event.
     * It disables the autoplay
     * @private
     */
    var _mouseEnterHandler = function _mouseEnterHandler() {
        try {
            if (swiper) {
                swiper.stopAutoplay();
            }
        } catch (e) {
            // Do not log the error
        }
    };

    /**
     * Event handler for the mouseleave event.
     * It enables the autoplay
     * @private
     */
    var _mouseLeaveHandler = function _mouseLeaveHandler() {
        try {
            if (swiper) {
                swiper.startAutoplay();
            }
        } catch (e) {
            // Do not log the error
        }
    };

    /**
     * Event handler for the goto event.
     * It switches the current slide to the given index
     * and adds the active class to the new active slide
     * @param       {object}    e       jQuery event object
     * @param       {number}    d       Index of the slide to show
     * @private
     */
    var _gotoHandler = function _gotoHandler(e, d) {
        e.stopPropagation();

        // Set the active slide
        _setActive(d);

        // Temporary deactivate the onSlideChangeStart event
        // to prevent looping through the goto / changeStart
        // events
        preventSlideStart = true;

        // Remove the autoplay after a goto event
        $this.off('mouseleave.swiper');
        swiper.stopAutoplay();

        // Try to correct the index between sliders
        // with and without duplicates
        var index = duplicates ? d + 1 : d;
        if (index > $slides.length) {
            index = 0;
        }

        // Goto the desired slide
        swiper.slideTo(index);

        // Reactivate the onSlideChangeEvent
        preventSlideStart = false;
    };

    /**
     * Click event handler that triggers a
     * "goto" event to the target swiper
     * @param       {object}        e       jQuery event object
     * @private
     */
    var _clickHandler = function _clickHandler(e) {
        e.preventDefault();
        e.stopPropagation();

        var $self = $(this),
            index = $self.index();

        index = duplicates ? index - 1 : index;

        // Set the active slide
        _setActive(index);

        // Inform the main swiper
        $target.trigger(jse.libs.theme.events.SWIPER_GOTO(), index);
    };

    /**
     * Event that gets triggered on slideChange.
     * If the slide gets changed, the controls
     * will follow the current slide in position
     * @private
     */
    var _triggerSlideChange = function _triggerSlideChange() {
        if (!preventSlideStart) {
            var index = _getIndex(),
                lastIndex = $slides.closest(".swiper-slide-prev").index() || $slides.length - 2;

            var previousSlider = $slides.closest(".swiper-slide-prev");
            if (previousSlider) {
                lastIndex = duplicates ? previousSlider.index() - 1 : previousSlider.index();
            }

            if (index > $slides.length - 1) {
                index = 0;
            }

            // Recalculate index if duplicate slides are inside the slider
            if (index < 0) {
                index = $slides.length + index;
            } else {
                index = duplicates && index === lastIndex ? index - lastIndex : index;
            }

            // Set the active slide
            _setActive(index);

            // Inform the controls
            $controls.trigger(jse.libs.theme.events.SWIPER_GOTO(), index);
        }
    };

    /**
     * Workaround for the translucence issue
     * that happens on small screens with enabled
     * fade effect. Maybe it can be removed, if the
     * swiper gets updated itself
     * @private
     */
    var _translucenceWorkaround = function _translucenceWorkaround() {
        if (!options.disableTranslucenceFix && sliderOptions && sliderOptions.effect === 'fade') {
            $this.find('.swiper-slide').filter(':not(.swiper-slide-active)').fadeTo(300, 0, function () {
                $(this).css('visibility', 'hidden');
            });

            $this.find('.swiper-slide').filter('.swiper-slide-active').fadeTo(300, 1, function () {
                $(this).css('visibility', '');
            });
        }
    };

    /**
     * The breakpoint handler initializes the swiper
     * with the settings for the current breakpoint.
     * Therefore it uses the default slider options,
     * the custom slider options given by the options
     * object and the breakpoint options object also
     * given by the options (in this order)
     * @private
     */
    var _breakpointHandler = function _breakpointHandler() {

        // Get the current viewmode
        var oldMode = mode || {},
            newMode = jse.libs.theme.responsive.breakpoint(),
            extendOptions = options.breakpoints[0] || {},
            newBreakpointDataset = null;

        // Only do something if the view was changed
        if (newMode.id !== oldMode.id) {

            // Store the new viewmode
            mode = $.extend({}, newMode);

            // Iterate through the breakpoints object to detect
            // the correct settings for the current breakpoint
            $.each(options.breakpoints, function (i, v) {
                if (v.breakpoint > newMode.id) {
                    return false;
                }
                newBreakpointDataset = i;
                extendOptions = v;
            });

            if (options.sliderOptions && options.sliderOptions.breakpoints) {
                $.each(options.sliderOptions.breakpoints, function (i, v) {
                    if (v.breakpoint === newMode.id) {
                        extendOptions = v;
                        return false;
                    }
                });
            }

            // Only do something if the settings change due browser
            // resize or if it's the first time run
            if (newBreakpointDataset !== breakpointDataset || init) {
                // Combine the settings
                sliderOptions = $.extend({}, sliderDefaults, options.sliderOptions || {}, extendOptions);

                // Add the preview image bullets function to the options object
                if (sliderOptions.usePreviewBullets && hasThumbnails) {
                    sliderOptions.paginationBulletRender = _generatePreviewButtons;
                }

                // Add the autoplay interval to the options object
                sliderOptions.autoplay = sliderOptions.autoplay ? sliderOptions.autoplay * 1000 : 0;

                // Disable loop if there is only one slider. 
                if ($this.find('.swiper-slide').length === 1) {
                    sliderOptions.loop = false;
                }

                // If an swiper exists, get the current
                // slide no. and remove the old swiper
                if (swiper) {
                    sliderOptions.initialSlide = _getIndex();
                    try {
                        swiper.destroy(true, true);
                    } catch (ignore) {
                        swiper = null;
                    }
                } else {
                    sliderOptions.initialSlide = options.initSlide || sliderOptions.initialSlide || 0;
                }

                var $duplicate = $this.find('.swiper-slide:not(.swiper-slide-duplicate)');

                if (!options.autoOff || $duplicate.length > sliderOptions.slidesPerView && options.autoOff) {
                    $this.addClass('swiper-is-active').removeClass('swiper-is-not-active');

                    // Initialize the swiper
                    try {
                        swiper = new Swiper($this, sliderOptions);
                    } catch (e) {
                        return; // Swiper might throw an error upon initialization that should not halt the script execution.
                    }

                    swiper.off('onTransitionEnd onSlideChangeStart').on('onTransitionEnd', _translucenceWorkaround);

                    // If this is a "main" swiper and has external controls, an
                    // goto event is triggered if the current slide is changed
                    if ($controls.length) {
                        swiper.on('onSlideChangeStart', _triggerSlideChange);
                    }

                    // Add the event handler
                    $this.off('mouseenter.swiper mouseleave.swiper ' + jse.libs.theme.events.SWIPER_GOTO() + ' ' + jse.libs.theme.events.SLIDES_UPDATE()).on('mouseenter.swiper', _mouseEnterHandler).on('mouseleave.swiper', _mouseLeaveHandler).on(jse.libs.theme.events.SWIPER_GOTO(), _gotoHandler).on(jse.libs.theme.events.SLIDES_UPDATE(), _updateSlides);

                    if (init) {
                        // Check if there are duplicates slides (generated by the swiper)
                        // after the first time init of the swiper
                        duplicates = !!$this.find('.swiper-slide-duplicate').length;
                    }

                    // Set the active slide
                    var index = init && options.initSlide ? options.initSlide : _getIndex();
                    _setActive(index);

                    // Inform the controls that the main swiper has changed
                    // In case that the other slider isn't initialized yet,
                    // set an data attribute to the markup element to inform
                    // it on init
                    if ($controls.length) {
                        $controls.attr('data-swiper-init-slide', index);
                        _triggerSlideChange();
                    }

                    // Unset the init flag
                    init = false;

                    setTimeout(function () {
                        return swiper.update();
                    });
                } else {
                    // Disable the swiper buttons
                    $this.removeClass('swiper-is-active').addClass('swiper-is-not-active');
                    init = true;
                }
            }
        }
    };

    /**
     * Event handler that adds & removes slides from the
     * swiper. After the slides were processed, the first
     * slide is shown
     * @param       {object}    e       jQuery event object
     * @param       {object}    d       JSON object that contains the categories / images
     * @private
     */
    var _updateSlides = function _updateSlides(e, d) {

        // Loops through each category inside the images array
        $.each(d, function (category, dataset) {
            var catName = category + '-category',
                add = [],
                remove = [],
                markup = $theme.html();

            // Get all indexes from the slides
            // of the same category and remove
            // them from the slider
            $slides.filter('.' + catName).each(function () {
                var $self = $(this),
                    index = $self.data().swiperSlideIndex;

                index = index === undefined ? $self.index() : index;
                remove.push(index);
            });
            swiper.removeSlide(remove);

            // Generate the markup for the new slides
            // and add them to the slider
            $.each(dataset || [], function (i, v) {
                v.className = catName;
                v.srcattr = 'src="' + v.src + '"';
                add.push(Mustache.render(markup, v));
            });
            swiper.appendSlide(add);
        });

        $slides = $this.find('.swiper-slide');

        // To prevent an inconsistent state
        // in control / main slider combinations
        // slide to the first slide
        _setActive(0);
        var index = duplicates ? 1 : 0;
        swiper.slideTo(index, 0);
    };

    /**
     * Prevent text selection by clicking on swiper buttons
     * @private
     */
    var _preventTextSelection = function _preventTextSelection() {
        $(options.sliderOptions.nextButton).on('selectstart', function () {
            return false;
        });
        $(options.sliderOptions.prevButton).on('selectstart', function () {
            return false;
        });
    };

    /**
     * Sets the initial height for one swiper image container to prevent cut off images on smaller swiper heights
     * @private
     */
    var _scaleThumbnailHeight = function _scaleThumbnailHeight() {
        var swiperContainer = $('.swiper-container-vertical .swiper-slide');
        var $containerHeight = swiperContainer.css('height');

        // Workaround for IE Browsers
        if ($('.swiper-container-vertical').hasClass('swiper-wp8-vertical')) {
            $containerHeight = swiperContainer.height() + 5;

            swiperContainer.css('height', $containerHeight);
        }

        if ($containerHeight === '0px') {
            $containerHeight = $('.product-info-thumbnails-mobile').css('height');
        }

        $('.align-middle').css('height', $containerHeight);
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $slides = $this.find('.swiper-slide');
        $controls = $(options.controls);
        $target = $(options.target);
        $theme = $this.find('theme');

        // Check if all images inside the swiper have
        // thumbnail image given
        $slides.each(function () {
            if (!$(this).data().thumbImage) {
                hasThumbnails = false;
                return false;
            }
        });

        // Add the breakpoint handler ty dynamically
        // set the options corresponding to the browser size (slider responsive will re-initialize the swiper).
        _breakpointHandler();

        // If this instance is a "control" swiper the target is the main swiper
        // which will be updated on a click inside this control swiper
        if (options.target) {
            $this.on('click.swiper', '.swiper-slide', _clickHandler);
        }

        $(document).ready(function () {
            $('.swiper-vertical .swiper-slide[data-index]').css('display', 'inline-block');
            $('.product-info-image .swiper-slide[data-index]').css('z-index', 'inherit');
            $('.product-info-image .swiper-slide[data-index] .swiper-slide-inside img.img-responsive').fadeIn(1000);
        });

        _translucenceWorkaround();
        _preventTextSelection();
        _scaleThumbnailHeight();

        // Fix for invisible Thumbnail-Images for switching from Tablet-Portrait to Tablet-Landscape
        $body.on(jse.libs.theme.events.BREAKPOINT(), function () {
            _scaleThumbnailHeight();
        });

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc3dpcGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkc2xpZGVzIiwiJGNvbnRyb2xzIiwiJHRhcmdldCIsIiR0aGVtZSIsImluaXQiLCJzd2lwZXIiLCJzbGlkZXJPcHRpb25zIiwiaGFzVGh1bWJuYWlscyIsIm1vZGUiLCJicmVha3BvaW50RGF0YXNldCIsImR1cGxpY2F0ZXMiLCJwcmV2ZW50U2xpZGVTdGFydCIsInNsaWRlckRlZmF1bHRzIiwib2JzZXJ2ZXIiLCJwYWdpbmF0aW9uIiwibmV4dEJ1dHRvbiIsInByZXZCdXR0b24iLCJwYWdpbmF0aW9uQ2xpY2thYmxlIiwibG9vcCIsImF1dG9wbGF5IiwiYXV0b3BsYXlEaXNhYmxlT25JbnRlcmFjdGlvbiIsImRlZmF1bHRzIiwiY29udHJvbHMiLCJ0YXJnZXQiLCJpbml0U2xpZGUiLCJhdXRvT2ZmIiwiZGlzYWJsZVRyYW5zbHVjZW5jZUZpeCIsImJyZWFrcG9pbnRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9nZW5lcmF0ZVByZXZpZXdCdXR0b25zIiwiaW5kZXgiLCJjbGFzc05hbWUiLCIkY3VycmVudFNsaWRlIiwiZXEiLCIkaW1hZ2UiLCJmaW5kIiwiYWx0VHh0IiwiYXR0ciIsInRodW1iSW1hZ2UiLCJfZ2V0SW5kZXgiLCJfc2V0QWN0aXZlIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsIl9tb3VzZUVudGVySGFuZGxlciIsInN0b3BBdXRvcGxheSIsImUiLCJfbW91c2VMZWF2ZUhhbmRsZXIiLCJzdGFydEF1dG9wbGF5IiwiX2dvdG9IYW5kbGVyIiwiZCIsInN0b3BQcm9wYWdhdGlvbiIsIm9mZiIsImxlbmd0aCIsInNsaWRlVG8iLCJfY2xpY2tIYW5kbGVyIiwicHJldmVudERlZmF1bHQiLCIkc2VsZiIsInRyaWdnZXIiLCJqc2UiLCJsaWJzIiwidGhlbWUiLCJldmVudHMiLCJTV0lQRVJfR09UTyIsIl90cmlnZ2VyU2xpZGVDaGFuZ2UiLCJsYXN0SW5kZXgiLCJjbG9zZXN0IiwicHJldmlvdXNTbGlkZXIiLCJfdHJhbnNsdWNlbmNlV29ya2Fyb3VuZCIsImVmZmVjdCIsImZpbHRlciIsImZhZGVUbyIsImNzcyIsIl9icmVha3BvaW50SGFuZGxlciIsIm9sZE1vZGUiLCJuZXdNb2RlIiwicmVzcG9uc2l2ZSIsImJyZWFrcG9pbnQiLCJleHRlbmRPcHRpb25zIiwibmV3QnJlYWtwb2ludERhdGFzZXQiLCJpZCIsImVhY2giLCJpIiwidiIsInVzZVByZXZpZXdCdWxsZXRzIiwicGFnaW5hdGlvbkJ1bGxldFJlbmRlciIsImluaXRpYWxTbGlkZSIsImRlc3Ryb3kiLCJpZ25vcmUiLCIkZHVwbGljYXRlIiwic2xpZGVzUGVyVmlldyIsIlN3aXBlciIsIm9uIiwiU0xJREVTX1VQREFURSIsIl91cGRhdGVTbGlkZXMiLCJzZXRUaW1lb3V0IiwidXBkYXRlIiwiY2F0ZWdvcnkiLCJkYXRhc2V0IiwiY2F0TmFtZSIsImFkZCIsInJlbW92ZSIsIm1hcmt1cCIsImh0bWwiLCJzd2lwZXJTbGlkZUluZGV4IiwidW5kZWZpbmVkIiwicHVzaCIsInJlbW92ZVNsaWRlIiwic3JjYXR0ciIsInNyYyIsIk11c3RhY2hlIiwicmVuZGVyIiwiYXBwZW5kU2xpZGUiLCJfcHJldmVudFRleHRTZWxlY3Rpb24iLCJfc2NhbGVUaHVtYm5haWxIZWlnaHQiLCJzd2lwZXJDb250YWluZXIiLCIkY29udGFpbmVySGVpZ2h0IiwiaGFzQ2xhc3MiLCJoZWlnaHQiLCJkb25lIiwiZG9jdW1lbnQiLCJyZWFkeSIsImZhZGVJbiIsIkJSRUFLUE9JTlQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7QUFFQTs7Ozs7QUFLQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0ksUUFESixFQUdJLENBQ09GLE9BQU9HLE1BRGQsbUJBRU9ILE9BQU9HLE1BRmQsc0JBSEosRUFRSSxVQUFVQyxJQUFWLEVBQWdC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Q0g7O0FBRUR6RCxVQUFFMEgsUUFBRixFQUFZQyxLQUFaLENBQWtCLFlBQVk7QUFDMUIzSCxjQUFFLDRDQUFGLEVBQWdEMEUsR0FBaEQsQ0FBb0QsU0FBcEQsRUFBK0QsY0FBL0Q7QUFDQTFFLGNBQUUsK0NBQUYsRUFBbUQwRSxHQUFuRCxDQUF1RCxTQUF2RCxFQUFrRSxTQUFsRTtBQUNBMUUsY0FBRSx1RkFBRixFQUEyRjRILE1BQTNGLENBQWtHLElBQWxHO0FBQ0gsU0FKRDs7QUFNQXREO0FBQ0E2QztBQUNBQzs7QUFFQTtBQUNBbkgsY0FBTTZGLEVBQU4sQ0FBU2pDLElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFlQyxNQUFmLENBQXNCNkQsVUFBdEIsRUFBVCxFQUE2QyxZQUFZO0FBQ3JEVDtBQUNILFNBRkQ7O0FBSUFLO0FBQ0gsS0ExQ0Q7O0FBNENBO0FBQ0EsV0FBTzdILE1BQVA7QUFDSCxDQW5pQkwiLCJmaWxlIjoid2lkZ2V0cy9zd2lwZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHN3aXBlci5qcyAyMDIwLTA2LTAyXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNyBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyogZ2xvYmFscyBTd2lwZXIgKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCBiaW5kcyB0aGUgc3dpcGVyIHBsdWdpbiAodGhpcmQgcGFydHkpIHRvIGEgRE9NIGVsZW1lbnRcbiAqXG4gKiBAdG9kbyBSZW1vdmUgdGhlIHRyeSAtIGNhdGNoIGJsb2NrcyBhbmQgYW5kIGNvcnJlY3QgdGhlIHN3aXBlciBpc3N1ZXMuXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcbiAgICAnc3dpcGVyJyxcblxuICAgIFtcbiAgICAgICAgYCR7Z2FtYmlvLnNvdXJjZX0vbGlicy9ldmVudHNgLFxuICAgICAgICBgJHtnYW1iaW8uc291cmNlfS9saWJzL3Jlc3BvbnNpdmVgXG4gICAgXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgJGJvZHkgPSAkKCdib2R5JyksXG4gICAgICAgICAgICAkc2xpZGVzID0gbnVsbCxcbiAgICAgICAgICAgICRjb250cm9scyA9IG51bGwsXG4gICAgICAgICAgICAkdGFyZ2V0ID0gbnVsbCxcbiAgICAgICAgICAgICR0aGVtZSA9IG51bGwsXG4gICAgICAgICAgICBpbml0ID0gdHJ1ZSxcbiAgICAgICAgICAgIHN3aXBlciA9IG51bGwsXG4gICAgICAgICAgICBzbGlkZXJPcHRpb25zID0gbnVsbCxcbiAgICAgICAgICAgIGhhc1RodW1ibmFpbHMgPSB0cnVlLFxuICAgICAgICAgICAgbW9kZSA9IG51bGwsXG4gICAgICAgICAgICBicmVha3BvaW50RGF0YXNldCA9IG51bGwsXG4gICAgICAgICAgICBkdXBsaWNhdGVzID0gZmFsc2UsXG4gICAgICAgICAgICBwcmV2ZW50U2xpZGVTdGFydCA9IGZhbHNlLFxuICAgICAgICAgICAgc2xpZGVyRGVmYXVsdHMgPSB7XG4gICAgICAgICAgICAgICAgb2JzZXJ2ZXI6IHRydWUsXG4gICAgICAgICAgICAgICAgLy8gRGVmYXVsdCBjb25maWd1cmF0aW9uIGZvciB0aGUgc3dpcGVyXG4gICAgICAgICAgICAgICAgcGFnaW5hdGlvbjogJy5zd2lwZXItcGFnaW5hdGlvbicsXG4gICAgICAgICAgICAgICAgbmV4dEJ1dHRvbjogJy5zd2lwZXItYnV0dG9uLW5leHQnLFxuICAgICAgICAgICAgICAgIHByZXZCdXR0b246ICcuc3dpcGVyLWJ1dHRvbi1wcmV2JyxcbiAgICAgICAgICAgICAgICBwYWdpbmF0aW9uQ2xpY2thYmxlOiB0cnVlLFxuICAgICAgICAgICAgICAgIGxvb3A6IHRydWUsXG4gICAgICAgICAgICAgICAgYXV0b3BsYXk6IDMsXG4gICAgICAgICAgICAgICAgYXV0b3BsYXlEaXNhYmxlT25JbnRlcmFjdGlvbjogZmFsc2VcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHtcbiAgICAgICAgICAgICAgICBvYnNlcnZlcjogdHJ1ZSxcbiAgICAgICAgICAgICAgICAvLyBKU09OIHRoYXQgZ2V0cyBtZXJnZWQgd2l0aCB0aGUgc2xpZGVyRGVmYXVsdHMgYW5kIGlzIHBhc3NlZCB0byBcInN3aXBlclwiIGRpcmVjdGx5LlxuICAgICAgICAgICAgICAgIHNsaWRlck9wdGlvbnM6IG51bGwsXG4gICAgICAgICAgICAgICAgLy8gSWYgdGhpcyBpbnN0YW5jZSBpcyBhIFwibWFpblwiIHN3aXBlciwgdGhlIGdpdmVuIHNlbGVjdG9yIHNlbGVjdHMgdGhlIFwiY29udHJvbFwiIHN3aXBlci5cbiAgICAgICAgICAgICAgICBjb250cm9sczogbnVsbCxcbiAgICAgICAgICAgICAgICAvLyBJZiB0aGlzIGluc3RhbmNlIGlzIGEgXCJjb250cm9sXCIgc3dpcGVyLCB0aGUgZ2l2ZW4gc2VsZWN0b3Igc2VsZWN0cyB0aGUgXCJtYWluXCIgc3dpcGVyLlxuICAgICAgICAgICAgICAgIHRhcmdldDogbnVsbCxcbiAgICAgICAgICAgICAgICAvLyBTZXRzIHRoZSBpbml0aWFsIHNsaWRlIChuZWVkZWQgdG8gcHJldmVudCBkaWZmZXJlbnQgaW5pdCBzbGlkZXMgaW4gbWFpbi9jb250cm9sbGVyIHNsaWRlcikuXG4gICAgICAgICAgICAgICAgaW5pdFNsaWRlOiBudWxsLFxuICAgICAgICAgICAgICAgIC8vIERldGVjdCBpZiBhIHN3aXBlciBpcyBuZWVkZWQgZm9yIHRoZSBicmVha3BvaW50LiBJZiBub3QsIHR1cm4gaXQgb2ZmXG4gICAgICAgICAgICAgICAgYXV0b09mZjogZmFsc2UsXG4gICAgICAgICAgICAgICAgLy8gVGhlIHRyYW5zbHVjZW5jZSBmaXggZW5hYmxlcyBzdXBwb3J0IGZvciBhIGZhZGUgZWZmZWN0IGJldHdlZW4gaW1hZ2VzIHdpdGggZGlmZmVyZW50IGFzcGVjdCByYXRpbyxcbiAgICAgICAgICAgICAgICAvLyBidXQgY2F1c2luZyBhIGRlbGF5IGJldHdlZW4gdGhlIGNoYW5nZVxuICAgICAgICAgICAgICAgIGRpc2FibGVUcmFuc2x1Y2VuY2VGaXg6IGZhbHNlLFxuICAgICAgICAgICAgICAgIGJyZWFrcG9pbnRzOiBbXVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogRnVuY3Rpb24gdGhhdCBnZW5lcmF0ZXMgdGhlIG1hcmt1cCBmb3JcbiAgICAgICAgICogdGhlIHByZXZpZXcgYnVsbGV0c1xuICAgICAgICAgKiBAcGFyYW0gICAgICAge1N3aXBlcn0gICAgICAgIHN3aXBlciAgICAgICAgICBTd2lwZXIgb2JqZWN0XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7aW50ZWdlcn0gICAgICAgaW5kZXggICAgICAgICAgIEluZGV4IG9mIHRoZSBzbGlkZVxuICAgICAgICAgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgICAgIGNsYXNzTmFtZSAgICAgICBUaGUgY2xhc3NuYW1lIHRoYXQgbXVzdCBiZSBhZGQgdG8gdGhlIG1hcmt1cFxuICAgICAgICAgKiBAcmV0dXJuICAgICAge3N0cmluZ30gICAgICAgICAgICAgICAgICAgICAgICBUaGUgcHJldmlldyBpbWFnZSBodG1sIHN0cmluZ1xuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9nZW5lcmF0ZVByZXZpZXdCdXR0b25zID0gZnVuY3Rpb24gKHN3aXBlciwgaW5kZXgsIGNsYXNzTmFtZSkge1xuICAgICAgICAgICAgdmFyICRjdXJyZW50U2xpZGUgPSAkc2xpZGVzLmVxKGluZGV4KSxcbiAgICAgICAgICAgICAgICAkaW1hZ2UgPSAkY3VycmVudFNsaWRlLmZpbmQoJ2ltZycpLFxuICAgICAgICAgICAgICAgIGFsdFR4dCA9ICRpbWFnZS5hdHRyKCdhbHQnKSxcbiAgICAgICAgICAgICAgICB0aHVtYkltYWdlID0gJGN1cnJlbnRTbGlkZS5kYXRhKCd0aHVtYkltYWdlJyk7XG5cbiAgICAgICAgICAgIGlmICh0aHVtYkltYWdlKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuICc8aW1nIHNyYz1cIicgKyB0aHVtYkltYWdlICsgJ1wiIGFsdD1cIicgKyBhbHRUeHQgKyAnXCIgY2xhc3M9XCInICsgY2xhc3NOYW1lICsgJ1wiIC8+JztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuICcnO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIZWxwZXIgZnVuY3Rpb24gdG8gZ2V0IHRoZSBpbmRleCBvZiB0aGVcbiAgICAgICAgICogYWN0aXZlIHNsaWRlXG4gICAgICAgICAqIEByZXR1cm4gICAgIHtpbnRlZ2VyfSAgICAgICAgICAgICAgICAgICAgICAgVGhlIGluZGV4IG9mIHRoZSBhY3RpdmUgc2xpZGVcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfZ2V0SW5kZXggPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICByZXR1cm4gJHRoaXNcbiAgICAgICAgICAgICAgICAuZmluZCgnLnN3aXBlci1zbGlkZS1hY3RpdmUnKVxuICAgICAgICAgICAgICAgIC5kYXRhKFwic3dpcGVyLXNsaWRlLWluZGV4XCIpIHx8IDA7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0byBhZGQgdGhlIGFjdGl2ZVxuICAgICAgICAgKiBjbGFzcyB0byB0aGUgYWN0aXZlIHNsaWRlXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7aW50ZWdlcn0gICAgICAgICAgIGluZGV4ICAgICAgIFRoZSBpbmRleCBvZiB0aGUgYWN0aXZlIHNsaWRlXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3NldEFjdGl2ZSA9IGZ1bmN0aW9uIChpbmRleCkge1xuICAgICAgICAgICAgJHNsaWRlcyA9ICR0aGlzLmZpbmQoJy5zd2lwZXItc2xpZGU6bm90KC5zd2lwZXItc2xpZGUtZHVwbGljYXRlKScpO1xuICAgICAgICAgICAgaW5kZXggPSBkdXBsaWNhdGVzID8gaW5kZXggKyAxIDogaW5kZXg7XG4gICAgICAgICAgICAkc2xpZGVzXG4gICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdhY3RpdmUnKVxuICAgICAgICAgICAgICAgIC5lcShpbmRleClcbiAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBtb3VzZWVudGVyIGV2ZW50LlxuICAgICAgICAgKiBJdCBkaXNhYmxlcyB0aGUgYXV0b3BsYXlcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfbW91c2VFbnRlckhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgIGlmIChzd2lwZXIpIHtcbiAgICAgICAgICAgICAgICAgICAgc3dpcGVyLnN0b3BBdXRvcGxheSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0gY2F0Y2ggKGUpIHtcbiAgICAgICAgICAgICAgICAvLyBEbyBub3QgbG9nIHRoZSBlcnJvclxuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIGZvciB0aGUgbW91c2VsZWF2ZSBldmVudC5cbiAgICAgICAgICogSXQgZW5hYmxlcyB0aGUgYXV0b3BsYXlcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfbW91c2VMZWF2ZUhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgIGlmIChzd2lwZXIpIHtcbiAgICAgICAgICAgICAgICAgICAgc3dpcGVyLnN0YXJ0QXV0b3BsYXkoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9IGNhdGNoIChlKSB7XG4gICAgICAgICAgICAgICAgLy8gRG8gbm90IGxvZyB0aGUgZXJyb3JcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIGdvdG8gZXZlbnQuXG4gICAgICAgICAqIEl0IHN3aXRjaGVzIHRoZSBjdXJyZW50IHNsaWRlIHRvIHRoZSBnaXZlbiBpbmRleFxuICAgICAgICAgKiBhbmQgYWRkcyB0aGUgYWN0aXZlIGNsYXNzIHRvIHRoZSBuZXcgYWN0aXZlIHNsaWRlXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtudW1iZXJ9ICAgIGQgICAgICAgSW5kZXggb2YgdGhlIHNsaWRlIHRvIHNob3dcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfZ290b0hhbmRsZXIgPSBmdW5jdGlvbiAoZSwgZCkge1xuICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcblxuICAgICAgICAgICAgLy8gU2V0IHRoZSBhY3RpdmUgc2xpZGVcbiAgICAgICAgICAgIF9zZXRBY3RpdmUoZCk7XG5cbiAgICAgICAgICAgIC8vIFRlbXBvcmFyeSBkZWFjdGl2YXRlIHRoZSBvblNsaWRlQ2hhbmdlU3RhcnQgZXZlbnRcbiAgICAgICAgICAgIC8vIHRvIHByZXZlbnQgbG9vcGluZyB0aHJvdWdoIHRoZSBnb3RvIC8gY2hhbmdlU3RhcnRcbiAgICAgICAgICAgIC8vIGV2ZW50c1xuICAgICAgICAgICAgcHJldmVudFNsaWRlU3RhcnQgPSB0cnVlO1xuXG4gICAgICAgICAgICAvLyBSZW1vdmUgdGhlIGF1dG9wbGF5IGFmdGVyIGEgZ290byBldmVudFxuICAgICAgICAgICAgJHRoaXMub2ZmKCdtb3VzZWxlYXZlLnN3aXBlcicpO1xuICAgICAgICAgICAgc3dpcGVyLnN0b3BBdXRvcGxheSgpO1xuXG4gICAgICAgICAgICAvLyBUcnkgdG8gY29ycmVjdCB0aGUgaW5kZXggYmV0d2VlbiBzbGlkZXJzXG4gICAgICAgICAgICAvLyB3aXRoIGFuZCB3aXRob3V0IGR1cGxpY2F0ZXNcbiAgICAgICAgICAgIHZhciBpbmRleCA9IGR1cGxpY2F0ZXMgPyBkICsgMSA6IGQ7XG4gICAgICAgICAgICBpZiAoaW5kZXggPiAkc2xpZGVzLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgIGluZGV4ID0gMDtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gR290byB0aGUgZGVzaXJlZCBzbGlkZVxuICAgICAgICAgICAgc3dpcGVyLnNsaWRlVG8oaW5kZXgpO1xuXG4gICAgICAgICAgICAvLyBSZWFjdGl2YXRlIHRoZSBvblNsaWRlQ2hhbmdlRXZlbnRcbiAgICAgICAgICAgIHByZXZlbnRTbGlkZVN0YXJ0ID0gZmFsc2U7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIENsaWNrIGV2ZW50IGhhbmRsZXIgdGhhdCB0cmlnZ2VycyBhXG4gICAgICAgICAqIFwiZ290b1wiIGV2ZW50IHRvIHRoZSB0YXJnZXQgc3dpcGVyXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2NsaWNrSGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICBlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICB2YXIgJHNlbGYgPSAkKHRoaXMpLFxuICAgICAgICAgICAgICAgIGluZGV4ID0gJHNlbGYuaW5kZXgoKTtcblxuICAgICAgICAgICAgaW5kZXggPSBkdXBsaWNhdGVzID8gaW5kZXggLSAxIDogaW5kZXg7XG5cbiAgICAgICAgICAgIC8vIFNldCB0aGUgYWN0aXZlIHNsaWRlXG4gICAgICAgICAgICBfc2V0QWN0aXZlKGluZGV4KTtcblxuICAgICAgICAgICAgLy8gSW5mb3JtIHRoZSBtYWluIHN3aXBlclxuICAgICAgICAgICAgJHRhcmdldC50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5TV0lQRVJfR09UTygpLCBpbmRleCk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEV2ZW50IHRoYXQgZ2V0cyB0cmlnZ2VyZWQgb24gc2xpZGVDaGFuZ2UuXG4gICAgICAgICAqIElmIHRoZSBzbGlkZSBnZXRzIGNoYW5nZWQsIHRoZSBjb250cm9sc1xuICAgICAgICAgKiB3aWxsIGZvbGxvdyB0aGUgY3VycmVudCBzbGlkZSBpbiBwb3NpdGlvblxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF90cmlnZ2VyU2xpZGVDaGFuZ2UgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBpZiAoIXByZXZlbnRTbGlkZVN0YXJ0KSB7XG4gICAgICAgICAgICAgICAgdmFyIGluZGV4ID0gX2dldEluZGV4KCksXG4gICAgICAgICAgICAgICAgICAgIGxhc3RJbmRleCA9ICRzbGlkZXMuY2xvc2VzdChcIi5zd2lwZXItc2xpZGUtcHJldlwiKS5pbmRleCgpIHx8ICRzbGlkZXMubGVuZ3RoIC0gMjtcblxuICAgICAgICAgICAgICAgIHZhciBwcmV2aW91c1NsaWRlciA9ICRzbGlkZXMuY2xvc2VzdChcIi5zd2lwZXItc2xpZGUtcHJldlwiKTtcbiAgICAgICAgICAgICAgICBpZihwcmV2aW91c1NsaWRlcikge1xuICAgICAgICAgICAgICAgICAgICBsYXN0SW5kZXggPSAoZHVwbGljYXRlcykgPyBwcmV2aW91c1NsaWRlci5pbmRleCgpIC0gMSA6IHByZXZpb3VzU2xpZGVyLmluZGV4KCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYoaW5kZXggPiAoJHNsaWRlcy5sZW5ndGggLTEgKSkge1xuICAgICAgICAgICAgICAgICAgICBpbmRleCA9IDA7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy8gUmVjYWxjdWxhdGUgaW5kZXggaWYgZHVwbGljYXRlIHNsaWRlcyBhcmUgaW5zaWRlIHRoZSBzbGlkZXJcbiAgICAgICAgICAgICAgICBpZiAoaW5kZXggPCAwKSB7XG4gICAgICAgICAgICAgICAgICAgIGluZGV4ID0gJHNsaWRlcy5sZW5ndGggKyBpbmRleDtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBpbmRleCA9IChkdXBsaWNhdGVzICYmIGluZGV4ID09PSBsYXN0SW5kZXgpID8gaW5kZXggLSBsYXN0SW5kZXggOiBpbmRleDtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvLyBTZXQgdGhlIGFjdGl2ZSBzbGlkZVxuICAgICAgICAgICAgICAgIF9zZXRBY3RpdmUoaW5kZXgpO1xuXG4gICAgICAgICAgICAgICAgLy8gSW5mb3JtIHRoZSBjb250cm9sc1xuICAgICAgICAgICAgICAgICRjb250cm9scy50cmlnZ2VyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5TV0lQRVJfR09UTygpLCBpbmRleCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cblxuICAgICAgICAvKipcbiAgICAgICAgICogV29ya2Fyb3VuZCBmb3IgdGhlIHRyYW5zbHVjZW5jZSBpc3N1ZVxuICAgICAgICAgKiB0aGF0IGhhcHBlbnMgb24gc21hbGwgc2NyZWVucyB3aXRoIGVuYWJsZWRcbiAgICAgICAgICogZmFkZSBlZmZlY3QuIE1heWJlIGl0IGNhbiBiZSByZW1vdmVkLCBpZiB0aGVcbiAgICAgICAgICogc3dpcGVyIGdldHMgdXBkYXRlZCBpdHNlbGZcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfdHJhbnNsdWNlbmNlV29ya2Fyb3VuZCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGlmICghb3B0aW9ucy5kaXNhYmxlVHJhbnNsdWNlbmNlRml4ICYmIHNsaWRlck9wdGlvbnMgJiYgc2xpZGVyT3B0aW9ucy5lZmZlY3QgPT09ICdmYWRlJykge1xuICAgICAgICAgICAgICAgICR0aGlzLmZpbmQoJy5zd2lwZXItc2xpZGUnKVxuICAgICAgICAgICAgICAgICAgICAuZmlsdGVyKCc6bm90KC5zd2lwZXItc2xpZGUtYWN0aXZlKScpXG4gICAgICAgICAgICAgICAgICAgIC5mYWRlVG8oMzAwLCAwLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNzcygndmlzaWJpbGl0eScsICdoaWRkZW4nKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlJylcbiAgICAgICAgICAgICAgICAgICAgLmZpbHRlcignLnN3aXBlci1zbGlkZS1hY3RpdmUnKVxuICAgICAgICAgICAgICAgICAgICAuZmFkZVRvKDMwMCwgMSwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ3Zpc2liaWxpdHknLCAnJyk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBUaGUgYnJlYWtwb2ludCBoYW5kbGVyIGluaXRpYWxpemVzIHRoZSBzd2lwZXJcbiAgICAgICAgICogd2l0aCB0aGUgc2V0dGluZ3MgZm9yIHRoZSBjdXJyZW50IGJyZWFrcG9pbnQuXG4gICAgICAgICAqIFRoZXJlZm9yZSBpdCB1c2VzIHRoZSBkZWZhdWx0IHNsaWRlciBvcHRpb25zLFxuICAgICAgICAgKiB0aGUgY3VzdG9tIHNsaWRlciBvcHRpb25zIGdpdmVuIGJ5IHRoZSBvcHRpb25zXG4gICAgICAgICAqIG9iamVjdCBhbmQgdGhlIGJyZWFrcG9pbnQgb3B0aW9ucyBvYmplY3QgYWxzb1xuICAgICAgICAgKiBnaXZlbiBieSB0aGUgb3B0aW9ucyAoaW4gdGhpcyBvcmRlcilcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfYnJlYWtwb2ludEhhbmRsZXIgPSBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgIC8vIEdldCB0aGUgY3VycmVudCB2aWV3bW9kZVxuICAgICAgICAgICAgdmFyIG9sZE1vZGUgPSBtb2RlIHx8IHt9LFxuICAgICAgICAgICAgICAgIG5ld01vZGUgPSBqc2UubGlicy50aGVtZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKSxcbiAgICAgICAgICAgICAgICBleHRlbmRPcHRpb25zID0gb3B0aW9ucy5icmVha3BvaW50c1swXSB8fCB7fSxcbiAgICAgICAgICAgICAgICBuZXdCcmVha3BvaW50RGF0YXNldCA9IG51bGw7XG5cbiAgICAgICAgICAgIC8vIE9ubHkgZG8gc29tZXRoaW5nIGlmIHRoZSB2aWV3IHdhcyBjaGFuZ2VkXG4gICAgICAgICAgICBpZiAobmV3TW9kZS5pZCAhPT0gb2xkTW9kZS5pZCkge1xuXG4gICAgICAgICAgICAgICAgLy8gU3RvcmUgdGhlIG5ldyB2aWV3bW9kZVxuICAgICAgICAgICAgICAgIG1vZGUgPSAkLmV4dGVuZCh7fSwgbmV3TW9kZSk7XG5cbiAgICAgICAgICAgICAgICAvLyBJdGVyYXRlIHRocm91Z2ggdGhlIGJyZWFrcG9pbnRzIG9iamVjdCB0byBkZXRlY3RcbiAgICAgICAgICAgICAgICAvLyB0aGUgY29ycmVjdCBzZXR0aW5ncyBmb3IgdGhlIGN1cnJlbnQgYnJlYWtwb2ludFxuICAgICAgICAgICAgICAgICQuZWFjaChvcHRpb25zLmJyZWFrcG9pbnRzLCBmdW5jdGlvbiAoaSwgdikge1xuICAgICAgICAgICAgICAgICAgICBpZiAodi5icmVha3BvaW50ID4gbmV3TW9kZS5pZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIG5ld0JyZWFrcG9pbnREYXRhc2V0ID0gaTtcbiAgICAgICAgICAgICAgICAgICAgZXh0ZW5kT3B0aW9ucyA9IHY7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICBpZiAob3B0aW9ucy5zbGlkZXJPcHRpb25zICYmIG9wdGlvbnMuc2xpZGVyT3B0aW9ucy5icmVha3BvaW50cykge1xuICAgICAgICAgICAgICAgICAgICAkLmVhY2gob3B0aW9ucy5zbGlkZXJPcHRpb25zLmJyZWFrcG9pbnRzLCBmdW5jdGlvbiAoaSwgdikge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHYuYnJlYWtwb2ludCA9PT0gbmV3TW9kZS5pZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGV4dGVuZE9wdGlvbnMgPSB2O1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy8gT25seSBkbyBzb21ldGhpbmcgaWYgdGhlIHNldHRpbmdzIGNoYW5nZSBkdWUgYnJvd3NlclxuICAgICAgICAgICAgICAgIC8vIHJlc2l6ZSBvciBpZiBpdCdzIHRoZSBmaXJzdCB0aW1lIHJ1blxuICAgICAgICAgICAgICAgIGlmIChuZXdCcmVha3BvaW50RGF0YXNldCAhPT0gYnJlYWtwb2ludERhdGFzZXQgfHwgaW5pdCkge1xuICAgICAgICAgICAgICAgICAgICAvLyBDb21iaW5lIHRoZSBzZXR0aW5nc1xuICAgICAgICAgICAgICAgICAgICBzbGlkZXJPcHRpb25zID0gJC5leHRlbmQoe30sIHNsaWRlckRlZmF1bHRzLCBvcHRpb25zLnNsaWRlck9wdGlvbnMgfHwge30sIGV4dGVuZE9wdGlvbnMpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vIEFkZCB0aGUgcHJldmlldyBpbWFnZSBidWxsZXRzIGZ1bmN0aW9uIHRvIHRoZSBvcHRpb25zIG9iamVjdFxuICAgICAgICAgICAgICAgICAgICBpZiAoc2xpZGVyT3B0aW9ucy51c2VQcmV2aWV3QnVsbGV0cyAmJiBoYXNUaHVtYm5haWxzKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzbGlkZXJPcHRpb25zLnBhZ2luYXRpb25CdWxsZXRSZW5kZXIgPSBfZ2VuZXJhdGVQcmV2aWV3QnV0dG9ucztcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vIEFkZCB0aGUgYXV0b3BsYXkgaW50ZXJ2YWwgdG8gdGhlIG9wdGlvbnMgb2JqZWN0XG4gICAgICAgICAgICAgICAgICAgIHNsaWRlck9wdGlvbnMuYXV0b3BsYXkgPSAoc2xpZGVyT3B0aW9ucy5hdXRvcGxheSkgPyAoc2xpZGVyT3B0aW9ucy5hdXRvcGxheSAqIDEwMDApIDogMDtcblxuICAgICAgICAgICAgICAgICAgICAvLyBEaXNhYmxlIGxvb3AgaWYgdGhlcmUgaXMgb25seSBvbmUgc2xpZGVyLiBcbiAgICAgICAgICAgICAgICAgICAgaWYgKCR0aGlzLmZpbmQoJy5zd2lwZXItc2xpZGUnKS5sZW5ndGggPT09IDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNsaWRlck9wdGlvbnMubG9vcCA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gSWYgYW4gc3dpcGVyIGV4aXN0cywgZ2V0IHRoZSBjdXJyZW50XG4gICAgICAgICAgICAgICAgICAgIC8vIHNsaWRlIG5vLiBhbmQgcmVtb3ZlIHRoZSBvbGQgc3dpcGVyXG4gICAgICAgICAgICAgICAgICAgIGlmIChzd2lwZXIpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNsaWRlck9wdGlvbnMuaW5pdGlhbFNsaWRlID0gX2dldEluZGV4KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN3aXBlci5kZXN0cm95KHRydWUsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBjYXRjaCAoaWdub3JlKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc3dpcGVyID0gbnVsbDtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgc2xpZGVyT3B0aW9ucy5pbml0aWFsU2xpZGUgPSBvcHRpb25zLmluaXRTbGlkZSB8fCBzbGlkZXJPcHRpb25zLmluaXRpYWxTbGlkZSB8fCAwO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICRkdXBsaWNhdGUgPSAkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlOm5vdCguc3dpcGVyLXNsaWRlLWR1cGxpY2F0ZSknKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoIW9wdGlvbnMuYXV0b09mZiB8fCAoJGR1cGxpY2F0ZS5sZW5ndGggPiBzbGlkZXJPcHRpb25zLnNsaWRlc1BlclZpZXcgJiYgb3B0aW9ucy5hdXRvT2ZmKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ3N3aXBlci1pcy1hY3RpdmUnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnc3dpcGVyLWlzLW5vdC1hY3RpdmUnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gSW5pdGlhbGl6ZSB0aGUgc3dpcGVyXG4gICAgICAgICAgICAgICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN3aXBlciA9IG5ldyBTd2lwZXIoJHRoaXMsIHNsaWRlck9wdGlvbnMpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSBjYXRjaCAoZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybjsgLy8gU3dpcGVyIG1pZ2h0IHRocm93IGFuIGVycm9yIHVwb24gaW5pdGlhbGl6YXRpb24gdGhhdCBzaG91bGQgbm90IGhhbHQgdGhlIHNjcmlwdCBleGVjdXRpb24uXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHN3aXBlclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5vZmYoJ29uVHJhbnNpdGlvbkVuZCBvblNsaWRlQ2hhbmdlU3RhcnQnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5vbignb25UcmFuc2l0aW9uRW5kJywgX3RyYW5zbHVjZW5jZVdvcmthcm91bmQpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBJZiB0aGlzIGlzIGEgXCJtYWluXCIgc3dpcGVyIGFuZCBoYXMgZXh0ZXJuYWwgY29udHJvbHMsIGFuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBnb3RvIGV2ZW50IGlzIHRyaWdnZXJlZCBpZiB0aGUgY3VycmVudCBzbGlkZSBpcyBjaGFuZ2VkXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoJGNvbnRyb2xzLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN3aXBlci5vbignb25TbGlkZUNoYW5nZVN0YXJ0JywgX3RyaWdnZXJTbGlkZUNoYW5nZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIEFkZCB0aGUgZXZlbnQgaGFuZGxlclxuICAgICAgICAgICAgICAgICAgICAgICAgJHRoaXNcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAub2ZmKCdtb3VzZWVudGVyLnN3aXBlciBtb3VzZWxlYXZlLnN3aXBlciAnICsganNlLmxpYnMudGhlbWUuZXZlbnRzLlNXSVBFUl9HT1RPKCkgKyAnICdcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKyBqc2UubGlicy50aGVtZS5ldmVudHMuU0xJREVTX1VQREFURSgpKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5vbignbW91c2VlbnRlci5zd2lwZXInLCBfbW91c2VFbnRlckhhbmRsZXIpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLm9uKCdtb3VzZWxlYXZlLnN3aXBlcicsIF9tb3VzZUxlYXZlSGFuZGxlcilcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAub24oanNlLmxpYnMudGhlbWUuZXZlbnRzLlNXSVBFUl9HT1RPKCksIF9nb3RvSGFuZGxlcilcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAub24oanNlLmxpYnMudGhlbWUuZXZlbnRzLlNMSURFU19VUERBVEUoKSwgX3VwZGF0ZVNsaWRlcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChpbml0KSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLy8gQ2hlY2sgaWYgdGhlcmUgYXJlIGR1cGxpY2F0ZXMgc2xpZGVzIChnZW5lcmF0ZWQgYnkgdGhlIHN3aXBlcilcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBhZnRlciB0aGUgZmlyc3QgdGltZSBpbml0IG9mIHRoZSBzd2lwZXJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkdXBsaWNhdGVzID0gISEkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlLWR1cGxpY2F0ZScpLmxlbmd0aDtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gU2V0IHRoZSBhY3RpdmUgc2xpZGVcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBpbmRleCA9IChpbml0ICYmIG9wdGlvbnMuaW5pdFNsaWRlKSA/IG9wdGlvbnMuaW5pdFNsaWRlIDogX2dldEluZGV4KCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBfc2V0QWN0aXZlKGluZGV4KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gSW5mb3JtIHRoZSBjb250cm9scyB0aGF0IHRoZSBtYWluIHN3aXBlciBoYXMgY2hhbmdlZFxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gSW4gY2FzZSB0aGF0IHRoZSBvdGhlciBzbGlkZXIgaXNuJ3QgaW5pdGlhbGl6ZWQgeWV0LFxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gc2V0IGFuIGRhdGEgYXR0cmlidXRlIHRvIHRoZSBtYXJrdXAgZWxlbWVudCB0byBpbmZvcm1cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIGl0IG9uIGluaXRcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICgkY29udHJvbHMubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGNvbnRyb2xzLmF0dHIoJ2RhdGEtc3dpcGVyLWluaXQtc2xpZGUnLCBpbmRleCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgX3RyaWdnZXJTbGlkZUNoYW5nZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBVbnNldCB0aGUgaW5pdCBmbGFnXG4gICAgICAgICAgICAgICAgICAgICAgICBpbml0ID0gZmFsc2U7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoKCkgPT4gc3dpcGVyLnVwZGF0ZSgpKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIERpc2FibGUgdGhlIHN3aXBlciBidXR0b25zXG4gICAgICAgICAgICAgICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnc3dpcGVyLWlzLWFjdGl2ZScpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKCdzd2lwZXItaXMtbm90LWFjdGl2ZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaW5pdCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIHRoYXQgYWRkcyAmIHJlbW92ZXMgc2xpZGVzIGZyb20gdGhlXG4gICAgICAgICAqIHN3aXBlci4gQWZ0ZXIgdGhlIHNsaWRlcyB3ZXJlIHByb2Nlc3NlZCwgdGhlIGZpcnN0XG4gICAgICAgICAqIHNsaWRlIGlzIHNob3duXG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3RcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGQgICAgICAgSlNPTiBvYmplY3QgdGhhdCBjb250YWlucyB0aGUgY2F0ZWdvcmllcyAvIGltYWdlc1xuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF91cGRhdGVTbGlkZXMgPSBmdW5jdGlvbiAoZSwgZCkge1xuXG4gICAgICAgICAgICAvLyBMb29wcyB0aHJvdWdoIGVhY2ggY2F0ZWdvcnkgaW5zaWRlIHRoZSBpbWFnZXMgYXJyYXlcbiAgICAgICAgICAgICQuZWFjaChkLCBmdW5jdGlvbiAoY2F0ZWdvcnksIGRhdGFzZXQpIHtcbiAgICAgICAgICAgICAgICB2YXIgY2F0TmFtZSA9IGNhdGVnb3J5ICsgJy1jYXRlZ29yeScsXG4gICAgICAgICAgICAgICAgICAgIGFkZCA9IFtdLFxuICAgICAgICAgICAgICAgICAgICByZW1vdmUgPSBbXSxcbiAgICAgICAgICAgICAgICAgICAgbWFya3VwID0gJHRoZW1lLmh0bWwoKTtcblxuICAgICAgICAgICAgICAgIC8vIEdldCBhbGwgaW5kZXhlcyBmcm9tIHRoZSBzbGlkZXNcbiAgICAgICAgICAgICAgICAvLyBvZiB0aGUgc2FtZSBjYXRlZ29yeSBhbmQgcmVtb3ZlXG4gICAgICAgICAgICAgICAgLy8gdGhlbSBmcm9tIHRoZSBzbGlkZXJcbiAgICAgICAgICAgICAgICAkc2xpZGVzXG4gICAgICAgICAgICAgICAgICAgIC5maWx0ZXIoJy4nICsgY2F0TmFtZSlcbiAgICAgICAgICAgICAgICAgICAgLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpbmRleCA9ICRzZWxmLmRhdGEoKS5zd2lwZXJTbGlkZUluZGV4O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpbmRleCA9IGluZGV4ID09PSB1bmRlZmluZWQgPyAkc2VsZi5pbmRleCgpIDogaW5kZXg7XG4gICAgICAgICAgICAgICAgICAgICAgICByZW1vdmUucHVzaChpbmRleCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIHN3aXBlci5yZW1vdmVTbGlkZShyZW1vdmUpO1xuXG4gICAgICAgICAgICAgICAgLy8gR2VuZXJhdGUgdGhlIG1hcmt1cCBmb3IgdGhlIG5ldyBzbGlkZXNcbiAgICAgICAgICAgICAgICAvLyBhbmQgYWRkIHRoZW0gdG8gdGhlIHNsaWRlclxuICAgICAgICAgICAgICAgICQuZWFjaChkYXRhc2V0IHx8IFtdLCBmdW5jdGlvbiAoaSwgdikge1xuICAgICAgICAgICAgICAgICAgICB2LmNsYXNzTmFtZSA9IGNhdE5hbWU7XG4gICAgICAgICAgICAgICAgICAgIHYuc3JjYXR0ciA9ICdzcmM9XCInICsgdi5zcmMgKyAnXCInO1xuICAgICAgICAgICAgICAgICAgICBhZGQucHVzaChNdXN0YWNoZS5yZW5kZXIobWFya3VwLCB2KSk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgc3dpcGVyLmFwcGVuZFNsaWRlKGFkZCk7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkc2xpZGVzID0gJHRoaXMuZmluZCgnLnN3aXBlci1zbGlkZScpO1xuXG4gICAgICAgICAgICAvLyBUbyBwcmV2ZW50IGFuIGluY29uc2lzdGVudCBzdGF0ZVxuICAgICAgICAgICAgLy8gaW4gY29udHJvbCAvIG1haW4gc2xpZGVyIGNvbWJpbmF0aW9uc1xuICAgICAgICAgICAgLy8gc2xpZGUgdG8gdGhlIGZpcnN0IHNsaWRlXG4gICAgICAgICAgICBfc2V0QWN0aXZlKDApO1xuICAgICAgICAgICAgdmFyIGluZGV4ID0gZHVwbGljYXRlcyA/IDEgOiAwO1xuICAgICAgICAgICAgc3dpcGVyLnNsaWRlVG8oaW5kZXgsIDApO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFByZXZlbnQgdGV4dCBzZWxlY3Rpb24gYnkgY2xpY2tpbmcgb24gc3dpcGVyIGJ1dHRvbnNcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfcHJldmVudFRleHRTZWxlY3Rpb24gPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAkKG9wdGlvbnMuc2xpZGVyT3B0aW9ucy5uZXh0QnV0dG9uKS5vbignc2VsZWN0c3RhcnQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAkKG9wdGlvbnMuc2xpZGVyT3B0aW9ucy5wcmV2QnV0dG9uKS5vbignc2VsZWN0c3RhcnQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFNldHMgdGhlIGluaXRpYWwgaGVpZ2h0IGZvciBvbmUgc3dpcGVyIGltYWdlIGNvbnRhaW5lciB0byBwcmV2ZW50IGN1dCBvZmYgaW1hZ2VzIG9uIHNtYWxsZXIgc3dpcGVyIGhlaWdodHNcbiAgICAgICAgICogQHByaXZhdGVcbiAgICAgICAgICovXG4gICAgICAgIHZhciBfc2NhbGVUaHVtYm5haWxIZWlnaHQgPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgc3dpcGVyQ29udGFpbmVyID0gJCgnLnN3aXBlci1jb250YWluZXItdmVydGljYWwgLnN3aXBlci1zbGlkZScpO1xuICAgICAgICAgICAgdmFyICRjb250YWluZXJIZWlnaHQgPSBzd2lwZXJDb250YWluZXIuY3NzKCdoZWlnaHQnKTtcblxuXG4gICAgICAgICAgICAvLyBXb3JrYXJvdW5kIGZvciBJRSBCcm93c2Vyc1xuICAgICAgICAgICAgaWYgKCQoJy5zd2lwZXItY29udGFpbmVyLXZlcnRpY2FsJykuaGFzQ2xhc3MoJ3N3aXBlci13cDgtdmVydGljYWwnKSkge1xuICAgICAgICAgICAgICAgICRjb250YWluZXJIZWlnaHQgPSBzd2lwZXJDb250YWluZXIuaGVpZ2h0KCkgKyA1O1xuXG4gICAgICAgICAgICAgICAgc3dpcGVyQ29udGFpbmVyLmNzcygnaGVpZ2h0JywgJGNvbnRhaW5lckhlaWdodCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICgkY29udGFpbmVySGVpZ2h0ID09PSAnMHB4Jykge1xuICAgICAgICAgICAgICAgICRjb250YWluZXJIZWlnaHQgPSAkKCcucHJvZHVjdC1pbmZvLXRodW1ibmFpbHMtbW9iaWxlJykuY3NzKCdoZWlnaHQnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJCgnLmFsaWduLW1pZGRsZScpLmNzcygnaGVpZ2h0JywgJGNvbnRhaW5lckhlaWdodCk7XG4gICAgICAgIH07XG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcbiAgICAgICAgICogQGNvbnN0cnVjdG9yXG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cbiAgICAgICAgICAgICRzbGlkZXMgPSAkdGhpcy5maW5kKCcuc3dpcGVyLXNsaWRlJyk7XG4gICAgICAgICAgICAkY29udHJvbHMgPSAkKG9wdGlvbnMuY29udHJvbHMpO1xuICAgICAgICAgICAgJHRhcmdldCA9ICQob3B0aW9ucy50YXJnZXQpO1xuICAgICAgICAgICAgJHRoZW1lID0gJHRoaXMuZmluZCgndGhlbWUnKTtcblxuICAgICAgICAgICAgLy8gQ2hlY2sgaWYgYWxsIGltYWdlcyBpbnNpZGUgdGhlIHN3aXBlciBoYXZlXG4gICAgICAgICAgICAvLyB0aHVtYm5haWwgaW1hZ2UgZ2l2ZW5cbiAgICAgICAgICAgICRzbGlkZXMuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgaWYgKCEkKHRoaXMpLmRhdGEoKS50aHVtYkltYWdlKSB7XG4gICAgICAgICAgICAgICAgICAgIGhhc1RodW1ibmFpbHMgPSBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvLyBBZGQgdGhlIGJyZWFrcG9pbnQgaGFuZGxlciB0eSBkeW5hbWljYWxseVxuICAgICAgICAgICAgLy8gc2V0IHRoZSBvcHRpb25zIGNvcnJlc3BvbmRpbmcgdG8gdGhlIGJyb3dzZXIgc2l6ZSAoc2xpZGVyIHJlc3BvbnNpdmUgd2lsbCByZS1pbml0aWFsaXplIHRoZSBzd2lwZXIpLlxuICAgICAgICAgICAgX2JyZWFrcG9pbnRIYW5kbGVyKCk7XG5cbiAgICAgICAgICAgIC8vIElmIHRoaXMgaW5zdGFuY2UgaXMgYSBcImNvbnRyb2xcIiBzd2lwZXIgdGhlIHRhcmdldCBpcyB0aGUgbWFpbiBzd2lwZXJcbiAgICAgICAgICAgIC8vIHdoaWNoIHdpbGwgYmUgdXBkYXRlZCBvbiBhIGNsaWNrIGluc2lkZSB0aGlzIGNvbnRyb2wgc3dpcGVyXG4gICAgICAgICAgICBpZiAob3B0aW9ucy50YXJnZXQpIHtcbiAgICAgICAgICAgICAgICAkdGhpcy5vbignY2xpY2suc3dpcGVyJywgJy5zd2lwZXItc2xpZGUnLCBfY2xpY2tIYW5kbGVyKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICQoJy5zd2lwZXItdmVydGljYWwgLnN3aXBlci1zbGlkZVtkYXRhLWluZGV4XScpLmNzcygnZGlzcGxheScsICdpbmxpbmUtYmxvY2snKTtcbiAgICAgICAgICAgICAgICAkKCcucHJvZHVjdC1pbmZvLWltYWdlIC5zd2lwZXItc2xpZGVbZGF0YS1pbmRleF0nKS5jc3MoJ3otaW5kZXgnLCAnaW5oZXJpdCcpO1xuICAgICAgICAgICAgICAgICQoJy5wcm9kdWN0LWluZm8taW1hZ2UgLnN3aXBlci1zbGlkZVtkYXRhLWluZGV4XSAuc3dpcGVyLXNsaWRlLWluc2lkZSBpbWcuaW1nLXJlc3BvbnNpdmUnKS5mYWRlSW4oMTAwMCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgX3RyYW5zbHVjZW5jZVdvcmthcm91bmQoKTtcbiAgICAgICAgICAgIF9wcmV2ZW50VGV4dFNlbGVjdGlvbigpO1xuICAgICAgICAgICAgX3NjYWxlVGh1bWJuYWlsSGVpZ2h0KCk7XG5cbiAgICAgICAgICAgIC8vIEZpeCBmb3IgaW52aXNpYmxlIFRodW1ibmFpbC1JbWFnZXMgZm9yIHN3aXRjaGluZyBmcm9tIFRhYmxldC1Qb3J0cmFpdCB0byBUYWJsZXQtTGFuZHNjYXBlXG4gICAgICAgICAgICAkYm9keS5vbihqc2UubGlicy50aGVtZS5ldmVudHMuQlJFQUtQT0lOVCgpLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgX3NjYWxlVGh1bWJuYWlsSGVpZ2h0KCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTtcbiJdfQ==

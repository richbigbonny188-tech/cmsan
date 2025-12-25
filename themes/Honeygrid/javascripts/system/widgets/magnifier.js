'use strict';

/* --------------------------------------------------------------
 magnifier.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that shows a zoom image on mouseover at a specific target
 */
gambio.widgets.module('magnifier', [gambio.source + '/libs/responsive'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        $body = $('body'),
        $target = null,
        dataWasSet = false,
        defaults = {
        // Default zoom image target selector
        target: null,
        // If true, the zoom image will always fill the whole target container
        keepInView: true,
        // The class that gets added to the body while the magnifier window is visible
        bodyClass: 'magnifier-active',
        // Maximum breakpoint for mobile view mode
        breakpoint: 60
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTIONS ##########

    /**
     * Helper function to calculate the sizes and positions
     * (that doesn't alter until the browser gets resized).
     * The data object is stored at the source image and returned
     * to the caller function
     * @param               {object}        $self           jQuery selection of the source image
     * @param               {object}        $thisTarget     jQuery selection of the zoom image target container
     * @param               {object}        $image          jQuery selection of the zoom image itself
     * @return             {object}                        JSON object which contains the calculated sizes and positions
     * @private
     */
    var _prepareData = function _prepareData($self, $thisTarget, $image) {
        var dataset = {
            offset: $self.offset(),
            height: $self.height(),
            width: $self.width(),
            targetWidth: $thisTarget.width(),
            targetHeight: $thisTarget.height(),
            imageWidth: $image.width(),
            imageHeight: $image.height()
        };

        dataset.aspectX = -1 / (dataset.width / dataset.imageWidth);
        dataset.aspectY = -1 / (dataset.height / dataset.imageHeight);
        dataset.boundaryX = -1 * (dataset.imageWidth - dataset.targetWidth);
        dataset.boundaryY = -1 * (dataset.imageHeight - dataset.targetHeight);

        $self.data('magnifier', dataset);
        dataWasSet = true;

        return $.extend({}, dataset);
    };

    // ########## EVENT HANDLER ##########

    /**
     * Event handler for the mousemove event. If the cursor gets
     * moved over the image, the cursor position will be scaled to
     * the zoom target and the zoom image gets positioned at that point
     * @param       {object}        e       jQuery event object
     * @private
     */
    var _mouseMoveHandler = function _mouseMoveHandler(e) {
        var $self = $(this),
            dataset = $self.data('magnifier'),
            $image = $target.children('img');

        dataset = dataset || _prepareData($self, $target, $image);

        var marginTop = dataset.aspectY * (e.pageY - dataset.offset.top) + dataset.targetHeight / 2,
            marginLeft = dataset.aspectX * (e.pageX - dataset.offset.left) + dataset.targetWidth / 2;

        // If this setting is true, the zoomed image will always
        // fill the whole preview container
        if (options.keepInView) {
            marginTop = Math.min(0, marginTop);
            marginTop = Math.max(dataset.boundaryY, marginTop);
            marginLeft = Math.min(0, marginLeft);
            marginLeft = Math.max(dataset.boundaryX, marginLeft);
        }

        // Set the calculated styles
        $image.css({
            'margin-top': marginTop + 'px',
            'margin-left': marginLeft + 'px'
        });
    };

    /**
     * Event handler for the mouse enter event
     * on the target. It creates the zoom image
     * and embeds it to the magnifier target
     * @private
     */
    var _mouseEnterHandler = function _mouseEnterHandler(e) {

        // Only open in desktop mode
        if (jse.libs.theme.responsive.breakpoint().id > options.breakpoint) {

            var $self = $(this),
                dataset = $self.data(),
                $preloader = $target.find('.preloader'),
                $image = $('<img />'),
                alt = $self.attr('alt'),
                title = $self.attr('title');

            // CleansUp the magnifier target
            $target.children('img').remove();

            $preloader.show();
            $body.addClass(options.bodyClass);

            // Creates the image element and binds
            // a load handler to it, so that the
            // preloader gets hidden after the image
            // is loaded by the browser
            $image.one('load', function () {
                $image.css({
                    'height': this.height + 'px',
                    'width': this.width + 'px'
                });
                $preloader.hide();

                // Bind the mousemove handler to zoom to
                // the correct position of the image
                $self.off('mousemove.magnifier').on('mousemove.magnifier', _mouseMoveHandler);
            }).attr({ src: dataset.magnifierSrc, alt: alt, title: title });

            // Append the image to the maginifier target
            $target.append($image).show();
        }
    };

    /**
     * Handler for the browser resize event.
     * It removes all stored data so that a
     * recalculation is forced
     * @private
     */
    var _resizeHandler = function _resizeHandler() {
        if (dataWasSet) {
            $this.find('img[data-magnifier-src]').removeData('magnifier');

            dataWasSet = false;
        }
    };

    /**
     * Event handler for the mouseleave event. In case
     * the cursor leaves the image, the zoom target gets
     * hidden
     * @private
     */
    var _mouseLeaveHandler = function _mouseLeaveHandler() {
        $target.hide();
        $body.removeClass(options.bodyClass);

        $this.off('mouseenter').on('mouseenter', 'img[data-magnifier-src]', _mouseEnterHandler);
    };

    /**
     * Removes the mouseenter handler on touchstart,
     * so that the magnifier not starts on touch.
     * The function gets reactivated in the mouseleave
     * handler
     * @private
     */
    var _touchHandler = function _touchHandler() {
        $this.off('mouseenter');
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $target = $(options.target);

        $this.on('touchstart', 'img[data-magnifier-src]', _touchHandler).on('mouseenter', 'img[data-magnifier-src]', _mouseEnterHandler).on('mouseleave', 'img[data-magnifier-src]', _mouseLeaveHandler);

        $(window).on('resize', _resizeHandler);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbWFnbmlmaWVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkdGFyZ2V0IiwiZGF0YVdhc1NldCIsImRlZmF1bHRzIiwidGFyZ2V0Iiwia2VlcEluVmlldyIsImJvZHlDbGFzcyIsImJyZWFrcG9pbnQiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3ByZXBhcmVEYXRhIiwiJHNlbGYiLCIkdGhpc1RhcmdldCIsIiRpbWFnZSIsImRhdGFzZXQiLCJvZmZzZXQiLCJoZWlnaHQiLCJ3aWR0aCIsInRhcmdldFdpZHRoIiwidGFyZ2V0SGVpZ2h0IiwiaW1hZ2VXaWR0aCIsImltYWdlSGVpZ2h0IiwiYXNwZWN0WCIsImFzcGVjdFkiLCJib3VuZGFyeVgiLCJib3VuZGFyeVkiLCJfbW91c2VNb3ZlSGFuZGxlciIsImUiLCJjaGlsZHJlbiIsIm1hcmdpblRvcCIsInBhZ2VZIiwidG9wIiwibWFyZ2luTGVmdCIsInBhZ2VYIiwibGVmdCIsIk1hdGgiLCJtaW4iLCJtYXgiLCJjc3MiLCJfbW91c2VFbnRlckhhbmRsZXIiLCJqc2UiLCJsaWJzIiwidGhlbWUiLCJyZXNwb25zaXZlIiwiaWQiLCIkcHJlbG9hZGVyIiwiZmluZCIsImFsdCIsImF0dHIiLCJ0aXRsZSIsInJlbW92ZSIsInNob3ciLCJhZGRDbGFzcyIsIm9uZSIsImhpZGUiLCJvZmYiLCJvbiIsInNyYyIsIm1hZ25pZmllclNyYyIsImFwcGVuZCIsIl9yZXNpemVIYW5kbGVyIiwicmVtb3ZlRGF0YSIsIl9tb3VzZUxlYXZlSGFuZGxlciIsInJlbW92ZUNsYXNzIiwiX3RvdWNoSGFuZGxlciIsImluaXQiLCJkb25lIiwid2luZG93Il0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSxXQURKLEVBR0ksQ0FDSUYsT0FBT0csTUFBUCxH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Q0FBYSxRQUFiLEVBQXVCSSxjQUF2Qjs7QUFFQU07QUFDSCxLQVpEOztBQWNBO0FBQ0EsV0FBT3ZFLE1BQVA7QUFDSCxDQTVOTCIsImZpbGUiOiJ3aWRnZXRzL21hZ25pZmllci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbWFnbmlmaWVyLmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IHNob3dzIGEgem9vbSBpbWFnZSBvbiBtb3VzZW92ZXIgYXQgYSBzcGVjaWZpYyB0YXJnZXRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuICAgICdtYWduaWZpZXInLFxuXG4gICAgW1xuICAgICAgICBnYW1iaW8uc291cmNlICsgJy9saWJzL3Jlc3BvbnNpdmUnXG4gICAgXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgJGJvZHkgPSAkKCdib2R5JyksXG4gICAgICAgICAgICAkdGFyZ2V0ID0gbnVsbCxcbiAgICAgICAgICAgIGRhdGFXYXNTZXQgPSBmYWxzZSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIC8vIERlZmF1bHQgem9vbSBpbWFnZSB0YXJnZXQgc2VsZWN0b3JcbiAgICAgICAgICAgICAgICB0YXJnZXQ6IG51bGwsXG4gICAgICAgICAgICAgICAgLy8gSWYgdHJ1ZSwgdGhlIHpvb20gaW1hZ2Ugd2lsbCBhbHdheXMgZmlsbCB0aGUgd2hvbGUgdGFyZ2V0IGNvbnRhaW5lclxuICAgICAgICAgICAgICAgIGtlZXBJblZpZXc6IHRydWUsXG4gICAgICAgICAgICAgICAgLy8gVGhlIGNsYXNzIHRoYXQgZ2V0cyBhZGRlZCB0byB0aGUgYm9keSB3aGlsZSB0aGUgbWFnbmlmaWVyIHdpbmRvdyBpcyB2aXNpYmxlXG4gICAgICAgICAgICAgICAgYm9keUNsYXNzOiAnbWFnbmlmaWVyLWFjdGl2ZScsXG4gICAgICAgICAgICAgICAgLy8gTWF4aW11bSBicmVha3BvaW50IGZvciBtb2JpbGUgdmlldyBtb2RlXG4gICAgICAgICAgICAgICAgYnJlYWtwb2ludDogNjBcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgICAgIG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhlbHBlciBmdW5jdGlvbiB0byBjYWxjdWxhdGUgdGhlIHNpemVzIGFuZCBwb3NpdGlvbnNcbiAgICAgICAgICogKHRoYXQgZG9lc24ndCBhbHRlciB1bnRpbCB0aGUgYnJvd3NlciBnZXRzIHJlc2l6ZWQpLlxuICAgICAgICAgKiBUaGUgZGF0YSBvYmplY3QgaXMgc3RvcmVkIGF0IHRoZSBzb3VyY2UgaW1hZ2UgYW5kIHJldHVybmVkXG4gICAgICAgICAqIHRvIHRoZSBjYWxsZXIgZnVuY3Rpb25cbiAgICAgICAgICogQHBhcmFtICAgICAgICAgICAgICAge29iamVjdH0gICAgICAgICRzZWxmICAgICAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBzb3VyY2UgaW1hZ2VcbiAgICAgICAgICogQHBhcmFtICAgICAgICAgICAgICAge29iamVjdH0gICAgICAgICR0aGlzVGFyZ2V0ICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSB6b29tIGltYWdlIHRhcmdldCBjb250YWluZXJcbiAgICAgICAgICogQHBhcmFtICAgICAgICAgICAgICAge29iamVjdH0gICAgICAgICRpbWFnZSAgICAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSB6b29tIGltYWdlIGl0c2VsZlxuICAgICAgICAgKiBAcmV0dXJuICAgICAgICAgICAgIHtvYmplY3R9ICAgICAgICAgICAgICAgICAgICAgICAgSlNPTiBvYmplY3Qgd2hpY2ggY29udGFpbnMgdGhlIGNhbGN1bGF0ZWQgc2l6ZXMgYW5kIHBvc2l0aW9uc1xuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9wcmVwYXJlRGF0YSA9IGZ1bmN0aW9uICgkc2VsZiwgJHRoaXNUYXJnZXQsICRpbWFnZSkge1xuICAgICAgICAgICAgdmFyIGRhdGFzZXQgPSB7XG4gICAgICAgICAgICAgICAgb2Zmc2V0OiAkc2VsZi5vZmZzZXQoKSxcbiAgICAgICAgICAgICAgICBoZWlnaHQ6ICRzZWxmLmhlaWdodCgpLFxuICAgICAgICAgICAgICAgIHdpZHRoOiAkc2VsZi53aWR0aCgpLFxuICAgICAgICAgICAgICAgIHRhcmdldFdpZHRoOiAkdGhpc1RhcmdldC53aWR0aCgpLFxuICAgICAgICAgICAgICAgIHRhcmdldEhlaWdodDogJHRoaXNUYXJnZXQuaGVpZ2h0KCksXG4gICAgICAgICAgICAgICAgaW1hZ2VXaWR0aDogJGltYWdlLndpZHRoKCksXG4gICAgICAgICAgICAgICAgaW1hZ2VIZWlnaHQ6ICRpbWFnZS5oZWlnaHQoKVxuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgZGF0YXNldC5hc3BlY3RYID0gLTEgLyAoZGF0YXNldC53aWR0aCAvIGRhdGFzZXQuaW1hZ2VXaWR0aCk7XG4gICAgICAgICAgICBkYXRhc2V0LmFzcGVjdFkgPSAtMSAvIChkYXRhc2V0LmhlaWdodCAvIGRhdGFzZXQuaW1hZ2VIZWlnaHQpO1xuICAgICAgICAgICAgZGF0YXNldC5ib3VuZGFyeVggPSAtMSAqIChkYXRhc2V0LmltYWdlV2lkdGggLSBkYXRhc2V0LnRhcmdldFdpZHRoKTtcbiAgICAgICAgICAgIGRhdGFzZXQuYm91bmRhcnlZID0gLTEgKiAoZGF0YXNldC5pbWFnZUhlaWdodCAtIGRhdGFzZXQudGFyZ2V0SGVpZ2h0KTtcblxuICAgICAgICAgICAgJHNlbGYuZGF0YSgnbWFnbmlmaWVyJywgZGF0YXNldCk7XG4gICAgICAgICAgICBkYXRhV2FzU2V0ID0gdHJ1ZTtcblxuICAgICAgICAgICAgcmV0dXJuICQuZXh0ZW5kKHt9LCBkYXRhc2V0KTtcbiAgICAgICAgfTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIGZvciB0aGUgbW91c2Vtb3ZlIGV2ZW50LiBJZiB0aGUgY3Vyc29yIGdldHNcbiAgICAgICAgICogbW92ZWQgb3ZlciB0aGUgaW1hZ2UsIHRoZSBjdXJzb3IgcG9zaXRpb24gd2lsbCBiZSBzY2FsZWQgdG9cbiAgICAgICAgICogdGhlIHpvb20gdGFyZ2V0IGFuZCB0aGUgem9vbSBpbWFnZSBnZXRzIHBvc2l0aW9uZWQgYXQgdGhhdCBwb2ludFxuICAgICAgICAgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9tb3VzZU1vdmVIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgZGF0YXNldCA9ICRzZWxmLmRhdGEoJ21hZ25pZmllcicpLFxuICAgICAgICAgICAgICAgICRpbWFnZSA9ICR0YXJnZXQuY2hpbGRyZW4oJ2ltZycpO1xuXG4gICAgICAgICAgICBkYXRhc2V0ID0gZGF0YXNldCB8fCBfcHJlcGFyZURhdGEoJHNlbGYsICR0YXJnZXQsICRpbWFnZSk7XG5cbiAgICAgICAgICAgIHZhciBtYXJnaW5Ub3AgPSBkYXRhc2V0LmFzcGVjdFkgKiAoZS5wYWdlWSAtIGRhdGFzZXQub2Zmc2V0LnRvcCkgKyBkYXRhc2V0LnRhcmdldEhlaWdodCAvIDIsXG4gICAgICAgICAgICAgICAgbWFyZ2luTGVmdCA9IGRhdGFzZXQuYXNwZWN0WCAqIChlLnBhZ2VYIC0gZGF0YXNldC5vZmZzZXQubGVmdCkgKyBkYXRhc2V0LnRhcmdldFdpZHRoIC8gMjtcblxuICAgICAgICAgICAgLy8gSWYgdGhpcyBzZXR0aW5nIGlzIHRydWUsIHRoZSB6b29tZWQgaW1hZ2Ugd2lsbCBhbHdheXNcbiAgICAgICAgICAgIC8vIGZpbGwgdGhlIHdob2xlIHByZXZpZXcgY29udGFpbmVyXG4gICAgICAgICAgICBpZiAob3B0aW9ucy5rZWVwSW5WaWV3KSB7XG4gICAgICAgICAgICAgICAgbWFyZ2luVG9wID0gTWF0aC5taW4oMCwgbWFyZ2luVG9wKTtcbiAgICAgICAgICAgICAgICBtYXJnaW5Ub3AgPSBNYXRoLm1heChkYXRhc2V0LmJvdW5kYXJ5WSwgbWFyZ2luVG9wKTtcbiAgICAgICAgICAgICAgICBtYXJnaW5MZWZ0ID0gTWF0aC5taW4oMCwgbWFyZ2luTGVmdCk7XG4gICAgICAgICAgICAgICAgbWFyZ2luTGVmdCA9IE1hdGgubWF4KGRhdGFzZXQuYm91bmRhcnlYLCBtYXJnaW5MZWZ0KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy8gU2V0IHRoZSBjYWxjdWxhdGVkIHN0eWxlc1xuICAgICAgICAgICAgJGltYWdlLmNzcyh7XG4gICAgICAgICAgICAgICAgJ21hcmdpbi10b3AnOiBtYXJnaW5Ub3AgKyAncHgnLFxuICAgICAgICAgICAgICAgICdtYXJnaW4tbGVmdCc6IG1hcmdpbkxlZnQgKyAncHgnXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIG1vdXNlIGVudGVyIGV2ZW50XG4gICAgICAgICAqIG9uIHRoZSB0YXJnZXQuIEl0IGNyZWF0ZXMgdGhlIHpvb20gaW1hZ2VcbiAgICAgICAgICogYW5kIGVtYmVkcyBpdCB0byB0aGUgbWFnbmlmaWVyIHRhcmdldFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9tb3VzZUVudGVySGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG5cbiAgICAgICAgICAgIC8vIE9ubHkgb3BlbiBpbiBkZXNrdG9wIG1vZGVcbiAgICAgICAgICAgIGlmIChqc2UubGlicy50aGVtZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKS5pZCA+IG9wdGlvbnMuYnJlYWtwb2ludCkge1xuXG4gICAgICAgICAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICAgICAgICAgZGF0YXNldCA9ICRzZWxmLmRhdGEoKSxcbiAgICAgICAgICAgICAgICAgICAgJHByZWxvYWRlciA9ICR0YXJnZXQuZmluZCgnLnByZWxvYWRlcicpLFxuICAgICAgICAgICAgICAgICAgICAkaW1hZ2UgPSAkKCc8aW1nIC8+JyksXG4gICAgICAgICAgICAgICAgICAgIGFsdCA9ICRzZWxmLmF0dHIoJ2FsdCcpLFxuICAgICAgICAgICAgICAgICAgICB0aXRsZSA9ICRzZWxmLmF0dHIoJ3RpdGxlJyk7XG5cbiAgICAgICAgICAgICAgICAvLyBDbGVhbnNVcCB0aGUgbWFnbmlmaWVyIHRhcmdldFxuICAgICAgICAgICAgICAgICR0YXJnZXRcbiAgICAgICAgICAgICAgICAgICAgLmNoaWxkcmVuKCdpbWcnKVxuICAgICAgICAgICAgICAgICAgICAucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAkcHJlbG9hZGVyLnNob3coKTtcbiAgICAgICAgICAgICAgICAkYm9keS5hZGRDbGFzcyhvcHRpb25zLmJvZHlDbGFzcyk7XG5cbiAgICAgICAgICAgICAgICAvLyBDcmVhdGVzIHRoZSBpbWFnZSBlbGVtZW50IGFuZCBiaW5kc1xuICAgICAgICAgICAgICAgIC8vIGEgbG9hZCBoYW5kbGVyIHRvIGl0LCBzbyB0aGF0IHRoZVxuICAgICAgICAgICAgICAgIC8vIHByZWxvYWRlciBnZXRzIGhpZGRlbiBhZnRlciB0aGUgaW1hZ2VcbiAgICAgICAgICAgICAgICAvLyBpcyBsb2FkZWQgYnkgdGhlIGJyb3dzZXJcbiAgICAgICAgICAgICAgICAkaW1hZ2Uub25lKCdsb2FkJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAkaW1hZ2UuY3NzKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICdoZWlnaHQnOiB0aGlzLmhlaWdodCArICdweCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAnd2lkdGgnOiB0aGlzLndpZHRoICsgJ3B4J1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgJHByZWxvYWRlci5oaWRlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy8gQmluZCB0aGUgbW91c2Vtb3ZlIGhhbmRsZXIgdG8gem9vbSB0b1xuICAgICAgICAgICAgICAgICAgICAvLyB0aGUgY29ycmVjdCBwb3NpdGlvbiBvZiB0aGUgaW1hZ2VcbiAgICAgICAgICAgICAgICAgICAgJHNlbGZcbiAgICAgICAgICAgICAgICAgICAgICAgIC5vZmYoJ21vdXNlbW92ZS5tYWduaWZpZXInKVxuICAgICAgICAgICAgICAgICAgICAgICAgLm9uKCdtb3VzZW1vdmUubWFnbmlmaWVyJywgX21vdXNlTW92ZUhhbmRsZXIpO1xuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIC5hdHRyKHtzcmM6IGRhdGFzZXQubWFnbmlmaWVyU3JjLCBhbHQ6IGFsdCwgdGl0bGU6IHRpdGxlfSk7XG5cbiAgICAgICAgICAgICAgICAvLyBBcHBlbmQgdGhlIGltYWdlIHRvIHRoZSBtYWdpbmlmaWVyIHRhcmdldFxuICAgICAgICAgICAgICAgICR0YXJnZXRcbiAgICAgICAgICAgICAgICAgICAgLmFwcGVuZCgkaW1hZ2UpXG4gICAgICAgICAgICAgICAgICAgIC5zaG93KCk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBIYW5kbGVyIGZvciB0aGUgYnJvd3NlciByZXNpemUgZXZlbnQuXG4gICAgICAgICAqIEl0IHJlbW92ZXMgYWxsIHN0b3JlZCBkYXRhIHNvIHRoYXQgYVxuICAgICAgICAgKiByZWNhbGN1bGF0aW9uIGlzIGZvcmNlZFxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9yZXNpemVIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgaWYgKGRhdGFXYXNTZXQpIHtcbiAgICAgICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgICAgICAuZmluZCgnaW1nW2RhdGEtbWFnbmlmaWVyLXNyY10nKVxuICAgICAgICAgICAgICAgICAgICAucmVtb3ZlRGF0YSgnbWFnbmlmaWVyJyk7XG5cbiAgICAgICAgICAgICAgICBkYXRhV2FzU2V0ID0gZmFsc2U7XG4gICAgICAgICAgICB9XG4gICAgICAgIH07XG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEV2ZW50IGhhbmRsZXIgZm9yIHRoZSBtb3VzZWxlYXZlIGV2ZW50LiBJbiBjYXNlXG4gICAgICAgICAqIHRoZSBjdXJzb3IgbGVhdmVzIHRoZSBpbWFnZSwgdGhlIHpvb20gdGFyZ2V0IGdldHNcbiAgICAgICAgICogaGlkZGVuXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX21vdXNlTGVhdmVIYW5kbGVyID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgJHRhcmdldC5oaWRlKCk7XG4gICAgICAgICAgICAkYm9keS5yZW1vdmVDbGFzcyhvcHRpb25zLmJvZHlDbGFzcyk7XG5cbiAgICAgICAgICAgICR0aGlzXG4gICAgICAgICAgICAgICAgLm9mZignbW91c2VlbnRlcicpXG4gICAgICAgICAgICAgICAgLm9uKCdtb3VzZWVudGVyJywgJ2ltZ1tkYXRhLW1hZ25pZmllci1zcmNdJywgX21vdXNlRW50ZXJIYW5kbGVyKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvKipcbiAgICAgICAgICogUmVtb3ZlcyB0aGUgbW91c2VlbnRlciBoYW5kbGVyIG9uIHRvdWNoc3RhcnQsXG4gICAgICAgICAqIHNvIHRoYXQgdGhlIG1hZ25pZmllciBub3Qgc3RhcnRzIG9uIHRvdWNoLlxuICAgICAgICAgKiBUaGUgZnVuY3Rpb24gZ2V0cyByZWFjdGl2YXRlZCBpbiB0aGUgbW91c2VsZWF2ZVxuICAgICAgICAgKiBoYW5kbGVyXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3RvdWNoSGFuZGxlciA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICR0aGlzLm9mZignbW91c2VlbnRlcicpO1xuICAgICAgICB9O1xuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICAgICAkdGFyZ2V0ID0gJChvcHRpb25zLnRhcmdldCk7XG5cbiAgICAgICAgICAgICR0aGlzXG4gICAgICAgICAgICAgICAgLm9uKCd0b3VjaHN0YXJ0JywgJ2ltZ1tkYXRhLW1hZ25pZmllci1zcmNdJywgX3RvdWNoSGFuZGxlcilcbiAgICAgICAgICAgICAgICAub24oJ21vdXNlZW50ZXInLCAnaW1nW2RhdGEtbWFnbmlmaWVyLXNyY10nLCBfbW91c2VFbnRlckhhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKCdtb3VzZWxlYXZlJywgJ2ltZ1tkYXRhLW1hZ25pZmllci1zcmNdJywgX21vdXNlTGVhdmVIYW5kbGVyKTtcblxuICAgICAgICAgICAgJCh3aW5kb3cpLm9uKCdyZXNpemUnLCBfcmVzaXplSGFuZGxlcik7XG5cbiAgICAgICAgICAgIGRvbmUoKTtcbiAgICAgICAgfTtcblxuICAgICAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfSk7XG4iXX0=

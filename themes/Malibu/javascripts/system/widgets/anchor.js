'use strict';

/* --------------------------------------------------------------
 anchor.js 2023-06-19 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('anchor', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {
        offset: 80, // Offset in px from top (to prevent the header is hiding an element)
        duration: 300 // Scroll duration in ms
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## EVENT HANDLER ##########

    /**
     * Handler for the click on an anchor
     * link. It calculates the position of
     * the target and scroll @ that position
     * @param       {object}        e           jQuery event object
     * @private
     */
    var _anchorHandler = function _anchorHandler(e) {
        var $self = $(this),
            $target = null,
            link = $self.attr('href'),
            position = null;

        // Only react if the link is an anchor
        if (link && link.indexOf('#') === 0 && link !== '#') {
            e.preventDefault();
            e.stopPropagation();

            $target = $(link);

            if ($target.length) {
                position = $target.offset().top;

                $('html, body').animate({ scrollTop: position - options.offset }, options.duration);
            }
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        $this.on('click', 'a:not(.js-open-modal):not(.carousel-control):not([data-toggle])', _anchorHandler);
        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvYW5jaG9yLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvZmZzZXQiLCJkdXJhdGlvbiIsIm9wdGlvbnMiLCJleHRlbmQiLCJfYW5jaG9ySGFuZGxlciIsImUiLCIkc2VsZiIsIiR0YXJnZXQiLCJsaW5rIiwiYXR0ciIsInBvc2l0aW9uIiwiaW5kZXhPZiIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwibGVuZ3RoIiwidG9wIiwiYW5pbWF0ZSIsInNjcm9sbFRvcCIsImluaXQiLCJkb25lIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLFFBQXRCLEVBQWdDLEVBQWhDLEVBQW9DLFVBQVVDLElBQVYsRUFBZ0I7O0FBRWhEOztBQUVKOztBQUVJLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsV0FBVztBQUNQQyxnQkFBUSxFQURELEVBQ1M7QUFDaEJDLGtCQUFVLEdBRkgsQ0FFVztBQUZYLEtBRGY7QUFBQSxRQUtJQyxVQUFVSixFQUFFSyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJKLFFBQW5CLEVBQTZCSCxJQUE3QixDQUxkO0FBQUEsUUFNSUQsU0FBUyxFQU5iOztBQVFKOztBQUVJOzs7Ozs7O0FBT0EsUUFBSVMsaUJBQWlCLFNBQWpCQSxjQUFpQixDQUFVQyxDQUFWLEVBQWE7QUFDOUIsWUFBSUMsUUFBUVIsRUFBRSxJQUFGLENBQVo7QUFBQSxZQUNJUyxVQUFVLElBRGQ7QUFBQSxZQUVJQyxPQUFPRixNQUFNRyxJQUFOLENBQVcsTUFBWCxDQUZYO0FBQUEsWUFHSUMsV0FBVyxJQUhmOztBQUtBO0FBQ0EsWUFBSUYsUUFBUUEsS0FBS0csT0FBTCxDQUFhLEdBQWIsTUFBc0IsQ0FBOUIsSUFBbUNILFNBQVMsR0FBaEQsRUFBcUQ7QUFDakRILGNBQUVPLGNBQUY7QUFDQVAsY0FBRVEsZUFBRjs7QUFFQU4sc0JBQVVULEVBQUVVLElBQUYsQ0FBVjs7QUFFQSxnQkFBSUQsUUFBUU8sTUFBWixFQUFvQjtBQUNoQkosMkJBQVdILFFBQ05QLE1BRE0sR0FFTmUsR0FGTDs7QUFJQWpCLGtCQUFFLFlBQUYsRUFBZ0JrQixPQUFoQixDQUF3QixFQUFDQyxXQUFXUCxXQUFXUixRQUFRRixNQUEvQixFQUF4QixFQUFnRUUsUUFBUUQsUUFBeEU7QUFDSDtBQUNKO0FBQ0osS0FyQkQ7O0FBdUJKOztBQUVJOzs7O0FBSUFOLFdBQU91QixJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUMxQnRCLGNBQU11QixFQUFOLENBQVMsT0FBVCxFQUFrQixpRUFBbEIsRUFBcUZoQixjQUFyRjtBQUNBZTtBQUNILEtBSEQ7O0FBS0E7QUFDQSxXQUFPeEIsTUFBUDtBQUNILENBM0REIiwiZmlsZSI6IndpZGdldHMvYW5jaG9yLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBhbmNob3IuanMgMjAyMy0wNi0xOSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMjMgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmdhbWJpby53aWRnZXRzLm1vZHVsZSgnYW5jaG9yJywgW10sIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgdmFyICR0aGlzID0gJCh0aGlzKSxcbiAgICAgICAgZGVmYXVsdHMgPSB7XG4gICAgICAgICAgICBvZmZzZXQ6IDgwLCAgICAgLy8gT2Zmc2V0IGluIHB4IGZyb20gdG9wICh0byBwcmV2ZW50IHRoZSBoZWFkZXIgaXMgaGlkaW5nIGFuIGVsZW1lbnQpXG4gICAgICAgICAgICBkdXJhdGlvbjogMzAwICAgICAvLyBTY3JvbGwgZHVyYXRpb24gaW4gbXNcbiAgICAgICAgfSxcbiAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgIG1vZHVsZSA9IHt9O1xuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG4gICAgLyoqXG4gICAgICogSGFuZGxlciBmb3IgdGhlIGNsaWNrIG9uIGFuIGFuY2hvclxuICAgICAqIGxpbmsuIEl0IGNhbGN1bGF0ZXMgdGhlIHBvc2l0aW9uIG9mXG4gICAgICogdGhlIHRhcmdldCBhbmQgc2Nyb2xsIEAgdGhhdCBwb3NpdGlvblxuICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZSAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuICAgICAqIEBwcml2YXRlXG4gICAgICovXG4gICAgdmFyIF9hbmNob3JIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgdmFyICRzZWxmID0gJCh0aGlzKSxcbiAgICAgICAgICAgICR0YXJnZXQgPSBudWxsLFxuICAgICAgICAgICAgbGluayA9ICRzZWxmLmF0dHIoJ2hyZWYnKSxcbiAgICAgICAgICAgIHBvc2l0aW9uID0gbnVsbDtcblxuICAgICAgICAvLyBPbmx5IHJlYWN0IGlmIHRoZSBsaW5rIGlzIGFuIGFuY2hvclxuICAgICAgICBpZiAobGluayAmJiBsaW5rLmluZGV4T2YoJyMnKSA9PT0gMCAmJiBsaW5rICE9PSAnIycpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgICR0YXJnZXQgPSAkKGxpbmspO1xuXG4gICAgICAgICAgICBpZiAoJHRhcmdldC5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICBwb3NpdGlvbiA9ICR0YXJnZXRcbiAgICAgICAgICAgICAgICAgICAgLm9mZnNldCgpXG4gICAgICAgICAgICAgICAgICAgIC50b3A7XG5cbiAgICAgICAgICAgICAgICAkKCdodG1sLCBib2R5JykuYW5pbWF0ZSh7c2Nyb2xsVG9wOiBwb3NpdGlvbiAtIG9wdGlvbnMub2Zmc2V0fSwgb3B0aW9ucy5kdXJhdGlvbik7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9O1xuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgIC8qKlxuICAgICAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAqL1xuICAgIG1vZHVsZS5pbml0ID0gZnVuY3Rpb24gKGRvbmUpIHtcbiAgICAgICAgJHRoaXMub24oJ2NsaWNrJywgJ2E6bm90KC5qcy1vcGVuLW1vZGFsKTpub3QoLmNhcm91c2VsLWNvbnRyb2wpOm5vdChbZGF0YS10b2dnbGVdKScsIF9hbmNob3JIYW5kbGVyKTtcbiAgICAgICAgZG9uZSgpO1xuICAgIH07XG5cbiAgICAvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG4gICAgcmV0dXJuIG1vZHVsZTtcbn0pOyJdfQ==

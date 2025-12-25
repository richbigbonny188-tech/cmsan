'use strict';

/* --------------------------------------------------------------
 transitions.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component that helps on applying css3 transitions on
 * elements. This component listens on events triggered on
 * objects that needs to be animated and calculates the
 * dimensions for the element before and after animation
 */
gambio.widgets.module('transitions', [gambio.source + '/libs/events'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        timer = [],
        defaults = {
        duration: 0.5, // Default transition duration in seconds
        open: true, // Is it a open or a close animation (needed to determine the correct classes)
        classClose: '', // Class added during close transition
        classOpen: '' // Class added during open animation
    },
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## HELPER FUNCTION ##########

    /**
     * Helper function that gets the current transition
     * duration from the given element (in ms). If the
     * current object hasn't an transition duration check
     * all child elements for a duration and stop after
     * finding the first one
     * @param       {object}    $element    jQuery selection of the animated element
     * @return     {integer}               Animation duration in ms
     * @private
     */
    var _getTransitionDuration = function _getTransitionDuration($element) {

        var duration = options.duration;

        $element.add($element.children()).each(function () {
            var time = $element.css('transition-duration') !== undefined ? $element.css('transition-duration') : $element.css('-webkit-transtion-duration') !== undefined ? $element.css('-webkit-transtion-duration') : $element.css('-moz-transtion-duration') !== undefined ? $element.css('-moz-transtion-duration') : $element.css('-ms-transtion-duration') !== undefined ? $element.css('-ms-transtion-duration') : $element.css('-o-transtion-duration') !== undefined ? $element.css('-o-transtion-duration') : -1;

            if (time >= 0) {
                duration = time;
                return false;
            }
        });

        duration = Math.round(parseFloat(duration) * 1000);
        return duration;
    };

    // ########## EVENT HANDLER ##########

    /**
     * Function that sets the classes and dimensions to an object
     * that needs to be animated. After the animation duration it
     * cleans up all unnecessary classes and style attributes
     * @param       {object}        e           jQuery event object
     * @param       {object}        d           JSON that contains the configuration
     * @private
     */
    var _transitionHandler = function _transitionHandler(e, d) {

        var $self = $(e.target),
            $clone = $self.clone(),
            // Avoid hiding the original element, use a clone as a helper.
        dataset = $.extend({}, $self.data().transition || {}, d),
            removeClass = dataset.open ? dataset.classClose : dataset.classOpen,
            addClass = dataset.open ? dataset.classOpen : dataset.classClose,
            initialHeight = null,
            initialWidth = null,
            height = null,
            width = null;

        dataset.uid = dataset.uid || parseInt(Math.random() * 100000, 10);
        removeClass = removeClass || '';
        addClass = addClass || '';

        // Stop current animation timers
        if (timer[dataset.uid]) {
            clearTimeout(timer[dataset.uid]);
        }

        $clone.appendTo($self.parent());

        // Get initial and final dimensions of the target
        // by getting the current width and height values
        // and the ones with the final classes appended to
        // the target
        $clone.css({
            visibility: 'hidden',
            display: 'initial'
        });

        initialHeight = $clone.outerHeight();
        initialWidth = $clone.outerWidth();

        $self.removeAttr('style').removeClass('transition ' + removeClass).addClass(addClass);

        height = $self.outerHeight();
        width = $self.outerWidth();

        // Check if the container height needs to be set
        if (dataset.calcHeight) {
            // Setup the transition by setting the initial
            // values BEFORE adding the transition classes.
            // After setting the transition classes, set the
            // final sizes
            $self.removeClass(addClass).css({
                height: initialHeight + 'px',
                width: initialWidth + 'px',
                visibility: 'initial',
                display: 'initial'
            }).addClass('transition ' + addClass).css({
                'height': height + 'px',
                'width': width + 'px'
            });
        } else {
            // Setup the transition by setting the transition classes.
            $self.removeClass(addClass).addClass('transition ' + addClass);
        }

        // Add an event listener to remove all unnecessary
        // classes and style attributes
        var duration = _getTransitionDuration($self);
        timer[dataset.uid] = setTimeout(function () {

            $self.removeAttr('style').removeClass('transition').removeData('transition').triggerHandler(jse.libs.theme.events.TRANSITION_FINISHED());
        }, duration);

        // Store the configuration data to the target object
        $self.data('transition', dataset);
        $clone.remove();
    };

    /**
     * Event handler that stops a transition timer set
     * by the _transitionHandler function.
     * @private
     */
    var _stopTransition = function _stopTransition() {
        var $self = $(this),
            dataset = $self.data('transition') || {};

        if (!$.isEmptyObject(dataset)) {

            timer[dataset.uid] = timer[dataset.uid] ? clearTimeout(timer[dataset.uid]) : null;

            $self.removeAttr('style').removeClass('transition').removeData('transition').triggerHandler(jse.libs.theme.events.TRANSITION_FINISHED());
        }
    };

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {

        $this.on(jse.libs.theme.events.TRANSITION(), _transitionHandler).on(jse.libs.theme.events.TRANSITION_STOP(), _stopTransition);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvdHJhbnNpdGlvbnMuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJ0aW1lciIsImRlZmF1bHRzIiwiZHVyYXRpb24iLCJvcGVuIiwiY2xhc3NDbG9zZSIsImNsYXNzT3BlbiIsIm9wdGlvbnMiLCJleHRlbmQiLCJfZ2V0VHJhbnNpdGlvbkR1cmF0aW9uIiwiJGVsZW1lbnQiLCJhZGQiLCJjaGlsZHJlbiIsImVhY2giLCJ0aW1lIiwiY3NzIiwidW5kZWZpbmVkIiwiTWF0aCIsInJvdW5kIiwicGFyc2VGbG9hdCIsIl90cmFuc2l0aW9uSGFuZGxlciIsImUiLCJkIiwiJHNlbGYiLCJ0YXJnZXQiLCIkY2xvbmUiLCJjbG9uZSIsImRhdGFzZXQiLCJ0cmFuc2l0aW9uIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImluaXRpYWxIZWlnaHQiLCJpbml0aWFsV2lkdGgiLCJoZWlnaHQiLCJ3aWR0aCIsInVpZCIsInBhcnNlSW50IiwicmFuZG9tIiwiY2xlYXJUaW1lb3V0IiwiYXBwZW5kVG8iLCJwYXJlbnQiLCJ2aXNpYmlsaXR5IiwiZGlzcGxheSIsIm91dGVySGVpZ2h0Iiwib3V0ZXJXaWR0aCIsInJlbW92ZUF0dHIiLCJjYWxjSGVpZ2h0Iiwic2V0VGltZW91dCIsInJlbW92ZURhdGEiLCJ0cmlnZ2VySGFuZGxlciIsImpzZSIsImxpYnMiLCJ0aGVtZSIsImV2ZW50cyIsIlRSQU5TSVRJT05fRklOSVNIRUQiLCJyZW1vdmUiLCJfc3RvcFRyYW5zaXRpb24iLCJpc0VtcHR5T2JqZWN0IiwiaW5pdCIsImRvbmUiLCJvbiIsIlRSQU5TSVRJT04iLCJUUkFOU0lUSU9OX1NUT1AiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7O0FBTUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLGFBREosRUFHSSxDQUNJRixPQUFPRyxNQUFQLEdBQWdCLGNB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aWRnZXRzL3RyYW5zaXRpb25zLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB0cmFuc2l0aW9ucy5qcyAyMDE2LTAzLTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBDb21wb25lbnQgdGhhdCBoZWxwcyBvbiBhcHBseWluZyBjc3MzIHRyYW5zaXRpb25zIG9uXG4gKiBlbGVtZW50cy4gVGhpcyBjb21wb25lbnQgbGlzdGVucyBvbiBldmVudHMgdHJpZ2dlcmVkIG9uXG4gKiBvYmplY3RzIHRoYXQgbmVlZHMgdG8gYmUgYW5pbWF0ZWQgYW5kIGNhbGN1bGF0ZXMgdGhlXG4gKiBkaW1lbnNpb25zIGZvciB0aGUgZWxlbWVudCBiZWZvcmUgYW5kIGFmdGVyIGFuaW1hdGlvblxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ3RyYW5zaXRpb25zJyxcblxuICAgIFtcbiAgICAgICAgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnXG4gICAgXSxcblxuICAgIGZ1bmN0aW9uIChkYXRhKSB7XG5cbiAgICAgICAgJ3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgdGltZXIgPSBbXSxcbiAgICAgICAgICAgIGRlZmF1bHRzID0ge1xuICAgICAgICAgICAgICAgIGR1cmF0aW9uOiAwLjUsICAgICAgICAvLyBEZWZhdWx0IHRyYW5zaXRpb24gZHVyYXRpb24gaW4gc2Vjb25kc1xuICAgICAgICAgICAgICAgIG9wZW46IHRydWUsICAgICAgIC8vIElzIGl0IGEgb3BlbiBvciBhIGNsb3NlIGFuaW1hdGlvbiAobmVlZGVkIHRvIGRldGVybWluZSB0aGUgY29ycmVjdCBjbGFzc2VzKVxuICAgICAgICAgICAgICAgIGNsYXNzQ2xvc2U6ICcnLCAgICAgICAgIC8vIENsYXNzIGFkZGVkIGR1cmluZyBjbG9zZSB0cmFuc2l0aW9uXG4gICAgICAgICAgICAgICAgY2xhc3NPcGVuOiAnJyAgICAgICAgICAvLyBDbGFzcyBhZGRlZCBkdXJpbmcgb3BlbiBhbmltYXRpb25cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgICAgIG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgZ2V0cyB0aGUgY3VycmVudCB0cmFuc2l0aW9uXG4gICAgICAgICAqIGR1cmF0aW9uIGZyb20gdGhlIGdpdmVuIGVsZW1lbnQgKGluIG1zKS4gSWYgdGhlXG4gICAgICAgICAqIGN1cnJlbnQgb2JqZWN0IGhhc24ndCBhbiB0cmFuc2l0aW9uIGR1cmF0aW9uIGNoZWNrXG4gICAgICAgICAqIGFsbCBjaGlsZCBlbGVtZW50cyBmb3IgYSBkdXJhdGlvbiBhbmQgc3RvcCBhZnRlclxuICAgICAgICAgKiBmaW5kaW5nIHRoZSBmaXJzdCBvbmVcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICRlbGVtZW50ICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGFuaW1hdGVkIGVsZW1lbnRcbiAgICAgICAgICogQHJldHVybiAgICAge2ludGVnZXJ9ICAgICAgICAgICAgICAgQW5pbWF0aW9uIGR1cmF0aW9uIGluIG1zXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX2dldFRyYW5zaXRpb25EdXJhdGlvbiA9IGZ1bmN0aW9uICgkZWxlbWVudCkge1xuXG4gICAgICAgICAgICB2YXIgZHVyYXRpb24gPSBvcHRpb25zLmR1cmF0aW9uO1xuXG4gICAgICAgICAgICAkZWxlbWVudFxuICAgICAgICAgICAgICAgIC5hZGQoJGVsZW1lbnQuY2hpbGRyZW4oKSlcbiAgICAgICAgICAgICAgICAuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciB0aW1lID0gKCRlbGVtZW50LmNzcygndHJhbnNpdGlvbi1kdXJhdGlvbicpICE9PSB1bmRlZmluZWQpXG4gICAgICAgICAgICAgICAgICAgICAgICA/ICRlbGVtZW50LmNzcygndHJhbnNpdGlvbi1kdXJhdGlvbicpXG4gICAgICAgICAgICAgICAgICAgICAgICA6ICgkZWxlbWVudC5jc3MoJy13ZWJraXQtdHJhbnN0aW9uLWR1cmF0aW9uJykgIT09IHVuZGVmaW5lZClcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA/ICRlbGVtZW50LmNzcygnLXdlYmtpdC10cmFuc3Rpb24tZHVyYXRpb24nKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDogKCRlbGVtZW50LmNzcygnLW1vei10cmFuc3Rpb24tZHVyYXRpb24nKSAhPT0gdW5kZWZpbmVkKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA/ICRlbGVtZW50LmNzcygnLW1vei10cmFuc3Rpb24tZHVyYXRpb24nKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA6ICgkZWxlbWVudC5jc3MoJy1tcy10cmFuc3Rpb24tZHVyYXRpb24nKSAhPT0gdW5kZWZpbmVkKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPyAkZWxlbWVudC5jc3MoJy1tcy10cmFuc3Rpb24tZHVyYXRpb24nKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgOiAoJGVsZW1lbnQuY3NzKCctby10cmFuc3Rpb24tZHVyYXRpb24nKSAhPT0gdW5kZWZpbmVkKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgID8gJGVsZW1lbnQuY3NzKCctby10cmFuc3Rpb24tZHVyYXRpb24nKSA6IC0xO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICh0aW1lID49IDApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGR1cmF0aW9uID0gdGltZTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBkdXJhdGlvbiA9IE1hdGgucm91bmQocGFyc2VGbG9hdChkdXJhdGlvbikgKiAxMDAwKTtcbiAgICAgICAgICAgIHJldHVybiBkdXJhdGlvbjtcblxuICAgICAgICB9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEZ1bmN0aW9uIHRoYXQgc2V0cyB0aGUgY2xhc3NlcyBhbmQgZGltZW5zaW9ucyB0byBhbiBvYmplY3RcbiAgICAgICAgICogdGhhdCBuZWVkcyB0byBiZSBhbmltYXRlZC4gQWZ0ZXIgdGhlIGFuaW1hdGlvbiBkdXJhdGlvbiBpdFxuICAgICAgICAgKiBjbGVhbnMgdXAgYWxsIHVubmVjZXNzYXJ5IGNsYXNzZXMgYW5kIHN0eWxlIGF0dHJpYnV0ZXNcbiAgICAgICAgICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG4gICAgICAgICAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZCAgICAgICAgICAgSlNPTiB0aGF0IGNvbnRhaW5zIHRoZSBjb25maWd1cmF0aW9uXG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICB2YXIgX3RyYW5zaXRpb25IYW5kbGVyID0gZnVuY3Rpb24gKGUsIGQpIHtcblxuICAgICAgICAgICAgdmFyICRzZWxmID0gJChlLnRhcmdldCksXG4gICAgICAgICAgICAgICAgJGNsb25lID0gJHNlbGYuY2xvbmUoKSwgLy8gQXZvaWQgaGlkaW5nIHRoZSBvcmlnaW5hbCBlbGVtZW50LCB1c2UgYSBjbG9uZSBhcyBhIGhlbHBlci5cbiAgICAgICAgICAgICAgICBkYXRhc2V0ID0gJC5leHRlbmQoe30sICRzZWxmLmRhdGEoKS50cmFuc2l0aW9uIHx8IHt9LCBkKSxcbiAgICAgICAgICAgICAgICByZW1vdmVDbGFzcyA9IChkYXRhc2V0Lm9wZW4pID8gZGF0YXNldC5jbGFzc0Nsb3NlIDogZGF0YXNldC5jbGFzc09wZW4sXG4gICAgICAgICAgICAgICAgYWRkQ2xhc3MgPSAoZGF0YXNldC5vcGVuKSA/IGRhdGFzZXQuY2xhc3NPcGVuIDogZGF0YXNldC5jbGFzc0Nsb3NlLFxuICAgICAgICAgICAgICAgIGluaXRpYWxIZWlnaHQgPSBudWxsLFxuICAgICAgICAgICAgICAgIGluaXRpYWxXaWR0aCA9IG51bGwsXG4gICAgICAgICAgICAgICAgaGVpZ2h0ID0gbnVsbCxcbiAgICAgICAgICAgICAgICB3aWR0aCA9IG51bGw7XG5cbiAgICAgICAgICAgIGRhdGFzZXQudWlkID0gZGF0YXNldC51aWQgfHwgcGFyc2VJbnQoTWF0aC5yYW5kb20oKSAqIDEwMDAwMCwgMTApO1xuICAgICAgICAgICAgcmVtb3ZlQ2xhc3MgPSByZW1vdmVDbGFzcyB8fCAnJztcbiAgICAgICAgICAgIGFkZENsYXNzID0gYWRkQ2xhc3MgfHwgJyc7XG5cbiAgICAgICAgICAgIC8vIFN0b3AgY3VycmVudCBhbmltYXRpb24gdGltZXJzXG4gICAgICAgICAgICBpZiAodGltZXJbZGF0YXNldC51aWRdKSB7XG4gICAgICAgICAgICAgICAgY2xlYXJUaW1lb3V0KHRpbWVyW2RhdGFzZXQudWlkXSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICRjbG9uZS5hcHBlbmRUbygkc2VsZi5wYXJlbnQoKSk7XG5cblxuICAgICAgICAgICAgLy8gR2V0IGluaXRpYWwgYW5kIGZpbmFsIGRpbWVuc2lvbnMgb2YgdGhlIHRhcmdldFxuICAgICAgICAgICAgLy8gYnkgZ2V0dGluZyB0aGUgY3VycmVudCB3aWR0aCBhbmQgaGVpZ2h0IHZhbHVlc1xuICAgICAgICAgICAgLy8gYW5kIHRoZSBvbmVzIHdpdGggdGhlIGZpbmFsIGNsYXNzZXMgYXBwZW5kZWQgdG9cbiAgICAgICAgICAgIC8vIHRoZSB0YXJnZXRcbiAgICAgICAgICAgICRjbG9uZS5jc3Moe1xuICAgICAgICAgICAgICAgIHZpc2liaWxpdHk6ICdoaWRkZW4nLFxuICAgICAgICAgICAgICAgIGRpc3BsYXk6ICdpbml0aWFsJ1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGluaXRpYWxIZWlnaHQgPSAkY2xvbmUub3V0ZXJIZWlnaHQoKTtcbiAgICAgICAgICAgIGluaXRpYWxXaWR0aCA9ICRjbG9uZS5vdXRlcldpZHRoKCk7XG5cbiAgICAgICAgICAgICRzZWxmXG4gICAgICAgICAgICAgICAgLnJlbW92ZUF0dHIoJ3N0eWxlJylcbiAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ3RyYW5zaXRpb24gJyArIHJlbW92ZUNsYXNzKVxuICAgICAgICAgICAgICAgIC5hZGRDbGFzcyhhZGRDbGFzcyk7XG5cbiAgICAgICAgICAgIGhlaWdodCA9ICRzZWxmLm91dGVySGVpZ2h0KCk7XG4gICAgICAgICAgICB3aWR0aCA9ICRzZWxmLm91dGVyV2lkdGgoKTtcblxuICAgICAgICAgICAgLy8gQ2hlY2sgaWYgdGhlIGNvbnRhaW5lciBoZWlnaHQgbmVlZHMgdG8gYmUgc2V0XG4gICAgICAgICAgICBpZiAoZGF0YXNldC5jYWxjSGVpZ2h0KSB7XG4gICAgICAgICAgICAgICAgLy8gU2V0dXAgdGhlIHRyYW5zaXRpb24gYnkgc2V0dGluZyB0aGUgaW5pdGlhbFxuICAgICAgICAgICAgICAgIC8vIHZhbHVlcyBCRUZPUkUgYWRkaW5nIHRoZSB0cmFuc2l0aW9uIGNsYXNzZXMuXG4gICAgICAgICAgICAgICAgLy8gQWZ0ZXIgc2V0dGluZyB0aGUgdHJhbnNpdGlvbiBjbGFzc2VzLCBzZXQgdGhlXG4gICAgICAgICAgICAgICAgLy8gZmluYWwgc2l6ZXNcbiAgICAgICAgICAgICAgICAkc2VsZlxuICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoYWRkQ2xhc3MpXG4gICAgICAgICAgICAgICAgICAgIC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICAgICAgaGVpZ2h0OiBpbml0aWFsSGVpZ2h0ICsgJ3B4JyxcbiAgICAgICAgICAgICAgICAgICAgICAgIHdpZHRoOiBpbml0aWFsV2lkdGggKyAncHgnLFxuICAgICAgICAgICAgICAgICAgICAgICAgdmlzaWJpbGl0eTogJ2luaXRpYWwnLFxuICAgICAgICAgICAgICAgICAgICAgICAgZGlzcGxheTogJ2luaXRpYWwnXG4gICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygndHJhbnNpdGlvbiAnICsgYWRkQ2xhc3MpXG4gICAgICAgICAgICAgICAgICAgIC5jc3Moe1xuICAgICAgICAgICAgICAgICAgICAgICAgJ2hlaWdodCc6IGhlaWdodCArICdweCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAnd2lkdGgnOiB3aWR0aCArICdweCdcbiAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIC8vIFNldHVwIHRoZSB0cmFuc2l0aW9uIGJ5IHNldHRpbmcgdGhlIHRyYW5zaXRpb24gY2xhc3Nlcy5cbiAgICAgICAgICAgICAgICAkc2VsZlxuICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoYWRkQ2xhc3MpXG4gICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygndHJhbnNpdGlvbiAnICsgYWRkQ2xhc3MpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBBZGQgYW4gZXZlbnQgbGlzdGVuZXIgdG8gcmVtb3ZlIGFsbCB1bm5lY2Vzc2FyeVxuICAgICAgICAgICAgLy8gY2xhc3NlcyBhbmQgc3R5bGUgYXR0cmlidXRlc1xuICAgICAgICAgICAgdmFyIGR1cmF0aW9uID0gX2dldFRyYW5zaXRpb25EdXJhdGlvbigkc2VsZik7XG4gICAgICAgICAgICB0aW1lcltkYXRhc2V0LnVpZF0gPSBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcblxuICAgICAgICAgICAgICAgICRzZWxmXG4gICAgICAgICAgICAgICAgICAgIC5yZW1vdmVBdHRyKCdzdHlsZScpXG4gICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygndHJhbnNpdGlvbicpXG4gICAgICAgICAgICAgICAgICAgIC5yZW1vdmVEYXRhKCd0cmFuc2l0aW9uJylcbiAgICAgICAgICAgICAgICAgICAgLnRyaWdnZXJIYW5kbGVyKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OX0ZJTklTSEVEKCkpO1xuXG4gICAgICAgICAgICB9LCBkdXJhdGlvbik7XG5cbiAgICAgICAgICAgIC8vIFN0b3JlIHRoZSBjb25maWd1cmF0aW9uIGRhdGEgdG8gdGhlIHRhcmdldCBvYmplY3RcbiAgICAgICAgICAgICRzZWxmLmRhdGEoJ3RyYW5zaXRpb24nLCBkYXRhc2V0KTtcbiAgICAgICAgICAgICRjbG9uZS5yZW1vdmUoKTtcbiAgICAgICAgfTtcblxuXG4gICAgICAgIC8qKlxuICAgICAgICAgKiBFdmVudCBoYW5kbGVyIHRoYXQgc3RvcHMgYSB0cmFuc2l0aW9uIHRpbWVyIHNldFxuICAgICAgICAgKiBieSB0aGUgX3RyYW5zaXRpb25IYW5kbGVyIGZ1bmN0aW9uLlxuICAgICAgICAgKiBAcHJpdmF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgdmFyIF9zdG9wVHJhbnNpdGlvbiA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciAkc2VsZiA9ICQodGhpcyksXG4gICAgICAgICAgICAgICAgZGF0YXNldCA9ICRzZWxmLmRhdGEoJ3RyYW5zaXRpb24nKSB8fCB7fTtcblxuICAgICAgICAgICAgaWYgKCEkLmlzRW1wdHlPYmplY3QoZGF0YXNldCkpIHtcblxuICAgICAgICAgICAgICAgIHRpbWVyW2RhdGFzZXQudWlkXSA9ICh0aW1lcltkYXRhc2V0LnVpZF0pID8gY2xlYXJUaW1lb3V0KHRpbWVyW2RhdGFzZXQudWlkXSkgOiBudWxsO1xuXG4gICAgICAgICAgICAgICAgJHNlbGZcbiAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUF0dHIoJ3N0eWxlJylcbiAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCd0cmFuc2l0aW9uJylcbiAgICAgICAgICAgICAgICAgICAgLnJlbW92ZURhdGEoJ3RyYW5zaXRpb24nKVxuICAgICAgICAgICAgICAgICAgICAudHJpZ2dlckhhbmRsZXIoanNlLmxpYnMudGhlbWUuZXZlbnRzLlRSQU5TSVRJT05fRklOSVNIRUQoKSk7XG5cbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuXG4gICAgICAgICAgICAkdGhpc1xuICAgICAgICAgICAgICAgIC5vbihqc2UubGlicy50aGVtZS5ldmVudHMuVFJBTlNJVElPTigpLCBfdHJhbnNpdGlvbkhhbmRsZXIpXG4gICAgICAgICAgICAgICAgLm9uKGpzZS5saWJzLnRoZW1lLmV2ZW50cy5UUkFOU0lUSU9OX1NUT1AoKSwgX3N0b3BUcmFuc2l0aW9uKTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTtcbiJdfQ==

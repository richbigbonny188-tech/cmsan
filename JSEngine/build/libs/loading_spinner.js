'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 loading_spinner.js 2016-06-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.loading_spinner = jse.libs.loading_spinner || {};

/**
 * ## Loading Spinner Library
 *
 * This library provides an easy and simple way to display a loading spinner inside any container
 * element to provide a smooth "loading" experience to the UI. If no container is specified then
 * the whole page will be taken for the display. The loading spinner comes from the Font Awesome
 * "fa-spinner" class. You can load this library as a dependency to existing modules.
 *
 * The following usage example will show you how to display and hide the spinner inside an element.
 *
 * ```javascript
 * // Create a selector variable for the target element.
 * var $targetElement = $('#my-div');
 *
 * // The $targetElement will be overlayed by the spinner.
 * var $spinner = window.jse.libs.loading_spinner.show($targetElement);
 *
 * // Do some stuff ...
 *
 * // Hide the spinner when the job is done.
 * window.jse.loading_spinner.hide($spinner);
 * ```
 *
 * @module JSE/Libs/loading_spinner
 * @exports jse.libs.loading_spinner
 */
(function (exports) {

    'use strict';

    /**
     * Contains a list of the active spinners so that they can be validated
     * before they are destroyed.
     *
     * @type {Array}
     */

    var instances = [];

    /**
     * Show the loading spinner to the target element.
     *
     * @param {jQuery} $targetElement (optional) The target element will be overlayed by the spinner. If no
     * argument is provided then the spinner will overlay the whole page.
     * @param {String} zIndex Optional ('auto'), give a specific z-index value to the loading spinner.
     *
     * @return {jQuery} Returns the selector of the spinner div element. You can further manipulate the spinner
     * if required, but you have to provide this selector as a parameter to the "hide" method below.
     */
    exports.show = function ($targetElement) {
        var zIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'auto';

        if ($targetElement !== undefined && (typeof $targetElement === 'undefined' ? 'undefined' : _typeof($targetElement)) !== 'object') {
            throw new Error('Invalid argument provided for the "show" method: ' + (typeof $targetElement === 'undefined' ? 'undefined' : _typeof($targetElement)));
        }

        if ($targetElement.length === 0) {
            return; // No element matches the provided selector. 
        }

        $targetElement = $targetElement || $('body'); // set default value

        var $spinner = $('<div class="loading-spinner"></div>');
        var fontSize = 80;

        $spinner.html('<i class="fa fa-spinner fa-pulse"></i>').css({
            width: $targetElement.innerWidth() + 'px',
            height: $targetElement.innerHeight() + 'px',
            boxSizing: 'border-box',
            background: '#FFF',
            opacity: '0.8',
            position: 'absolute',
            top: $targetElement.offset().top,
            left: $targetElement.offset().left,
            fontSize: fontSize + 'px',
            color: '#002337', // primary color
            zIndex: zIndex
        }).appendTo('body');

        $spinner.find('i').css({
            position: 'absolute',
            left: $spinner.width() / 2 - fontSize / 2,
            top: $spinner.height() / 2 - fontSize / 2
        });

        instances.push($spinner);

        return $spinner;
    };

    /**
     * Hide an existing spinner.
     *
     * This method will hide and remove the loading spinner markup from the document entirely.
     *
     * @param {jQuery} $spinner Must be the selector provided from the "show" method. If the selector
     * is invalid or no elements were found then an exception will be thrown.
     *
     * @return {jQuery.Promise} Returns a promise object that will be resolved once the spinner is removed.
     *
     * @throws Error If the $spinner selector was not found in the spinner instances.
     */
    exports.hide = function ($spinner) {
        var index = instances.indexOf($spinner);
        var deferred = $.Deferred();

        if (index === -1) {
            throw new Error('The provided spinner instance does not exist.');
        }

        instances.splice(index, 1);

        $spinner.fadeOut(400, function () {
            $spinner.remove();
            deferred.resolve();
        });

        return deferred.promise();
    };
})(jse.libs.loading_spinner);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxvYWRpbmdfc3Bpbm5lci5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwibG9hZGluZ19zcGlubmVyIiwiZXhwb3J0cyIsImluc3RhbmNlcyIsInNob3ciLCIkdGFyZ2V0RWxlbWVudCIsInpJbmRleCIsInVuZGVmaW5lZCIsIkVycm9yIiwibGVuZ3RoIiwiJCIsIiRzcGlubmVyIiwiZm9udFNpemUiLCJodG1sIiwiY3NzIiwid2lkdGgiLCJpbm5lcldpZHRoIiwiaGVpZ2h0IiwiaW5uZXJIZWlnaHQiLCJib3hTaXppbmciLCJiYWNrZ3JvdW5kIiwib3BhY2l0eSIsInBvc2l0aW9uIiwidG9wIiwib2Zmc2V0IiwibGVmdCIsImNvbG9yIiwiYXBwZW5kVG8iLCJmaW5kIiwicHVzaCIsImhpZGUiLCJpbmRleCIsImluZGV4T2YiLCJkZWZlcnJlZCIsIkRlZmVycmVkIiwic3BsaWNlIiwiZmFkZU91dCIsInJlbW92ZSIsInJlc29sdmUiLCJwcm9taXNlIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxlQUFULEdBQTJCRixJQUFJQyxJQUFKLENBQVNDLGVBQVQsSUFBNEIsRUFBdkQ7O0FBRUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBMEJBLENBQUMsVUFBVUMsT0FBVixFQUFtQjs7QUFFaEI7O0FBRUE7Ozs7Ozs7QUFNQSxRQUFNQyxZQUFZLEVBQWxCOztBQUVBOzs7Ozs7Ozs7O0FBVUFELFlBQVFFLElBQVIsR0FBZSxVQUFVQyxjQUFWLEVBQTJDO0FBQUEsWUFBakJDLE1BQWlCLHVFQUFSLE1BQVE7O0FBQ3RELFlBQUlELG1CQUFtQkUsU0FBbkIsSUFBZ0MsUUFBT0YsY0FBUCx5Q0FBT0EsY0FBUCxPQUEwQixRQUE5RCxFQUF3RTtBQUNwRSxrQkFBTSxJQUFJRyxLQUFKLENBQVUsOERBQTZESCxjQUE3RCx5Q0FBNkRBLGNBQTdELEVBQVYsQ0FBTjtBQUNIOztBQUVELFlBQUlBLGVBQWVJLE1BQWYsS0FBMEIsQ0FBOUIsRUFBaUM7QUFDN0IsbUJBRDZCLENBQ3JCO0FBQ1g7O0FBRURKLHlCQUFpQkEsa0JBQWtCSyxFQUFFLE1BQUYsQ0FBbkMsQ0FUc0QsQ0FTUjs7QUFFOUMsWUFBTUMsV0FBV0QsRUFBRSxxQ0FBRixDQUFqQjtBQUNBLFlBQU1FLFdBQVcsRUFBakI7O0FBRUFELGlCQUNLRSxJQURMLENBQ1Usd0NBRFYsRUFFS0MsR0FGTCxDQUVTO0FBQ0RDLG1CQUFPVixlQUFlVyxVQUFmLEtBQThCLElBRHBDO0FBRURDLG9CQUFRWixlQUFlYSxXQUFmLEtBQStCLElBRnRDO0FBR0RDLHVCQUFXLFlBSFY7QUFJREMsd0JBQVksTUFKWDtBQUtEQyxxQkFBUyxLQUxSO0FBTURDLHNCQUFVLFVBTlQ7QUFPREMsaUJBQUtsQixlQUFlbUIsTUFBZixHQUF3QkQsR0FQNUI7QUFRREUsa0JBQU1wQixlQUFlbUIsTUFBZixHQUF3QkMsSUFSN0I7QUFTRGIsc0JBQVVBLFdBQVcsSUFUcEI7QUFVRGMsbUJBQU8sU0FWTixFQVVpQjtBQUNsQnBCLG9CQUFRQTtBQVhQLFNBRlQsRUFlS3FCLFFBZkwsQ0FlYyxNQWZkOztBQWlCQWhCLGlCQUFTaUIsSUFBVCxDQUFjLEdBQWQsRUFBbUJkLEdBQW5CLENBQXVCO0FBQ25CUSxzQkFBVSxVQURTO0FBRW5CRyxrQkFBTWQsU0FBU0ksS0FBVCxLQUFtQixDQUFuQixHQUF1QkgsV0FBVyxDQUZyQjtBQUduQlcsaUJBQUtaLFNBQVNNLE1BQVQsS0FBb0IsQ0FBcEIsR0FBd0JMLFdBQVc7QUFIckIsU0FBdkI7O0FBTUFULGtCQUFVMEIsSUFBVixDQUFlbEIsUUFBZjs7QUFFQSxlQUFPQSxRQUFQO0FBQ0gsS0F4Q0Q7O0FBMENBOzs7Ozs7Ozs7Ozs7QUFZQVQsWUFBUTRCLElBQVIsR0FBZSxVQUFVbkIsUUFBVixFQUFvQjtBQUMvQixZQUFNb0IsUUFBUTVCLFVBQVU2QixPQUFWLENBQWtCckIsUUFBbEIsQ0FBZDtBQUNBLFlBQU1zQixXQUFXdkIsRUFBRXdCLFFBQUYsRUFBakI7O0FBRUEsWUFBSUgsVUFBVSxDQUFDLENBQWYsRUFBa0I7QUFDZCxrQkFBTSxJQUFJdkIsS0FBSixDQUFVLCtDQUFWLENBQU47QUFDSDs7QUFFREwsa0JBQVVnQyxNQUFWLENBQWlCSixLQUFqQixFQUF3QixDQUF4Qjs7QUFFQXBCLGlCQUFTeUIsT0FBVCxDQUFpQixHQUFqQixFQUFzQixZQUFZO0FBQzlCekIscUJBQVMwQixNQUFUO0FBQ0FKLHFCQUFTSyxPQUFUO0FBQ0gsU0FIRDs7QUFLQSxlQUFPTCxTQUFTTSxPQUFULEVBQVA7QUFDSCxLQWhCRDtBQWtCSCxDQTlGRCxFQThGR3hDLElBQUlDLElBQUosQ0FBU0MsZUE5RloiLCJmaWxlIjoibG9hZGluZ19zcGlubmVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBsb2FkaW5nX3NwaW5uZXIuanMgMjAxNi0wNi0xNVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lciA9IGpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lciB8fCB7fTtcblxuLyoqXG4gKiAjIyBMb2FkaW5nIFNwaW5uZXIgTGlicmFyeVxuICpcbiAqIFRoaXMgbGlicmFyeSBwcm92aWRlcyBhbiBlYXN5IGFuZCBzaW1wbGUgd2F5IHRvIGRpc3BsYXkgYSBsb2FkaW5nIHNwaW5uZXIgaW5zaWRlIGFueSBjb250YWluZXJcbiAqIGVsZW1lbnQgdG8gcHJvdmlkZSBhIHNtb290aCBcImxvYWRpbmdcIiBleHBlcmllbmNlIHRvIHRoZSBVSS4gSWYgbm8gY29udGFpbmVyIGlzIHNwZWNpZmllZCB0aGVuXG4gKiB0aGUgd2hvbGUgcGFnZSB3aWxsIGJlIHRha2VuIGZvciB0aGUgZGlzcGxheS4gVGhlIGxvYWRpbmcgc3Bpbm5lciBjb21lcyBmcm9tIHRoZSBGb250IEF3ZXNvbWVcbiAqIFwiZmEtc3Bpbm5lclwiIGNsYXNzLiBZb3UgY2FuIGxvYWQgdGhpcyBsaWJyYXJ5IGFzIGEgZGVwZW5kZW5jeSB0byBleGlzdGluZyBtb2R1bGVzLlxuICpcbiAqIFRoZSBmb2xsb3dpbmcgdXNhZ2UgZXhhbXBsZSB3aWxsIHNob3cgeW91IGhvdyB0byBkaXNwbGF5IGFuZCBoaWRlIHRoZSBzcGlubmVyIGluc2lkZSBhbiBlbGVtZW50LlxuICpcbiAqIGBgYGphdmFzY3JpcHRcbiAqIC8vIENyZWF0ZSBhIHNlbGVjdG9yIHZhcmlhYmxlIGZvciB0aGUgdGFyZ2V0IGVsZW1lbnQuXG4gKiB2YXIgJHRhcmdldEVsZW1lbnQgPSAkKCcjbXktZGl2Jyk7XG4gKlxuICogLy8gVGhlICR0YXJnZXRFbGVtZW50IHdpbGwgYmUgb3ZlcmxheWVkIGJ5IHRoZSBzcGlubmVyLlxuICogdmFyICRzcGlubmVyID0gd2luZG93LmpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lci5zaG93KCR0YXJnZXRFbGVtZW50KTtcbiAqXG4gKiAvLyBEbyBzb21lIHN0dWZmIC4uLlxuICpcbiAqIC8vIEhpZGUgdGhlIHNwaW5uZXIgd2hlbiB0aGUgam9iIGlzIGRvbmUuXG4gKiB3aW5kb3cuanNlLmxvYWRpbmdfc3Bpbm5lci5oaWRlKCRzcGlubmVyKTtcbiAqIGBgYFxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvbG9hZGluZ19zcGlubmVyXG4gKiBAZXhwb3J0cyBqc2UubGlicy5sb2FkaW5nX3NwaW5uZXJcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICAvKipcbiAgICAgKiBDb250YWlucyBhIGxpc3Qgb2YgdGhlIGFjdGl2ZSBzcGlubmVycyBzbyB0aGF0IHRoZXkgY2FuIGJlIHZhbGlkYXRlZFxuICAgICAqIGJlZm9yZSB0aGV5IGFyZSBkZXN0cm95ZWQuXG4gICAgICpcbiAgICAgKiBAdHlwZSB7QXJyYXl9XG4gICAgICovXG4gICAgY29uc3QgaW5zdGFuY2VzID0gW107XG5cbiAgICAvKipcbiAgICAgKiBTaG93IHRoZSBsb2FkaW5nIHNwaW5uZXIgdG8gdGhlIHRhcmdldCBlbGVtZW50LlxuICAgICAqXG4gICAgICogQHBhcmFtIHtqUXVlcnl9ICR0YXJnZXRFbGVtZW50IChvcHRpb25hbCkgVGhlIHRhcmdldCBlbGVtZW50IHdpbGwgYmUgb3ZlcmxheWVkIGJ5IHRoZSBzcGlubmVyLiBJZiBub1xuICAgICAqIGFyZ3VtZW50IGlzIHByb3ZpZGVkIHRoZW4gdGhlIHNwaW5uZXIgd2lsbCBvdmVybGF5IHRoZSB3aG9sZSBwYWdlLlxuICAgICAqIEBwYXJhbSB7U3RyaW5nfSB6SW5kZXggT3B0aW9uYWwgKCdhdXRvJyksIGdpdmUgYSBzcGVjaWZpYyB6LWluZGV4IHZhbHVlIHRvIHRoZSBsb2FkaW5nIHNwaW5uZXIuXG4gICAgICpcbiAgICAgKiBAcmV0dXJuIHtqUXVlcnl9IFJldHVybnMgdGhlIHNlbGVjdG9yIG9mIHRoZSBzcGlubmVyIGRpdiBlbGVtZW50LiBZb3UgY2FuIGZ1cnRoZXIgbWFuaXB1bGF0ZSB0aGUgc3Bpbm5lclxuICAgICAqIGlmIHJlcXVpcmVkLCBidXQgeW91IGhhdmUgdG8gcHJvdmlkZSB0aGlzIHNlbGVjdG9yIGFzIGEgcGFyYW1ldGVyIHRvIHRoZSBcImhpZGVcIiBtZXRob2QgYmVsb3cuXG4gICAgICovXG4gICAgZXhwb3J0cy5zaG93ID0gZnVuY3Rpb24gKCR0YXJnZXRFbGVtZW50LCB6SW5kZXggPSAnYXV0bycpIHtcbiAgICAgICAgaWYgKCR0YXJnZXRFbGVtZW50ICE9PSB1bmRlZmluZWQgJiYgdHlwZW9mICR0YXJnZXRFbGVtZW50ICE9PSAnb2JqZWN0Jykge1xuICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKCdJbnZhbGlkIGFyZ3VtZW50IHByb3ZpZGVkIGZvciB0aGUgXCJzaG93XCIgbWV0aG9kOiAnICsgdHlwZW9mICR0YXJnZXRFbGVtZW50KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICgkdGFyZ2V0RWxlbWVudC5sZW5ndGggPT09IDApIHtcbiAgICAgICAgICAgIHJldHVybjsgLy8gTm8gZWxlbWVudCBtYXRjaGVzIHRoZSBwcm92aWRlZCBzZWxlY3Rvci4gXG4gICAgICAgIH1cblxuICAgICAgICAkdGFyZ2V0RWxlbWVudCA9ICR0YXJnZXRFbGVtZW50IHx8ICQoJ2JvZHknKTsgLy8gc2V0IGRlZmF1bHQgdmFsdWVcblxuICAgICAgICBjb25zdCAkc3Bpbm5lciA9ICQoJzxkaXYgY2xhc3M9XCJsb2FkaW5nLXNwaW5uZXJcIj48L2Rpdj4nKTtcbiAgICAgICAgY29uc3QgZm9udFNpemUgPSA4MDtcblxuICAgICAgICAkc3Bpbm5lclxuICAgICAgICAgICAgLmh0bWwoJzxpIGNsYXNzPVwiZmEgZmEtc3Bpbm5lciBmYS1wdWxzZVwiPjwvaT4nKVxuICAgICAgICAgICAgLmNzcyh7XG4gICAgICAgICAgICAgICAgd2lkdGg6ICR0YXJnZXRFbGVtZW50LmlubmVyV2lkdGgoKSArICdweCcsXG4gICAgICAgICAgICAgICAgaGVpZ2h0OiAkdGFyZ2V0RWxlbWVudC5pbm5lckhlaWdodCgpICsgJ3B4JyxcbiAgICAgICAgICAgICAgICBib3hTaXppbmc6ICdib3JkZXItYm94JyxcbiAgICAgICAgICAgICAgICBiYWNrZ3JvdW5kOiAnI0ZGRicsXG4gICAgICAgICAgICAgICAgb3BhY2l0eTogJzAuOCcsXG4gICAgICAgICAgICAgICAgcG9zaXRpb246ICdhYnNvbHV0ZScsXG4gICAgICAgICAgICAgICAgdG9wOiAkdGFyZ2V0RWxlbWVudC5vZmZzZXQoKS50b3AsXG4gICAgICAgICAgICAgICAgbGVmdDogJHRhcmdldEVsZW1lbnQub2Zmc2V0KCkubGVmdCxcbiAgICAgICAgICAgICAgICBmb250U2l6ZTogZm9udFNpemUgKyAncHgnLFxuICAgICAgICAgICAgICAgIGNvbG9yOiAnIzAwMjMzNycsIC8vIHByaW1hcnkgY29sb3JcbiAgICAgICAgICAgICAgICB6SW5kZXg6IHpJbmRleFxuICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIC5hcHBlbmRUbygnYm9keScpO1xuXG4gICAgICAgICRzcGlubmVyLmZpbmQoJ2knKS5jc3Moe1xuICAgICAgICAgICAgcG9zaXRpb246ICdhYnNvbHV0ZScsXG4gICAgICAgICAgICBsZWZ0OiAkc3Bpbm5lci53aWR0aCgpIC8gMiAtIGZvbnRTaXplIC8gMixcbiAgICAgICAgICAgIHRvcDogJHNwaW5uZXIuaGVpZ2h0KCkgLyAyIC0gZm9udFNpemUgLyAyXG4gICAgICAgIH0pO1xuXG4gICAgICAgIGluc3RhbmNlcy5wdXNoKCRzcGlubmVyKTtcblxuICAgICAgICByZXR1cm4gJHNwaW5uZXI7XG4gICAgfTtcblxuICAgIC8qKlxuICAgICAqIEhpZGUgYW4gZXhpc3Rpbmcgc3Bpbm5lci5cbiAgICAgKlxuICAgICAqIFRoaXMgbWV0aG9kIHdpbGwgaGlkZSBhbmQgcmVtb3ZlIHRoZSBsb2FkaW5nIHNwaW5uZXIgbWFya3VwIGZyb20gdGhlIGRvY3VtZW50IGVudGlyZWx5LlxuICAgICAqXG4gICAgICogQHBhcmFtIHtqUXVlcnl9ICRzcGlubmVyIE11c3QgYmUgdGhlIHNlbGVjdG9yIHByb3ZpZGVkIGZyb20gdGhlIFwic2hvd1wiIG1ldGhvZC4gSWYgdGhlIHNlbGVjdG9yXG4gICAgICogaXMgaW52YWxpZCBvciBubyBlbGVtZW50cyB3ZXJlIGZvdW5kIHRoZW4gYW4gZXhjZXB0aW9uIHdpbGwgYmUgdGhyb3duLlxuICAgICAqXG4gICAgICogQHJldHVybiB7alF1ZXJ5LlByb21pc2V9IFJldHVybnMgYSBwcm9taXNlIG9iamVjdCB0aGF0IHdpbGwgYmUgcmVzb2x2ZWQgb25jZSB0aGUgc3Bpbm5lciBpcyByZW1vdmVkLlxuICAgICAqXG4gICAgICogQHRocm93cyBFcnJvciBJZiB0aGUgJHNwaW5uZXIgc2VsZWN0b3Igd2FzIG5vdCBmb3VuZCBpbiB0aGUgc3Bpbm5lciBpbnN0YW5jZXMuXG4gICAgICovXG4gICAgZXhwb3J0cy5oaWRlID0gZnVuY3Rpb24gKCRzcGlubmVyKSB7XG4gICAgICAgIGNvbnN0IGluZGV4ID0gaW5zdGFuY2VzLmluZGV4T2YoJHNwaW5uZXIpO1xuICAgICAgICBjb25zdCBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblxuICAgICAgICBpZiAoaW5kZXggPT09IC0xKSB7XG4gICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoJ1RoZSBwcm92aWRlZCBzcGlubmVyIGluc3RhbmNlIGRvZXMgbm90IGV4aXN0LicpO1xuICAgICAgICB9XG5cbiAgICAgICAgaW5zdGFuY2VzLnNwbGljZShpbmRleCwgMSk7XG5cbiAgICAgICAgJHNwaW5uZXIuZmFkZU91dCg0MDAsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICRzcGlubmVyLnJlbW92ZSgpO1xuICAgICAgICAgICAgZGVmZXJyZWQucmVzb2x2ZSgpO1xuICAgICAgICB9KTtcblxuICAgICAgICByZXR1cm4gZGVmZXJyZWQucHJvbWlzZSgpO1xuICAgIH07XG5cbn0pKGpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lcik7XG4iXX0=

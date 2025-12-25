'use strict';

/* --------------------------------------------------------------
 interaction.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.theme.interaction = jse.libs.theme.interaction || {};

/**
 * ## Honeygrid Interaction Library
 *
 * Handles the theme interactions.
 *
 * @module Honeygrid/Libs/interaction
 * @exports jse.libs.theme.interaction
 */
(function (exports) {
    'use strict';

    var $body = $('body'),
        mousedown = false;

    /**
     * Returns the mousedown state.
     *
     * @param  {object} e Event
     * @return {boolean} True if mousedown is active
     */
    var _clickHandler = function _clickHandler(e) {
        mousedown = e.data.mousedown;
    };

    $body.on('mousedown', { mousedown: true }, _clickHandler).on('mouseup', { mousedown: false }, _clickHandler);

    /**
     * Returns true if a mouse button is clicked.
     *
     * @return {Boolean} Is the mouse clicked?
     */
    exports.isMouseDown = function () {
        return mousedown;
    };
})(jse.libs.theme.interaction);

jse.libs.template = jse.libs.template || {};
jse.libs.template.interaction = jse.libs.theme.interaction;
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxpYnMvaW50ZXJhY3Rpb24uanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInRoZW1lIiwiaW50ZXJhY3Rpb24iLCJleHBvcnRzIiwiJGJvZHkiLCIkIiwibW91c2Vkb3duIiwiX2NsaWNrSGFuZGxlciIsImUiLCJkYXRhIiwib24iLCJpc01vdXNlRG93biIsInRlbXBsYXRlIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsS0FBVCxDQUFlQyxXQUFmLEdBQTZCSCxJQUFJQyxJQUFKLENBQVNDLEtBQVQsQ0FBZUMsV0FBZixJQUE4QixFQUEzRDs7QUFFQTs7Ozs7Ozs7QUFRQyxXQUFVQyxPQUFWLEVBQW1CO0FBQ2hCOztBQUVBLFFBQUlDLFFBQVFDLEVBQUUsTUFBRixDQUFaO0FBQUEsUUFDSUMsWUFBWSxLQURoQjs7QUFHQTs7Ozs7O0FBTUEsUUFBSUMsZ0JBQWdCLFNBQWhCQSxhQUFnQixDQUFVQyxDQUFWLEVBQWE7QUFDN0JGLG9CQUFZRSxFQUFFQyxJQUFGLENBQU9ILFNBQW5CO0FBQ0gsS0FGRDs7QUFJQUYsVUFDS00sRUFETCxDQUNRLFdBRFIsRUFDcUIsRUFBQ0osV0FBVyxJQUFaLEVBRHJCLEVBQ3dDQyxhQUR4QyxFQUVLRyxFQUZMLENBRVEsU0FGUixFQUVtQixFQUFDSixXQUFXLEtBQVosRUFGbkIsRUFFdUNDLGFBRnZDOztBQUlBOzs7OztBQUtBSixZQUFRUSxXQUFSLEdBQXNCLFlBQVk7QUFDOUIsZUFBT0wsU0FBUDtBQUNILEtBRkQ7QUFJSCxDQTdCQSxFQTZCQ1AsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLFdBN0JoQixDQUFEOztBQStCQUgsSUFBSUMsSUFBSixDQUFTWSxRQUFULEdBQW9CYixJQUFJQyxJQUFKLENBQVNZLFFBQVQsSUFBcUIsRUFBekM7QUFDQWIsSUFBSUMsSUFBSixDQUFTWSxRQUFULENBQWtCVixXQUFsQixHQUFnQ0gsSUFBSUMsSUFBSixDQUFTQyxLQUFULENBQWVDLFdBQS9DIiwiZmlsZSI6ImxpYnMvaW50ZXJhY3Rpb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGludGVyYWN0aW9uLmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UubGlicy50aGVtZS5pbnRlcmFjdGlvbiA9IGpzZS5saWJzLnRoZW1lLmludGVyYWN0aW9uIHx8IHt9O1xuXG4vKipcbiAqICMjIEhvbmV5Z3JpZCBJbnRlcmFjdGlvbiBMaWJyYXJ5XG4gKlxuICogSGFuZGxlcyB0aGUgdGhlbWUgaW50ZXJhY3Rpb25zLlxuICpcbiAqIEBtb2R1bGUgSG9uZXlncmlkL0xpYnMvaW50ZXJhY3Rpb25cbiAqIEBleHBvcnRzIGpzZS5saWJzLnRoZW1lLmludGVyYWN0aW9uXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIHZhciAkYm9keSA9ICQoJ2JvZHknKSxcbiAgICAgICAgbW91c2Vkb3duID0gZmFsc2U7XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIHRoZSBtb3VzZWRvd24gc3RhdGUuXG4gICAgICpcbiAgICAgKiBAcGFyYW0gIHtvYmplY3R9IGUgRXZlbnRcbiAgICAgKiBAcmV0dXJuIHtib29sZWFufSBUcnVlIGlmIG1vdXNlZG93biBpcyBhY3RpdmVcbiAgICAgKi9cbiAgICB2YXIgX2NsaWNrSGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG4gICAgICAgIG1vdXNlZG93biA9IGUuZGF0YS5tb3VzZWRvd247XG4gICAgfTtcblxuICAgICRib2R5XG4gICAgICAgIC5vbignbW91c2Vkb3duJywge21vdXNlZG93bjogdHJ1ZX0sIF9jbGlja0hhbmRsZXIpXG4gICAgICAgIC5vbignbW91c2V1cCcsIHttb3VzZWRvd246IGZhbHNlfSwgX2NsaWNrSGFuZGxlcik7XG5cbiAgICAvKipcbiAgICAgKiBSZXR1cm5zIHRydWUgaWYgYSBtb3VzZSBidXR0b24gaXMgY2xpY2tlZC5cbiAgICAgKlxuICAgICAqIEByZXR1cm4ge0Jvb2xlYW59IElzIHRoZSBtb3VzZSBjbGlja2VkP1xuICAgICAqL1xuICAgIGV4cG9ydHMuaXNNb3VzZURvd24gPSBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHJldHVybiBtb3VzZWRvd247XG4gICAgfTtcblxufShqc2UubGlicy50aGVtZS5pbnRlcmFjdGlvbikpO1xuXG5qc2UubGlicy50ZW1wbGF0ZSA9IGpzZS5saWJzLnRlbXBsYXRlIHx8IHt9O1xuanNlLmxpYnMudGVtcGxhdGUuaW50ZXJhY3Rpb24gPSBqc2UubGlicy50aGVtZS5pbnRlcmFjdGlvbjtcbiJdfQ==

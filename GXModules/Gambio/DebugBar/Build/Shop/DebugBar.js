'use strict';

/* --------------------------------------------------------------
 DebugBar.js 2018-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Debug Bar JavaScript Enhancements
 *
 * This module will perform some adjustments to the default PHP Debug Bar instance.
 */
$(function () {
    'use strict';

    var $debugBar = $('.phpdebugbar');

    if (!$debugBar.length) {
        return; // The Debug Bar is not loaded on this page or could not be detected.
    }

    // Remove the message counter from "Help" section.
    var $help = $debugBar.find('.phpdebugbar-text').filter(function () {
        return $(this).text().indexOf('Help') !== -1;
    });

    if ($help.length) {
        $help.next().remove(); // Remove the counter element.		
    }

    // Remove unnecessary line break element added by the Debug Bar when minimized.
    $debugBar.on('click', '.phpdebugbar-close-btn', function () {
        $debugBar.next('br').remove();
    });

    $debugBar.on('click', '.phpdebugbar-restore-btn', function () {
        $('<br/>').insertAfter($debugBar);
    });

    if ($debugBar.hasClass('phpdebugbar-closed')) {
        $debugBar.next('br').remove();
    }

    // Correct initial display of Debug Bar in admin layout pages.
    if ($('aside#main-menu').length) {
        setTimeout(function () {
            phpdebugbar.resize();
        }, 2000);
    }

    // Set default initial Debug Bar state to minimized.
    phpdebugbar.minimize();
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIlNob3AvRGVidWdCYXIuanMiXSwibmFtZXMiOlsiJCIsIiRkZWJ1Z0JhciIsImxlbmd0aCIsIiRoZWxwIiwiZmluZCIsImZpbHRlciIsInRleHQiLCJpbmRleE9mIiwibmV4dCIsInJlbW92ZSIsIm9uIiwiaW5zZXJ0QWZ0ZXIiLCJoYXNDbGFzcyIsInNldFRpbWVvdXQiLCJwaHBkZWJ1Z2JhciIsInJlc2l6ZSIsIm1pbmltaXplIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLEVBQUUsWUFBWTtBQUNWOztBQUVBLFFBQUlDLFlBQVlELEVBQUUsY0FBRixDQUFoQjs7QUFFQSxRQUFJLENBQUNDLFVBQVVDLE1BQWYsRUFBdUI7QUFDbkIsZUFEbUIsQ0FDWDtBQUNYOztBQUVEO0FBQ0EsUUFBSUMsUUFBUUYsVUFBVUcsSUFBVixDQUFlLG1CQUFmLEVBQW9DQyxNQUFwQyxDQUEyQyxZQUFZO0FBQy9ELGVBQU9MLEVBQUUsSUFBRixFQUFRTSxJQUFSLEdBQWVDLE9BQWYsQ0FBdUIsTUFBdkIsTUFBbUMsQ0FBQyxDQUEzQztBQUNILEtBRlcsQ0FBWjs7QUFJQSxRQUFJSixNQUFNRCxNQUFWLEVBQWtCO0FBQ2RDLGNBQU1LLElBQU4sR0FBYUMsTUFBYixHQURjLENBQ1M7QUFDMUI7O0FBRUQ7QUFDQVIsY0FBVVMsRUFBVixDQUFhLE9BQWIsRUFBc0Isd0JBQXRCLEVBQWdELFlBQVk7QUFDeERULGtCQUFVTyxJQUFWLENBQWUsSUFBZixFQUFxQkMsTUFBckI7QUFDSCxLQUZEOztBQUlBUixjQUFVUyxFQUFWLENBQWEsT0FBYixFQUFzQiwwQkFBdEIsRUFBa0QsWUFBWTtBQUMxRFYsVUFBRSxPQUFGLEVBQVdXLFdBQVgsQ0FBdUJWLFNBQXZCO0FBQ0gsS0FGRDs7QUFJQSxRQUFJQSxVQUFVVyxRQUFWLENBQW1CLG9CQUFuQixDQUFKLEVBQThDO0FBQzFDWCxrQkFBVU8sSUFBVixDQUFlLElBQWYsRUFBcUJDLE1BQXJCO0FBQ0g7O0FBRUQ7QUFDQSxRQUFJVCxFQUFFLGlCQUFGLEVBQXFCRSxNQUF6QixFQUFpQztBQUM3QlcsbUJBQVcsWUFBWTtBQUNuQkMsd0JBQVlDLE1BQVo7QUFDSCxTQUZELEVBRUcsSUFGSDtBQUdIOztBQUVEO0FBQ0FELGdCQUFZRSxRQUFaO0FBQ0gsQ0F4Q0QiLCJmaWxlIjoiU2hvcC9EZWJ1Z0Jhci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gRGVidWdCYXIuanMgMjAxOC0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTggR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogRGVidWcgQmFyIEphdmFTY3JpcHQgRW5oYW5jZW1lbnRzXG4gKlxuICogVGhpcyBtb2R1bGUgd2lsbCBwZXJmb3JtIHNvbWUgYWRqdXN0bWVudHMgdG8gdGhlIGRlZmF1bHQgUEhQIERlYnVnIEJhciBpbnN0YW5jZS5cbiAqL1xuJChmdW5jdGlvbiAoKSB7XG4gICAgJ3VzZSBzdHJpY3QnO1xuXG4gICAgdmFyICRkZWJ1Z0JhciA9ICQoJy5waHBkZWJ1Z2JhcicpO1xuXG4gICAgaWYgKCEkZGVidWdCYXIubGVuZ3RoKSB7XG4gICAgICAgIHJldHVybjsgLy8gVGhlIERlYnVnIEJhciBpcyBub3QgbG9hZGVkIG9uIHRoaXMgcGFnZSBvciBjb3VsZCBub3QgYmUgZGV0ZWN0ZWQuXG4gICAgfVxuXG4gICAgLy8gUmVtb3ZlIHRoZSBtZXNzYWdlIGNvdW50ZXIgZnJvbSBcIkhlbHBcIiBzZWN0aW9uLlxuICAgIHZhciAkaGVscCA9ICRkZWJ1Z0Jhci5maW5kKCcucGhwZGVidWdiYXItdGV4dCcpLmZpbHRlcihmdW5jdGlvbiAoKSB7XG4gICAgICAgIHJldHVybiAkKHRoaXMpLnRleHQoKS5pbmRleE9mKCdIZWxwJykgIT09IC0xO1xuICAgIH0pO1xuXG4gICAgaWYgKCRoZWxwLmxlbmd0aCkge1xuICAgICAgICAkaGVscC5uZXh0KCkucmVtb3ZlKCk7IC8vIFJlbW92ZSB0aGUgY291bnRlciBlbGVtZW50Llx0XHRcbiAgICB9XG5cbiAgICAvLyBSZW1vdmUgdW5uZWNlc3NhcnkgbGluZSBicmVhayBlbGVtZW50IGFkZGVkIGJ5IHRoZSBEZWJ1ZyBCYXIgd2hlbiBtaW5pbWl6ZWQuXG4gICAgJGRlYnVnQmFyLm9uKCdjbGljaycsICcucGhwZGVidWdiYXItY2xvc2UtYnRuJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAkZGVidWdCYXIubmV4dCgnYnInKS5yZW1vdmUoKTtcbiAgICB9KTtcblxuICAgICRkZWJ1Z0Jhci5vbignY2xpY2snLCAnLnBocGRlYnVnYmFyLXJlc3RvcmUtYnRuJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAkKCc8YnIvPicpLmluc2VydEFmdGVyKCRkZWJ1Z0Jhcik7XG4gICAgfSk7XG5cbiAgICBpZiAoJGRlYnVnQmFyLmhhc0NsYXNzKCdwaHBkZWJ1Z2Jhci1jbG9zZWQnKSkge1xuICAgICAgICAkZGVidWdCYXIubmV4dCgnYnInKS5yZW1vdmUoKTtcbiAgICB9XG5cbiAgICAvLyBDb3JyZWN0IGluaXRpYWwgZGlzcGxheSBvZiBEZWJ1ZyBCYXIgaW4gYWRtaW4gbGF5b3V0IHBhZ2VzLlxuICAgIGlmICgkKCdhc2lkZSNtYWluLW1lbnUnKS5sZW5ndGgpIHtcbiAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBwaHBkZWJ1Z2Jhci5yZXNpemUoKTtcbiAgICAgICAgfSwgMjAwMCk7XG4gICAgfVxuXG4gICAgLy8gU2V0IGRlZmF1bHQgaW5pdGlhbCBEZWJ1ZyBCYXIgc3RhdGUgdG8gbWluaW1pemVkLlxuICAgIHBocGRlYnVnYmFyLm1pbmltaXplKCk7XG59KTsiXX0=

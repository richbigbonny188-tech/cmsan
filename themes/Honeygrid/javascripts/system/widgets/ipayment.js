'use strict';

/* --------------------------------------------------------------
	ipayment.js 2016-06-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

gambio.widgets.module('ipayment', [], function (data) {
    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     * @constructor
     */
    module.init = function (done) {
        $('#ipayment_form').submit();
        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaXBheW1lbnQuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJpbml0IiwiZG9uZSIsInN1Ym1pdCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FBc0IsVUFBdEIsRUFBa0MsRUFBbEMsRUFBc0MsVUFBVUMsSUFBVixFQUFnQjtBQUNsRDs7QUFFQTs7QUFDQSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFdBQVcsRUFEZjtBQUFBLFFBRUlDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBRmQ7QUFBQSxRQUdJRCxTQUFTLEVBSGI7O0FBS0E7O0FBRUE7Ozs7QUFJQUEsV0FBT08sSUFBUCxHQUFjLFVBQVVDLElBQVYsRUFBZ0I7QUFDMUJMLFVBQUUsZ0JBQUYsRUFBb0JNLE1BQXBCO0FBQ0FEO0FBQ0gsS0FIRDs7QUFLQTtBQUNBLFdBQU9SLE1BQVA7QUFDSCxDQXRCRCIsImZpbGUiOiJ3aWRnZXRzL2lwYXltZW50LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0aXBheW1lbnQuanMgMjAxNi0wNi0yN1xuXHRHYW1iaW8gR21iSFxuXHRodHRwOi8vd3d3LmdhbWJpby5kZVxuXHRDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcblx0UmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG5cdFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuXHQtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuKi9cblxuZ2FtYmlvLndpZGdldHMubW9kdWxlKCdpcGF5bWVudCcsIFtdLCBmdW5jdGlvbiAoZGF0YSkge1xuICAgICd1c2Ugc3RyaWN0JztcblxuICAgIC8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgIGRlZmF1bHRzID0ge30sXG4gICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICBtb2R1bGUgPSB7fTtcblxuICAgIC8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG4gICAgLyoqXG4gICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICogQGNvbnN0cnVjdG9yXG4gICAgICovXG4gICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAkKCcjaXBheW1lbnRfZm9ybScpLnN1Ym1pdCgpO1xuICAgICAgICBkb25lKCk7XG4gICAgfTtcblxuICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICByZXR1cm4gbW9kdWxlO1xufSk7XG4iXX0=

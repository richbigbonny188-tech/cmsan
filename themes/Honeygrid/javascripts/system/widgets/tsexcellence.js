'use strict';

/* --------------------------------------------------------------
	tsexcellence.js 2016-09-26
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

gambio.widgets.module('tsexcellence', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    module.init = function (done) {
        $('button#remove_tsbp').on('click', function (e) {
            e.preventDefault();
            $.ajax({
                "data": {
                    "remove_tsbp": "true"
                },
                "url": jse.core.config.get('appUrl') + '/request_port.php?module=TrustedShopsExcellence',
                "type": "POST"
            }).done(function (data) {
                window.location = window.location;
            });
        });
        $('button#add_tsbp').on('click', function (e) {
            e.preventDefault();
            $.ajax({
                "data": {
                    "add_tsbp": "true",
                    "amount": $("input[name=tsbp_amount]").val()
                },
                "url": jse.core.config.get('appUrl') + '/request_port.php?module=TrustedShopsExcellence',
                "type": "POST"
            }).done(function (data) {
                window.location = window.location;
            });
        });
        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvdHNleGNlbGxlbmNlLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCJvbiIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImFqYXgiLCJqc2UiLCJjb3JlIiwiY29uZmlnIiwiZ2V0Iiwid2luZG93IiwibG9jYXRpb24iLCJ2YWwiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLGNBQXRCLEVBQXNDLEVBQXRDLEVBQTBDLFVBQVVDLElBQVYsRUFBZ0I7O0FBRXREOztBQUVKOztBQUVJLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsV0FBVyxFQURmO0FBQUEsUUFFSUMsVUFBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FGZDtBQUFBLFFBR0lELFNBQVMsRUFIYjs7QUFLQUEsV0FBT08sSUFBUCxHQUFjLFVBQVVDLElBQVYsRUFBZ0I7QUFDMUJMLFVBQUUsb0JBQUYsRUFBd0JNLEVBQXhCLENBQTJCLE9BQTNCLEVBQW9DLFVBQVVDLENBQVYsRUFBYTtBQUM3Q0EsY0FBRUMsY0FBRjtBQUNBUixjQUFFUyxJQUFGLENBQU87QUFDSCx3QkFBUTtBQUNKLG1DQUFlO0FBRFgsaUJBREw7QUFJSCx1QkFBT0MsSUFBSUMsSUFBSixDQUFTQyxNQUFULENBQWdCQyxHQUFoQixDQUFvQixRQUFwQixJQUFnQyxpREFKcEM7QUFLSCx3QkFBUTtBQUxMLGFBQVAsRUFNR1IsSUFOSCxDQU1RLFVBQVVQLElBQVYsRUFBZ0I7QUFDcEJnQix1QkFBT0MsUUFBUCxHQUFrQkQsT0FBT0MsUUFBekI7QUFDSCxhQVJEO0FBU0gsU0FYRDtBQVlBZixVQUFFLGlCQUFGLEVBQXFCTSxFQUFyQixDQUF3QixPQUF4QixFQUFpQyxVQUFVQyxDQUFWLEVBQWE7QUFDMUNBLGNBQUVDLGNBQUY7QUFDQVIsY0FBRVMsSUFBRixDQUFPO0FBQ0gsd0JBQVE7QUFDSixnQ0FBWSxNQURSO0FBRUosOEJBQVVULEVBQUUseUJBQUYsRUFBNkJnQixHQUE3QjtBQUZOLGlCQURMO0FBS0gsdUJBQU9OLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0MsaURBTHBDO0FBTUgsd0JBQVE7QUFOTCxhQUFQLEVBT0dSLElBUEgsQ0FPUSxVQUFVUCxJQUFWLEVBQWdCO0FBQ3BCZ0IsdUJBQU9DLFFBQVAsR0FBa0JELE9BQU9DLFFBQXpCO0FBQ0gsYUFURDtBQVVILFNBWkQ7QUFhQVY7QUFDSCxLQTNCRDs7QUE2QkE7QUFDQSxXQUFPUixNQUFQO0FBQ0gsQ0ExQ0QiLCJmaWxlIjoid2lkZ2V0cy90c2V4Y2VsbGVuY2UuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHR0c2V4Y2VsbGVuY2UuanMgMjAxNi0wOS0yNlxuXHRHYW1iaW8gR21iSFxuXHRodHRwOi8vd3d3LmdhbWJpby5kZVxuXHRDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcblx0UmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG5cdFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuXHQtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuKi9cblxuZ2FtYmlvLndpZGdldHMubW9kdWxlKCd0c2V4Y2VsbGVuY2UnLCBbXSwgZnVuY3Rpb24gKGRhdGEpIHtcblxuICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICBkZWZhdWx0cyA9IHt9LFxuICAgICAgICBvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcbiAgICAgICAgbW9kdWxlID0ge307XG5cbiAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG4gICAgICAgICQoJ2J1dHRvbiNyZW1vdmVfdHNicCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIFwiZGF0YVwiOiB7XG4gICAgICAgICAgICAgICAgICAgIFwicmVtb3ZlX3RzYnBcIjogXCJ0cnVlXCIsXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICBcInVybFwiOiBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvcmVxdWVzdF9wb3J0LnBocD9tb2R1bGU9VHJ1c3RlZFNob3BzRXhjZWxsZW5jZScsXG4gICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiUE9TVFwiXG4gICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uIChkYXRhKSB7XG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uID0gd2luZG93LmxvY2F0aW9uO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgICAgICAkKCdidXR0b24jYWRkX3RzYnAnKS5vbignY2xpY2snLCBmdW5jdGlvbiAoZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICBcImRhdGFcIjoge1xuICAgICAgICAgICAgICAgICAgICBcImFkZF90c2JwXCI6IFwidHJ1ZVwiLFxuICAgICAgICAgICAgICAgICAgICBcImFtb3VudFwiOiAkKFwiaW5wdXRbbmFtZT10c2JwX2Ftb3VudF1cIikudmFsKClcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIFwidXJsXCI6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9yZXF1ZXN0X3BvcnQucGhwP21vZHVsZT1UcnVzdGVkU2hvcHNFeGNlbGxlbmNlJyxcbiAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJQT1NUXCJcbiAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24gKGRhdGEpIHtcbiAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24gPSB3aW5kb3cubG9jYXRpb247XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSk7XG4gICAgICAgIGRvbmUoKTtcbiAgICB9O1xuXG4gICAgLy8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuICAgIHJldHVybiBtb2R1bGU7XG59KTtcbiJdfQ==

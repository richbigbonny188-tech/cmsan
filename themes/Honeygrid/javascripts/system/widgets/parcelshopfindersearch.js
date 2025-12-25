'use strict';

/* --------------------------------------------------------------
 parcelshopfinderresult.js 2017-07-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module('parcelshopfindersearch', [], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ##########

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## INITIALIZATION ##########

    /**
     * Initialize Module
     * @constructor
     */
    module.init = function (done) {
        $('#psfnewsearch').on('click', function (e) {
            e.preventDefault();
            $('#psf-form').show();
            $(this).hide();
        });
        done();
    };

    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGFyY2Vsc2hvcGZpbmRlcnNlYXJjaC5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwib24iLCJlIiwicHJldmVudERlZmF1bHQiLCJzaG93IiwiaGlkZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDSSx3QkFESixFQUdJLEVBSEosRUFLSSxVQUFVQyxJQUFWLEVBQWdCOztBQUVaOztBQUVBOztBQUVBLFFBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsUUFDSUMsV0FBVyxFQURmO0FBQUEsUUFFSUMsVUFBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FGZDtBQUFBLFFBR0lELFNBQVMsRUFIYjs7QUFLQTs7QUFFQTs7OztBQUlBQSxXQUFPTyxJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUMxQkwsVUFBRSxlQUFGLEVBQW1CTSxFQUFuQixDQUFzQixPQUF0QixFQUErQixVQUFVQyxDQUFWLEVBQWE7QUFDeENBLGNBQUVDLGNBQUY7QUFDQVIsY0FBRSxXQUFGLEVBQWVTLElBQWY7QUFDQVQsY0FBRSxJQUFGLEVBQVFVLElBQVI7QUFDSCxTQUpEO0FBS0FMO0FBQ0gsS0FQRDs7QUFTQSxXQUFPUixNQUFQO0FBQ0gsQ0FoQ0wiLCJmaWxlIjoid2lkZ2V0cy9wYXJjZWxzaG9wZmluZGVyc2VhcmNoLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBwYXJjZWxzaG9wZmluZGVycmVzdWx0LmpzIDIwMTctMDctMDZcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ3BhcmNlbHNob3BmaW5kZXJzZWFyY2gnLFxuXG4gICAgW10sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuICAgICAgICAvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpLFxuICAgICAgICAgICAgZGVmYXVsdHMgPSB7fSxcbiAgICAgICAgICAgIG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuICAgICAgICAgICAgbW9kdWxlID0ge307XG5cbiAgICAgICAgLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEluaXRpYWxpemUgTW9kdWxlXG4gICAgICAgICAqIEBjb25zdHJ1Y3RvclxuICAgICAgICAgKi9cbiAgICAgICAgbW9kdWxlLmluaXQgPSBmdW5jdGlvbiAoZG9uZSkge1xuICAgICAgICAgICAgJCgnI3BzZm5ld3NlYXJjaCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICQoJyNwc2YtZm9ybScpLnNob3coKTtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmhpZGUoKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIHJldHVybiBtb2R1bGU7XG4gICAgfVxuKTtcbiJdfQ==

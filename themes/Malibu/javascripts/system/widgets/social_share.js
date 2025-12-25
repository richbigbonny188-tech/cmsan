'use strict';

/* --------------------------------------------------------------
 social_share.js 2017-07-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2017 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that enables the social sharing support
 *
 * (e.g.: Facebook, Twitter, Google+)
 *
 * {@link https://github.com/heiseonline/shariff}
 */
gambio.widgets.module('social_share', [jse.source + '/vendor/shariff/shariff.min.js', jse.source + '/vendor/shariff/shariff.min.css'], function (data) {

    'use strict';

    // ########## VARIABLE INITIALIZATION ########## 

    var $this = $(this),
        defaults = {},
        options = $.extend(true, {}, defaults, data),
        module = {};

    // ########## INITIALIZATION ##########

    /**
     * Init function of the widget
     */
    module.init = function (done) {
        $this.addClass('shariff');

        var config = {
            url: window.location.href,
            theme: 'standard',
            lang: jse.core.config.get('languageCode'),
            services: [],
            mediaUrl: []
        };

        if (options.facebook !== undefined) {
            config.services.push('facebook');
        }

        if (options.twitter !== undefined) {
            config.services.push('twitter');
        }

        if (options.pinterest !== undefined) {
            config.services.push('pinterest');
        }

        if (options.whatsapp !== undefined) {
            config.services.push('whatsapp');
        }

        new Shariff($this, config);

        done();
    };

    // Return data to widget engine
    return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc29jaWFsX3NoYXJlLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJqc2UiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCJhZGRDbGFzcyIsImNvbmZpZyIsInVybCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsInRoZW1lIiwibGFuZyIsImNvcmUiLCJnZXQiLCJzZXJ2aWNlcyIsIm1lZGlhVXJsIiwiZmFjZWJvb2siLCJ1bmRlZmluZWQiLCJwdXNoIiwidHdpdHRlciIsInBpbnRlcmVzdCIsIndoYXRzYXBwIiwiU2hhcmlmZiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNJLGNBREosRUFHSSxDQUNPQyxJQUFJQyxNQURYLHFDQUVPRCxJQUFJQyxNQUZYLHFDQUhKLEVBUUksVUFBVUMsSUFBVixFQUFnQjs7QUFFWjs7QUFFUjs7QUFFUSxRQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLFFBQ0lDLFdBQVcsRUFEZjtBQUFBLFFBRUlDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBRmQ7QUFBQSxRQUdJSCxTQUFTLEVBSGI7O0FBTVI7O0FBRVE7OztBQUdBQSxXQUFPUyxJQUFQLEdBQWMsVUFBVUMsSUFBVixFQUFnQjtBQUMxQk4sY0FBTU8sUUFBTixDQUFlLFNBQWY7O0FBRUEsWUFBSUMsU0FBUztBQUNUQyxpQkFBS0MsT0FBT0MsUUFBUCxDQUFnQkMsSUFEWjtBQUVUQyxtQkFBTyxVQUZFO0FBR1RDLGtCQUFNakIsSUFBSWtCLElBQUosQ0FBU1AsTUFBVCxDQUFnQlEsR0FBaEIsQ0FBb0IsY0FBcEIsQ0FIRztBQUlUQyxzQkFBVSxFQUpEO0FBS1RDLHNCQUFVO0FBTEQsU0FBYjs7QUFRQSxZQUFJZixRQUFRZ0IsUUFBUixLQUFxQkMsU0FBekIsRUFBb0M7QUFDaENaLG1CQUFPUyxRQUFQLENBQWdCSSxJQUFoQixDQUFxQixVQUFyQjtBQUNIOztBQUVELFlBQUlsQixRQUFRbUIsT0FBUixLQUFvQkYsU0FBeEIsRUFBbUM7QUFDL0JaLG1CQUFPUyxRQUFQLENBQWdCSSxJQUFoQixDQUFxQixTQUFyQjtBQUNIOztBQUVELFlBQUlsQixRQUFRb0IsU0FBUixLQUFzQkgsU0FBMUIsRUFBcUM7QUFDakNaLG1CQUFPUyxRQUFQLENBQWdCSSxJQUFoQixDQUFxQixXQUFyQjtBQUNIOztBQUVELFlBQUlsQixRQUFRcUIsUUFBUixLQUFxQkosU0FBekIsRUFBb0M7QUFDaENaLG1CQUFPUyxRQUFQLENBQWdCSSxJQUFoQixDQUFxQixVQUFyQjtBQUNIOztBQUVELFlBQUlJLE9BQUosQ0FBWXpCLEtBQVosRUFBbUJRLE1BQW5COztBQUVBRjtBQUNILEtBOUJEOztBQWdDQTtBQUNBLFdBQU9WLE1BQVA7QUFDSCxDQTNETCIsImZpbGUiOiJ3aWRnZXRzL3NvY2lhbF9zaGFyZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc29jaWFsX3NoYXJlLmpzIDIwMTctMDctMDVcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE3IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IGVuYWJsZXMgdGhlIHNvY2lhbCBzaGFyaW5nIHN1cHBvcnRcbiAqXG4gKiAoZS5nLjogRmFjZWJvb2ssIFR3aXR0ZXIsIEdvb2dsZSspXG4gKlxuICoge0BsaW5rIGh0dHBzOi8vZ2l0aHViLmNvbS9oZWlzZW9ubGluZS9zaGFyaWZmfVxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG4gICAgJ3NvY2lhbF9zaGFyZScsXG5cbiAgICBbXG4gICAgICAgIGAke2pzZS5zb3VyY2V9L3ZlbmRvci9zaGFyaWZmL3NoYXJpZmYubWluLmpzYCxcbiAgICAgICAgYCR7anNlLnNvdXJjZX0vdmVuZG9yL3NoYXJpZmYvc2hhcmlmZi5taW4uY3NzYFxuICAgIF0sXG5cbiAgICBmdW5jdGlvbiAoZGF0YSkge1xuXG4gICAgICAgICd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjIFxuXG4gICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyksXG4gICAgICAgICAgICBkZWZhdWx0cyA9IHt9LFxuICAgICAgICAgICAgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG4gICAgICAgICAgICBtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuICAgICAgICAvKipcbiAgICAgICAgICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG4gICAgICAgICAqL1xuICAgICAgICBtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG4gICAgICAgICAgICAkdGhpcy5hZGRDbGFzcygnc2hhcmlmZicpO1xuXG4gICAgICAgICAgICB2YXIgY29uZmlnID0ge1xuICAgICAgICAgICAgICAgIHVybDogd2luZG93LmxvY2F0aW9uLmhyZWYsXG4gICAgICAgICAgICAgICAgdGhlbWU6ICdzdGFuZGFyZCcsXG4gICAgICAgICAgICAgICAgbGFuZzoganNlLmNvcmUuY29uZmlnLmdldCgnbGFuZ3VhZ2VDb2RlJyksXG4gICAgICAgICAgICAgICAgc2VydmljZXM6IFtdLFxuICAgICAgICAgICAgICAgIG1lZGlhVXJsOiBbXVxuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgaWYgKG9wdGlvbnMuZmFjZWJvb2sgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIGNvbmZpZy5zZXJ2aWNlcy5wdXNoKCdmYWNlYm9vaycpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy50d2l0dGVyICE9PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICBjb25maWcuc2VydmljZXMucHVzaCgndHdpdHRlcicpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAob3B0aW9ucy5waW50ZXJlc3QgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIGNvbmZpZy5zZXJ2aWNlcy5wdXNoKCdwaW50ZXJlc3QnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKG9wdGlvbnMud2hhdHNhcHAgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgIGNvbmZpZy5zZXJ2aWNlcy5wdXNoKCd3aGF0c2FwcCcpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBuZXcgU2hhcmlmZigkdGhpcywgY29uZmlnKTtcblxuICAgICAgICAgICAgZG9uZSgpO1xuICAgICAgICB9O1xuXG4gICAgICAgIC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcbiAgICAgICAgcmV0dXJuIG1vZHVsZTtcbiAgICB9KTsiXX0=

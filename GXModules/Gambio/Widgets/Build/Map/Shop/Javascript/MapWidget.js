"use strict";

/* --------------------------------------------------------------
  MapWidget.js 2020-11-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/
(function () {
    "use strict";

    var googleScriptIsLoaded = false;
    var widgetsStack = [];
    var windowDocument = window.document.getElementById("shop-iframe-editor") || window.document;

    var initialize = function initialize(apiKey, widgetId, mapsConfig) {
        widgetsStack.push({
            apiKey: apiKey,
            widgetId: widgetId,
            mapsConfig: mapsConfig
        });

        if (hasScriptTag()) {
            initMap();
            return;
        }

        addGoogleMapsScript();
    };

    var hasScriptTag = function hasScriptTag() {
        return windowDocument.getElementById("map-widget-script") !== null;
    };

    var initMap = function initMap() {
        if (!googleScriptIsLoaded || !widgetsStack.length) {
            return;
        }

        while (widgetsStack.length) {
            var config = widgetsStack.pop();

            var widgetConfigJson = config.mapsConfig;
            var mapElement = windowDocument.getElementById(config.widgetId);

            var map = new google.maps.Map(mapElement, widgetConfigJson);
            var marker = new google.maps.Marker({ position: widgetConfigJson.center, map: map });
        }
    };

    var addGoogleMapsScript = function addGoogleMapsScript() {
        if (!hasScriptTag()) {
            var googleKey = widgetsStack[0].apiKey || "";
            var widgetScriptTag = windowDocument.createElement("script");

            widgetScriptTag.setAttribute("src", "https://maps.googleapis.com/maps/api/js?key=" + googleKey);
            widgetScriptTag.id = "map-widget-script";
            widgetScriptTag.onload = function () {
                googleScriptIsLoaded = true;
                initMap();
            };
            windowDocument.body.appendChild(widgetScriptTag);
        }
    };

    window.MapWidget = window.MapWidget || {};
    window.MapWidget = Object.assign({}, { initialize: initialize }, window.MapWidget);
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIk1hcC9TaG9wL0phdmFzY3JpcHQvTWFwV2lkZ2V0LmpzIl0sIm5hbWVzIjpbImdvb2dsZVNjcmlwdElzTG9hZGVkIiwid2lkZ2V0c1N0YWNrIiwid2luZG93RG9jdW1lbnQiLCJ3aW5kb3ciLCJkb2N1bWVudCIsImdldEVsZW1lbnRCeUlkIiwiaW5pdGlhbGl6ZSIsImFwaUtleSIsIndpZGdldElkIiwibWFwc0NvbmZpZyIsInB1c2giLCJoYXNTY3JpcHRUYWciLCJpbml0TWFwIiwiYWRkR29vZ2xlTWFwc1NjcmlwdCIsImxlbmd0aCIsImNvbmZpZyIsInBvcCIsIndpZGdldENvbmZpZ0pzb24iLCJtYXBFbGVtZW50IiwibWFwIiwiZ29vZ2xlIiwibWFwcyIsIk1hcCIsIm1hcmtlciIsIk1hcmtlciIsInBvc2l0aW9uIiwiY2VudGVyIiwiZ29vZ2xlS2V5Iiwid2lkZ2V0U2NyaXB0VGFnIiwiY3JlYXRlRWxlbWVudCIsInNldEF0dHJpYnV0ZSIsImlkIiwib25sb2FkIiwiYm9keSIsImFwcGVuZENoaWxkIiwiTWFwV2lkZ2V0IiwiT2JqZWN0IiwiYXNzaWduIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7OztBQVFBLENBQUMsWUFBWTtBQUNUOztBQUVBLFFBQUlBLHVCQUF1QixLQUEzQjtBQUNBLFFBQUlDLGVBQWUsRUFBbkI7QUFDQSxRQUFNQyxpQkFBaUJDLE9BQU9DLFFBQVAsQ0FBZ0JDLGNBQWhCLENBQStCLG9CQUEvQixLQUF3REYsT0FBT0MsUUFBdEY7O0FBRUEsUUFBTUUsYUFBYSxTQUFiQSxVQUFhLENBQUNDLE1BQUQsRUFBU0MsUUFBVCxFQUFtQkMsVUFBbkIsRUFBa0M7QUFDakRSLHFCQUFhUyxJQUFiLENBQWtCO0FBQ2RILDBCQURjO0FBRWRDLDhCQUZjO0FBR2RDO0FBSGMsU0FBbEI7O0FBTUEsWUFBSUUsY0FBSixFQUFvQjtBQUNoQkM7QUFDQTtBQUNIOztBQUVEQztBQUNILEtBYkQ7O0FBZUEsUUFBTUYsZUFBZSxTQUFmQSxZQUFlO0FBQUEsZUFBTVQsZUFBZUcsY0FBZixDQUE4QixtQkFBOUIsTUFBdUQsSUFBN0Q7QUFBQSxLQUFyQjs7QUFFQSxRQUFNTyxVQUFVLFNBQVZBLE9BQVUsR0FBTTtBQUNsQixZQUFJLENBQUNaLG9CQUFELElBQXlCLENBQUNDLGFBQWFhLE1BQTNDLEVBQW1EO0FBQy9DO0FBQ0g7O0FBRUQsZUFBT2IsYUFBYWEsTUFBcEIsRUFBNEI7QUFDeEIsZ0JBQU1DLFNBQVNkLGFBQWFlLEdBQWIsRUFBZjs7QUFFQSxnQkFBSUMsbUJBQW1CRixPQUFPTixVQUE5QjtBQUNBLGdCQUFJUyxhQUFhaEIsZUFBZUcsY0FBZixDQUE4QlUsT0FBT1AsUUFBckMsQ0FBakI7O0FBRUEsZ0JBQUlXLE1BQU0sSUFBSUMsT0FBT0MsSUFBUCxDQUFZQyxHQUFoQixDQUFvQkosVUFBcEIsRUFBZ0NELGdCQUFoQyxDQUFWO0FBQ0EsZ0JBQUlNLFNBQVMsSUFBSUgsT0FBT0MsSUFBUCxDQUFZRyxNQUFoQixDQUF1QixFQUFFQyxVQUFVUixpQkFBaUJTLE1BQTdCLEVBQXFDUCxLQUFLQSxHQUExQyxFQUF2QixDQUFiO0FBQ0g7QUFDSixLQWREOztBQWdCQSxRQUFNTixzQkFBc0IsU0FBdEJBLG1CQUFzQixHQUFNO0FBQzlCLFlBQUksQ0FBQ0YsY0FBTCxFQUFxQjtBQUNqQixnQkFBTWdCLFlBQVkxQixhQUFhLENBQWIsRUFBZ0JNLE1BQWhCLElBQTBCLEVBQTVDO0FBQ0EsZ0JBQUlxQixrQkFBa0IxQixlQUFlMkIsYUFBZixDQUE2QixRQUE3QixDQUF0Qjs7QUFFQUQsNEJBQWdCRSxZQUFoQixDQUE2QixLQUE3QixtREFBbUZILFNBQW5GO0FBQ0FDLDRCQUFnQkcsRUFBaEIsR0FBcUIsbUJBQXJCO0FBQ0FILDRCQUFnQkksTUFBaEIsR0FBeUIsWUFBTTtBQUMzQmhDLHVDQUF1QixJQUF2QjtBQUNBWTtBQUNILGFBSEQ7QUFJQVYsMkJBQWUrQixJQUFmLENBQW9CQyxXQUFwQixDQUFnQ04sZUFBaEM7QUFDSDtBQUNKLEtBYkQ7O0FBZUF6QixXQUFPZ0MsU0FBUCxHQUFtQmhDLE9BQU9nQyxTQUFQLElBQW9CLEVBQXZDO0FBQ0FoQyxXQUFPZ0MsU0FBUCxHQUFtQkMsT0FBT0MsTUFBUCxDQUFjLEVBQWQsRUFBa0IsRUFBRS9CLHNCQUFGLEVBQWxCLEVBQWtDSCxPQUFPZ0MsU0FBekMsQ0FBbkI7QUFDSCxDQXpERCIsImZpbGUiOiJNYXAvU2hvcC9KYXZhc2NyaXB0L01hcFdpZGdldC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gIE1hcFdpZGdldC5qcyAyMDIwLTExLTA2XG4gIEdhbWJpbyBHbWJIXG4gIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gIENvcHlyaWdodCAoYykgMjAyMCBHYW1iaW8gR21iSFxuICBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiAgW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tKi9cbihmdW5jdGlvbiAoKSB7XG4gICAgXCJ1c2Ugc3RyaWN0XCI7XG5cbiAgICBsZXQgZ29vZ2xlU2NyaXB0SXNMb2FkZWQgPSBmYWxzZTtcbiAgICBsZXQgd2lkZ2V0c1N0YWNrID0gW107XG4gICAgY29uc3Qgd2luZG93RG9jdW1lbnQgPSB3aW5kb3cuZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJzaG9wLWlmcmFtZS1lZGl0b3JcIikgfHwgd2luZG93LmRvY3VtZW50O1xuXG4gICAgY29uc3QgaW5pdGlhbGl6ZSA9IChhcGlLZXksIHdpZGdldElkLCBtYXBzQ29uZmlnKSA9PiB7XG4gICAgICAgIHdpZGdldHNTdGFjay5wdXNoKHtcbiAgICAgICAgICAgIGFwaUtleSxcbiAgICAgICAgICAgIHdpZGdldElkLFxuICAgICAgICAgICAgbWFwc0NvbmZpZyxcbiAgICAgICAgfSk7XG5cbiAgICAgICAgaWYgKGhhc1NjcmlwdFRhZygpKSB7XG4gICAgICAgICAgICBpbml0TWFwKCk7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBhZGRHb29nbGVNYXBzU2NyaXB0KCk7XG4gICAgfTtcblxuICAgIGNvbnN0IGhhc1NjcmlwdFRhZyA9ICgpID0+IHdpbmRvd0RvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwibWFwLXdpZGdldC1zY3JpcHRcIikgIT09IG51bGw7XG5cbiAgICBjb25zdCBpbml0TWFwID0gKCkgPT4ge1xuICAgICAgICBpZiAoIWdvb2dsZVNjcmlwdElzTG9hZGVkIHx8ICF3aWRnZXRzU3RhY2subGVuZ3RoKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICB3aGlsZSAod2lkZ2V0c1N0YWNrLmxlbmd0aCkge1xuICAgICAgICAgICAgY29uc3QgY29uZmlnID0gd2lkZ2V0c1N0YWNrLnBvcCgpO1xuXG4gICAgICAgICAgICBsZXQgd2lkZ2V0Q29uZmlnSnNvbiA9IGNvbmZpZy5tYXBzQ29uZmlnO1xuICAgICAgICAgICAgbGV0IG1hcEVsZW1lbnQgPSB3aW5kb3dEb2N1bWVudC5nZXRFbGVtZW50QnlJZChjb25maWcud2lkZ2V0SWQpO1xuXG4gICAgICAgICAgICBsZXQgbWFwID0gbmV3IGdvb2dsZS5tYXBzLk1hcChtYXBFbGVtZW50LCB3aWRnZXRDb25maWdKc29uKTtcbiAgICAgICAgICAgIGxldCBtYXJrZXIgPSBuZXcgZ29vZ2xlLm1hcHMuTWFya2VyKHsgcG9zaXRpb246IHdpZGdldENvbmZpZ0pzb24uY2VudGVyLCBtYXA6IG1hcCB9KTtcbiAgICAgICAgfVxuICAgIH07XG5cbiAgICBjb25zdCBhZGRHb29nbGVNYXBzU2NyaXB0ID0gKCkgPT4ge1xuICAgICAgICBpZiAoIWhhc1NjcmlwdFRhZygpKSB7XG4gICAgICAgICAgICBjb25zdCBnb29nbGVLZXkgPSB3aWRnZXRzU3RhY2tbMF0uYXBpS2V5IHx8IFwiXCI7XG4gICAgICAgICAgICBsZXQgd2lkZ2V0U2NyaXB0VGFnID0gd2luZG93RG9jdW1lbnQuY3JlYXRlRWxlbWVudChcInNjcmlwdFwiKTtcblxuICAgICAgICAgICAgd2lkZ2V0U2NyaXB0VGFnLnNldEF0dHJpYnV0ZShcInNyY1wiLCBgaHR0cHM6Ly9tYXBzLmdvb2dsZWFwaXMuY29tL21hcHMvYXBpL2pzP2tleT0ke2dvb2dsZUtleX1gKTtcbiAgICAgICAgICAgIHdpZGdldFNjcmlwdFRhZy5pZCA9IFwibWFwLXdpZGdldC1zY3JpcHRcIjtcbiAgICAgICAgICAgIHdpZGdldFNjcmlwdFRhZy5vbmxvYWQgPSAoKSA9PiB7XG4gICAgICAgICAgICAgICAgZ29vZ2xlU2NyaXB0SXNMb2FkZWQgPSB0cnVlO1xuICAgICAgICAgICAgICAgIGluaXRNYXAoKTtcbiAgICAgICAgICAgIH07XG4gICAgICAgICAgICB3aW5kb3dEb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHdpZGdldFNjcmlwdFRhZyk7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgd2luZG93Lk1hcFdpZGdldCA9IHdpbmRvdy5NYXBXaWRnZXQgfHwge307XG4gICAgd2luZG93Lk1hcFdpZGdldCA9IE9iamVjdC5hc3NpZ24oe30sIHsgaW5pdGlhbGl6ZSB9LCB3aW5kb3cuTWFwV2lkZ2V0KTtcbn0pKCk7XG4iXX0=

'use strict';

/* --------------------------------------------------------------
   gambio_store_updates_badge.js 2023-01-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function checkForStoreUpdates() {
    var gambioStoreMenuElement = document.querySelector('.brand-alt.type-standalone');

    if (gambioStoreMenuElement === null) {
        return;
    }

    var jsElement = document.getElementById('gambio-store-updates-badge-js');

    fetch(jsElement.dataset.shopUrl + 'admin/admin.php?do=MenuBadge/AvailableUpdatesCount').then(function (response) {
        return response.json();
    }).then(function (data) {
        if (!data.availableUpdatesCount) {
            return;
        }

        var styleSheet = document.createElement('link');
        styleSheet.setAttribute('rel', 'stylesheet');
        styleSheet.setAttribute('type', 'text/css');
        styleSheet.setAttribute('href', jsElement.dataset.shopUrl + 'GXModules/Gambio/MenuBadge/Build/Admin/Styles/gambio_store_updates_badge.min.css');
        if (jsElement.hasAttribute('data-devmode')) {
            styleSheet.href = jsElement.dataset.shopUrl + 'GXModules/Gambio/MenuBadge/Build/Admin/Styles/gambio_store_updates_badge.css';
        }

        styleSheet.onload = function () {
            var updatesAvailableElement = document.createElement('div');

            gambioStoreMenuElement.classList.add('gambio_store_updates_badge_container');
            updatesAvailableElement.classList.add('gambio_store_updates_badge');
            updatesAvailableElement.innerText = data.availableUpdatesCount;
            gambioStoreMenuElement.appendChild(updatesAvailableElement);
        };

        document.head.appendChild(styleSheet);
    });
}

if (document.readyState !== 'loading') {
    checkForStoreUpdates();
} else {
    document.addEventListener('DOMContentLoaded', checkForStoreUpdates);
}
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvZ2FtYmlvX3N0b3JlX3VwZGF0ZXNfYmFkZ2UuanMiXSwibmFtZXMiOlsiY2hlY2tGb3JTdG9yZVVwZGF0ZXMiLCJnYW1iaW9TdG9yZU1lbnVFbGVtZW50IiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwianNFbGVtZW50IiwiZ2V0RWxlbWVudEJ5SWQiLCJmZXRjaCIsImRhdGFzZXQiLCJzaG9wVXJsIiwidGhlbiIsInJlc3BvbnNlIiwianNvbiIsImRhdGEiLCJhdmFpbGFibGVVcGRhdGVzQ291bnQiLCJzdHlsZVNoZWV0IiwiY3JlYXRlRWxlbWVudCIsInNldEF0dHJpYnV0ZSIsImhhc0F0dHJpYnV0ZSIsImhyZWYiLCJvbmxvYWQiLCJ1cGRhdGVzQXZhaWxhYmxlRWxlbWVudCIsImNsYXNzTGlzdCIsImFkZCIsImlubmVyVGV4dCIsImFwcGVuZENoaWxkIiwiaGVhZCIsInJlYWR5U3RhdGUiLCJhZGRFdmVudExpc3RlbmVyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUEsU0FBU0Esb0JBQVQsR0FBZ0M7QUFDNUIsUUFBTUMseUJBQXlCQyxTQUFTQyxhQUFULENBQXVCLDRCQUF2QixDQUEvQjs7QUFFQSxRQUFJRiwyQkFBMkIsSUFBL0IsRUFBcUM7QUFDakM7QUFDSDs7QUFFRCxRQUFNRyxZQUFZRixTQUFTRyxjQUFULENBQXdCLCtCQUF4QixDQUFsQjs7QUFFQUMsVUFBTUYsVUFBVUcsT0FBVixDQUFrQkMsT0FBbEIsR0FBNEIsb0RBQWxDLEVBQ0tDLElBREwsQ0FDVTtBQUFBLGVBQVlDLFNBQVNDLElBQVQsRUFBWjtBQUFBLEtBRFYsRUFFS0YsSUFGTCxDQUVVLGdCQUFRO0FBQ1YsWUFBSSxDQUFDRyxLQUFLQyxxQkFBVixFQUFpQztBQUM3QjtBQUNIOztBQUVELFlBQU1DLGFBQWFaLFNBQVNhLGFBQVQsQ0FBdUIsTUFBdkIsQ0FBbkI7QUFDQUQsbUJBQVdFLFlBQVgsQ0FBd0IsS0FBeEIsRUFBK0IsWUFBL0I7QUFDQUYsbUJBQVdFLFlBQVgsQ0FBd0IsTUFBeEIsRUFBZ0MsVUFBaEM7QUFDQUYsbUJBQVdFLFlBQVgsQ0FBd0IsTUFBeEIsRUFBZ0NaLFVBQVVHLE9BQVYsQ0FBa0JDLE9BQWxCLEdBQTRCLGtGQUE1RDtBQUNBLFlBQUlKLFVBQVVhLFlBQVYsQ0FBdUIsY0FBdkIsQ0FBSixFQUE0QztBQUN4Q0gsdUJBQVdJLElBQVgsR0FBa0JkLFVBQVVHLE9BQVYsQ0FBa0JDLE9BQWxCLEdBQTRCLDhFQUE5QztBQUNIOztBQUVETSxtQkFBV0ssTUFBWCxHQUFvQixZQUFXO0FBQzNCLGdCQUFNQywwQkFBMEJsQixTQUFTYSxhQUFULENBQXVCLEtBQXZCLENBQWhDOztBQUVBZCxtQ0FBdUJvQixTQUF2QixDQUFpQ0MsR0FBakMsQ0FBcUMsc0NBQXJDO0FBQ0FGLG9DQUF3QkMsU0FBeEIsQ0FBa0NDLEdBQWxDLENBQXNDLDRCQUF0QztBQUNBRixvQ0FBd0JHLFNBQXhCLEdBQW9DWCxLQUFLQyxxQkFBekM7QUFDQVosbUNBQXVCdUIsV0FBdkIsQ0FBbUNKLHVCQUFuQztBQUNILFNBUEQ7O0FBU0FsQixpQkFBU3VCLElBQVQsQ0FBY0QsV0FBZCxDQUEwQlYsVUFBMUI7QUFDSCxLQXpCTDtBQTBCSDs7QUFFRCxJQUFJWixTQUFTd0IsVUFBVCxLQUF3QixTQUE1QixFQUF1QztBQUNuQzFCO0FBQ0gsQ0FGRCxNQUVPO0FBQ0hFLGFBQVN5QixnQkFBVCxDQUEwQixrQkFBMUIsRUFBOEMzQixvQkFBOUM7QUFDSCIsImZpbGUiOiJBZG1pbi9KYXZhc2NyaXB0L2dhbWJpb19zdG9yZV91cGRhdGVzX2JhZGdlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgIGdhbWJpb19zdG9yZV91cGRhdGVzX2JhZGdlLmpzIDIwMjMtMDEtMjRcbiAgIEdhbWJpbyBHbWJIXG4gICBodHRwOi8vd3d3LmdhbWJpby5kZVxuICAgQ29weXJpZ2h0IChjKSAyMDIzIEdhbWJpbyBHbWJIXG4gICBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiAgIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuICAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiovXG5cbmZ1bmN0aW9uIGNoZWNrRm9yU3RvcmVVcGRhdGVzKCkge1xuICAgIGNvbnN0IGdhbWJpb1N0b3JlTWVudUVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcuYnJhbmQtYWx0LnR5cGUtc3RhbmRhbG9uZScpO1xuICAgIFxuICAgIGlmIChnYW1iaW9TdG9yZU1lbnVFbGVtZW50ID09PSBudWxsKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG4gICAgXG4gICAgY29uc3QganNFbGVtZW50ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2dhbWJpby1zdG9yZS11cGRhdGVzLWJhZGdlLWpzJyk7XG4gICAgXG4gICAgZmV0Y2goanNFbGVtZW50LmRhdGFzZXQuc2hvcFVybCArICdhZG1pbi9hZG1pbi5waHA/ZG89TWVudUJhZGdlL0F2YWlsYWJsZVVwZGF0ZXNDb3VudCcpXG4gICAgICAgIC50aGVuKHJlc3BvbnNlID0+IHJlc3BvbnNlLmpzb24oKSlcbiAgICAgICAgLnRoZW4oZGF0YSA9PiB7XG4gICAgICAgICAgICBpZiAoIWRhdGEuYXZhaWxhYmxlVXBkYXRlc0NvdW50KSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgXG4gICAgICAgICAgICBjb25zdCBzdHlsZVNoZWV0ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnbGluaycpO1xuICAgICAgICAgICAgc3R5bGVTaGVldC5zZXRBdHRyaWJ1dGUoJ3JlbCcsICdzdHlsZXNoZWV0Jyk7XG4gICAgICAgICAgICBzdHlsZVNoZWV0LnNldEF0dHJpYnV0ZSgndHlwZScsICd0ZXh0L2NzcycpO1xuICAgICAgICAgICAgc3R5bGVTaGVldC5zZXRBdHRyaWJ1dGUoJ2hyZWYnLCBqc0VsZW1lbnQuZGF0YXNldC5zaG9wVXJsICsgJ0dYTW9kdWxlcy9HYW1iaW8vTWVudUJhZGdlL0J1aWxkL0FkbWluL1N0eWxlcy9nYW1iaW9fc3RvcmVfdXBkYXRlc19iYWRnZS5taW4uY3NzJyk7XG4gICAgICAgICAgICBpZiAoanNFbGVtZW50Lmhhc0F0dHJpYnV0ZSgnZGF0YS1kZXZtb2RlJykpIHtcbiAgICAgICAgICAgICAgICBzdHlsZVNoZWV0LmhyZWYgPSBqc0VsZW1lbnQuZGF0YXNldC5zaG9wVXJsICsgJ0dYTW9kdWxlcy9HYW1iaW8vTWVudUJhZGdlL0J1aWxkL0FkbWluL1N0eWxlcy9nYW1iaW9fc3RvcmVfdXBkYXRlc19iYWRnZS5jc3MnO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgXG4gICAgICAgICAgICBzdHlsZVNoZWV0Lm9ubG9hZCA9IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGNvbnN0IHVwZGF0ZXNBdmFpbGFibGVFbGVtZW50ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG4gICAgXG4gICAgICAgICAgICAgICAgZ2FtYmlvU3RvcmVNZW51RWxlbWVudC5jbGFzc0xpc3QuYWRkKCdnYW1iaW9fc3RvcmVfdXBkYXRlc19iYWRnZV9jb250YWluZXInKTtcbiAgICAgICAgICAgICAgICB1cGRhdGVzQXZhaWxhYmxlRWxlbWVudC5jbGFzc0xpc3QuYWRkKCdnYW1iaW9fc3RvcmVfdXBkYXRlc19iYWRnZScpO1xuICAgICAgICAgICAgICAgIHVwZGF0ZXNBdmFpbGFibGVFbGVtZW50LmlubmVyVGV4dCA9IGRhdGEuYXZhaWxhYmxlVXBkYXRlc0NvdW50O1xuICAgICAgICAgICAgICAgIGdhbWJpb1N0b3JlTWVudUVsZW1lbnQuYXBwZW5kQ2hpbGQodXBkYXRlc0F2YWlsYWJsZUVsZW1lbnQpO1xuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgZG9jdW1lbnQuaGVhZC5hcHBlbmRDaGlsZChzdHlsZVNoZWV0KTtcbiAgICAgICAgfSlcbn1cblxuaWYgKGRvY3VtZW50LnJlYWR5U3RhdGUgIT09ICdsb2FkaW5nJykge1xuICAgIGNoZWNrRm9yU3RvcmVVcGRhdGVzKCk7XG59IGVsc2Uge1xuICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCBjaGVja0ZvclN0b3JlVXBkYXRlcyk7XG59XG4iXX0=

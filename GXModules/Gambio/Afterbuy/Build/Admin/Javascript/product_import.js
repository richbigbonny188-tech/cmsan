'use strict';

/* --------------------------------------------------------------
   product_import.js 2023-03-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

(function () {
    var configuration = {};
    var go = true;
    var stopButton = document.querySelector('#btn_stop_products_import');
    var lastImportDatetime = document.querySelector('#last_import_datetime');

    var doStep = function doStep(step) {
        var messageElement = document.querySelector('#ab_import_message');
        var progressBar = document.querySelector('#ab_import_progress_bar');

        return window.fetch(configuration.importStepUrl + '&step=' + step).then(function (response) {
            return response.json();
        }).then(function (data) {
            messageElement.textContent = data.message;
            progressBar.style.width = 100 * parseFloat(data.progress) + '%';
            if (data.lastimportdatetime !== '') {
                lastImportDatetime.value = data.lastimportdatetime;
            }
            if (data.progress < 1 && go) {
                return doStep(step + 1);
            } else {
                if (data.progress >= 1) {
                    lastImportDatetime.removeAttribute('disabled');
                    lastImportDatetime.parentElement.classList.remove('running');
                    lastImportDatetime.parentElement.classList.add('notrunning');
                }
            }
        });
    };

    var goButtonClick = function goButtonClick(event) {
        var backdrop = document.querySelector('#ab_import_progress_backdrop');
        var body = document.querySelector('body');
        event.preventDefault();

        go = true;
        stopButton.removeAttribute('disabled');
        lastImportDatetime.setAttribute('disabled', 'disabled');
        lastImportDatetime.parentElement.classList.remove('notrunning');
        lastImportDatetime.parentElement.classList.add('running');
        backdrop.remove();
        body.prepend(backdrop);
        backdrop.style.display = 'flex';

        doStep(0).then(function () {
            var messageElement = document.querySelector('#ab_import_message');
            //messageElement.textContent = 'done.';
            setTimeout(function () {
                backdrop.style.display = 'none';
            }, 5000);
        });
    };

    var mainCallback = function mainCallback() {
        var goButton = document.querySelector('#btn_start_products_import');
        var configJson = document.querySelector('#product_import_config');
        if (goButton === null || configJson === null) {
            return;
        }
        configuration = JSON.parse(configJson.textContent);
        goButton.addEventListener('click', goButtonClick);
        if (stopButton !== null) {
            stopButton.addEventListener('click', function (event) {
                event.preventDefault();
                go = false;
                stopButton.setAttribute('disabled', 'disabled');
            });
        }
    };

    if (document.readyState === 'complete' || document.readyState !== 'loading' && !document.documentElement.doScroll) {
        mainCallback();
    } else {
        document.addEventListener('DOMContentLoaded', mainCallback);
    }
})();
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIkFkbWluL0phdmFzY3JpcHQvcHJvZHVjdF9pbXBvcnQuanMiXSwibmFtZXMiOlsiY29uZmlndXJhdGlvbiIsImdvIiwic3RvcEJ1dHRvbiIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsImxhc3RJbXBvcnREYXRldGltZSIsImRvU3RlcCIsInN0ZXAiLCJtZXNzYWdlRWxlbWVudCIsInByb2dyZXNzQmFyIiwid2luZG93IiwiZmV0Y2giLCJpbXBvcnRTdGVwVXJsIiwidGhlbiIsInJlc3BvbnNlIiwianNvbiIsImRhdGEiLCJ0ZXh0Q29udGVudCIsIm1lc3NhZ2UiLCJzdHlsZSIsIndpZHRoIiwicGFyc2VGbG9hdCIsInByb2dyZXNzIiwibGFzdGltcG9ydGRhdGV0aW1lIiwidmFsdWUiLCJyZW1vdmVBdHRyaWJ1dGUiLCJwYXJlbnRFbGVtZW50IiwiY2xhc3NMaXN0IiwicmVtb3ZlIiwiYWRkIiwiZ29CdXR0b25DbGljayIsImV2ZW50IiwiYmFja2Ryb3AiLCJib2R5IiwicHJldmVudERlZmF1bHQiLCJzZXRBdHRyaWJ1dGUiLCJwcmVwZW5kIiwiZGlzcGxheSIsInNldFRpbWVvdXQiLCJtYWluQ2FsbGJhY2siLCJnb0J1dHRvbiIsImNvbmZpZ0pzb24iLCJKU09OIiwicGFyc2UiLCJhZGRFdmVudExpc3RlbmVyIiwicmVhZHlTdGF0ZSIsImRvY3VtZW50RWxlbWVudCIsImRvU2Nyb2xsIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUMsYUFBVztBQUNSLFFBQUlBLGdCQUFnQixFQUFwQjtBQUNBLFFBQUlDLEtBQUssSUFBVDtBQUNBLFFBQUlDLGFBQWFDLFNBQVNDLGFBQVQsQ0FBdUIsMkJBQXZCLENBQWpCO0FBQ0EsUUFBSUMscUJBQXFCRixTQUFTQyxhQUFULENBQXVCLHVCQUF2QixDQUF6Qjs7QUFFQSxRQUFJRSxTQUFTLFNBQVRBLE1BQVMsQ0FBU0MsSUFBVCxFQUFlO0FBQ3hCLFlBQUlDLGlCQUFpQkwsU0FBU0MsYUFBVCxDQUF1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QU1HLGNBQU47QUFDQWpDLHFCQUFLLEtBQUw7QUFDQUMsMkJBQVdpQyxZQUFYLENBQXdCLFVBQXhCLEVBQW9DLFVBQXBDO0FBQ0gsYUFKRDtBQUtIO0FBQ0osS0FmRDs7QUFpQkEsUUFBSWhDLFNBQVMwQyxVQUFULEtBQXdCLFVBQXhCLElBQXVDMUMsU0FBUzBDLFVBQVQsS0FBd0IsU0FBeEIsSUFDcEMsQ0FBQzFDLFNBQVMyQyxlQUFULENBQXlCQyxRQURqQyxFQUM0QztBQUN4Q1I7QUFDSCxLQUhELE1BR087QUFDSHBDLGlCQUFTeUMsZ0JBQVQsQ0FBMEIsa0JBQTFCLEVBQThDTCxZQUE5QztBQUNIO0FBQ0osQ0E3RUEsR0FBRCIsImZpbGUiOiJBZG1pbi9KYXZhc2NyaXB0L3Byb2R1Y3RfaW1wb3J0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAgIHByb2R1Y3RfaW1wb3J0LmpzIDIwMjMtMDMtMjhcbiAgIEdhbWJpbyBHbWJIXG4gICBodHRwOi8vd3d3LmdhbWJpby5kZVxuICAgQ29weXJpZ2h0IChjKSAyMDIzIEdhbWJpbyBHbWJIXG4gICBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiAgIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuICAgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiovXG5cbihmdW5jdGlvbigpIHtcbiAgICBsZXQgY29uZmlndXJhdGlvbiA9IHt9O1xuICAgIGxldCBnbyA9IHRydWU7XG4gICAgbGV0IHN0b3BCdXR0b24gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjYnRuX3N0b3BfcHJvZHVjdHNfaW1wb3J0Jyk7XG4gICAgbGV0IGxhc3RJbXBvcnREYXRldGltZSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNsYXN0X2ltcG9ydF9kYXRldGltZScpO1xuICAgIFxuICAgIGxldCBkb1N0ZXAgPSBmdW5jdGlvbihzdGVwKSB7XG4gICAgICAgIGxldCBtZXNzYWdlRWxlbWVudCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNhYl9pbXBvcnRfbWVzc2FnZScpO1xuICAgICAgICBsZXQgcHJvZ3Jlc3NCYXIgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjYWJfaW1wb3J0X3Byb2dyZXNzX2JhcicpO1xuICAgICAgICBcbiAgICAgICAgcmV0dXJuIHdpbmRvdy5mZXRjaChjb25maWd1cmF0aW9uLmltcG9ydFN0ZXBVcmwgKyAnJnN0ZXA9JyArIHN0ZXApXG4gICAgICAgICAgICAudGhlbihyZXNwb25zZSA9PiByZXNwb25zZS5qc29uKCkpXG4gICAgICAgICAgICAudGhlbihmdW5jdGlvbihkYXRhKSB7XG4gICAgICAgICAgICAgICAgbWVzc2FnZUVsZW1lbnQudGV4dENvbnRlbnQgPSBkYXRhLm1lc3NhZ2U7XG4gICAgICAgICAgICAgICAgcHJvZ3Jlc3NCYXIuc3R5bGUud2lkdGggPSAoMTAwICogcGFyc2VGbG9hdChkYXRhLnByb2dyZXNzKSkgKyAnJSc7XG4gICAgICAgICAgICAgICAgaWYgKGRhdGEubGFzdGltcG9ydGRhdGV0aW1lICE9PSAnJykge1xuICAgICAgICAgICAgICAgICAgICBsYXN0SW1wb3J0RGF0ZXRpbWUudmFsdWUgPSBkYXRhLmxhc3RpbXBvcnRkYXRldGltZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaWYgKGRhdGEucHJvZ3Jlc3MgPCAxICYmIGdvKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBkb1N0ZXAoc3RlcCArIDEpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChkYXRhLnByb2dyZXNzID49IDEpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhc3RJbXBvcnREYXRldGltZS5yZW1vdmVBdHRyaWJ1dGUoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBsYXN0SW1wb3J0RGF0ZXRpbWUucGFyZW50RWxlbWVudC5jbGFzc0xpc3QucmVtb3ZlKCdydW5uaW5nJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICBsYXN0SW1wb3J0RGF0ZXRpbWUucGFyZW50RWxlbWVudC5jbGFzc0xpc3QuYWRkKCdub3RydW5uaW5nJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgXG4gICAgfVxuICAgIFxuICAgIGxldCBnb0J1dHRvbkNsaWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgbGV0IGJhY2tkcm9wID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI2FiX2ltcG9ydF9wcm9ncmVzc19iYWNrZHJvcCcpO1xuICAgICAgICBsZXQgYm9keSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2JvZHknKTtcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICBnbyA9IHRydWU7XG4gICAgICAgIHN0b3BCdXR0b24ucmVtb3ZlQXR0cmlidXRlKCdkaXNhYmxlZCcpO1xuICAgICAgICBsYXN0SW1wb3J0RGF0ZXRpbWUuc2V0QXR0cmlidXRlKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICAgICAgICBsYXN0SW1wb3J0RGF0ZXRpbWUucGFyZW50RWxlbWVudC5jbGFzc0xpc3QucmVtb3ZlKCdub3RydW5uaW5nJyk7XG4gICAgICAgIGxhc3RJbXBvcnREYXRldGltZS5wYXJlbnRFbGVtZW50LmNsYXNzTGlzdC5hZGQoJ3J1bm5pbmcnKTtcbiAgICAgICAgYmFja2Ryb3AucmVtb3ZlKCk7XG4gICAgICAgIGJvZHkucHJlcGVuZChiYWNrZHJvcCk7XG4gICAgICAgIGJhY2tkcm9wLnN0eWxlLmRpc3BsYXkgPSAnZmxleCc7XG5cbiAgICAgICAgZG9TdGVwKDApLnRoZW4oZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBsZXQgbWVzc2FnZUVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjYWJfaW1wb3J0X21lc3NhZ2UnKTtcbiAgICAgICAgICAgIC8vbWVzc2FnZUVsZW1lbnQudGV4dENvbnRlbnQgPSAnZG9uZS4nO1xuICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBiYWNrZHJvcC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgfSwgNTAwMCk7XG4gICAgICAgIH0pO1xuICAgIH1cbiAgICBcbiAgICBsZXQgbWFpbkNhbGxiYWNrID0gZnVuY3Rpb24oKSB7XG4gICAgICAgIGxldCBnb0J1dHRvbiA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNidG5fc3RhcnRfcHJvZHVjdHNfaW1wb3J0Jyk7XG4gICAgICAgIGxldCBjb25maWdKc29uID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI3Byb2R1Y3RfaW1wb3J0X2NvbmZpZycpO1xuICAgICAgICBpZiAoZ29CdXR0b24gPT09IG51bGwgfHwgY29uZmlnSnNvbiA9PT0gbnVsbCkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgICAgIGNvbmZpZ3VyYXRpb24gPSBKU09OLnBhcnNlKGNvbmZpZ0pzb24udGV4dENvbnRlbnQpO1xuICAgICAgICBnb0J1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGdvQnV0dG9uQ2xpY2spO1xuICAgICAgICBpZiAoc3RvcEJ1dHRvbiAhPT0gbnVsbCkge1xuICAgICAgICAgICAgc3RvcEJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICBnbyA9IGZhbHNlO1xuICAgICAgICAgICAgICAgIHN0b3BCdXR0b24uc2V0QXR0cmlidXRlKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgfSlcbiAgICAgICAgfVxuICAgIH1cbiAgICBcbiAgICBpZiAoZG9jdW1lbnQucmVhZHlTdGF0ZSA9PT0gJ2NvbXBsZXRlJyB8fCAoZG9jdW1lbnQucmVhZHlTdGF0ZSAhPT0gJ2xvYWRpbmcnXG4gICAgICAgICYmICFkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuZG9TY3JvbGwpKSB7XG4gICAgICAgIG1haW5DYWxsYmFjaygpO1xuICAgIH0gZWxzZSB7XG4gICAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCBtYWluQ2FsbGJhY2spO1xuICAgIH1cbn0oKSk7XG5cbiJdfQ==

/* --------------------------------------------------------------
   product_import.js 2023-03-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

(function() {
    let configuration = {};
    let go = true;
    let stopButton = document.querySelector('#btn_stop_products_import');
    let lastImportDatetime = document.querySelector('#last_import_datetime');
    
    let doStep = function(step) {
        let messageElement = document.querySelector('#ab_import_message');
        let progressBar = document.querySelector('#ab_import_progress_bar');
        
        return window.fetch(configuration.importStepUrl + '&step=' + step)
            .then(response => response.json())
            .then(function(data) {
                messageElement.textContent = data.message;
                progressBar.style.width = (100 * parseFloat(data.progress)) + '%';
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
        
    }
    
    let goButtonClick = function(event) {
        let backdrop = document.querySelector('#ab_import_progress_backdrop');
        let body = document.querySelector('body');
        event.preventDefault();

        go = true;
        stopButton.removeAttribute('disabled');
        lastImportDatetime.setAttribute('disabled', 'disabled');
        lastImportDatetime.parentElement.classList.remove('notrunning');
        lastImportDatetime.parentElement.classList.add('running');
        backdrop.remove();
        body.prepend(backdrop);
        backdrop.style.display = 'flex';

        doStep(0).then(function() {
            let messageElement = document.querySelector('#ab_import_message');
            //messageElement.textContent = 'done.';
            setTimeout(function() {
                backdrop.style.display = 'none';
            }, 5000);
        });
    }
    
    let mainCallback = function() {
        let goButton = document.querySelector('#btn_start_products_import');
        let configJson = document.querySelector('#product_import_config');
        if (goButton === null || configJson === null) {
            return;
        }
        configuration = JSON.parse(configJson.textContent);
        goButton.addEventListener('click', goButtonClick);
        if (stopButton !== null) {
            stopButton.addEventListener('click', function(event) {
                event.preventDefault();
                go = false;
                stopButton.setAttribute('disabled', 'disabled');
            })
        }
    }
    
    if (document.readyState === 'complete' || (document.readyState !== 'loading'
        && !document.documentElement.doScroll)) {
        mainCallback();
    } else {
        document.addEventListener('DOMContentLoaded', mainCallback);
    }
}());


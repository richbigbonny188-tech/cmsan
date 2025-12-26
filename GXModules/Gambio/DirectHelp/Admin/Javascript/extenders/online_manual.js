/* --------------------------------------------------------------
 online_manual.js 2022-11-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Add an icon containing the URL to the online manual reference
 */
(function () {
    'use strict';

    const VISIBLE_CSS_CLASS = 'visible';
    const LINK_TARGET = '_blank';

    const {url} = document.currentScript.dataset;
    let element;

    function onPageReady() {
        const request = new XMLHttpRequest();
        const baseAdminUrl = window.location.href.replace(/(\/admin\/.*)/i, '/admin/');
        const requestUrl = `${baseAdminUrl}${url}&origin=${encodeURIComponent(window.location.href)}`;
    
        request.responseType = 'json';
        
        if (!element) {
            element = document.createElement('a');
            element.classList.add('online-manual-icon');
            document.body.appendChild(element);
        }

        function onLoad() {
            let response = request.response;

            if (this.status !== 200 || !response.link) {
                return;
            }

            element.href = response.link;
            element.title = response.tooltip;
            element.target = LINK_TARGET;
            element.classList.add(VISIBLE_CSS_CLASS);
        }

        request.open('GET', requestUrl, true);
        request.onload = onLoad;
        request.send();
    }

    function onHashChange() {
        element.classList.remove(VISIBLE_CSS_CLASS);
        onPageReady();
    }

    document.addEventListener('JSENGINE_INIT_FINISHED', onPageReady);
    window.addEventListener('hashchange', onHashChange);
    
    // Once the Admin VueJS pages are bootstrapped, the application fires the
    // "LAYOUT_BOOTSTRAPPED -> gx-admin:layout-bootstrapped" event
    // See: GambioAdmin/Layout/ui/assets/finalize.ts
    window.addEventListener('gx-admin:layout-bootstrapped', onPageReady);
})();

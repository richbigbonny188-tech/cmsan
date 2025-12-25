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
    const gambioStoreMenuElement = document.querySelector('.brand-alt.type-standalone');
    
    if (gambioStoreMenuElement === null) {
        return;
    }
    
    const jsElement = document.getElementById('gambio-store-updates-badge-js');
    
    fetch(jsElement.dataset.shopUrl + 'admin/admin.php?do=MenuBadge/AvailableUpdatesCount')
        .then(response => response.json())
        .then(data => {
            if (!data.availableUpdatesCount) {
                return;
            }
            
            const styleSheet = document.createElement('link');
            styleSheet.setAttribute('rel', 'stylesheet');
            styleSheet.setAttribute('type', 'text/css');
            styleSheet.setAttribute('href', jsElement.dataset.shopUrl + 'GXModules/Gambio/MenuBadge/Build/Admin/Styles/gambio_store_updates_badge.min.css');
            if (jsElement.hasAttribute('data-devmode')) {
                styleSheet.href = jsElement.dataset.shopUrl + 'GXModules/Gambio/MenuBadge/Build/Admin/Styles/gambio_store_updates_badge.css';
            }
            
            styleSheet.onload = function() {
                const updatesAvailableElement = document.createElement('div');
    
                gambioStoreMenuElement.classList.add('gambio_store_updates_badge_container');
                updatesAvailableElement.classList.add('gambio_store_updates_badge');
                updatesAvailableElement.innerText = data.availableUpdatesCount;
                gambioStoreMenuElement.appendChild(updatesAvailableElement);
            };
            
            document.head.appendChild(styleSheet);
        })
}

if (document.readyState !== 'loading') {
    checkForStoreUpdates();
} else {
    document.addEventListener('DOMContentLoaded', checkForStoreUpdates);
}

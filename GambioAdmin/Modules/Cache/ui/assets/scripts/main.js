document.addEventListener("DOMContentLoaded", function(event) {
    const apiUrl = `${window.jsEnvironment.baseUrl}/admin/admin.php?do=AdminCacheAjax`;
    const clearCacheItems = document.querySelectorAll('.btn-clear-cache');
    
    const clearCacheRequest = async (cache) => {
        const request = await fetch(`${apiUrl}&action=${cache}`);
        return await request.json();
    }
    
    clearCacheItems.forEach(function(clearCacheBtn) {
        clearCacheBtn.addEventListener('click', function(btn) {
            const button = btn.target;
            
            button.classList.add('disabled');
            button.innerText = window.jsEnvironment.vuePage.translations.TEXT_EXECUTING;
            
            const cacheId = button.getAttribute('data-cache-id');
            const cacheName = button.getAttribute('data-cache-name');
            
            clearCacheRequest(cacheId)
                .then((data) => {
                    const status = data.success ? 'success' : 'warning';
                    const title = window.jsEnvironment.vuePage.translations.HEADING_TITLE;
                    const message = data.success ? window.jsEnvironment.vuePage.translations.TEXT_SUCCESS.replace('{cacheName}', cacheName) : window.jsEnvironment.vuePage.translations.TEXT_ERROR.replace('{cacheName}', cacheName);
                    
                    const infoBox = window.InfoBox;
                    if(infoBox) {
                        infoBox.notify(status, message, title);
                        return;
                    }
                    
                    alert(`${title}: ${message}`);
                })
                .finally(() => {
                    button.classList.remove('disabled');
                    button.innerText = window.jsEnvironment.vuePage.translations.TEXT_EXECUTE;
                })
        });
    });
});
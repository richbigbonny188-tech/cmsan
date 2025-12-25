<?php
/* --------------------------------------------------------------
   ShopOfflinePageHelper.inc.php 2021-05-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

/**
 * Class ShopOfflinePageHelper
 */
class ShopOfflinePageHelper
{
    /**
     * Path to the cached HTML code for the shop offline page.
     *
     * @var string
     */
    protected static $shopOfflineHtmlFile = __DIR__ . '/../../../cache/shop_offline_page.tpl';
    protected static $shopDefaultOfflineHtmlFile = __DIR__ . '/../../../admin/html/content/shop_offline/shop_offline_page.tpl';
    protected static $updateCssFileFlag = __DIR__ . '/../../../cache/update_shop_offline_page_css.flag';
    
    
    /**
     * Rebuilds the shop offline page.
     */
    public static function showShopOfflinePage()
    {
        header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
        header('Retry-After: 3600');
        header('Cache-Control: no-cache');
        
        echo self::getShopOfflineHtml();
        exit();
    }
    
    
    /**
     * Rebuilds the shop offline page.
     */
    public static function getShopOfflineHtml()
    {
        $shopOfflineHtml = 'Shop offline';
        if (file_exists(static::$shopOfflineHtmlFile)) {
            $shopOfflineHtml = file_get_contents(static::$shopOfflineHtmlFile);
        }
        else if (file_exists(static::$shopDefaultOfflineHtmlFile)) {
            $shopOfflineHtml = file_get_contents(static::$shopDefaultOfflineHtmlFile);
        }
        
        return $shopOfflineHtml;
    }
    
    
    /**
     * Checks if a rebuild of the shop offline page is needed.
     */
    public static function pageRebuildNeeded()
    {
        $mainCssDirectory = StaticGXCoreLoader::getThemeControl()->getThemeCssPath();
        $mainCssFilePath = DIR_FS_CATALOG . $mainCssDirectory . (file_exists(DIR_FS_CATALOG
                                                                             . '.dev-environment') ? 'main.css' : 'main.min.css');

        return (!file_exists(static::$shopOfflineHtmlFile) || file_exists(static::$updateCssFileFlag))
               && file_exists($mainCssFilePath);
    }
    
    
    /**
     * Rebuilds the shop offline page.
     */
    public static function rebuildShopOfflinePage()
    {
        $logoManager       = MainFactory::create('GMLogoManager', 'gm_logo_favicon');
        $headerContentView = MainFactory::create(HeaderContentView::class);

        $favicon = $logoManager->logo_use === '1' ? $logoManager->logo_path . $logoManager->logo_file : '';
        $css     = "<style>{$headerContentView->getInlineCss()}</style>";

        if ($css === '<style></style>') {
            $css = '<link type="text/css" rel="stylesheet" href="dynamic_theme_style.css.php" />';
        } elseif (file_exists(static::$updateCssFileFlag)) {
            unlink(static::$updateCssFileFlag);
        }
        
        $storeName     = defined('STORE_NAME') ? htmlspecialchars(STORE_NAME) : 'Shop offline';
        $customContent = gm_get_conf('GM_SHOP_OFFLINE_MSG', 'ASSOC', true);
        
        $shopOfflineHtml = '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html" />
        <title>' . $storeName . '</title>
        <base href="' . GM_HTTP_SERVER . DIR_WS_CATALOG . '" />
        <link rel="shortcut icon" href="' . $favicon . '" type="image/x-icon" />
        ' . $css . '
    </head>
    <body>
        <!-- shop mode: offline -->
        ' . $customContent . '
    </body>
</html>';
        
        file_put_contents(static::$shopOfflineHtmlFile, $shopOfflineHtml);
    }
}

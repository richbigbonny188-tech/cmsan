<?php
/* --------------------------------------------------------------
   ErrorPageGenerator.inc.php 2022-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ErrorPageGenerator
 */
class ErrorPageGenerator
{
    /**
     * Returns the path to the cached file not found error page. If useFallback parameter is true,
     * it returns the file path to the german error page, if there is no error page for the provided language code.
     *
     * @param LanguageCode|null $languageCode
     * @param bool              $useFallback
     *
     * @return string
     */
    public static function fileNotFoundErrorPageCacheFile(LanguageCode $languageCode = null, $useFallback = true)
    {
        $languageCode = ($languageCode !== null) ? strtolower($languageCode->asString()) : 'de';
        $filePath     = DIR_FS_CATALOG . 'public/error_pages/404-' . $languageCode . '.html';
        
        if ($languageCode !== 'de' && !file_exists($filePath) && $useFallback) {
            $filePath = DIR_FS_CATALOG . 'public/error_pages/404-de.html';
        }
        
        return $filePath;
    }
    
    
    /**
     * Returns the path to the cached file not found error page. If useFallback parameter is true,
     * it returns the file path to the german error page, if there is no error page for the provided language code.
     *
     * @param LanguageCode|null $languageCode
     * @param bool              $useFallback
     *
     * @return string
     */
    public static function unexpectedErrorPageCacheFile(LanguageCode $languageCode = null, $useFallback = true)
    {
        $languageCode = ($languageCode !== null) ? strtolower($languageCode->asString()) : 'de';
        $filePath     = DIR_FS_CATALOG . 'public/error_pages/500-' . $languageCode . '.html';
        
        if ($languageCode !== 'de' && !file_exists($filePath) && $useFallback) {
            $filePath = DIR_FS_CATALOG . 'public/error_pages/500-de.html';
        }
        
        return $filePath;
    }
    
    
    /**
     * Checks if a rebuild of the provided cache file is needed.
     *
     * @param string $cacheFilePath
     *
     * @return bool
     */
    public static function cacheFileRebuildNeeded($cacheFilePath)
    {
        $mainCssDirectory = StaticGXCoreLoader::getThemeControl()->getThemeCssPath();
        $mainCssFilePath = DIR_FS_CATALOG . $mainCssDirectory . (file_exists(DIR_FS_CATALOG
                                                                             . '.dev-environment') ? 'main.css' : 'main.min.css');
        
        return !file_exists($cacheFilePath) && file_exists($mainCssFilePath);
    }
    
    
    /**
     * Creates or updated a page cache file.
     *
     * @param string $customContent
     * @param string $cacheFilePath
     */
    public static function createPageCache($customContent, $cacheFilePath)
    {
        $logoManager       = MainFactory::create('GMLogoManager', 'gm_logo_favicon');
        $headerContentView = MainFactory::create('HeaderThemeContentView');
        
        $favicon   = $logoManager->logo_use === '1' ? $logoManager->logo_path . $logoManager->logo_file : '';
        $inlineCss = $headerContentView->getInlineCss();
        $storeName = defined('STORE_NAME') ? htmlspecialchars(STORE_NAME) : 'Shop offline';
        
        $shopOfflineHtml = '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html" />
        <title>' . $storeName . '</title>
        <base href="' . GM_HTTP_SERVER . DIR_WS_CATALOG . '" />
        <link rel="shortcut icon" href="' . $favicon . '" type="image/x-icon" />
        <style>' . $inlineCss . '</style>
    </head>
    <body>
        ' . $customContent . '
    </body>
</html>';
        
        if (!file_exists(DIR_FS_CATALOG . 'public/error_pages')) {
            mkdir(DIR_FS_CATALOG . 'public/error_pages', 0777, true);
            chmod(DIR_FS_CATALOG . 'public/error_pages', 0777);
        }
        
        file_put_contents($cacheFilePath, $shopOfflineHtml);
    }
}
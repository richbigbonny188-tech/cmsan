<?php
/* --------------------------------------------------------------
   ErrorPageGenerator.php 2021-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Writer;

use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class ErrorPageGenerator
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Writer
 */
class ErrorPageGenerator
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    /**
     * @var Url
     */
    private $url;
    
    /**
     * @var Environment
     */
    private $environment;
    
    /**
     * @var string
     */
    private $mainCssDir;
    
    
    /**
     * ErrorPageGenerator constructor.
     *
     * @param ConfigurationService $configurationService
     * @param Url                  $url
     * @param Environment          $environment
     */
    public function __construct(
        ConfigurationService $configurationService,
        Url $url,
        Environment $environment,
        string $mainCssDir
    ) {
        $this->configurationService = $configurationService;
        $this->url                  = $url;
        $this->environment          = $environment;
        $this->mainCssDir           = $mainCssDir;
    }
    
    
    /**
     * @param string $customContent
     *
     * @return string
     */
    public function generateErrorPageHtml(string $customContent): string
    {
        $faviconUsed = $this->configurationService->find('gm_configuration/GM_LOGO_FAVICON_USE');
        $favicon     = $faviconUsed !== null && $faviconUsed->value() === '1' ? $this->url->base()
                                                                                . 'images/logo/GM_LOGO_FAVICON' : '';
        
        $storeName = $this->configurationService->find('configuration/STORE_NAME');
        $storeName = $storeName !== null ? $storeName->value() : 'Shop offline';
        
        return '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html"/>
        <title>' . $storeName . '</title>
        <base href="' . $this->url->base() . '"/>
        <link rel="shortcut icon" href="' . $favicon . '" type="image/x-icon"/>
        <style>' . $this->getInlineCss() . '</style>
    </head>
    <body>
        ' . $customContent . '
    </body>
</html>';
    }
    
    
    /**
     * @return string
     */
    private function getInlineCss(): string
    {
        $mainCssSuffix  = $this->environment->isDev() ? '.css' : '.min.css';
        $mainCssPath    = $this->mainCssDir . '/main' . $mainCssSuffix;
        $mainCssContent = file_exists($mainCssPath) ? file_get_contents($mainCssPath) : '';
        
        return str_replace(['assets/fonts/', 'styles/', 'assets/images/'],
                           ['public/theme/fonts/', 'public/theme/styles/', 'public/theme/images/'],
                           $mainCssContent);
    }
}
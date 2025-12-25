<?php
/* --------------------------------------------------------------
   GambioStoreController.inc.php 2019-01-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioStoreController
 *
 * Bootstraps the Gambio Store pages.
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class GambioStoreController extends AdminHttpViewController
{
    /**
     * Default Action
     *
     * Render the downloads Gambio Store page.
     *
     * @return AdminLayoutHttpControllerResponse
     */
    public function actionDefault()
    {
        $languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_store', $_SESSION['languages_id']);
        $title               = new NonEmptyStringType($languageTextManager->get_text('PAGE_TITLE'));
        $template            = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                                       . '/html/content/gambio_store/gambio_store_downloads.html'));
        $contentNavigation   = MainFactory::create('ContentNavigationCollection', []);
        
        $gambioUrl = gm_get_conf('GAMBIO_STORE_URL');
        
        // Fall back to the production Gambio Store URL if none is set.
        if ($gambioUrl === '') {
            $gambioUrl = 'https://store.gambio.com/a';
            gm_set_conf('GAMBIO_STORE_URL', 'https://store.gambio.com/a');
        }
        
        $gambioToken = gm_get_conf('GAMBIO_STORE_TOKEN');
        
        if ($this->_getQueryParameter('reset-token') || $this->_getQueryParameter('reset-token') === '') {
            gm_set_conf('GAMBIO_STORE_TOKEN', '');
            gm_set_conf('GAMBIO_STORE_IS_REGISTERED', 'false');
            
            return new RedirectHttpControllerResponse('./admin.php?do=GambioStore');
        }
        
        if (gm_get_conf('ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING') === 'false') {
            $gambioUrl .= '/dataprocessing';
        } elseif (gm_get_conf('GAMBIO_STORE_IS_REGISTERED') === 'false') {
            if (!$gambioToken) {
                $tokenGenerator = MainFactory::create('GambioStoreTokenGenerator');
                
                $gambioToken = $tokenGenerator->generateToken();
                
                gm_set_conf('GAMBIO_STORE_TOKEN', $gambioToken);
            }
            
            $gambioUrl .= '/register';
        } elseif (gm_get_conf('ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING') === 'true') {
            $gambioUrl .= '/downloads';
            
            $contentNavigation->add(new StringType($languageTextManager->get_text('DOWNLOADS', 'gambio_store')),
                new StringType('admin.php?do=GambioStore'), new BoolType(true));
            $contentNavigation->add(new StringType($languageTextManager->get_text('INSTALLED_PACKAGES_AND_UPDATES',
                'gambio_store')), new StringType('admin.php?do=GambioStore/Installations'), new BoolType(false));
        }
        
        setcookie('auto_updater_admin_check', 'admin_logged_in', time() + 5 * 60, '/');
        
        $data = MainFactory::create('KeyValueCollection', [
                'storeUrl'   => $gambioUrl,
                'storeToken' => $gambioToken
            ]);
        
        $assets = MainFactory::create('AssetCollection',
                                      [
                                          MainFactory::create('Asset', 'gambio_store.lang.inc.php')
                                      ]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse',
                                   $title,
                                   $template,
                                   $data,
                                   $assets,
                                   $contentNavigation);
    }
    
    /**
     * Installations Action
     *
     * Renders the installations page.
     *
     * @return AdminLayoutHttpControllerResponse
     */
    public function actionInstallations()
    {
        if (gm_get_conf('ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING') !== 'true') {
            return $this->actionDefault();
        }
    
        $languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_store', $_SESSION['languages_id']);
        $title               = new NonEmptyStringType($languageTextManager->get_text('PAGE_TITLE'));
        $template            = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                                       . '/html/content/gambio_store/gambio_store_installations.html'));
        
        setcookie('auto_updater_admin_check', 'admin_logged_in', time() + 5 * 60, '/');
        
        $data = MainFactory::create('KeyValueCollection', [
                'storeUrl'   => gm_get_conf('GAMBIO_STORE_URL') . '/installations',
                'storeToken' => gm_get_conf('GAMBIO_STORE_TOKEN')
            ]);
    
        $assets = MainFactory::create('AssetCollection', [
                MainFactory::create('Asset', 'gambio_store.lang.inc.php')
            ]);
    
        $contentNavigation = MainFactory::create('ContentNavigationCollection', []);
    
        $contentNavigation->add(new StringType($languageTextManager->get_text('DOWNLOADS', 'gambio_store')),
            new StringType('admin.php?do=GambioStore'), new BoolType(false));
    
        $contentNavigation->add(new StringType($languageTextManager->get_text('INSTALLED_PACKAGES_AND_UPDATES',
            'gambio_store')), new StringType('admin.php?do=GambioStore/Installations'), new BoolType(true));
    
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template,
                                   $data,
                                   $assets,
                                   $contentNavigation);
    }
    
    
    public function actionConfiguration()
    {
        if (isset($_POST) && isset($_POST['url'])) {
            if ($_POST['url'] !== gm_get_conf('GAMBIO_STORE_URL')) {
                if (filter_var($_POST['url'], FILTER_VALIDATE_URL) === $_POST['url']) {
                    gm_set_conf('GAMBIO_STORE_URL', $_POST['url']);
                }
            }
        }
    
        $languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_store', $_SESSION['languages_id']);
        $title               = new NonEmptyStringType($languageTextManager->get_text('PAGE_TITLE'));
        $template            = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN
                                                                       . '/html/content/gambio_store/gambio_store_configuration.html'));
        $gambioStoreUrl      = isset($_POST['url']) ? $_POST['url'] : gm_get_conf('GAMBIO_STORE_URL');
        $data                = MainFactory::create('KeyValueCollection', ['url' => $gambioStoreUrl]);
    
        $assets = MainFactory::create('AssetCollection', [
                MainFactory::create('Asset', 'gambio_store.lang.inc.php')
            ]);
    
        $contentNavigation = MainFactory::create('ContentNavigationCollection', []);
    
        $contentNavigation->add(new StringType($languageTextManager->get_text('DOWNLOADS', 'gambio_store')),
            new StringType('admin.php?do=GambioStore'), new BoolType(false));
    
        $contentNavigation->add(new StringType($languageTextManager->get_text('INSTALLED_PACKAGES_AND_UPDATES',
            'gambio_store')), new StringType('admin.php?do=GambioStore/Installations'), new BoolType(false));
    
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template,
                                   $data,
                                   $assets,
                                   $contentNavigation);
    }
    
    
    /**
     * Set the accept data processing value to true and render the default page.
     *
     * @return AdminLayoutHttpControllerResponse
     */
    public function actionAcceptDataProcessing()
    {
        gm_set_conf('ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING', 'true');
        
        return $this->actionDefault();
    }
    
    
    /**
     * Activate a theme
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionActivateTheme()
    {
        if (!isset($_POST) || !isset($_POST['themeStorageName'])) {
            return MainFactory::create('JsonHttpControllerResponse', ['success' => false]);
        }
        
        $themeService = StaticGXCoreLoader::getService('Theme');
        $themeName    = $_POST['themeStorageName'];
        
        try {
            $themeService->activateTheme($themeName);
        } catch (Exception $e) {
            return MainFactory::create('JsonHttpControllerResponse', ['success' => false]);
        }
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
    }
}

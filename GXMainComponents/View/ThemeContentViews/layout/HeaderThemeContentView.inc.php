<?php
/* --------------------------------------------------------------
  HeaderThemeContentView.inc.php 2023-07-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(header.php,v 1.40 2003/03/14); www.oscommerce.com
  (c) 2003	 nextcommerce (header.php,v 1.13 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: header.php 1140 2005-08-10 10:16:00Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_output_warning.inc.php');
require_once(DIR_FS_INC . 'xtc_parse_input_field_data.inc.php');
require_once(DIR_FS_INC . 'xtc_banner_exists.inc.php');
require_once(DIR_FS_INC . 'xtc_display_banner.inc.php');
require_once(DIR_FS_INC . 'xtc_update_banner_display_count.inc.php');
if (!function_exists('xtc_image')) {
    require_once(DIR_FS_INC . 'xtc_image.inc.php');
}
if (!function_exists('xtc_draw_separator')) {
    require_once(DIR_FS_INC . 'xtc_draw_separator.inc.php');
}

class HeaderThemeContentView extends ThemeContentView
{
    protected $style_edit_mode;
    protected $script_name;
    protected $c_path;
    protected $coo_product;
    protected $languages_id;
    protected $extender_html;
    protected $header_top_extender_html;
    protected $coo_payment;
    protected $is_create_account_page = false;
    /**
     * @var xtcPrice_ORIGIN
     */
    protected $xtcPrice;
    protected $styleEditStyleName;
    
    /**
     * @var RoutineLockerInterface
     */
    protected $locker;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->set_content_template('layout_head.html');
    }
    
    
    public function prepare_data()
    {
        $this->content_array['HTML_PARAMS'] = HTML_PARAMS;
        
        $this->content_array['BASE_URL'] = GM_HTTP_SERVER . DIR_WS_CATALOG;
        
        $this->content_array['SHOP_VERSION'] = gm_get_conf('INSTALLED_VERSION');
        
        $developmentEnvironment = file_exists(DIR_FS_CATALOG . '.dev-environment');
        
        if ($developmentEnvironment && $_SESSION['customers_status']['customers_status_id'] === '0') {
            // Enable the debug bar.
            $this->content_array['debug_bar']                = true;
            $debugBarAssets                                  = StaticGXCoreLoader::getDebugBarAssets();
            $this->content_array['debug_bar_header_content'] = $debugBarAssets['head'];
        } else {
            // Disable the debug bar.
            $this->content_array['debug_bar'] = false;
        }
        
        $coo_logo_manager = MainFactory::create_object('GMLogoManager', ['gm_logo_favicon']);
        if ($coo_logo_manager->logo_use == '1') {
            $this->content_array['FAVICON'] = $coo_logo_manager->logo_path . $coo_logo_manager->logo_file;
        }

        $this->content_array['FAVICON_IPAD'] = false;
        $coo_logo_manager_ipad = MainFactory::create_object('GMLogoManager', ['gm_logo_favicon_ipad']);
        if ($coo_logo_manager_ipad->logo_use == '1') {
            $this->content_array['FAVICON_IPAD'] = $coo_logo_manager_ipad->logo_path
                                                   . $coo_logo_manager_ipad->logo_file;
        }
        
        $this->content_array['MAIN_CSS']        = false;
        $this->content_array['REBUILD_CSS']     = false;
    
        $isLocked = $this->getLocker()->isLocked();
        $mainCssPath = $this->getMainCssPath();
        $mainCssExists = file_exists($mainCssPath);
        $forceRenewal = StyleEditServiceFactory::service()->forceCssCacheRenewal();
        $cssCacheFileExists = file_exists(DIR_FS_CATALOG . 'cache/__dynamics.css');
    
        if ($this->_isStyleEdit3Active() || $forceRenewal || (!$cssCacheFileExists && !$isLocked) || !$mainCssExists) {
            $this->content_array['REBUILD_CSS'] = true;
        }
    
        if ($mainCssExists) {
                $this->content_array['MAIN_CSS'] = $this->getMainCss();
        }
        
        $this->content_array['CSS_FILE']              = $this->getCssFile();
        $this->content_array['CSS_PARAMS']            = implode('&amp;', $this->getCssParams());
        $this->content_array['STYLE_EDIT_MODE']       = $this->style_edit_mode;
        $this->content_array['additional_html_array'] = $this->get_additional_html_array();
        
        // Number widget
        $actualCurrencyArray                    = $this->xtcPrice->currencies[$this->xtcPrice->actualCurr];
        $this->content_array['numberSeparator'] = $actualCurrencyArray['decimal_point'];
        
        $hyphenopolyLanguagesMap                    = MainFactory::create('HyphenopolyLanguageMappingProvider');
        $languageCode                               = new LanguageCode(new StringType($_SESSION['language_code']));
        $this->content_array['hyphenopolyLanguage'] = $hyphenopolyLanguagesMap->getHyphenopolyLanguage($languageCode);
        
        $this->content_array['hyphenopolyLoader'] = '';
        
        if (gm_get_conf('ENABLE_JS_HYPHENATION') === 'true') {
            $this->content_array['hyphenopolyLoader'] = file_get_contents(DIR_FS_CATALOG
                                                                          . 'JSEngine/build/vendor/hyphenopoly/Hyphenopoly_Loader.js');
        }
        
        $this->assignWarnings();
    }
    
    
    /**
     * @return string
     * @deprecated
     */
    public function get_warnings_html()
    {
        // deprecated
        return '';
    }
    
    
    protected function assignWarnings()
    {
        $this->content_array['show_shop_offline_warning'] = gm_get_conf('GM_SHOP_OFFLINE') === 'checked'
                                                            && $_SESSION['customers_status']['customers_status_id']
                                                               === '0';
        
        $warnings = [];
        
        // check if the 'install' directory exists, and warn of its existence
        if (defined('WARN_INSTALL_EXISTENCE') && WARN_INSTALL_EXISTENCE === 'true'
            && $_SESSION['customers_status']['customers_status_id'] === '0'
            && file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/gambio_installer/request_port.php')
            && file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/.dev-environment') === false) {
            $warnings[] = sprintf(WARNING_INSTALL_DIRECTORY_EXISTS, substr(DIR_WS_CATALOG, 0, -1));
        }
        
        // check if the configure.php file is writeable
        if (defined('WARN_CONFIG_WRITEABLE') && WARN_CONFIG_WRITEABLE === 'true'
            && file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php')
            && is_writable(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php')) {
            $warnings[] = sprintf(WARNING_CONFIG_FILE_WRITEABLE, substr(DIR_WS_CATALOG, 0, -1));
        }
        
        // check if the session folder is writeable
        if (ini_get('session.save_handler') === 'files' && defined('WARN_SESSION_DIRECTORY_NOT_WRITEABLE')
            && WARN_SESSION_DIRECTORY_NOT_WRITEABLE === 'true') {
            if (!is_dir(xtc_session_save_path())) {
                $warnings[] = sprintf(WARNING_SESSION_DIRECTORY_NON_EXISTENT, xtc_session_save_path());
            } elseif (!is_writeable(xtc_session_save_path())) {
                $warnings[] = sprintf(WARNING_SESSION_DIRECTORY_NOT_WRITEABLE, xtc_session_save_path());
            }
        }
        
        // check session.auto_start is disabled
        if (defined('WARN_SESSION_AUTO_START') && WARN_SESSION_AUTO_START === 'true'
            && function_exists('ini_get')
            && ini_get('session.auto_start') == '1') {
            $warnings[] = WARNING_SESSION_AUTO_START;
        }
        
        if (defined('WARN_DOWNLOAD_DIRECTORY_NOT_READABLE') && WARN_DOWNLOAD_DIRECTORY_NOT_READABLE === 'true'
            && DOWNLOAD_ENABLED === 'true'
            && !is_dir(DIR_FS_DOWNLOAD)) {
            $warnings[] = sprintf(WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT, DIR_FS_DOWNLOAD);
        }
        
        
        
        $this->content_array['additional_warnings'] = $warnings;
    }
    
    
    public function get_additional_html_array()
    {
        $t_html_array                   = [];
        $t_html_array['head']           = [];
        $t_html_array['head']['first']  = '';
        $t_html_array['head']['top']    = '';
        $t_html_array['head']['bottom'] = '';
        $t_html_array['body']           = [];
        $t_html_array['body']['params'] = '';
        $t_html_array['body']['top']    = '';
        
        $t_uninitialized_array = $this->get_uninitialized_variables(['extender_html', 'header_top_extender_html']);
        if (empty($t_uninitialized_array)) {
            $t_html_array['head']['first'] .= $this->header_top_extender_html;
            $t_html_array['head']['top']   .= $this->get_meta_tags_html();
            
            ob_start();
            
            if (strpos($GLOBALS['PHP_SELF'], FILENAME_CHECKOUT_PAYMENT) !== false) {
                $coo_payment = $this->coo_payment;
                $coo_payment->javascript_validation();
            }
            
            $t_html_array['head']['bottom'] .= ob_get_clean();
            $t_html_array['head']['bottom'] .= $this->extender_html;
            
            if (strpos($GLOBALS['PHP_SELF'], FILENAME_POPUP_IMAGE) !== false) {
                $t_html_array['body']['params'] .= ' onload="resize();" ';
            }
            
            $t_html_array['body']['top'] .= $this->get_warnings_html();
            
            $this->get_modules_html($t_html_array);
        } else {
            trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
        
        return $t_html_array;
    }
    
    
    public function get_modules_html(&$p_html_array)
    {
        //Use this function for overloading
        
        return $p_html_array;
    }
    
    
    public function get_meta_tags_html()
    {
        $t_meta_tags_html      = '';
        $t_uninitialized_array = $this->get_uninitialized_variables([
                                                                        'c_path',
                                                                        'coo_product',
                                                                        'script_name'
                                                                    ]);
        if (empty($t_uninitialized_array)) {
            
            $coo_meta         = MainFactory::create_object('GMMeta', [false]);
            $t_meta_tags_html = $coo_meta->get($this->c_path, $this->coo_product);
            
            if ($this->is_create_account_page) {
                $t_meta_tags_html .= "\t\t" . '<meta http-equiv="pragma" content="no-cache" />' . "\n";
            }
        } else {
            trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class "
                          . get_class($this) . " or is/are null",
                          E_USER_ERROR);
        }
        
        return $t_meta_tags_html;
    }
    
    
    protected function set_validation_rules()
    {
        // GENERAL VALIDATION RULES
        $this->validation_rules_array['style_edit_mode']          = [
            'type'   => 'string',
            'strict' => 'true'
        ];
        $this->validation_rules_array['script_name']              = [
            'type'   => 'string',
            'strict' => 'true'
        ];
        $this->validation_rules_array['c_path']                   = [
            'type'   => 'string',
            'strict' => 'true'
        ];
        $this->validation_rules_array['coo_product']              = [
            'type'        => 'object',
            'object_type' => 'product'
        ];
        $this->validation_rules_array['languages_id']             = ['type' => 'int'];
        $this->validation_rules_array['header_top_extender_html'] = [
            'type'   => 'string',
            'strict' => 'true'
        ];
        $this->validation_rules_array['extender_html']            = [
            'type'   => 'string',
            'strict' => 'true'
        ];
        $this->validation_rules_array['coo_payment']              = [
            'type'        => 'object',
            'object_type' => 'payment'
        ];
    }
    
    
    /**
     * @param bool $p_is_create_account_page
     */
    public function set_is_create_account_page($p_is_create_account_page)
    {
        $this->is_create_account_page = (bool)$p_is_create_account_page;
    }
    
    
    public function setStyleEditStyleName($p_styleName)
    {
        $this->styleEditStyleName = (string)$p_styleName;
    }
    
    
    /**
     * Checks if user has an active StyleEdit 3 session
     *
     * @deprecated
     *
     * @return bool
     */
    protected function _isStyleEdit3Active()
    {
        return false;
    }
    
    
    /**
     * @deprecated
     *
     * @return bool
     */
    public function isStyleEdit3Active()
    {
        return $this->_isStyleEdit3Active();
    }
    
    
    /**
     * @return array
     */
    public function getCssParams()
    {
        $t_css_params_array = [];
        
        $t_css_params_array[] = 'theme=' . StaticGXCoreLoader::getThemeControl()->getCurrentTheme();
        $t_css_params_array[] = 'bust=' . time();
        
        if ($this->styleEditStyleName !== null) {
            $t_css_params_array[] = 'style_name=' . rawurlencode($this->styleEditStyleName);
        }
        
        return $t_css_params_array;
    }
    
    
    /**
     * @return string
     */
    public function getCssFile()
    {
        $cssFile = 'dynamic_theme_style.css.php';
        
        if (isset($_SERVER['gambio_mod_rewrite_working'], $_SERVER['gambio_htaccessVersion'])
            && (bool)$_SERVER['gambio_mod_rewrite_working']
            && version_compare($_SERVER['gambio_htaccessVersion'], '2.8') >= 0
            && @constant('USE_BUSTFILES') === 'true') {
            $cssFile = 'dynamic_theme_style-bust_' . time() . '.css.php';
        }
        
        if (StyleEditServiceFactory::service()->forceCssCacheRenewal()) {
            
            $cssFile = 'dynamic_theme_style.css.php?renew_cache=1';
        }
        
        return $cssFile;
    }
    
    
    /**
     * @return string
     */
    public function getMainCss()
    {
        $mainCssBase   = StaticGXCoreLoader::getThemeControl()->getThemeCssPath() . 'main';
        $mainCssSuffix = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '.css' : '.min.css';
        $mainCssPath   = DIR_FS_CATALOG . $mainCssBase . $mainCssSuffix;
        
        // avoid warning, if file does not exits, eventhough this should not happen
        $filemtime = file_exists($mainCssPath) ? filemtime($mainCssPath) : time();
        
        if (isset($_SERVER['gambio_mod_rewrite_working'], $_SERVER['gambio_htaccessVersion'])
            && (bool)$_SERVER['gambio_mod_rewrite_working']
            && version_compare($_SERVER['gambio_htaccessVersion'], '2.8') >= 0
            && @constant('USE_BUSTFILES') === 'true') {
            $mainCss = $mainCssBase . '-bust_' . $filemtime . $mainCssSuffix;
        } else {
            $mainCss = $mainCssBase . $mainCssSuffix . '?bust=' . $filemtime;
        }
        
        return $mainCss;
    }
    
    
    protected function getMainCssPath()
    {
        $mainCssBase   = StaticGXCoreLoader::getThemeControl()->getThemeCssPath() . 'main';
        $mainCssSuffix = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '.css' : '.min.css';
        $mainCssPath   = DIR_FS_CATALOG . $mainCssBase . $mainCssSuffix;
        
        return $mainCssPath;
    }
    
    /**
     * Returns the inline CSS styles for general shop pages.
     *
     * @return string
     */
    public function getInlineCss()
    {
        $mainCssPath    = $this->getMainCssPath();
        $mainCssContent = file_exists($mainCssPath) ? file_get_contents($mainCssPath) : '';
        
        return str_replace([
                               'assets/fonts/',
                               'styles/',
                               'assets/images/',
                           ],
                           [
                               StaticGXCoreLoader::getThemeControl()->getThemeFontsPath(),
                               StaticGXCoreLoader::getThemeControl()->getThemePath() . 'styles/',
                               StaticGXCoreLoader::getThemeControl()->getThemeImagePath(),
                           ],
                           $mainCssContent);
    }
    
    /**
     * @return RoutineLockerInterface
     */
    protected function getLocker(): RoutineLockerInterface
    {
        if ($this->locker === null) {
            $this->locker = CssRoutineLocker::create( DIR_FS_CATALOG);
        }
        
        return $this->locker;
    }
}

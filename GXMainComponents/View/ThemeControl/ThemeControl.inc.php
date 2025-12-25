<?php
/*--------------------------------------------------------------------------------------------------
    ThemeControl.inc.php 2021-05-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

class ThemeControl
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $currentTemplate;
    
    /**
     * @var bool
     */
    protected $themeSystemActive;
    
    /**
     * @var string
     */
    protected $currentTheme;
    
    /**
     * @var string
     */
    protected $themesPath;
    
    /**
     * @var string
     */
    protected $themePath;
    
    /**
     * @var string
     */
    protected $themeHtmlPath;
    
    /**
     * @var string
     */
    protected $themeSmartyPath;
    
    /**
     * @var string
     */
    protected $themeCssPath;
    
    /**
     * @var string
     */
    protected $themeJsPath;
    
    /**
     * @var string
     */
    protected $themeImagePath;
    
    /**
     * @var string
     */
    protected $themeFontsPath;
    
    /**
     * @var string
     */
    protected $themeConfigPath;
    
    /**
     * @var string
     */
    protected $categoryListingTemplatePath;
    
    /**
     * @var string
     */
    protected $filterSectionTemplatePath;
    
    /**
     * @var string
     */
    protected $productInfoTemplatePath;
    
    /**
     * @var string
     */
    protected $productListingTemplatePath;
    
    /**
     * @var string
     */
    protected $productOptionsTemplatePath;
    
    /**
     * @var string
     */
    protected $gmProductOptionsTemplatePath;
    
    /**
     * @var string
     */
    protected $propertiesTemplatePath;
    
    /**
     * @var string
     */
    protected $dynamicCssFilePath;
    
    /**
     * @var string
     */
    protected $templateSettingsFilePath;
    
    /**
     * @var array
     */
    protected $themeSettings;
    
    /**
     * @var float
     */
    protected $themeVersion;
    
    /**
     * @var string
     */
    protected $publishedThemePath;
    /**
     * @var ViewSettings
     */
    protected $viewSettings = null;
    
    
    /**
     * ThemeControl constructor.
     *
     * @param ViewSettings $viewSettings
     */
    public function __construct(ViewSettings $viewSettings)
    {
        $this->viewSettings       = $viewSettings;
        $this->publishedThemePath = 'public/theme';
    }
    
    
    /**
     * Returns the status of the theme system. True, if theme system is active, otherwise false.
     *
     * @return bool
     */
    public function isThemeSystemActive()
    {
        if ($this->themeSystemActive === null) {
            $this->_determineCurrentThemeStatus();
        }
        
        return $this->themeSystemActive;
    }
    
    
    /**
     * Returns the current theme name. If the theme system is not active, the current template name will be returned.
     *
     * @return string
     */
    public function getCurrentTheme()
    {
        if ($this->currentTheme === null) {
            $this->_determineCurrentThemeStatus();
        }
        
        return $this->currentTheme;
    }
    
    
    /**
     * @return string
     */
    public function getCompiledTemplatesFolder(): ?string
    {
        return StyleEditServiceFactory::service()->getCompiledTemplatesFolder() ??
               'cache' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR;
    }
    
    
    /**
     * Returns the current theme hierarchy. If the theme system is not active, the current template name will be
     * returned as array.
     *
     * @return array
     */
    public function getCurrentThemeHierarchy()
    {
        $hierarchy   = [];
        $parentTheme = $this->getCurrentTheme();
        
        while ($parentTheme !== null) {
            $hierarchy[]   = $parentTheme;
            $themeJsonFile = DIR_FS_CATALOG . $this->getThemesPath() . $parentTheme . '/theme.json';
            $parentTheme   = null;
            
            if (file_exists($themeJsonFile)) {
                $themeJson = json_decode(file_get_contents($themeJsonFile), true);
                if (isset($themeJson['extends'])) {
                    $parentTheme = $themeJson['extends'];
                }
            }
        }
        
        return $hierarchy;
    }
    
    
    /**
     * Returns the path to the themes, based on the shop root directory.
     *
     * @return string
     */
    public function getThemesPath()
    {
        if ($this->themesPath === null) {
            $this->themesPath = 'themes/';
        }
        
        return $this->themesPath;
    }
    
    
    /**
     * Returns the path to the theme, based on the shop root directory.
     *
     * @return string
     */
    public function getThemePath()
    {
        if ($this->themePath === null) {
            $this->themePath = $this->getPublishedThemePath() . '/';
        }
        
        return $this->themePath;
    }
    
    
    /**
     * Returns the path to the published theme path.
     *
     * @return string
     */
    public function getPublishedThemePath()
    {
        return StyleEditServiceFactory::service()->getPublishedThemePath() ?? $this->publishedThemePath;
    }
    
    
    /**
     * Returns the path to the theme html directory, based on the shop root directory.
     *
     * @return string
     */
    public function getThemeHtmlPath()
    {
        if ($this->themeHtmlPath === null) {
            $this->themeHtmlPath = $this->getPublishedThemePath() . '/html/system/';
        }
        
        return $this->themeHtmlPath;
    }
    
    
    /**
     * Returns the path to the theme smarty directory, based on the shop root directory.
     *
     * @return string
     */
    public function getThemeSmartyPath()
    {
        if ($this->themeSmartyPath === null) {
            $this->themeSmartyPath = $this->getPublishedThemePath() . '/html/smarty/';
        }
        
        return $this->themeSmartyPath;
    }
    
    
    /**
     * Returns the path to the theme css directory, based on the shop root directory.
     *
     * @return string
     */
    public function getThemeCssPath()
    {
        if ($this->themeCssPath === null) {
            $this->themeCssPath = $this->getPublishedThemePath() . '/styles/system/';
        }
        
        return $this->themeCssPath;
    }
    
    
    /**
     * Returns the path to the theme javascript directory, based on the shop root directory.
     *
     * @return string
     */
    public function getThemeJsPath()
    {
        if ($this->themeJsPath === null) {
            $this->themeJsPath = $this->getPublishedThemePath() . '/javascripts/system/';
        }
        
        return $this->themeJsPath;
    }
    
    
    /**
     * Returns the path to the theme image directory, based on the shop root directory.
     *
     * @return string
     */
    public function getThemeImagePath()
    {
        if ($this->themeImagePath === null) {
            $this->themeImagePath = $this->getPublishedThemePath() . '/images/';
        }
        
        return $this->themeImagePath;
    }
    
    
    /**
     * Returns the path to the theme image directory, based on the shop root directory.
     *
     * @return string
     */
    public function getThemeFontsPath()
    {
        if ($this->themeFontsPath === null) {
            $this->themeFontsPath = $this->getPublishedThemePath() . '/fonts/';
        }
        
        return $this->themeFontsPath;
    }
    
    
    /**
     * Returns the path to the theme config directory, based on the shop root directory.
     *
     * @return string
     */
    public function getThemeConfigPath()
    {
        if ($this->themeConfigPath === null) {
            $this->themeConfigPath = $this->getPublishedThemePath() . '/config/';
        }
        
        return $this->themeConfigPath;
    }
    
    
    /**
     * Returns the path to the category listing templates directory, based on the shop root directory.
     *
     * @return string
     */
    public function getCategoryListingTemplatePath()
    {
        if ($this->categoryListingTemplatePath === null) {
            $this->categoryListingTemplatePath = $this->getPublishedThemePath() . '/html/system/';
        }
        
        return $this->categoryListingTemplatePath;
    }
    
    
    /**
     * Returns the path to the category listing templates directory, based on the shop root directory.
     *
     * @return string
     */
    public function getFilterSelectionTemplatePath()
    {
        if ($this->filterSectionTemplatePath === null) {
            $this->filterSectionTemplatePath = $this->getPublishedThemePath() . '/html/system/';
        }
        
        return $this->filterSectionTemplatePath;
    }
    
    
    /**
     * Returns the path to the product info templates directory, based on the shop root directory.
     *
     * @return string
     */
    public function getProductInfoTemplatePath()
    {
        if ($this->productInfoTemplatePath === null) {
            $this->productInfoTemplatePath = $this->getPublishedThemePath() . '/html/system/';
        }
        
        return $this->productInfoTemplatePath;
    }
    
    
    /**
     * Returns the path to the product listing templates directory, based on the shop root directory.
     *
     * @return string
     */
    public function getProductListingTemplatePath()
    {
        if ($this->productListingTemplatePath === null) {
            $this->productListingTemplatePath = $this->getPublishedThemePath() . '/html/system/';
        }
        
        return $this->productListingTemplatePath;
    }
    
    
    /**
     * Returns the path to the product options templates directory, based on the shop root directory.
     *
     * @return string
     */
    public function getProductOptionsTemplatePath()
    {
        if ($this->productOptionsTemplatePath === null) {
            $this->productOptionsTemplatePath = $this->getPublishedThemePath() . '/html/system/';
        }
        
        return $this->productOptionsTemplatePath;
    }
    
    
    /**
     * Returns the path to the gm product option templates directory, based on the shop root directory.
     *
     * @return string
     */
    public function getGmProductOptionsTemplatePath()
    {
        if ($this->gmProductOptionsTemplatePath === null) {
            $this->gmProductOptionsTemplatePath = $this->getPublishedThemePath() . '/html/system/';
        }
        
        return $this->gmProductOptionsTemplatePath;
    }
    
    
    /**
     * Returns the path to the properties templates directory, based on the shop root directory.
     *
     * @return string
     */
    public function getPropertiesTemplatePath()
    {
        if ($this->propertiesTemplatePath === null) {
            $this->propertiesTemplatePath = $this->getPublishedThemePath() . '/html/system/';
        }
        
        return $this->propertiesTemplatePath;
    }
    
    
    /**
     * Returns the path to the dynamic css file, based on the shop root directory.
     *
     * @return string
     */
    public function getDynamicCssFilePath()
    {
        if ($this->dynamicCssFilePath === null) {
            $this->dynamicCssFilePath = 'dynamic_theme_style.css.php';
        }
        
        return $this->dynamicCssFilePath;
    }
    
    
    /**
     * Returns the path to the template settings file, based on the shop root directory.
     *
     * @return string
     */
    public function getTemplateSettingsFilePath()
    {
        if ($this->templateSettingsFilePath === null) {
            $this->templateSettingsFilePath = $this->getPublishedThemePath() . '/config/theme_settings.php';
        }
        
        return $this->templateSettingsFilePath;
    }
    
    
    /**
     * Returns the path to the template settings file, based on the shop root directory.
     *
     * @return array
     */
    public function getThemeSettings()
    {
        if ($this->themeSettings === null) {
            $this->themeSettings = [];
            $filename            = DIR_FS_CATALOG . $this->getTemplateSettingsFilePath();
            if (file_exists($filename)) {
                $themeSettingsArray = [];
                include $filename;
                
                if (isset($t_template_settings_array)) {
                    $themeSettingsArray = $t_template_settings_array;
                }
                
                $this->themeSettings = $themeSettingsArray;
            }
        }
        
        return $this->themeSettings;
    }
    
    
    /**
     * Returns the path to the template settings file, based on the shop root directory.
     *
     * @return float
     */
    public function getThemeVersion()
    {
        if ($this->themeVersion === null) {
            $this->themeVersion = $this->getThemeSettings()['THEME_PRESENTATION_VERSION'] ?? 1.0;
        }
        
        return $this->themeVersion;
    }
    
    
    /**
     * Determines the current theme name and theme system status.
     */
    protected function _determineCurrentThemeStatus()
    {
        $this->themeSystemActive = StyleEditServiceFactory::service()->isThemeSystemActive()
                                   || $this->viewSettings->isThemeSystemActive();
        $this->currentTheme      = StyleEditServiceFactory::service()->getCurrentTheme() ?? $this->viewSettings->name();
        $this->currentTemplate   = $this->currentTheme;
    }
    
    
    /**
     * @return ViewSettings
     */
    public function getViewSettings(): ViewSettings
    {
        if ($this->viewSettings === null) {
            $dbSettings = ['type' => 'theme', 'name' => 'Honeygrid'];
            
            $themeConfig = $this->db->select('value')
                ->from('gx_configurations')
                ->where('key', 'configuration/CURRENT_THEME')
                ->get()
                ->row_array();
            
            if ($themeConfig !== null && $themeConfig['value'] !== ''
                && is_dir(DIR_FS_CATALOG . 'themes/' . $themeConfig['value'])) {
                $dbSettings['type'] = 'theme';
                $dbSettings['name'] = $themeConfig['value'];
            }
            
            $this->viewSettings = new ThemeDBSettings($dbSettings['type'], $dbSettings['name']);
        }
        
        return $this->viewSettings;
    }
}

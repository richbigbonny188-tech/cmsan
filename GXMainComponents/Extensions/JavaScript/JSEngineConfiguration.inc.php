<?php
/* --------------------------------------------------------------
   JSEngineConfiguration.inc.php 2022-04-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class JSEngineConfiguration
 *
 * @category   System
 * @package    Extensions
 */
class JSEngineConfiguration
{
    /**
     * @var array
     */
    protected $config;
    
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * @var array
     */
    protected $sections;
    
    /**
     * @var string
     */
    protected $environment;
    
    /**
     * @var string
     */
    protected $baseUrl;
    
    /**
     * @var string
     */
    protected $templatePath;
    
    /**
     * @var string
     */
    protected $languageCode;
    
    /**
     * @var string
     */
    protected $pageToken = '';
    
    /**
     * @var string
     */
    protected $cacheToken = '';
    
    /**
     * @var bool
     */
    protected $bustFiles = false;
    
    
    public function __construct(
        NonEmptyStringType $baseUrl,
        NonEmptyStringType $templatePath,
        LanguageCode $languageCode,
        LanguageTextManager $languageTextManager,
        EditableKeyValueCollection $sections,
        BoolType $debugMode,
        StringType $pageToken = null,
        StringType $cacheToken = null
    ) {
        $this->baseUrl             = $baseUrl->asString();
        $this->templatePath        = $templatePath->asString();
        $this->languageCode        = strtolower($languageCode->asString());
        $this->languageTextManager = $languageTextManager;
        $this->sections            = $sections->getArray();
        $this->environment         = $debugMode->asBool() ? 'development' : 'production';
        $this->pageToken           = !empty($pageToken) ? $pageToken->asString() : '';
        $this->cacheToken          = !empty($cacheToken) ? $cacheToken->asString() : '';
        if (isset($_SERVER['gambio_mod_rewrite_working'], $_SERVER['gambio_htaccessVersion'])
            && (bool)$_SERVER['gambio_mod_rewrite_working']
            && version_compare($_SERVER['gambio_htaccessVersion'], '2.8') >= 0
            && @constant('USE_BUSTFILES') === 'true') {
            $this->bustFiles = true;
        }
    }
    
    
    /**
     * Get JSEngine configuration as an JSON encoded string
     *
     * @return string
     */
    public function asJson()
    {
        if ($this->config === null) {
            $this->_init();
        }
        
        return json_encode($this->config);
    }
    
    
    /**
     * @return string
     */
    public function getJavaScript()
    {
        return 'window.JSEngineConfiguration = ' . $this->asJson() . ';';
    }
    
    
    /**
     * Initialize JSEngine configuration.
     */
    protected function _init()
    {
        $this->config = [
            'environment'  => $this->environment,
            'appUrl'       => $this->baseUrl,
            'tplPath'      => $this->templatePath,
            'translations' => $this->_getTranslations(),
            'languageCode' => $this->languageCode,
            'pageToken'    => $this->pageToken,
            'cacheToken'   => $this->cacheToken,
            'bustFiles'    => $this->bustFiles,
            'vue'          => [
                'el' => '.vue-instance'
            ],
            'registry'     => [
                'userId'     => $_SESSION['customer_id'] ?? null,
                'languageId' => $_SESSION['languages_id'] ?? '2'
            ]
        ];
    }
    
    
    /**
     * Get translations array.
     *
     * @return string
     */
    protected function _getTranslations()
    {
        $translations = [];
        $sections     = $this->_getSections();
        
        foreach ($sections as $key => $section) {
            $translations[$key] = $this->languageTextManager->get_section_array($section);
        }
        
        return $translations;
    }
    
    
    /**
     * Helper method for adding additional language sections.
     *
     * Overload example for adding the section "section_name":
     *
     * protected function _getSections()
     * {
     *     $additionalSection = array('js_section_name' => 'section_name');
     *     $this->sections = array_merge($this->sections, $additionalSection);
     *
     *     return parent::_getSections();
     * }
     *
     * Use in JS:
     * jse.core.lang.translate('phrase_name', 'js_section_name') // phrase value will be returned;
     *
     * Visit https://developers.gambio.de for more information.
     *
     * @return array
     */
    protected function _getSections()
    {
        return $this->sections;
    }
}

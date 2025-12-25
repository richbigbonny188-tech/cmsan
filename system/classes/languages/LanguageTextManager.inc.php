<?php
/* --------------------------------------------------------------
  LanguageTextManager.inc.php 2022-12-16
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Example:
 * $textManager = MainFactory::create_object('LanguageTextManager', array('index', $_SESSION['languages_id']), true);
 * echo 'test: '. $textManager->get_text('wishlist');
 *
 * Class LanguageTextManager
 */
class LanguageTextManager
{
	protected static $db;
	protected static $languagePhrasesCacheTable = 'language_phrases_cache';

	protected $shopWideDefaultLanguage   = null;
	protected $defaultLanguageId = 0;
	protected $defaultSection    = '';
	protected static $languages  = array();
	protected static $sectionMappings;
	protected $sectionsContent   = array();
	protected $useFallback       = true;


	/**
	 * @param string $p_defaultSection
	 * @param int    $p_defaultLanguageId
	 * @param bool   $p_useFallback
	 */
	public function __construct($p_defaultSection = '', $p_defaultLanguageId = 0, $p_useFallback = true)
	{
		$this->_initLanguages();
		$this->_initMappingArray();

		if((int)$p_defaultLanguageId > 0)
		{
			$this->defaultLanguageId = (int)$p_defaultLanguageId;
		}
		elseif(isset($_SESSION['languages_id']))
		{
			$this->defaultLanguageId = (int)($_SESSION['languages_id'] ?? null);
		}

		$this->useFallback = (bool)$p_useFallback;
		$defaultSection    = (string)$p_defaultSection;

		if($defaultSection !== '')
		{
			$this->defaultSection    = $defaultSection;
            
            $this->_initSection($this->defaultSection, $this->defaultLanguageId);
		}
	}


	/**
	 * @param string $p_defaultSection
	 * @param int    $p_defaultLanguageId
	 * @param bool   $p_useFallback
	 *
	 * @return LanguageTextManager
	 */
	static function get_instance($p_defaultSection = '', $p_defaultLanguageId = 0, $p_useFallback = true)
	{
		/** @var LanguageTextManager $instance */
		static $instance;

		if($instance === null)
		{
			/** @var LanguageTextManager $instance */
			$instance = MainFactory::create_object('LanguageTextManager',
			                                       array($p_defaultSection, $p_defaultLanguageId, $p_useFallback));
		}
		else
		{
			$defaultLanguageId = (int)$p_defaultLanguageId;
			if($defaultLanguageId === 0 && isset($_SESSION['languages_id']))
			{
				$defaultLanguageId = (int)($_SESSION['languages_id'] ?? null);
			}

			if($instance->defaultSection != $p_defaultSection || $instance->defaultLanguageId != $defaultLanguageId)
			{
				# re-init with new parameters
				$instance->defaultSection    = $p_defaultSection;
				$instance->defaultLanguageId = $defaultLanguageId;

				$instance->_initSection($p_defaultSection, $p_defaultLanguageId);
			}
		}

		return $instance;
	}


	/**
	 * @param string $p_phraseName
	 * @param string $p_section
	 * @param int    $p_languageId
	 *
	 * @return string
	 */
	public function get_text($p_phraseName, $p_section = '', $p_languageId = 0)
	{
		if($p_section === '')
		{
			$section = $this->defaultSection;
		}
		else
		{
			$section = $this->_getSectionName($p_section);
		}

		$languageId = (int)$p_languageId;
		if($languageId === 0)
		{
			$languageId = $this->defaultLanguageId;
		}

		# section content already available?
        if (!isset($this->sectionsContent[$section][$languageId])) {
            $sectionContent = $this->findSectionContentInCache($section, $languageId);
            if (!empty($sectionContent)) {
                $this->_addSection($section, $sectionContent, $languageId);
            } else {
                $this->_initSection($section, $languageId);
            }
        }

		# get var value and return
		$phraseName = (string)$p_phraseName;
		$phraseText = $phraseName;
        if (isset($this->sectionsContent[$section][$languageId][$phraseName])) {
            $phraseText = $this->sectionsContent[$section][$languageId][$phraseName];
        } elseif ($this->useFallback
                  && isset($this->sectionsContent[$section][self::getFallbackLanguageId()][$phraseName])) {
            $phraseText = $this->sectionsContent[$section][self::getFallbackLanguageId()][$phraseName];
        }

		return $phraseText;
	}


	/**
	 * @param string $p_filePath
	 * @param int    $p_languageId
	 */
	public function init_from_lang_file($p_filePath, $p_languageId = 0)
	{
		if($p_languageId !== 0)
		{
			$c_languageId = (int)$p_languageId;
		}
		else
		{
			$c_languageId = $this->_getLanguageIdByFilePath($p_filePath);
			if($c_languageId === 0)
			{
				$c_languageId = $this->defaultLanguageId;
			}
		}

		$sectionName = $this->_getSectionName($p_filePath);

		$this->_initSection($sectionName, $c_languageId);
		$this->_initConstants($sectionName, $c_languageId);
		$this->_initConstantsFromDeprecatedLangFile($p_filePath);
	}


	/**
	 * @param string $p_section
	 * @param int    $p_languageId
	 *
	 * @return string
	 */
	public function get_section_array($p_section = '', $p_languageId = 0)
	{
		if($p_section === '')
		{
			$section = $this->defaultSection;
		}
		else
		{
			$section = $p_section;
		}

		$languageId = (int)$p_languageId;
		if($languageId === 0)
		{
			$languageId = $this->defaultLanguageId;
		}

		# section content already available?
		if(isset($this->sectionsContent[$section][$languageId]) === false)
		{
			# do init if not
			$this->_initSection($section, $languageId);
		}

		$sectionArray = $this->sectionsContent[$section][$languageId];

		return $sectionArray;
	}


	/**
	 * Returns the default language ID
	 *
	 * @return int The default language id
	 */
    public function getDefaultLanguageId()
    {
        if ($this->shopWideDefaultLanguage === null) {
            $fallbackLanguageId = self::getFallbackLanguageId();
            
            if ($fallbackLanguageId !== 0) {
                $this->shopWideDefaultLanguage = $fallbackLanguageId;
            }
        }
        
        return $this->shopWideDefaultLanguage;
    }
    
    
    public static function clearCache()
    {
        $dataCache = DataCache::get_instance();
        $dataCache->clear_cache('LanguageTextManager*');
    }


	/**
	 * @param string $p_section
	 * @param int    $p_languageId
	 */
	protected function _initSection($p_section, $p_languageId = 0)
	{
		$section = $this->_getSectionName($p_section);

		$stopWatch = LogControl::get_instance()->get_stop_watch();
		$stopWatch->start('init_section');

		// read fallback section phrases
		$languageId = ($p_languageId != 0) ? (int)$p_languageId : $this->defaultLanguageId;
        $this->_resetSection($section, $languageId);
        
        if ($this->useFallback && self::getFallbackLanguageId() !== 0
            && $languageId !== self::getFallbackLanguageId()) {
            $sectionContent = $this->findSectionContentInCache($section, self::getFallbackLanguageId());
            if (!empty($sectionContent)) {
                $this->_addSection($section,
                                   $sectionContent,
                                   self::getFallbackLanguageId());
            } else {
                // read section from fallback language
                $this->_readSectionFromDB($section, self::getFallbackLanguageId());
                $this->writeCache($section, self::getFallbackLanguageId());
            }
        }
        
        $sectionContent = $this->findSectionContentInCache($section, $p_languageId);
        if (!empty($sectionContent)) {
            $this->_addSection($section, $sectionContent, $p_languageId);
        } else {
            $this->_readSectionFromDB($section, $p_languageId);
            $this->writeCache($section, $p_languageId);
        }

		$stopWatch->stop('init_section');
	}


	/**
	 * @param string $p_section
	 * @param array  $p_sectionArray
	 * @param int    $languageId
	 */
	protected function _addSection($p_section, array $p_sectionArray, int $languageId)
	{
        if (isset($this->sectionsContent[$p_section][$languageId])) {
            $this->sectionsContent[$p_section][$languageId] = array_merge($this->sectionsContent[$p_section][$languageId],
                                                                          $p_sectionArray);
        } elseif (isset($this->sectionsContent[$p_section])) {
            $this->sectionsContent[$p_section][$languageId] = $p_sectionArray;
        } else {
            $this->sectionsContent[$p_section] = [$languageId => $p_sectionArray];
        }
	}


	/**
	 * @param string $p_section
	 */
	protected function _resetSection(string $p_section, int $languageId)
	{
        if (isset($this->sectionsContent[$p_section])) {
            $this->sectionsContent[$p_section][$languageId] = [];
        }
        
        $this->sectionsContent[$p_section] = [$languageId => []];
	}


	/**
	 * old lang file paths are mapped to a section name
	 */
	protected function _initMappingArray()
	{
		if(!empty(self::$sectionMappings))
		{
			return;
		}
		
		$mappingArrayGerman = require __DIR__ . '/LanguageMappingArrayGerman.inc.php';
		$mappingArrayEnglish = require __DIR__ . '/LanguageMappingArrayEnglish.inc.php';
		self::$sectionMappings = array_merge($mappingArrayGerman, $mappingArrayEnglish);

		$mappingArray = require __DIR__ . '/LanguageMappingArray.inc.php';
		
		foreach(self::$languages as $languageArray)
		{
			if(in_array($languageArray['directory'], ['german', 'english']))
			{
				continue;
			}
			foreach($mappingArray as $old => $new)
			{
				$old = 'lang/' . $languageArray['directory'] . '/' . sprintf($old, $languageArray['directory']);
				$new = sprintf($new, $languageArray['directory']);

				self::$sectionMappings[$old] = $new;
			}
		}
	}


	/**
	 * @param string $p_section
	 * @param int    $p_languageId
	 */
	protected function _readSectionFromDB($p_section, $p_languageId)
	{
        static $sectionsCache = [];

        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();

        if (!isset($sectionsCache[$p_languageId][$p_section])) {
            $results = $db->select('phrase_name, phrase_text')
                ->from(self::$languagePhrasesCacheTable)
                ->where([
                            'language_id' => $p_languageId,
                            'section_name' => $p_section
                        ])
                ->get()
                ->result_array();

            $sectionArray = [];

            foreach ($results as $row) {
                $sectionArray[$row['phrase_name']] = $row['phrase_text'];
            }

            $sectionsCache[$p_languageId][$p_section] = $sectionArray;
        }

        $this->_addSection($p_section, $sectionsCache[$p_languageId][$p_section], $p_languageId);
    }


	/**
	 * @param string $p_section
	 * @param int    $languageId
	 */
	protected function _initConstants(string $p_section, int $languageId)
	{
		foreach($this->sectionsContent[$p_section][$languageId] as $phraseName => $phraseText)
		{
			if(defined($phraseName) == false)
			{
				define($phraseName, $phraseText);
			}
		}
	}


	/**
	 * @param $p_filePath
	 *
	 * @return int
	 */
	protected function _getLanguageIdByFilePath($p_filePath)
	{
		$languageId    = 0;
		$languageArray = $this->_getLanguageArrayByFilePath($p_filePath);
		if(!empty($languageArray) && isset($languageArray['language_id']))
		{
			$languageId = (int)$languageArray['language_id'];
		}

		return $languageId;
	}


	/**
	 * @param $p_filePath
	 *
	 * @return array
	 */
	protected function _getLanguageArrayByFilePath($p_filePath)
	{
		$languageArray = array();
		foreach(self::$languages as $language)
		{
			if(strpos($p_filePath, 'lang/' . $language['directory'] . '/') !== false)
			{
				$languageArray = $language;
				break;
			}
		}

		return $languageArray;
	}


	/**
	 * load languages from database
	 */
	protected function _initLanguages()
	{
		if(!empty(self::$languages))
		{
			return;
		}

		$query  = 'SELECT * FROM ' . TABLE_LANGUAGES;
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result))
		{
			self::$languages[$row['code']]                = array();
			self::$languages[$row['code']]['language_id'] = (int)$row['languages_id'];
			self::$languages[$row['code']]['directory']   = $row['directory'];
		}
	}


	/**
	 * @param string $p_section
	 *
	 * @return string
	 */
	protected function _getSectionName($p_section)
	{
		$this->_initMappingArray();

		if(isset(self::$sectionMappings[$p_section]))
		{
			$p_section = self::$sectionMappings[$p_section];
		}

		return $p_section;
	}


	/**
	 * support for deprecated language files containing define() statements
	 *
	 * @param string $p_filePath
	 */
	protected function _initConstantsFromDeprecatedLangFile($p_filePath)
	{
		if(file_exists(DIR_FS_CATALOG . $p_filePath) && strpos($p_filePath, '..') === false)
		{
			$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
			include_once DIR_FS_CATALOG . $p_filePath;
		}
	}
    
    
    /**
     * @return int
     */
    protected static function getFallbackLanguageId(): int
    {
        if (defined('DEFAULT_LANGUAGE') && isset(self::$languages[DEFAULT_LANGUAGE]['language_id'])) {
            return self::$languages[DEFAULT_LANGUAGE]['language_id'];
        }
    
        return 0;
    }
    
    
    /**
     * @param string $section
     * @param int    $languageId
     *
     * @return array|null
     */
    protected function findSectionContentInCache(string $section, int $languageId): ?array
    {
        $dataCache = DataCache::get_instance();
        if ($dataCache->key_exists($this->getCacheKey($section, $languageId), true)) {
            return $dataCache->get_data($this->getCacheKey($section, $languageId));
        }
        
        return null;
    }
    
    
    /**
     * @param string $section
     * @param int    $languageId
     *
     * @return void
     */
    protected function writeCache(string $section, int $languageId): void
    {
        $dataCache = DataCache::get_instance();
        $dataCache->set_data($this->getCacheKey($section, $languageId),
                             $this->sectionsContent[$section][$languageId],
                             true);
    }
    
    
    /**
     * @param string $section
     * @param int    $languageId
     *
     * @return string
     */
    protected function getCacheKey(string $section, int $languageId): string
    {
        return 'LanguageTextManager-' . $languageId . '-' . $section;
    }
}

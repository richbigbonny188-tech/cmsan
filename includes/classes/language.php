<?php
/* --------------------------------------------------------------
   language.php 2018-09-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class language_ORIGIN
{
	public $languages;
	public $catalog_languages;
	public $admin_languages;
	public $browser_languages;
	public $language;
	
	
	public function __construct($lng = '')
	{
		$this->languages = [
			'ar'         => ['ar([-_][[:alpha:]]{2})?|arabic', 'arabic', 'ar'],
			'bg-win1251' => ['bg|bulgarian', 'bulgarian-win1251', 'bg'],
			'bg-koi8r'   => ['bg|bulgarian', 'bulgarian-koi8', 'bg'],
			'ca'         => ['ca|catalan', 'catala', 'ca'],
			'cs-iso'     => ['cs|czech', 'czech-iso', 'cs'],
			'cs-win1250' => ['cs|czech', 'czech-win1250', 'cs'],
			'da'         => ['da|danish', 'danish', 'da'],
			'de'         => ['de([-_][[:alpha:]]{2})?|german', 'german', 'de'],
			'el'         => ['el|greek', 'greek', 'el'],
			'en'         => ['en([-_][[:alpha:]]{2})?|english', 'english', 'en'],
			'es'         => ['es([-_][[:alpha:]]{2})?|spanish', 'spanish', 'es'],
			'et'         => ['et|estonian', 'estonian', 'et'],
			'fi'         => ['fi|finnish', 'finnish', 'fi'],
			'fr'         => ['fr([-_][[:alpha:]]{2})?|french', 'french', 'fr'],
			'gl'         => ['gl|galician', 'galician', 'gl'],
			'he'         => ['he|hebrew', 'hebrew', 'he'],
			'hu'         => ['hu|hungarian', 'hungarian', 'hu'],
			'id'         => ['id|indonesian', 'indonesian', 'id'],
			'it'         => ['it|italian', 'italian', 'it'],
			'ja-euc'     => ['ja|japanese', 'japanese-euc', 'ja'],
			'ja-sjis'    => ['ja|japanese', 'japanese-sjis', 'ja'],
			'ko'         => ['ko|korean', 'korean', 'ko'],
			'ka'         => ['ka|georgian', 'georgian', 'ka'],
			'lt'         => ['lt|lithuanian', 'lithuanian', 'lt'],
			'lv'         => ['lv|latvian', 'latvian', 'lv'],
			'nl'         => ['nl([-_][[:alpha:]]{2})?|dutch', 'dutch', 'nl'],
			'no'         => ['no|norwegian', 'norwegian', 'no'],
			'pl'         => ['pl|polish', 'polish', 'pl'],
			'pt-br'      => ['pt[-_]br|brazilian portuguese', 'brazilian_portuguese', 'pt-BR'],
			'pt'         => ['pt([-_][[:alpha:]]{2})?|portuguese', 'portuguese', 'pt'],
			'ro'         => ['ro|romanian', 'romanian', 'ro'],
			'ru-koi8r'   => ['ru|russian', 'russian-koi8', 'ru'],
			'ru-win1251' => ['ru|russian', 'russian-win1251', 'ru'],
			'sk'         => ['sk|slovak', 'slovak-iso', 'sk'],
			'sk-win1250' => ['sk|slovak', 'slovak-win1250', 'sk'],
			'sr-win1250' => ['sr|serbian', 'serbian-win1250', 'sr'],
			'sv'         => ['sv|swedish', 'swedish', 'sv'],
			'th'         => ['th|thai', 'thai', 'th'],
			'tr'         => ['tr|turkish', 'turkish', 'tr'],
			'uk-win1251' => ['uk|ukrainian', 'ukrainian-win1251', 'uk'],
			'zh-tw'      => ['zh[-_]tw|chinese traditional', 'chinese_big5', 'zh-TW'],
			'zh'         => ['zh|chinese simplified', 'chinese_gb', 'zh']
		];
		
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		
		$languages = $db->select('*')
		                ->select('languages_id AS id')
		                ->order_by('sort_order')
		                ->get('languages')
		                ->result_array();
		
		$this->catalog_languages = [];
		foreach($languages as $language)
		{
			$this->catalog_languages[$language['code']] = $language;
		}
		
		$this->browser_languages = '';
		$this->language          = '';
		
		if(!empty($lng) && isset($this->catalog_languages[$lng]))
		{
			$this->language = $this->catalog_languages[$lng];
		}
		else
		{
			$this->language = $this->catalog_languages[DEFAULT_LANGUAGE];
		}
	}
	
	
	public function get_browser_language()
	{
		$this->browser_languages = explode(',', getenv('HTTP_ACCEPT_LANGUAGE'));
		
		for($i = 0, $n = count($this->browser_languages); $i < $n; $i++)
		{
			reset($this->languages);
			foreach($this->languages as $langCode => $langData)
			{
				if(isset($this->catalog_languages[$langCode])
				   && (bool)$this->catalog_languages[$langCode]['status'] === true
				   && preg_match('/^(' . $langData[0] . ')(;q=[0-9]\\.[0-9])?$/i', $this->browser_languages[$i]))
				{
					$this->language = $this->catalog_languages[$langCode];
					break 2;
				}
			}
		}
	}
}

MainFactory::load_origin_class('language');

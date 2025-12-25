<?php
/* --------------------------------------------------------------
   HyphenopolyLanguageMappingProvider.inc.php 2018-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class HyphenopolyLanguageMappingProvider
{
    
    /**
     * Maps the shop’s language to Hyphenopoly’s language.
     *
     * @param \LanguageCode $languageCode
     *
     * @return string
     */
    public function getHyphenopolyLanguage(LanguageCode $languageCode)
    {
        $languageCodeString      = strtolower($languageCode->asString());
        $languageMap             = [
            'af'  => 'af',
            'as'  => 'as',
            'be'  => 'be',
            'bg'  => 'bg',
            'bn'  => 'bn',
            'ca'  => 'ca',
            'cs'  => 'cs',
            'cu'  => 'cu',
            'cy'  => 'cy',
            'da'  => 'da',
            'de'  => 'de',
            'el'  => 'el-monoton',
            //'el' => 'el-polyton',
            'en'  => 'en-gb',
            //'en' => 'en-us',
            'eo'  => 'eo',
            'es'  => 'es',
            'et'  => 'et',
            'eu'  => 'eu',
            'fi'  => 'fi',
            'fr'  => 'fr',
            'fur' => 'fur',
            'ga'  => 'ga',
            'gl'  => 'gl',
            'grc' => 'grc',
            'gu'  => 'gu',
            'hi'  => 'hi',
            'hr'  => 'hr',
            'hsb' => 'hsb',
            'hu'  => 'hu',
            'hy'  => 'hy',
            'ia'  => 'ia',
            'id'  => 'id',
            'is'  => 'is',
            'it'  => 'it',
            'ka'  => 'ka',
            'kmr' => 'kmr',
            'kn'  => 'kn',
            //'la' => 'la__',
            'la'  => 'la',
            //'la' => 'la-x-liturgic',
            'lt'  => 'lt',
            'lv'  => 'lv',
            'ml'  => 'ml',
            'mn'  => 'mn-cyrl',
            'mr'  => 'mr',
            //'mul-ethi' => 'mul-ethi',
            'nb'  => 'nb-no',
            'no'  => 'nb-no',
            'nl'  => 'nl',
            'nn'  => 'nn',
            'oc'  => 'oc',
            'or'  => 'or',
            'pa'  => 'pa',
            'pl'  => 'pl',
            'pms' => 'pms',
            'pt'  => 'pt',
            'rm'  => 'rm',
            'ro'  => 'ro',
            'ru'  => 'ru',
            'sa'  => 'sa',
            'sh'  => 'sh-cyrl',
            //'sh' => 'sh-latn',
            'sk'  => 'sk',
            'sl'  => 'sl',
            'sr'  => 'sr-cyrl',
            'sv'  => 'sv',
            'ta'  => 'ta',
            'te'  => 'te',
            'th'  => 'th',
            'tk'  => 'tk',
            'tr'  => 'tr',
            'uk'  => 'uk',
            'zh'  => 'zh-latn-pinyin',
        ];
        $hyphenopolyLanguageCode = '';
        if (array_key_exists($languageCodeString, $languageMap)) {
            $hyphenopolyLanguageCode = $languageMap[$languageCodeString];
        }
        
        return $hyphenopolyLanguageCode;
    }
}

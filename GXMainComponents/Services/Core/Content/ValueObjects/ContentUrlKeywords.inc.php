<?php

/* --------------------------------------------------------------
   ContentUrlKeywords.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentUrlKeywords
 *
 * This class represents the localized content URL keywords
 *
 * @category   System
 * @package    Content
 */
class ContentUrlKeywords implements LocalizedContentAttributeInterface
{
    /**
     * Content URL keywords
     *
     * @var string
     */
    protected $urlKeywords;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentUrlKeywords constructor
     *
     * @param string       $urlKeywords  Content  URL keywords
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(string $urlKeywords, LanguageCode $languageCode)
    {
        $this->urlKeywords  = $urlKeywords;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content URL keywords
     *
     * @return string
     */
    public function content(): string
    {
        return $this->urlKeywords;
    }
    
    
    /**
     * Return the language code
     *
     * @return string
     */
    public function languageCode(): string
    {
        return $this->languageCode;
    }
}

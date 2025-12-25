<?php

/* --------------------------------------------------------------
   ContentMetaKeywords.inc.php 2019-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentMetaKeywords
 *
 * This class represents the localized content meta keywords
 *
 * @category   System
 * @package    Content
 */
class ContentMetaKeywords implements LocalizedContentAttributeInterface
{
    /**
     * Content meta keywords
     *
     * @var string
     */
    protected $metaKeywords;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentMetaKeywords constructor
     *
     * @param string       $metaKeywords Content meta keywords
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(string $metaKeywords, LanguageCode $languageCode)
    {
        $this->metaKeywords = $metaKeywords;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content meta keywords
     *
     * @return string
     */
    public function content(): string
    {
        return $this->metaKeywords;
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

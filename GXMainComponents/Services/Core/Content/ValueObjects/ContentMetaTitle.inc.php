<?php

/* --------------------------------------------------------------
   ContentMetaTitle.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentMetaTitle
 *
 * This class represents the localized content meta title
 *
 * @category   System
 * @package    Content
 */
class ContentMetaTitle implements LocalizedContentAttributeInterface
{
    /**
     * Content meta title
     *
     * @var string
     */
    protected $metaTitle;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentMetaTitle constructor
     *
     * @param string       $metaTitle    Content meta title
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(string $metaTitle, LanguageCode $languageCode)
    {
        $this->metaTitle    = $metaTitle;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content meta title
     *
     * @return string
     */
    public function content(): string
    {
        return $this->metaTitle;
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

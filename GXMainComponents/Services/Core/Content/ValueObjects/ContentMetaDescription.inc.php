<?php

/* --------------------------------------------------------------
   ContentMetaDescription.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentMetaDescription
 *
 * This class represents the localized content meta description
 *
 * @category   System
 * @package    Content
 */
class ContentMetaDescription implements LocalizedContentAttributeInterface
{
    /**
     * Content meta description
     *
     * @var string
     */
    protected $metaDescription;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentMetaDescription constructor
     *
     * @param string       $metaDescription Content meta description
     * @param LanguageCode $languageCode    Language code
     */
    public function __construct(string $metaDescription, LanguageCode $languageCode)
    {
        $this->metaDescription = $metaDescription;
        $this->languageCode    = $languageCode->asString();
    }
    
    
    /**
     * Return the content meta description
     *
     * @return string
     */
    public function content(): string
    {
        return $this->metaDescription;
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

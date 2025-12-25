<?php

/* --------------------------------------------------------------
   ContentOpengraphImage.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentOpengraphImage
 *
 * This class represents the localized content OpenGraph image
 *
 * @category   System
 * @package    Content
 */
class ContentOpengraphImage implements LocalizedContentAttributeInterface
{
    /**
     * Content OpenGraph image
     *
     * @var string
     */
    protected $opengraphImage;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentOpengraphImage constructor
     *
     * @param string       $opengraphImage Content OpenGraph image
     * @param LanguageCode $languageCode   Language code
     */
    public function __construct(string $opengraphImage, LanguageCode $languageCode)
    {
        $this->opengraphImage = $opengraphImage;
        $this->languageCode   = $languageCode->asString();
    }
    
    
    /**
     * Return the content OpenGraph image
     *
     * @return string
     */
    public function content(): string
    {
        return $this->opengraphImage;
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

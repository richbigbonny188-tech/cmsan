<?php
/* --------------------------------------------------------------
   ContentLink.inc.php 2023-05-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentLink
 *
 * This class represents the localized content link
 *
 * @category   System
 * @package    Content
 */
class ContentLink implements LocalizedContentAttributeInterface
{
    /**
     * Content link
     *
     * @var string
     */
    protected $link;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentLink constructor
     *
     * @param string       $link         Content link
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(string $link, LanguageCode $languageCode)
    {
        $this->link         = $link;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content link
     *
     * @return string
     */
    public function content(): string
    {
        return $this->link;
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
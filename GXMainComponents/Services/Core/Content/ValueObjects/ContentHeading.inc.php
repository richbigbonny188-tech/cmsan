<?php

/* --------------------------------------------------------------
   ContentHeading.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentHeading
 *
 * This class represents the localized content heading
 *
 * @category   System
 * @package    Content
 */
class ContentHeading implements LocalizedContentAttributeInterface
{
    /**
     * Content heading
     *
     * @var string
     */
    protected $heading;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentHeading constructor
     *
     * @param string       $heading      Content heading
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(string $heading, LanguageCode $languageCode)
    {
        $this->heading      = $heading;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content heading
     *
     * @return string
     */
    public function content(): string
    {
        return $this->heading;
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
<?php

/* --------------------------------------------------------------
   ContentName.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentName
 *
 * This class represents the localized content name
 *
 * @category   System
 * @package    Content
 */
class ContentName implements LocalizedContentAttributeInterface
{
    /**
     * Content name
     *
     * @var string
     */
    protected $name;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentName constructor
     *
     * @param string       $name         Content name
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(string $name, LanguageCode $languageCode)
    {
        $this->name         = $name;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content name
     *
     * @return string
     */
    public function content(): string
    {
        return $this->name;
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

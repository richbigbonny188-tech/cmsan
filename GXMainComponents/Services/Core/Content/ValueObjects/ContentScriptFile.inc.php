<?php

/* --------------------------------------------------------------
   ContentScriptFile.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentScriptFile
 *
 * This class represents the localized content script file
 *
 * @category   System
 * @package    Content
 */
class ContentScriptFile implements LocalizedContentAttributeInterface
{
    /**
     * Content script file
     *
     * @var string
     */
    protected $scriptFile;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentScriptFile constructor
     *
     * @param string $scriptFile   Content script file
     * @param LanguageCode       $languageCode Language code
     */
    public function __construct(string $scriptFile, LanguageCode $languageCode)
    {
        $this->scriptFile   = $scriptFile;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content script file
     *
     * @return string
     */
    public function content(): string
    {
        return $this->scriptFile;
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
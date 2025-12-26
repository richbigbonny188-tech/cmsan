<?php

/* --------------------------------------------------------------
   ContentDownloadFile.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentDownloadFile
 *
 * This class represents the localized content download file
 *
 * @category   System
 * @package    Content
 */
class ContentDownloadFile implements LocalizedContentAttributeInterface
{
    /**
     * Content download file
     *
     * @var string
     */
    protected $downloadFile;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentDownloadFile constructor
     *
     * @param string       $downloadFile Content download file
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(string $downloadFile, LanguageCode $languageCode)
    {
        $this->downloadFile = $downloadFile;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content download file
     *
     * @return string
     */
    public function content(): string
    {
        return $this->downloadFile;
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
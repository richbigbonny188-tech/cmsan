<?php
/* --------------------------------------------------------------
   ContentOpenInNewTabStatus.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentOpenInNewTabStatus
 *
 * This class represents whether the content is opened in a new tab
 *
 * @category   System
 * @package    Content
 */
class ContentOpenInNewTabStatus
{
    /**
     * Open in a new tab status
     *
     * @var bool
     */
    protected $openInNewTab;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentOpenInNewTabStatus constructor
     *
     * @param bool $openInNewTab Whether the content is opened in a new tab
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(bool $openInNewTab, LanguageCode $languageCode)
    {
        $this->openInNewTab = $openInNewTab;
        $this->languageCode = $languageCode;
    }
    
    
    /**
     * Return whether the content is opened in a new tab
     *
     * @return bool
     */
    public function opensInNewTab(): bool
    {
        return $this->openInNewTab;
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
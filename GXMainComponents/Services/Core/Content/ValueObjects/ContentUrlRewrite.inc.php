<?php

/* --------------------------------------------------------------
   ContentUrlRewrite.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentUrlRewrite
 *
 * This class represents the localized content URL rewrite
 *
 * @category   System
 * @package    Content
 */
class ContentUrlRewrite implements LocalizedContentAttributeInterface
{
    /**
     * Content URL rewrite
     *
     * @var string
     */
    protected $urlRewrite;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentUrlRewrite constructor
     *
     * @param string       $urlRewrite   Content  URL rewrite
     * @param LanguageCode $languageCode Language code
     */
    public function __construct(string $urlRewrite, LanguageCode $languageCode)
    {
        $this->urlRewrite   = $urlRewrite;
        $this->languageCode = $languageCode->asString();
    }
    
    
    /**
     * Return the content URL rewrite
     *
     * @return string
     */
    public function content(): string
    {
        return $this->urlRewrite;
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

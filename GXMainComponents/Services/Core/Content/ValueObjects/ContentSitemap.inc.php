<?php
/* --------------------------------------------------------------
   ContentSitemap.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentSitemap
 *
 * This class represents the content sitemap information
 *
 * @category   System
 * @package    Content
 */
class ContentSitemap
{
    /**
     * Sitemap content visibility
     *
     * @var bool
     */
    protected $visible;
    
    /**
     * Sitemap content priorit
     *
     * @var string
     */
    protected $priority;
    
    /**
     * Sitemap content change frequency
     *
     * @var string
     */
    protected $changeFrequency;
    
    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode;
    
    
    /**
     * ContentSitemap constructor
     *
     * @param ContentSitemapVisibility      $visibility      Site map content visibility
     * @param ContentSitemapPriority        $priority        Site map content priority collection
     * @param ContentSitemapChangeFrequency $changeFrequency Site map content change frequency
     * @param LanguageCode                  $languageCode    Language code
     */
    public function __construct(
        ContentSitemapVisibility      $visibility,
        ContentSitemapPriority        $priority,
        ContentSitemapChangeFrequency $changeFrequency,
        LanguageCode                  $languageCode
    ) {
        $this->visible         = $visibility->isVisible();
        $this->priority        = $priority->content();
        $this->changeFrequency = $changeFrequency->frequencyOfChange();
        $this->languageCode    = $languageCode->asString();
    }
    
    
    /**
     * Return whether the content is visible in the site map
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }
    
    
    /**
     * Return the site map content priority
     *
     * @return string
     */
    public function priority(): string
    {
        return $this->priority;
    }
    
    
    /**
     * Return the change frequency of the content
     *
     * @return string
     */
    public function frequencyOfChange(): string
    {
        return $this->changeFrequency;
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
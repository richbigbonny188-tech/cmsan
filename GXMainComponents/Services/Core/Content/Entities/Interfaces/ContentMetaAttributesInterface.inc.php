<?php

/* --------------------------------------------------------------
   ContentMetaAttributesInterface.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentMetaAttributesInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentMetaAttributesInterface
{
    /**
     * Return the localized content meta titles
     *
     * @return ContentMetaTitleCollection
     */
    public function metaTitles(): ?ContentMetaTitleCollection;
    
    
    /**
     * Return the localized content meta titles
     *
     * @return ContentMetaKeywordsCollection
     */
    public function metaKeywords(): ?ContentMetaKeywordsCollection;
    
    
    /**
     * Return the localized content meta descriptions
     *
     * @return ContentMetaDescriptionCollection
     */
    public function metaDescriptions(): ?ContentMetaDescriptionCollection;
    
    
    /**
     * Return the localized content URL keywords
     *
     * @return ContentUrlKeywordsCollection
     */
    public function urlKeywords(): ?ContentUrlKeywordsCollection;
    
    
    /**
     * Return the localized content URL rewrites
     *
     * @return ContentUrlRewriteCollection
     */
    public function urlRewrites(): ?ContentUrlRewriteCollection;
    
    
    /**
     * Return the localized content sitemap information
     *
     * @return ContentSitemapCollection
     */
    public function sitemaps(): ?ContentSitemapCollection;
    
    
    /**
     * Return the localized content allow robots statuses
     *
     * @return ContentAllowRobotsStatusCollection|null
     */
    public function allowRobotsStatuses(): ?ContentAllowRobotsStatusCollection;
    
    
    /**
     * Return the localized OpenGraph images
     *
     * @return ContentOpengraphImageCollection
     */
    public function opengraphImages(): ?ContentOpengraphImageCollection;
}
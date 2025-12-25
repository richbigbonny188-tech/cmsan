<?php

/* --------------------------------------------------------------
   InfoPageContentBuilderInterface.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InfoPageContentBuilderInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface InfoPageContentBuilderInterface
{
    /**
     * Set the page position
     *
     * @param PagePosition $position Page position
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function inPosition(PagePosition $position): InfoPageContentBuilder;
    
    
    /**
     * Set the activation status
     *
     * @param ContentStatusCollection $status Activation status
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingStatus(ContentStatusCollection $status): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content titles
     *
     * @param ContentTitleCollection $titles Localized titles
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingTitles(ContentTitleCollection $titles): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content headings
     *
     * @param ContentHeadingCollection $headings Localized headings
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingHeadings(ContentHeadingCollection $headings): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content texts
     *
     * @param ContentTextCollection $texts Localized texts
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingTexts(ContentTextCollection $texts): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content download files
     *
     * @param ContentDownloadFileCollection $downloads Localized download files
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingDownloadFiles(ContentDownloadFileCollection $downloads): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content meta titles
     *
     * @param ContentMetaTitleCollection $metaTitles Localized meta titles
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingMetaTitles(ContentMetaTitleCollection $metaTitles): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content meta keywords
     *
     * @param ContentMetaKeywordsCollection $metaKeywords Localized meta keywords
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingMetaKeywords(ContentMetaKeywordsCollection $metaKeywords): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content OpenGraph images
     *
     * @param ContentOpengraphImageCollection $opengraphImages Localized OpenGraph images
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingOpengraphImages(ContentOpengraphImageCollection $opengraphImages): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content meta descriptions
     *
     * @param ContentMetaDescriptionCollection $metaDescriptions Localized meta descriptions
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingMetaDescriptions(ContentMetaDescriptionCollection $metaDescriptions): InfoPageContentBuilder;
    
    
    /**
     * Set the content "Allow robots" status
     *
     * @param ContentAllowRobotsStatusCollection $allowRobotsStatus "Allow robots" status
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingAllowRobotsStatuses(ContentAllowRobotsStatusCollection $allowRobotsStatus): InfoPageContentBuilder;
    
    
    /**
     * Set the content sitemap information
     *
     * @param ContentSitemapCollection|null $sitemaps Sitemap information
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingSitemaps(?ContentSitemapCollection $sitemaps = null): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content URL rewrite
     *
     * @param ContentUrlRewriteCollection $urlRewrites Localized URL rewrites
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingUrlRewrites(ContentUrlRewriteCollection $urlRewrites): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content URL keywords
     *
     * @param ContentUrlKeywordsCollection $urlKeywords Localized URL keywords
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingUrlKeywords(ContentUrlKeywordsCollection $urlKeywords): InfoPageContentBuilder;
    
    
    /**
     * Set the localized content names
     *
     * @param ContentNameCollection $names Localized names
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingNames(ContentNameCollection $names): InfoPageContentBuilder;
    
    
    /**
     * Set the content group id
     *
     * @param ContentIdentificationInterface|null $id
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingId(ContentIdentificationInterface $id = null): InfoPageContentBuilder;
    
    
    /**
     * Sets the content sort order
     *
     * @param ContentSortOrderInterface |null $sortOrder
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingSortOrder(ContentSortOrderInterface $sortOrder = null): InfoPageContentBuilder;
    
    
    /**
     * Sets the content sort order
     *
     * @param ContentDeleteInterface |null $deletable
     *
     * @return InfoPageContentBuilder Same instance
     */
    public function usingDelete(ContentDeleteInterface $deletable = null): InfoPageContentBuilder;
}

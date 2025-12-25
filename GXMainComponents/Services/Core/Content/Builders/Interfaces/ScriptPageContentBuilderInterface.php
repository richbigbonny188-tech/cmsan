<?php
/*--------------------------------------------------------------------------------------------------
    ScriptPageContentBuilderInterface.php 2021-08-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


interface ScriptPageContentBuilderInterface
{
    /**
     * Set the page position
     *
     * @param PagePosition $position Page position
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function inPosition(PagePosition $position): ScriptPageContentBuilder;
    
    
    /**
     * Set the activation status
     *
     * @param ContentStatusCollection $status Activation status
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingStatus(ContentStatusCollection $status): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content titles
     *
     * @param ContentTitleCollection $titles Localized titles
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingTitles(ContentTitleCollection $titles): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content texts
     *
     * @param ContentTextCollection $texts Localized texts
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingTexts(ContentTextCollection $texts): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content script files
     *
     * @param ContentScriptFileCollection $scripts Localized script files
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingScriptFiles(ContentScriptFileCollection $scripts): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content meta titles
     *
     * @param ContentMetaTitleCollection $metaTitles Localized meta titles
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingMetaTitles(ContentMetaTitleCollection $metaTitles): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content meta keywords
     *
     * @param ContentMetaKeywordsCollection $metaKeywords Localized meta keywords
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingMetaKeywords(ContentMetaKeywordsCollection $metaKeywords): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content meta descriptions
     *
     * @param ContentMetaDescriptionCollection $metaDescriptions Localized meta descriptions
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingMetaDescriptions(ContentMetaDescriptionCollection $metaDescriptions): ScriptPageContentBuilder;
    
    
    /**
     * Set the content "Allow robots" status
     *
     * @param ContentAllowRobotsStatusCollection $allowRobotsStatuses "Allow robots" status
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingAllowRobotsStatus(ContentAllowRobotsStatusCollection $allowRobotsStatuses): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content sitemap information
     *
     * @param ContentSitemapCollection $sitemaps Localized sitemap information
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingSitemaps(ContentSitemapCollection $sitemaps): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content URL rewrite
     *
     * @param ContentUrlRewriteCollection $urlRewrites Localized URL rewrites
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingUrlRewrites(ContentUrlRewriteCollection $urlRewrites): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content URL keywords
     *
     * @param ContentUrlKeywordsCollection $urlKeywords Localized URL keywords
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingUrlKeywords(ContentUrlKeywordsCollection $urlKeywords): ScriptPageContentBuilder;
    
    
    /**
     * Set the localized content names
     *
     * @param ContentNameCollection $names Localized names
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingNames(ContentNameCollection $names): ScriptPageContentBuilder;
    
    
    /**
     * Set the content group id
     *
     * @param ContentIdentificationInterface|null $id
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingId(ContentIdentificationInterface $id = null): ScriptPageContentBuilder;
    
    
    /**
     * Sets the content sort order
     *
     * @param ContentSortOrderInterface |null $sortOrder
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingSortOrder(ContentSortOrderInterface $sortOrder = null): ScriptPageContentBuilder;
    
    
    /**
     * Sets the content sort order
     *
     * @param ContentDeleteInterface |null $deletable
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingDelete(ContentDeleteInterface $deletable = null): ScriptPageContentBuilder;
    
    
    /**
     * Set whether the script page opens in a new tab
     *
     * @param ContentOpenInNewTabStatusCollection $openInNewTabStatus Whether the script page opens in a new tab
     *
     * @return ScriptPageContentBuilder Same instance
     */
    public function usingOpenInNewTabStatus(ContentOpenInNewTabStatusCollection $openInNewTabStatus): ScriptPageContentBuilder;
}
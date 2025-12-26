<?php

/* --------------------------------------------------------------
   LinkPageContentBuilderInterface.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface LinkPageContentBuilderInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface LinkPageContentBuilderInterface
{
    /**
     * Set the page position
     *
     * @param PagePosition $position Page position
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function inPosition(PagePosition $position): LinkPageContentBuilder;
    
    
    /**
     * Set the activation status
     *
     * @param ContentStatusCollection $status Activation status
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingStatus(ContentStatusCollection $status): LinkPageContentBuilder;
    
    
    /**
     * Set the localized content titles
     *
     * @param ContentTitleCollection $titles Localized titles
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingTitles(ContentTitleCollection $titles): LinkPageContentBuilder;
    
    
    /**
     * Set the localized content names
     *
     * @param ContentNameCollection $names Localized names
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingNames(ContentNameCollection $names): LinkPageContentBuilder;
    
    
    /**
     * Set the localized content links
     *
     * @param ContentLinkCollection $links Localized links
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingLinks(ContentLinkCollection $links): LinkPageContentBuilder;
    
    
    /**
     * Set whether the link page opens in a new tab
     *
     * @param ContentOpenInNewTabStatusCollection $openInNewTabStatus Whether the link page opens in a new tab
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingOpenInNewTabStatus(ContentOpenInNewTabStatusCollection $openInNewTabStatus): LinkPageContentBuilder;
    
    
    /**
     * Set the content group id
     *
     * @param ContentIdentificationInterface|null $id
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingId(ContentIdentificationInterface $id = null): LinkPageContentBuilder;
    
    
    /**
     * Set the content sort order
     *
     * @param ContentSortOrderInterface |null $sortOrder
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingSortOrder(ContentSortOrderInterface $sortOrder = null): LinkPageContentBuilder;
    
    
    /**
     * Set the content delete
     *
     * @param ContentDeleteInterface |null $deletable
     *
     * @return LinkPageContentBuilder Same instance
     */
    public function usingDelete(ContentDeleteInterface $deletable = null): LinkPageContentBuilder;
}

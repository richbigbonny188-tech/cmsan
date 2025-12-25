<?php

/* --------------------------------------------------------------
   InfoElementContentBuilderInterface.inc.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InfoElementContentBuilderInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface InfoElementContentBuilderInterface
{
    /**
     * Set the page position
     *
     * @param ElementPosition $position Element position
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function inPosition(ElementPosition $position): InfoElementContentBuilder;
    
    
    /**
     * Set the activation status
     *
     * @param ContentStatusCollection $status Activation status
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingStatus(ContentStatusCollection $status): InfoElementContentBuilder;
    
    
    /**
     * Set the localized content titles
     *
     * @param ContentTitleCollection $titles Localized titles
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingTitles(ContentTitleCollection $titles): InfoElementContentBuilder;
    
    
    /**
     * Set the localized content headings
     *
     * @param ContentHeadingCollection $headings Localized headings
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingHeadings(ContentHeadingCollection $headings): InfoElementContentBuilder;
    
    
    /**
     * Set the localized content texts
     *
     * @param ContentTextCollection $texts Localized texts
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingTexts(ContentTextCollection $texts): InfoElementContentBuilder;
    
    
    /**
     * Set the sort order
     *
     * @param ContentSortOrderInterface $sortOrder
     *
     * @return $this
     */
    public function usingSortOrder(ContentSortOrderInterface $sortOrder): InfoElementContentBuilder;
    
    
    /**
     * Set the content group id
     *
     * @param ContentIdentificationInterface $id
     *
     * @return InfoElementContentBuilder Same instance
     */
    public function usingId(ContentIdentificationInterface $id = null): InfoElementContentBuilder;
}

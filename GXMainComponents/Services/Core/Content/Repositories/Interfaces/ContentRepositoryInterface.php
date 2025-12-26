<?php

/* --------------------------------------------------------------
   ContentRepositoryInterface.php 2019-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentRepositoryInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentRepositoryInterface
{
    /**
     * Save the info page content
     *
     * @param InfoPageContent $infoPageContent Info page content
     *
     * @return ContentRepositoryInterface Same instance for chained method calls
     */
    public function storeInfoPageContent(InfoPageContent $infoPageContent): ContentRepositoryInterface;
    
    
    /**
     * Save the link page content
     *
     * @param LinkPageContent $linkPageContent Link page content
     *
     * @return ContentRepositoryInterface Same instance for chained method calls
     */
    public function storeLinkPageContent(LinkPageContent $linkPageContent): ContentRepositoryInterface;
    
    
    /**
     * Save the info element content
     *
     * @param InfoElementContent $infoElementContent Info element content
     *
     * @return ContentRepositoryInterface Same instance for chained method calls
     */
    public function storeInfoElementContent(InfoElementContent $infoElementContent): ContentRepositoryInterface;
    
    
    /**
     * Updates an info page content.
     *
     * @param InfoPageContent $infoPage
     */
    public function updateInfoPageContent(InfoPageContent $infoPage): void;
    
    
    /**
     * Updates a link page content.
     *
     * @param LinkPageContent $linkPage
     */
    public function updateLinkPageContent(LinkPageContent $linkPage): void;
    
    
    /**
     * Updates an info element content
     *
     * @param InfoElementContent $infoElement
     */
    public function updateInfoElementContent(InfoElementContent $infoElement): void;
    
    
    /**
     * @param mixed $id id of the Content
     *
     * @return mixed
     * @throws ContentNotFoundException
     */
    public function findById(ContentIdentificationInterface $id);
    
    
    /**
     * deletes the content data in database by id.
     *
     * @param ContentIdentificationInterface $Id
     *
     * @return ContentRepositoryInterface Same instance for chained method calls.
     */
    public function deleteById(ContentIdentificationInterface $Id);
    
    
    /**
     * @return ContentIdentificationInterface
     */
    public function nextContentGroupId(): ContentIdentificationInterface;
    
    
    /**
     * @return InfoElementContent[]
     * @throws ContentNotFoundException
     * @throws UnfinishedBuildException
     */
    public function getAllInfoElements(): array;
    
    
    /**
     * @return array
     */
    public function getAllContentPages(): array;
}

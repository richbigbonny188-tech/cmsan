<?php
/*--------------------------------------------------------------------------------------------------
    ContentWriteServiceInterface.inc.php 2019-7-5
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Class ContentWriteServiceInterface
 */
interface ContentWriteServiceInterface
{
    
    /**
     * Stores an info page content.
     *
     * @param InfoPageContent $infoPage
     */
    public function storeInfoPageContent(InfoPageContent $infoPage): void;
    
    
    /**
     * Stores an link page content.
     *
     * @param LinkPageContent $linkPage
     */
    public function storeLinkPageContent(LinkPageContent $linkPage): void;
    
    
    /**
     * Stores an info element content.
     *
     * @param InfoElementContent $infoElement
     */
    public function storeInfoElementContent(InfoElementContent $infoElement): void;
    
    
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
}
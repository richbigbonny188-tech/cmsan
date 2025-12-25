<?php

/* --------------------------------------------------------------
   ContentWriterInterface.inc.php 2019-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentWriterInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentWriterInterface
{
    /**
     * Store an info element content into the database
     *
     * @param InfoElementContent $infoElementContent Info element content
     *
     * @return string New content group ID
     */
    public function storeInfoElementContent(InfoElementContent $infoElementContent): string;
    
    
    /**
     * Store an info page content into the database
     *
     * @param InfoPageContent $infoPageContent Info page content
     *
     * @return string New content group ID
     */
    public function storeInfoPageContent(InfoPageContent $infoPageContent): string;
    
    
    /**
     * Store a link page content into the database
     *
     * @param LinkPageContent $linkPageContent Link page content
     *
     * @return string New content group ID
     */
    public function storeLinkPageContent(LinkPageContent $linkPageContent): string;
    
    
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
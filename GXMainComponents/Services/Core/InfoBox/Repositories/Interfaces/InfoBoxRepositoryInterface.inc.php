<?php

/* --------------------------------------------------------------
   InfoBoxRepositoryInterface.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InfoBoxRepositoryInterface
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Interfaces
 */
interface InfoBoxRepositoryInterface
{
    /**
     * Returns all info box messages.
     *
     * @return InfoBoxMessageCollection
     */
    public function getAllMessages();
    
    
    /**
     * Adds a new info box message.
     *
     * @param InfoBoxMessageInterface $message Message to save.
     */
    public function addMessage(InfoBoxMessageInterface $message);
    
    
    /**
     * Reactivates the messages.
     */
    public function reactivateMessages();
    
    
    /**
     * Deletes a message based on the source.
     *
     * @param StringType $source Message source.
     */
    public function deleteMessageBySource(StringType $source);
    
    
    /**
     * Deletes a message based on its identifier.
     *
     * @param StringType $identifier Message identifier.
     */
    public function deleteMessageByIdentifier(StringType $identifier);
    
    
    /**
     * Deletes a message by its ID.
     *
     * @param IdType $id Message ID.
     */
    public function deleteMessageById(IdType $id);
    
    
    /**
     * Updates a message status.
     *
     * @param IdType     $id     Message ID.
     * @param StringType $status Message status.
     */
    public function setMessageStatus(IdType $id, StringType $status);
}
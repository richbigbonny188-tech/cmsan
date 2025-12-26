<?php

/* --------------------------------------------------------------
   InfoBoxService.php 2016-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoBoxService
 *
 * @category System
 * @package  InfoBox
 */
class InfoBoxService implements InfoBoxServiceInterface
{
    /**
     * @var InfoBoxRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * InfoBoxService constructor.
     *
     * @param InfoBoxRepositoryInterface $repository Repository Instance
     */
    public function __construct(InfoBoxRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns all info box messages.
     *
     * @return InfoBoxMessageCollection
     */
    public function getAllMessages()
    {
        return $this->repository->getAllMessages();
    }
    
    
    /**
     * Adds a new info box message.
     *
     * @param InfoBoxMessage $message Message to save.
     */
    public function addMessage(InfoBoxMessage $message)
    {
        $this->repository->addMessage($message);
    }
    
    
    /**
     * Reactivates the messages.
     */
    public function reactivateMessages()
    {
        $this->repository->reactivateMessages();
    }
    
    
    /**
     * Deletes a message based on the source.
     *
     * @param StringType $source Message source.
     */
    public function deleteMessageBySource(StringType $source)
    {
        $this->repository->deleteMessageBySource($source);
    }
    
    
    /**
     * Deletes a message based on its identifier.
     *
     * @param StringType $identifier Message identifier.
     */
    public function deleteMessageByIdentifier(StringType $identifier)
    {
        $this->repository->deleteMessageByIdentifier($identifier);
    }
    
    
    /**
     * Deletes a message by its ID.
     *
     * @param IdType $id Message ID.
     */
    public function deleteMessageById(IdType $id)
    {
        $this->repository->deleteMessageById($id);
    }
    
    
    /**
     * Updates a message status.
     *
     * @param IdType     $id     Message ID.
     * @param StringType $status Message status.
     */
    public function setMessageStatus(IdType $id, StringType $status)
    {
        $this->repository->setMessageStatus($id, $status);
    }
}
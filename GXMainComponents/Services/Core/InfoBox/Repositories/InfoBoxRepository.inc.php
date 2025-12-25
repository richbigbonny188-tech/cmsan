<?php

/* --------------------------------------------------------------
   InfoBoxRepository.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoBoxRepository
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Repositories
 */
class InfoBoxRepository implements InfoBoxRepositoryInterface
{
    /**
     * @var InfoBoxRepositoryReaderInterface
     */
    protected $reader;
    
    /**
     * @var InfoBoxRepositoryWriterInterface
     */
    protected $writer;
    
    /**
     * @var InfoBoxRepositoryDeleterInterface
     */
    protected $deleter;
    
    
    /**
     * InfoBoxRepository constructor.
     *
     * @param InfoBoxRepositoryReaderInterface  $reader
     * @param InfoBoxRepositoryWriterInterface  $writer
     * @param InfoBoxRepositoryDeleterInterface $deleter
     */
    public function __construct(
        InfoBoxRepositoryReaderInterface $reader,
        InfoBoxRepositoryWriterInterface $writer,
        InfoBoxRepositoryDeleterInterface $deleter
    ) {
        $this->writer  = $writer;
        $this->reader  = $reader;
        $this->deleter = $deleter;
    }
    
    
    /**
     * Returns all info box messages.
     *
     * @return InfoBoxMessageCollection
     */
    public function getAllMessages()
    {
        return $this->reader->getAll();
    }
    
    
    /**
     * Adds a new info box message.
     *
     * @param InfoBoxMessageInterface $message Message to save.
     */
    public function addMessage(InfoBoxMessageInterface $message)
    {
        $this->writer->write($message);
    }
    
    
    /**
     * Reactivates the messages.
     */
    public function reactivateMessages()
    {
        $this->writer->reactivate();
    }
    
    
    /**
     * Deletes a message based on the source.
     *
     * @param StringType $source Message source.
     */
    public function deleteMessageBySource(StringType $source)
    {
        $this->deleter->deleteBySource($source);
    }
    
    
    /**
     * Deletes a message based on its identifier.
     *
     * @param StringType $identifier Message identifier.
     */
    public function deleteMessageByIdentifier(StringType $identifier)
    {
        $this->deleter->deleteByIdentifier($identifier);
    }
    
    
    /**
     * Deletes a message by its ID.
     *
     * @param IdType $id Message ID.
     */
    public function deleteMessageById(IdType $id)
    {
        $this->deleter->deleteById($id);
    }
    
    
    /**
     * Updates a message status.
     *
     * @param IdType     $id     Message ID.
     * @param StringType $status Message status.
     */
    public function setMessageStatus(IdType $id, StringType $status)
    {
        $this->writer->setStatus($id, $status);
    }
}
<?php

/* --------------------------------------------------------------
   InfoBoxReader.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoBoxReader
 *
 * @category System
 * @package  InfoBox
 */
class InfoBoxRepositoryReader implements InfoBoxRepositoryReaderInterface
{
    /**
     * Database connection.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * Name of the table for the info box messages entries.
     *
     * @var string
     */
    protected $infoBoxTable = 'infobox_messages';
    
    /**
     * Name of the table for the info box description entries.
     *
     * @var string
     */
    protected $infoBoxTableDescription = 'infobox_messages_description';
    
    /**
     * Language provider.
     *
     * @var LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * InfoBoxReader constructor.
     *
     * @param CI_DB_query_builder $db Database connection.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db               = $db;
        $this->languageProvider = MainFactory::create('LanguageProvider', $db);
    }
    
    
    /**
     * Returns all messages.
     * @return InfoBoxMessageCollection
     */
    public function getAll()
    {
        $messages = $this->db->get_where($this->infoBoxTable, ['status !=' => 'deleted'])->result_array();
        
        return $this->_createCollection($messages);
    }
    
    
    protected function _createCollection(array $rows)
    {
        $collection = [];
        
        foreach ($rows as $row) {
            $collection[] = $this->_createObject($row);
        }
        
        return MainFactory::create('InfoBoxMessageCollection', $collection);
    }
    
    
    protected function _createObject(array $message)
    {
        $dbIdentifier = 'infobox_messages_id';
        
        $messageDescription = $this->db->select('*')
            ->from($this->infoBoxTableDescription)
            ->where($dbIdentifier,
                    (int)$message[$dbIdentifier])
            ->get()
            ->result_array();
        
        $infoBoxMessage = MainFactory::create('InfoBoxMessage');
        
        $id               = new IdType((string)$message[$dbIdentifier]);
        $source           = new StringType((string)$message['source']);
        $identifier       = new StringType((string)$message['identifier']);
        $status           = new StringType((string)$message['status']);
        $type             = new StringType((string)$message['type']);
        $visibility       = new StringType((string)$message['visibility']);
        $buttonLink       = new StringType((string)$message['button_link']);
        $customerId       = new IdType(isset($message['customer_id']) ? (int)message['customer_id'] : 0);
        $addedDateTime    = new DateTime($message['date_added']);
        $modifiedDateTime = new DateTime($message['date_modified']);
        
        $infoBoxMessage->setId($id)
            ->setSource($source)
            ->setIdentifier($identifier)
            ->setStatus($status)
            ->setType($type)
            ->setVisibility($visibility)
            ->setButtonLink($buttonLink)
            ->setCustomerId($customerId)
            ->setAddedDateTime($addedDateTime)
            ->setModifiedDateTime($modifiedDateTime);
        
        foreach ($messageDescription as $description) {
            $languageId = new IdType((int)$description['languages_id']);
            
            $languageCode = $this->languageProvider->getCodeById($languageId);
            $headline     = new StringType((string)$description['headline'] ? : '');
            $textMessage  = new StringType((string)$description['message'] ? : '');
            $button_label = new StringType((string)$description['button_label'] ? : '');
            
            $infoBoxMessage->setHeadLine($headline, $languageCode)
                ->setMessage($textMessage, $languageCode)
                ->setButtonLabel($button_label, $languageCode);
        }
        
        return $infoBoxMessage;
    }
}

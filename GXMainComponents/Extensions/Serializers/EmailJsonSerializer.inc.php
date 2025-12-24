<?php
/* --------------------------------------------------------------
   EmailsJsonSerializer.inc.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class EmailsJsonSerializer
 *
 * This class will serialize and deserialize an email entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class EmailJsonSerializer extends AbstractJsonSerializer
{
    /**
     * Serialize email object (from Email instance to json string)
     *
     * @param EmailInterface $object    Contains the email data.
     * @param bool           $encode    (optional) Whether to json_encode the result of the method (default true).
     *                                  Sometimes it might be required to encode an array of multiple email records
     *                                  together and not one by one.
     *
     * @return string|array
     */
    public function serialize($object, $encode = true)
    {
        if (!is_a($object, 'EmailInterface')) {
            throw new InvalidArgumentException('Invalid argument provided, EmailInterface object required: '
                                               . get_class($object));
        }
        
        // Main Properties
        
        $email = [
            'id'           => ($object->getId()) ? (int)(string)$object->getId() : null,
            'subject'      => ($object->getSubject()) ? (string)$object->getSubject() : null,
            'sender'       => ($object->getSender()) ? $this->_serializeContact($object->getSender()) : null,
            'recipient'    => ($object->getRecipient()) ? $this->_serializeContact($object->getRecipient()) : null,
            'replyTo'      => ($object->getReplyTo()) ? $this->_serializeContact($object->getReplyTo()) : null,
            'contentHtml'  => ($object->getContentHtml()) ? (string)$object->getContentHtml() : null,
            'contentPlain' => ($object->getContentPlain()) ? (string)$object->getContentPlain() : null,
            'isPending'    => $object->isPending(),
            'creationDate' => $object->getCreationDate()->format('Y-m-d H:i:s'),
            'sentDate'     => ($object->getSentDate()) ? $object->getSentDate()->format('Y-m-d H:i:s') : null,
            'bcc'          => [],
            'cc'           => [],
            'attachments'  => []
        ];
        
        // BCC & CC
        
        foreach ($object->getBcc()->getArray() as $contact) {
            $email['bcc'][] = $this->_serializeContact($contact);
        }
        
        foreach ($object->getCc()->getArray() as $contact) {
            $email['cc'][] = $this->_serializeContact($contact);
        }
        
        // Attachments
        
        foreach ($object->getAttachments()->getArray() as $attachment) {
            $email['attachments'][] = $this->_serializeAttachment($attachment);
        }
        
        return ($encode) ? $this->jsonEncode($email) : $email;
    }
    
    
    /**
     * Deserialize email JSON string.
     *
     * @param string $string     JSON string that contains the data of the email.
     * @param object $baseObject (optional) If provided, this will be the base object to be updated
     *                           and no new instance will be created.
     *
     * @return EmailInterface Returns the deserialized Email object.
     * @throws InvalidArgumentException If the argument is not a string or is empty.
     */
    public function deserialize($string, $baseObject = null)
    {
        if (!is_string($string) || empty($string)) {
            throw new InvalidArgumentException('Invalid argument provided for deserialization: ' . gettype($string));
        }
        
        $json = json_decode($string);
        
        if ($json === null && json_last_error() > 0) {
            throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: ' . $string);
        }
        
        if (!$baseObject) {
            $email = MainFactory::create('Email');
        } else {
            $email = $baseObject;
        }
        
        // Deserialize Main Properties
        
        if (property_exists($json, 'id') && $json->id !== null) {
            $email->setId(new IdType((int)$json->id));
        }
        
        if (property_exists($json, 'subject') && $json->subject !== null) {
            $email->setSubject(MainFactory::create('EmailSubject', $json->subject));
        }
        
        if (property_exists($json, 'contentHtml') && $json->contentHtml !== null) {
            $email->setContentHtml(MainFactory::create('EmailContent', $json->contentHtml));
        }
        
        if (property_exists($json, 'contentPlain') && $json->contentPlain !== null) {
            $email->setContentPlain(MainFactory::create('EmailContent', $json->contentPlain));
        }
        
        if (property_exists($json, 'isPending') && $json->isPending !== null) {
            $email->setPending((bool)$json->isPending);
        }
        
        if (property_exists($json, 'creationDate') && $json->creationDate !== null) {
            $email->setCreationDate(new EmptyDateTime($json->creationDate));
        }
        
        if (property_exists($json, 'sentDate') && $json->sentDate !== null) {
            $email->setSentDate(new EmptyDateTime($json->sentDate));
        }
        
        // Deserialize Contacts
        
        if (property_exists($json, 'sender') && $json->sender !== null) {
            $sender = $this->_deserializeContact($json->sender, ContactType::SENDER);
            $email->setSender($sender);
        }
        
        if (property_exists($json, 'recipient') && $json->recipient !== null) {
            $recipient = $this->_deserializeContact($json->recipient, ContactType::RECIPIENT);
            $email->setRecipient($recipient);
        }
        
        if (property_exists($json, 'replyTo') && $json->replyTo !== null) {
            $replyTo = $this->_deserializeContact($json->replyTo, ContactType::REPLY_TO);
            $email->setReplyTo($replyTo);
        }
        
        if (property_exists($json, 'bcc') && $json->bcc !== null) {
            foreach ($json->bcc as $contact) {
                $email->getBcc()->add($this->_deserializeContact($contact, ContactType::BCC));
            }
        }
        
        if (property_exists($json, 'cc') && $json->cc) {
            foreach ($json->cc as $contact) {
                $email->getCc()->add($this->_deserializeContact($contact, ContactType::CC));
            }
        }
        
        // Deserialize Attachments 
        
        if (property_exists($json, 'attachments') && $json->attachments !== null) {
            foreach ($json->attachments as $attachment) {
                $email->getAttachments()->add($this->_deserializeAttachment($attachment));
            }
        }
        
        return $email;
    }
    
    
    /**
     * Serialize EmailContact
     *
     * @param EmailContactInterface $contact
     *
     * @return array
     */
    protected function _serializeContact(EmailContactInterface $contact)
    {
        return [
            'emailAddress' => ($contact->getEmailAddress()) ? (string)$contact->getEmailAddress() : null,
            'contactName'  => ($contact->getContactName()) ? (string)$contact->getContactName() : null
        ];
    }
    
    
    /**
     * Deserialize EmailContact
     *
     * @param stdClass $contact
     * @param          $type
     *
     * @return EmailContact
     */
    protected function _deserializeContact(stdClass $contact, $type)
    {
        $emailAddress = ($contact->emailAddress !== null) ? MainFactory::create('EmailAddress',
                                                                                $contact->emailAddress) : null;
        $contactType  = MainFactory::create('ContactType', $type);
        $contactName  = ($contact->contactName !== null) ? MainFactory::create('ContactName',
                                                                               $contact->contactName) : null;
        
        return MainFactory::create('EmailContact', $emailAddress, $contactType, $contactName);
    }
    
    
    /**
     * Serialize EmailAttachment
     *
     * @param EmailAttachmentInterface $attachment
     *
     * @return array
     */
    protected function _serializeAttachment(EmailAttachmentInterface $attachment)
    {
        return [
            'path' => ($attachment->getPath()) ? (string)$attachment->getPath() : null,
            'name' => ($attachment->getName()) ? (string)$attachment->getName() : null
        ];
    }
    
    
    /**
     * Deserialize EmailAttachment
     *
     * @param stdClass $attachment
     *
     * @return EmailAttachment
     */
    protected function _deserializeAttachment(stdClass $attachment)
    {
        $path = ($attachment->path !== null) ? MainFactory::create('AttachmentPath', $attachment->path) : null;
        $name = ($attachment->name !== null) ? MainFactory::create('AttachmentName', $attachment->name) : null;
        
        return MainFactory::create('EmailAttachment', $path, $name);
    }
}
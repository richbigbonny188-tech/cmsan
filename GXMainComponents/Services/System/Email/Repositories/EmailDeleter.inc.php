<?php
/* --------------------------------------------------------------
   EmailDeleter.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailDeleterInterface');

/**
 * Class EmailDeleter
 *
 * Deletes email records from the database.
 *
 * @category   System
 * @package    Email
 * @subpackage Repository
 */
class EmailDeleter implements EmailDeleterInterface
{
    /**
     * Query builder.
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * Class Constructor
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Removes a record from the database.
     *
     * This method will delete all the email relevant entities from the database. It will
     * not throw an exception if the given record is not found.
     *
     * @param EmailInterface $email E-Mail.
     */
    public function delete(EmailInterface $email)
    {
        $this->db->delete(['emails', 'email_contacts', 'email_attachments'],
                          ['email_id' => $email->getId()]);
    }
    
    
    /**
     * Removes all emails from the data base that have the given email address as sender or recipient.
     *
     * @param CustomerEmail $email E-Mail address.
     */
    public function deleteEmailsByEmailAddress(CustomerEmail $email)
    {
        $this->db->query('
            DELETE `emails`.*, `email_contacts`.*, `email_attachments`.* FROM `email_contacts`
                LEFT JOIN `emails` ON (`email_contacts`.`email_id` = `emails`.`email_id`)
                LEFT JOIN `email_attachments` ON (`email_contacts`.`email_id` = `email_attachments`.`email_id`)
            WHERE `email_contacts`.`email_address` = "' . $email . '"');
    }
    
    
    /**
     * Removes all emails from the database that have creation date below the give date.
     *
     * @param \DateTime $date
     */
    public function deleteOldEmailsByDate(DateTime $date)
    {
        $this->db->query('
            DELETE `emails`.*, `email_contacts`.*, `email_attachments`.*
            FROM `emails`
                LEFT JOIN `email_contacts` ON (`emails`.`email_id` = `email_contacts`.`email_id`)
                LEFT JOIN `email_attachments` ON (`email_contacts`.`email_id` = `email_attachments`.`email_id`)
            WHERE `emails`.`creation_date` < "' . $date->format('Y-m-d H:i:s') . '"');
    }
}
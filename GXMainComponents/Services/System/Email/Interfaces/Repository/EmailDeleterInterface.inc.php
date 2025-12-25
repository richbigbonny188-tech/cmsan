<?php
/* --------------------------------------------------------------
   EmailDeleterInterface.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailDeleterInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailDeleterInterface
{
    /**
     * Removes a record from the database.
     *
     * This method will delete all the email relevant entities from the database. It will
     * not throw an exception if the given record is not found.
     *
     * @param EmailInterface $email E-Mail.
     */
    public function delete(EmailInterface $email);
    
    
    /**
     * Removes all emails from the data base that have the given email address as sender or recipient.
     *
     * @param CustomerEmail $email E-Mail address.
     */
    public function deleteEmailsByEmailAddress(CustomerEmail $email);
    
    
    /**
     * Removes all emails from the database that have creation date below the give date.
     *
     * @param \DateTime $date
     */
    public function deleteOldEmailsByDate(DateTime $date);
}
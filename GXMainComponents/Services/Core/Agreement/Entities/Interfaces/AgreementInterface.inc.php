<?php
/* --------------------------------------------------------------
   AgreementInterface.inc.php 2018-05-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AgreementInterface
 *
 * @category   Core
 * @package    Agreement
 * @subpackage Interfaces
 */
interface AgreementInterface
{
    /**
     * Stores the agreement.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function store();
    
    
    /**
     * Deletes the agreement.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function delete();
    
    
    /**
     * Sets the agreement id.
     *
     * @param \IdType $id The agreement id to be set.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setId(IdType $id);
    
    
    /**
     * Returns the agreement id.
     *
     * @return IdType Agreement id.
     */
    public function getId();
    
    
    /**
     * Sets the date of the agreement.
     *
     * @param \DateTime $dateTime Date and time.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setDateAdded(DateTime $dateTime);
    
    
    /**
     * Returns the date and time of the agreement.
     *
     * @return \DateTime Date and time of the agreement.
     */
    public function getDateAdded();
    
    
    /**
     * Sets the last modified date of the agreement.
     *
     * @param \DateTime $dateTime Date and time.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setLastModifiedDate(DateTime $dateTime);
    
    
    /**
     * Returns the last modified date of the agreement.
     *
     * @return \DateTime Date and time of the agreement.
     */
    public function getLastModifiedDateTime();
    
    
    /**
     * Sets the customer of the agreement.
     *
     * @param \AgreementCustomerInterface $customer Customer.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setCustomer(AgreementCustomerInterface $customer);
    
    
    /**
     * Returns the customers of the agreement.
     *
     * @return \AgreementCustomerInterface Customer.
     */
    public function getCustomer();
    
    
    /**
     * Sets the language ID of the agreement.
     *
     * @param \IdType $languageId Language ID.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setLanguageId(IdType $languageId);
    
    
    /**
     * Returns the language ID of the agreement.
     *
     * @return \IdType Language ID.
     */
    public function getLanguageId();
    
    
    /**
     * Sets the IP address of the customer which confirmed the agreement.
     *
     * @param StringType $ipAddress
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setIpAddress($ipAddress);
    
    
    /**
     * Returns the IP address of the customer which confirmed the agreement.
     *
     * @return StringType
     */
    public function getIpAddress();
    
    
    /**
     * Sets the text of the agreement.
     *
     * @param \NonEmptyStringType $text Agreement text.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setText(NonEmptyStringType $text);
    
    
    /**
     * Returns the text of the agreement.
     *
     * @return StringType
     */
    public function getText();
    
    
    /**
     * Sets the version of the legal text.
     *
     * @param StringType $legalTextVersion Version of the legal text.
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setLegalTextVersion(StringType $legalTextVersion);
    
    
    /**
     * Returns the version of the legal text.
     *
     * @return StringType
     */
    public function getLegalTextVersion();
    
    
    /**
     * Sets the content group of the legal text.
     *
     * @param \IdType $contentGroup
     *
     * @return $this|\AgreementInterface Same instance for chained method calls.
     */
    public function setContentGroup($contentGroup);
    
    
    /**
     * Returns the content group ID of the legal text.
     *
     * @return \IdType
     */
    public function getContentGroup();
}
<?php
/* --------------------------------------------------------------
   AgreementAccessRepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AgreementAccessRepository
 *
 * @category   System
 * @package    Agreement
 * @subpackage Repositories
 */
class AgreementAccessRepository implements AgreementAccessRepositoryInterface
{
    /**
     * @var \AgreementFactory
     */
    protected $factory;
    
    /**
     * @var \AgreementReaderInterface
     */
    protected $reader;
    
    
    /**
     * ReviewAccessRepository constructor.
     *
     * @param \AgreementFactory         $factory
     * @param \AgreementReaderInterface $reader
     */
    public function __construct(AgreementFactory $factory, AgreementReaderInterface $reader)
    {
        $this->factory = $factory;
        $this->reader  = $reader;
    }
    
    
    /**
     * Returns all agreement entities as an array.
     *
     * @return AgreementCollection
     */
    public function getAll()
    {
        $collection = $this->factory->createCollection();
        
        $rawData = $this->reader->getAll();
        foreach ($rawData as $data) {
            $agreement = $this->factory->create();
            $agreement->setId(new IdType($data['id']))
                ->setDateAdded(new DateTime($data['dateAdded']))
                ->setLastModifiedDate(new DateTime($data['lastModified']))
                ->setIpAddress(new StringType($data['ipAddress']))
                ->setText(new NonEmptyStringType($data['text']))
                ->setLegalTextVersion(new StringType($data['legalTextVersion']))
                ->setContentGroup(new IdType($data['contentGroup']))
                ->setCustomer($this->_createCustomer($data['customer']))
                ->setLanguageId(new IdType($data['languageId']));
            
            $collection->addItem($agreement);
        }
        
        return $collection;
    }
    
    
    /**
     * Returns an agreement entity by the provided id.
     *
     * @param \IdType $id
     *
     * @return Agreement
     */
    public function getById(IdType $id)
    {
        $rawData = $this->reader->getById($id);
        
        $agreement = $this->factory->create();
        
        $agreement->setId(new IdType($rawData['id']))
            ->setDateAdded(new DateTime($rawData['dateAdded']))
            ->setLastModifiedDate(new DateTime($rawData['lastModified']))
            ->setIpAddress(new StringType($rawData['ipAddress']))
            ->setText(new NonEmptyStringType($rawData['text']))
            ->setLegalTextVersion(new StringType($rawData['legalTextVersion']))
            ->setContentGroup(new IdType($rawData['contentGroup']))
            ->setCustomer($this->_createCustomer($rawData['customer']))
            ->setLanguageId(new IdType($rawData['languageId']));
        
        return $agreement;
    }
    
    
    /**
     * Returns the agreements by the provided customer email address.
     *
     * @param \StringType $customerEmail
     *
     * @return AgreementCollection
     */
    public function getAgreementsByCustomerEmail(StringType $customerEmail)
    {
        $collection = $this->factory->createCollection();
        
        $rawData = $this->reader->getAgreementsByCustomerEmail($customerEmail);
        foreach ($rawData as $data) {
            $agreement = $this->factory->create();
            $agreement->setId(new IdType($data['id']))
                ->setDateAdded(new DateTime($data['dateAdded']))
                ->setLastModifiedDate(new DateTime($data['lastModified']))
                ->setIpAddress(new StringType($data['ipAddress']))
                ->setText(new NonEmptyStringType($data['text']))
                ->setLegalTextVersion(new StringType($data['legalTextVersion']))
                ->setContentGroup(new IdType($data['contentGroup']))
                ->setCustomer($this->_createCustomer($data['customer']))
                ->setLanguageId(new IdType($data['languageId']));
            
            $collection->addItem($agreement);
        }
        
        return $collection;
    }
    
    
    /**
     * Creates a AgreementCustomer by the provided array.
     *
     * @param array $dataSet
     *
     * @return \AgreementCustomer
     */
    protected function _createCustomer(array $dataSet)
    {
        return $this->factory->createCustomer(new StringType($dataSet['customerName']),
                                              new AgreementCustomerEmail($dataSet['customerEmail']));
    }
}
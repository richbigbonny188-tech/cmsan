<?php
/* --------------------------------------------------------------
   AgreementReader.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AgreementReader
 *
 * @category   System
 * @package    Agreement
 * @subpackage Repositories
 */
class AgreementReader implements AgreementReaderInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * AgreementReader constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Returns all agreement entities as an array.
     *
     * @return array
     */
    public function getAll()
    {
        $agreements = [];
        $rawData    = $this->queryBuilder->select()->from('agreements')->get()->result_array();
        foreach ($rawData as $row) {
            $agreements[] = $this->_buildAgreementDataArray($row);
        }
        
        return $agreements;
    }
    
    
    /**
     * Returns an agreement entity  by the provided id.
     *
     * @param \IdType $id
     *
     * @return array
     * @throws \EntityNotFoundException
     *
     */
    public function getById(IdType $id)
    {
        $rawData = $this->queryBuilder->select()
            ->from('agreements')
            ->where('agreements.agreements_id', $id->asInt())
            ->get()
            ->row_array();
        
        if ($rawData === null) {
            throw new EntityNotFoundException('Agreement entity was not found with provided id "' . $id->asInt() . '"');
        }
        
        return $this->_buildAgreementDataArray($rawData);
    }
    
    
    /**
     * Returns the agreements by the provided customer email address.
     *
     * @param \StringType $customerEmailAddress
     *
     * @return array
     */
    public function getAgreementsByCustomerEmail(StringType $customerEmailAddress)
    {
        $agreements = [];
        $rawData    = $this->queryBuilder->select()
            ->from('agreements')
            ->where('customers_email',
                    $customerEmailAddress->asString())
            ->get()
            ->result_array();
        foreach ($rawData as $data) {
            $agreements[] = $this->_buildAgreementDataArray($data);
        }
        
        return $agreements;
    }
    
    
    /**
     * @param $rawDataArray
     *
     * @return array
     */
    protected function _buildAgreementDataArray($rawDataArray)
    {
        return [
            'id'               => $rawDataArray['agreements_id'],
            'dateAdded'        => $rawDataArray['date_added'],
            'lastModified'     => $rawDataArray['last_modified'],
            'ipAddress'        => $rawDataArray['ip_address'],
            'text'             => $rawDataArray['text'],
            'legalTextVersion' => $rawDataArray['legal_text_version'],
            'contentGroup'     => $rawDataArray['content_group'],
            'languageId'       => $rawDataArray['language_id'],
            'customer'         => [
                'customerName'  => $rawDataArray['customers_name'],
                'customerEmail' => $rawDataArray['customers_email']
            ]
        ];
    }
}
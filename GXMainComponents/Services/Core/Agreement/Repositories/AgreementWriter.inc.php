<?php
/* --------------------------------------------------------------
   AgreementWriter.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AgreementWriter
 *
 * @category   System
 * @package    Agreement
 * @subpackage Repositories
 */
class AgreementWriter implements AgreementWriterInterface
{
    
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * AgreementWriter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Saves an agreement entity.
     *
     * @param \AgreementInterface $agreement Agreement entity to be saved.
     *
     * @return $this|\AgreementWriterInterface Same instance for chained method calls.
     */
    public function store(AgreementInterface $agreement)
    {
        $agreementDataArray = [
            'customers_name'     => $agreement->getCustomer()->getCustomerName(),
            'customers_email'    => $agreement->getCustomer()->getCustomerEmail(),
            'language_id'        => $agreement->getLanguageId()->asInt(),
            'ip_address'         => $agreement->getIpAddress()->asString(),
            'text'               => $agreement->getText()->asString(),
            'legal_text_version' => $agreement->getLegalTextVersion()->asString(),
            'content_group'      => $agreement->getContentGroup()->asInt(),
            'date_added'         => $agreement->getDateAdded()->format('Y-m-d H:i:s'),
            'last_modified'      => $agreement->getLastModifiedDateTime()->format('Y-m-d H:i:s')
        ];
        
        $this->queryBuilder->insert('agreements', $agreementDataArray);
        
        $agreementId = $this->queryBuilder->insert_id();
        
        $agreement->setId(new IdType($agreementId));
        
        return $this;
    }
    
    
    /**
     * Updates an agreement entity.
     *
     * @param \AgreementInterface $agreement Agreement to be updated.
     *
     * @return $this|\AgreementWriterInterface Same instance for chained method calls.
     */
    public function update(AgreementInterface $agreement)
    {
        $this->queryBuilder->update('agreements',
                                    [
                                        'customers_name'     => $agreement->getCustomer()->getCustomerName(),
                                        'customers_email'    => $agreement->getCustomer()->getCustomerEmail(),
                                        'language_id'        => $agreement->getLanguageId()->asInt(),
                                        'ip_address'         => $agreement->getIpAddress()->asString(),
                                        'text'               => $agreement->getText()->asString(),
                                        'legal_text_version' => $agreement->getLegalTextVersion()->asString(),
                                        'content_group'      => $agreement->getContentGroup()->asInt(),
                                    ],
                                    ['agreements_id' => $agreement->getId()->asInt()]);
        
        return $this;
    }
}
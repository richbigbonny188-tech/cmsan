<?php
/* --------------------------------------------------------------
   AgreementReadService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AgreementReadService
 *
 * This class provides methods in order to read agreements.
 *
 * @category   System
 * @package    Agreement
 * @implements AgreementReadServiceInterface
 */
class AgreementReadService implements AgreementReadServiceInterface
{
    
    /**
     * @var \AgreementRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * AgreementReadService constructor.
     *
     * @param \AgreementAccessRepositoryInterface $repository
     */
    public function __construct(AgreementAccessRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns all agreement entities.
     *
     * @return AgreementCollection
     */
    public function getAll()
    {
        return $this->repository->getAll();
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
        return $this->repository->getById($id);
    }
    
    
    /**
     * Returns the agreements by the provided customer email address.
     *
     * @param StringType $customerEmail
     *
     * @return AgreementCollection
     */
    public function getAgreementsByCustomerEmail(StringType $customerEmail)
    {
        return $this->repository->getAgreementsByCustomerEmail($customerEmail);
    }
}
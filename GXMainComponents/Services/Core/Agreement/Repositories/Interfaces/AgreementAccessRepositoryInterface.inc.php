<?php
/* --------------------------------------------------------------
   AgreementAccessRepositoryInterface.inc.php 2018-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

interface AgreementAccessRepositoryInterface
{
    /**
     * Returns all agreement entities as an array.
     *
     * @return AgreementCollection
     */
    public function getAll();
    
    
    /**
     * Returns an agreement entity by the provided id.
     *
     * @param \IdType $id
     *
     * @return Agreement
     */
    public function getById(IdType $id);
    
    
    /**
     * Returns the agreements by the provided customer email address.
     *
     * @param \StringType $customerEmail
     *
     * @return AgreementCollection
     */
    public function getAgreementsByCustomerEmail(StringType $customerEmail);
}
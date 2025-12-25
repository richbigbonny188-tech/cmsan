<?php
/* --------------------------------------------------------------
  AgreementRepositoryInterface.inc.php 2018-05-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2018 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface AgreementRepositoryInterface
 *
 * @category   System
 * @package    Agreement
 * @subpackage Interfaces
 */
interface AgreementRepositoryInterface
{
    /**
     * Stores the provided agreement.
     *
     * @param \AgreementInterface $agreement
     *
     * @return $this|\AgreementWriteServiceInterface Same instance for chained method calls.
     */
    public function store(AgreementInterface $agreement);
    
    
    /**
     * Deletes the provided agreement.
     *
     * @param \AgreementInterface $agreement
     *
     * @return $this|\AgreementWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(AgreementInterface $agreement);
}
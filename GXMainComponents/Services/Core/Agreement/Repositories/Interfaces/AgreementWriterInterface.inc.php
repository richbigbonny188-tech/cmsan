<?php
/* --------------------------------------------------------------
   AgreementWriterInterface.inc.php 2018-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AgreementWriterInterface
 *
 * @category   System
 * @package    Agreement
 * @subpackage Repositories
 */
interface AgreementWriterInterface
{
    /**
     * Saves an agreement entity.
     *
     * @param \AgreementInterface $agreement Agreement entity to be saved.
     *
     * @return $this|\AgreementWriterInterface Same instance for chained method calls.
     */
    public function store(AgreementInterface $agreement);
    
    
    /**
     * Updates an agreement entity.
     *
     * @param \AgreementInterface $agreement Agreement to be updated.
     *
     * @return $this|\AgreementWriterInterface Same instance for chained method calls.
     */
    public function update(AgreementInterface $agreement);
}
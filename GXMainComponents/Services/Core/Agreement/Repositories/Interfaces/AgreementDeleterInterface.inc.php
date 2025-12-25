<?php
/* --------------------------------------------------------------
   AgreementDeleterInterface.inc.php 2018-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AgreementDeleterInterface
 *
 * @category   System
 * @package    Agreement
 * @subpackage Repositories
 */
interface AgreementDeleterInterface
{
    /**
     * Deletes an agreement entity.
     *
     * @param \AgreementInterface $agreement Agreement entity to delete.
     *
     * @return $this|\AgreementDeleterInterface Same instance for chained method calls.
     */
    public function delete(AgreementInterface $agreement);
}
<?php
/* --------------------------------------------------------------
   ReviewDeleterInterface.inc.php 2017-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ReviewDeleterInterface
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage Repositories
 */
interface ReviewDeleterInterface
{
    /**
     * Deletes review entity data in database.
     *
     * @param \ReviewInterface $review Review entity to be delete.
     *
     * @return $this|\ReviewDeleterInterface Same instance for chained method calls.
     */
    public function delete(ReviewInterface $review);
}
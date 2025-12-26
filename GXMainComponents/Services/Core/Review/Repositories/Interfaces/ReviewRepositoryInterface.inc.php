<?php
/* --------------------------------------------------------------
   ReviewRepositoryInterface.inc.php 2017-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ReviewRepositoryInterface
 *
 * @category   System
 * @package    Review
 * @subpackage Repositories
 */
interface ReviewRepositoryInterface
{
    /**
     * Saves review entity in database.
     *
     * @param \ReviewInterface $review Review entity to be saved.
     *
     * @return $this|\ReviewRepositoryInterface Same instance for chained method calls.
     */
    public function store(ReviewInterface $review);
    
    
    /**
     * Deletes review entity from database.
     *
     * @param \ReviewInterface $review Review entity to de deleted.
     *
     * @return $this|\ReviewRepositoryInterface Same instance for chained method calls.
     */
    public function delete(ReviewInterface $review);
}
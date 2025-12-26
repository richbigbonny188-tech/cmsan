<?php
/* --------------------------------------------------------------
   ReviewWriterInterface.inc.php 2017-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ReviewWriterInterface
 *
 * @category   System
 * @package    Review
 * @subpackage Repositories
 */
interface ReviewWriterInterface
{
    /**
     * Saves review entity data in database.
     *
     * @param \ReviewInterface $review Review entity to be saved.
     *
     * @return $this|\ReviewWriterInterface Same instance for chained method calls.
     */
    public function store(ReviewInterface $review);
    
    
    /**
     * Updates review entity data in database.
     *
     * @param \ReviewInterface $review Review to be updated.
     *
     * @return $this|\ReviewWriterInterface Same instance for chained method calls.
     */
    public function update(ReviewInterface $review);
}
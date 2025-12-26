<?php

/* --------------------------------------------------------------
   ReviewRepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewRepository
 *
 * @category   System
 * @package    Review
 * @subpackage Repositories
 */
class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * @var \ReviewWriterInterface
     */
    protected $writer;
    
    /**
     * @var \ReviewDeleterInterface
     */
    protected $deleter;
    
    
    /**
     * ReviewRepository constructor.
     *
     * @param \ReviewWriterInterface  $writer
     * @param \ReviewDeleterInterface $deleter
     */
    public function __construct(ReviewWriterInterface $writer, ReviewDeleterInterface $deleter)
    {
        $this->writer  = $writer;
        $this->deleter = $deleter;
    }
    
    
    /**
     * Saves review entity in database.
     *
     * @param \ReviewInterface $review Review entity to be saved.
     *
     * @return $this|\ReviewRepositoryInterface Same instance for chained method calls.
     */
    public function store(ReviewInterface $review)
    {
        if ($review->getId() === 0) {
            $this->writer->store($review);
        } else {
            $this->writer->update($review);
        }
        
        return $this;
    }
    
    
    /**
     * Deletes review entity from database.
     *
     * @param \ReviewInterface $review Review entity to de deleted.
     *
     * @return $this|\ReviewRepositoryInterface Same instance for chained method calls.
     */
    public function delete(ReviewInterface $review)
    {
        $this->deleter->delete($review);
        
        return $this;
    }
}
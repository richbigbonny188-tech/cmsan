<?php
/* --------------------------------------------------------------
   ContentDeleteService.inc.php 2019-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentDeleteService
 */
class ContentDeleteService implements ContentDeleteServiceInterface
{
    /**
     * @var ContentRepository
     */
    protected $repository;
    
    
    /**
     * ContentDeleteService constructor.
     *
     * @param ContentRepositoryInterface $repository
     */
    public function __construct(ContentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Deletes the content data in database by id.
     *
     * @param $contentGroupId
     *
     * @return $this|ContentRepositoryInterface Same instance for chained method calls.
     */
    public function deleteById($contentGroupId)
    {
        return $this->repository->deleteById($contentGroupId);
    }
}
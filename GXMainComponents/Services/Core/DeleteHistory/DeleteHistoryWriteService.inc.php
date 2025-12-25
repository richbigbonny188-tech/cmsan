<?php
/* --------------------------------------------------------------
   DeleteHistoryWriteService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryWriteService
 */
class DeleteHistoryWriteService implements DeleteHistoryWriteServiceInterface
{
    /**
     * @var \DeleteHistoryRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * DeleteHistoryWriteService constructor.
     *
     * @param \DeleteHistoryRepositoryInterface $repository
     */
    public function __construct(DeleteHistoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Reports the deletion of an entity with the given id in the given scope.
     *
     * @param \DeletedId          $id    Value of deleted id.
     * @param \DeleteHistoryScope $scope Scope of deleted entity.
     *
     * @return void
     */
    public function reportDeletion(DeletedId $id, DeleteHistoryScope $scope)
    {
        $this->repository->reportDeletion($id, $scope);
    }
}
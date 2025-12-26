<?php
/* --------------------------------------------------------------
   DeleteHistoryReadService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryReadService
 */
class DeleteHistoryReadService implements DeleteHistoryReadServiceInterface
{
    /**
     * @var \DeleteHistoryRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * DeleteHistoryReadService constructor.
     *
     * @param \DeleteHistoryRepositoryInterface $repository
     */
    public function __construct(DeleteHistoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Reports about removed entities in given scope and date range.
     *
     * @param \DateRange          $range Date range for search.
     * @param \DeleteHistoryScope $scope Domain scope for search.
     *
     * @return \DeleteHistoryReport Report about removed entities.
     */
    public function findDeleted(DateRange $range, DeleteHistoryScope $scope)
    {
        return $this->repository->findDeleted($range, $scope);
    }
}
<?php
/* --------------------------------------------------------------
  ContentReadService.php 2019-07-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class ContentReadService
 */
class ContentReadService implements ContentReadServiceInterface
{
    /**
     * @var ContentRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ContentService constructor.
     *
     * @param ContentRepositoryInterface $repository
     */
    public function __construct(ContentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @param mixed $id id of the Content
     *
     * @return mixed
     * @throws ContentNotFoundException
     */
    public function findById($id)
    {
        return $this->repository->findById($id);
    }
    
    
    /**
     * @return ContentIdentificationInterface
     */
    public function nextContentGroupId(): ContentIdentificationInterface
    {
        return $this->repository->nextContentGroupId();
    }
    
    
    /**
     * @return InfoElementContent[]
     * @throws ContentNotFoundException
     * @throws UnfinishedBuildException
     */
    public function getAllInfoElements(): array
    {
        return $this->repository->getAllInfoElements();
    }
    
    
    /**
     * @return array
     */
    public function getAllContentPages(): array
    {
        return $this->repository->getAllContentPages();
    }
}

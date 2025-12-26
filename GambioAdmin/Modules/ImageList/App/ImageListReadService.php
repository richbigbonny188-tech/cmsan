<?php
/*--------------------------------------------------------------
   ImageListReadService.php 2021-05-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App;

use Gambio\Admin\Modules\ImageList\Model\Collections\ImageLists;
use Gambio\Admin\Modules\ImageList\Model\ImageList;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListRepository as ImageListRepositoryInterface;

/**
 * Class ImageListReadService
 * @package Gambio\Admin\Modules\ImageList\App
 */
class ImageListReadService implements ImageListReadServiceInterface
{
    /**
     * @var ImageListRepositoryInterface
     */
    private $repository;
    
    /**
     * @var ImageListFactory
     */
    private $factory;
    
    
    /**
     * ImageListReadService constructor.
     *
     * @param ImageListRepositoryInterface $repository
     * @param ImageListFactory             $factory
     */
    public function __construct(
        ImageListRepositoryInterface $repository,
        ImageListFactory $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getImageListById(int $imageListId): ImageList
    {
        $imageListId = $this->factory->createImageListId($imageListId);
        
        return $this->repository->getImageListById($imageListId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllImageLists(): ImageLists
    {
        return $this->repository->getAllImageLists();
    }
}
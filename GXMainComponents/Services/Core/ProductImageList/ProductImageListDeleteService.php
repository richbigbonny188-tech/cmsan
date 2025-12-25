<?php
/**
 * ProductImageListDeleteService.php 2023-03-06
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2023 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\ProductImageList;

use Gambio\ProductImageList\DeleteService\Interfaces\DeleteRepositoryInterface;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\ImageList\ValueObjects\ListModifierId;
use Gambio\ProductImageList\ImageList\ValueObjects\ListModifierType;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\Interfaces\ProductImageListDeleteServiceInterface;

/**
 * Class ProductImageListDeleteService
 * @package Gambio\ProductImageList\DeleteService
 */
class ProductImageListDeleteService implements ProductImageListDeleteServiceInterface
{
    /**
     * @var DeleteRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ProductImageListDeleteService constructor.
     *
     * @param DeleteRepositoryInterface $repository
     */
    public function __construct(
        DeleteRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteImageList(ListId $id, ListModifierId $entityId, ListModifierType $entityType) : void
    {
        $this->repository->deleteImageListById($id, $entityId, $entityType);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteImage(ListModifierId $modifierId, ListModifierType $modifierType, Id ...$ids) : void
    {
        $this->repository->deleteImageById($modifierId, $modifierType, ...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteImageListCombiAssignment(int $combis_id): void
    {
        $this->repository->deleteImageListCombiAssignment($combis_id);
    }
}
<?php
/* --------------------------------------------------------------
  ProductImageListCreateService.php 2020-01-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList;

use Gambio\ProductImageList\CreateService\Interfaces\CreateServiceRepositoryInterface;
use Gambio\ProductImageList\CreateService\Interfaces\ImageListImageDtoInterface;
use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\Interfaces\ProductImageListCreateServiceInterface;

/**
 * Class ProductImageListCreateService
 * @package Gambio\ProductImageList
 */
class ProductImageListCreateService implements ProductImageListCreateServiceInterface
{
    /**
     * @var CreateServiceRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ProductImageListCreateService constructor.
     *
     * @param CreateServiceRepositoryInterface $repository
     */
    public function __construct(CreateServiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImageList(string $listName): void
    {
        $this->repository->createImageList($listName);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImage(ImageListImageDtoInterface $image): Id
    {
        return $this->repository->createImage($image);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImageTexts(TextCollection $titles, TextCollection $altTitles): void
    {
        $this->repository->createImageTexts($titles, $altTitles);
    }
}
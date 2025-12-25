<?php
/* --------------------------------------------------------------
  CreateServiceRepository.php 2020-01-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\CreateService\Repositories;

use Gambio\ProductImageList\CreateService\Interfaces\CreateServiceDatabaseWriterInterface;
use Gambio\ProductImageList\CreateService\Interfaces\CreateServiceRepositoryInterface;
use Gambio\ProductImageList\CreateService\Interfaces\ImageListImageDtoInterface;
use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;

/**
 * Class CreateServiceRepository
 * @package Gambio\ProductImageList\CreateService\Repositories
 */
class CreateServiceRepository implements CreateServiceRepositoryInterface
{
    /**
     * @var CreateServiceDatabaseWriterInterface
     */
    protected $writer;
    
    
    /**
     * CreateServiceRepository constructor.
     *
     * @param CreateServiceDatabaseWriterInterface $writer
     */
    public function __construct(CreateServiceDatabaseWriterInterface $writer)
    {
        $this->writer = $writer;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImageList(string $listName): void
    {
        $this->writer->createImageList($listName);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImage(ImageListImageDtoInterface $image): Id
    {
        $imageId = $this->writer->createImage($image);
        
        return new Id($imageId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createImageTexts(TextCollection $titles, TextCollection $altTitles): void
    {
        $this->writer->createImageTexts($titles, $altTitles);
    }
}
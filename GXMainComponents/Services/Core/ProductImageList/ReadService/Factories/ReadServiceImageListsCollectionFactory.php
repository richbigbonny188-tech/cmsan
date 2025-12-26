<?php
/* --------------------------------------------------------------
  ReadServiceImageListFactory.php 2020-03-16
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Factories;

use Gambio\ProductImageList\Collections\ImageListsCollection;
use Gambio\ProductImageList\Image\Collections\TextCollection;
use Gambio\ProductImageList\Image\Exceptions\FileDoesNotExistException;
use Gambio\ProductImageList\Image\Exceptions\PathIsNotAnUrlException;
use Gambio\ProductImageList\Image\Exceptions\UnfinishedBuildException;
use Gambio\ProductImageList\Image\Interfaces\ImageBuilderInterface;
use Gambio\ProductImageList\Image\Interfaces\ImageInterface;
use Gambio\ProductImageList\Image\ValueObjects\AbstractText;
use Gambio\ProductImageList\Image\ValueObjects\AltTitle;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\Image\ValueObjects\InvalidLocalFilePath;
use Gambio\ProductImageList\Image\ValueObjects\InvalidWebFilePath;
use Gambio\ProductImageList\Image\ValueObjects\LanguageCode;
use Gambio\ProductImageList\Image\ValueObjects\LocalFilePath;
use Gambio\ProductImageList\Image\ValueObjects\SortOrder;
use Gambio\ProductImageList\Image\ValueObjects\Title;
use Gambio\ProductImageList\Image\ValueObjects\WebFilePath;
use Gambio\ProductImageList\ImageList\Exceptions\UnfinishedBuildException as UnfinishedListBuildException;
use Gambio\ProductImageList\ImageList\Interfaces\ImageListBuilderInterface;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\ImageList\ValueObjects\ListName;
use Gambio\ProductImageList\ReadService\Interfaces\ImageListImageDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ImageListImageTextDtoInterface;
use Gambio\ProductImageList\ReadService\Interfaces\ReadServiceImageListsCollectionFactoryInterface;
use IdType;
use InvalidArgumentException;
use LanguageProviderInterface;

/**
 * Class ReadServiceImageListFactory
 * @package Gambio\ProductImageList\ReadService\Factories
 */
class ReadServiceImageListsCollectionFactory implements ReadServiceImageListsCollectionFactoryInterface
{
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    /**
     * @var Id[]
     */
    protected $imageIdStack = [];
    
    /**
     * @var LanguageCode[]
     */
    protected $languageCodeStack = [];
    
    /**
     * @var TextCollection[]
     */
    protected $textCollections = [];
    
    /**
     * @var ImageInterface[]
     */
    protected $images = [];
    
    /**
     * @var ImageBuilderInterface
     */
    protected $imageBuilder;
    
    /**
     * @var string
     */
    protected $shopWebPath;
    
    /**
     * @var string
     */
    protected $shopLocalPath;
    
    /**
     * @var ImageListBuilderInterface
     */
    protected $imageListBuilder;
    
    
    /**
     * ReadServiceImageListFactory constructor.
     *
     * @param LanguageProviderInterface $languageProvider
     * @param ImageBuilderInterface     $imageBuilder
     * @param ImageListBuilderInterface $imageListBuilder
     * @param string                    $shopWebPath
     * @param string                    $shopLocalPath
     */
    public function __construct(
        LanguageProviderInterface $languageProvider,
        ImageBuilderInterface $imageBuilder,
        ImageListBuilderInterface $imageListBuilder,
        string $shopWebPath,
        string $shopLocalPath
    ) {
        $this->languageProvider = $languageProvider;
        $this->imageBuilder     = $imageBuilder;
        $this->imageListBuilder = $imageListBuilder;
        $this->shopWebPath      = $shopWebPath;
        $this->shopLocalPath    = $shopLocalPath;
    }
    
    
    /**
     * @inheritDoc
     *
     * @throws FileDoesNotExistException
     * @throws PathIsNotAnUrlException
     * @throws UnfinishedBuildException
     * @throws UnfinishedListBuildException
     */
    public function createImageListCollection(
        array $imageListDtos,
        array $imageListImageDtos,
        array $imageListImageTextDtos
    ): ImageListsCollection {
        
        $this->factoryReset();
        $this->createImageListImageTextCollections($imageListImageTextDtos);
        $this->createImages($imageListImageDtos);
    
        $result = new ImageListsCollection;
        
        foreach ($imageListDtos as $dto) {
            
            $this->imageListBuilder->reset();
            $this->imageListBuilder->withListId(new ListId($dto->listId()))
                ->withListName(new ListName($dto->listName()));
            
    
            if (isset($this->images[$dto->listId()]) && count($this->images[$dto->listId()])) {
                
                foreach ($this->images[$dto->listId()] as $image) {
                    
                    $this->imageListBuilder->withImage($image);
                }
            }
            
            $result[] = $this->imageListBuilder->build();
        }
        
        return $result;
    }
    
    
    /**
     * @param ImageListImageDtoInterface[] $imageListImageDtos
     *
     * @throws FileDoesNotExistException
     * @throws PathIsNotAnUrlException
     * @throws UnfinishedBuildException
     */
    protected function createImages(array $imageListImageDtos): void
    {
        foreach ($imageListImageDtos as $dto) {
            
            $this->imageBuilder->reset();
            $this->assignBuilderImagePaths($dto);
            $this->imageBuilder->withId($this->createImageId($dto->imageId()))
                ->withSortOrder(new SortOrder($dto->sortOder()))
                ->withTitles($this->textCollections[$dto->imageId()][AbstractText::TEXT_TYPE_TITLE])
                ->withAltTitles($this->textCollections[$dto->imageId()][AbstractText::TEXT_TYPE_ALT_TITLE]);
            
            if (!isset($this->images[$dto->listId()])) {
                
                $this->images[$dto->listId()] = [];
            }
            
            $this->images[$dto->listId()][$dto->sortOder()] = $this->imageBuilder->build();
        }
    }
    
    
    /**
     * @param ImageListImageTextDtoInterface[] $imageListImageTextDtos
     */
    protected function createImageListImageTextCollections(array $imageListImageTextDtos)
    {
        foreach ($imageListImageTextDtos as $dto) {
            
            $textData = [
                $this->createImageId($dto->imageId()),
                $dto->textValue(),
                $this->createLanguageCode($dto->languageId())
            ];
            
            if (!isset($this->textCollections[$dto->imageId()])) {
                
                $this->textCollections[$dto->imageId()] = [];
            }
            
            if (!isset($this->textCollections[$dto->imageId()][$dto->textType()])) {
                
                $this->textCollections[$dto->imageId()][$dto->textType()] = new TextCollection;
            }
            
            switch ($dto->textType()) {
                
                case AbstractText::TEXT_TYPE_ALT_TITLE :
                    
                    $this->textCollections[$dto->imageId()][$dto->textType()][] = new AltTitle(...$textData);
                    break;
                
                case AbstractText::TEXT_TYPE_TITLE:
                    
                    $this->textCollections[$dto->imageId()][$dto->textType()][] = new Title(...$textData);
                    break;
                
                default:
                    
                    throw new InvalidArgumentException('TextType: ' . $dto->textType() . ' is not an '
                                                       . AbstractText::class);
            }
        }
    }
    
    
    /**
     * @param int $imageId
     *
     * @return Id
     */
    protected function createImageId(int $imageId): Id
    {
        if (!isset($this->imageIdStack[$imageId])) {
            
            $this->imageIdStack[$imageId] = new Id($imageId);
        }
        
        return $this->imageIdStack[$imageId];
    }
    
    
    /**
     * @param int $languageId
     *
     * @return LanguageCode
     */
    protected function createLanguageCode(int $languageId): LanguageCode
    {
        if (!isset($this->languageCodeStack[$languageId])) {
            
            $code = $this->languageProvider->getCodeById(new IdType($languageId))
                ->asString();
            $this->languageCodeStack[$languageId] = new LanguageCode($code);
        }
        
        return $this->languageCodeStack[$languageId];
    }
    
    
    protected function factoryReset(): void
    {
        $this->imageIdStack = $this->languageCodeStack = $this->textCollections = $this->images = [];
    }
    
    
    /**
     * @param string $localPath
     *
     * @return string
     */
    protected function webPathFromLocalPath(string $localPath): string
    {
        $webPath = str_replace($this->shopLocalPath, $this->shopWebPath, $localPath);
        $webPath = explode('/', $webPath);

        // $i = 0 to 2 is https://domain and should be skipped for filtering
        for($i = 3; $i < count($webPath); $i++) {
            $webPath[$i] = rawurlencode(rawurldecode($webPath[$i]));
        }

        $webPath = implode('/', $webPath);

        return $webPath;
    }

    /**
     * @param ImageListImageDtoInterface $dto
     */
    protected function assignBuilderImagePaths(ImageListImageDtoInterface $dto): void
    {
        try {
            $this->imageBuilder->withLocalFilePath(
                new LocalFilePath($this->shopLocalPath . urldecode($dto->localPath()))
            );
            $this->imageBuilder->withWebFilePath(new WebFilePath($this->webPathFromLocalPath($this->shopLocalPath . $dto->localPath())));
        } catch (PathIsNotAnUrlException | FileDoesNotExistException $e) {
            $this->imageBuilder->withLocalFilePath(
                new InvalidLocalFilePath($this->shopLocalPath, $dto->localPath())
            );
            $this->imageBuilder->withWebFilePath(
                new InvalidWebFilePath($this->shopWebPath, $dto->localPath())
            );
        }

    }
}
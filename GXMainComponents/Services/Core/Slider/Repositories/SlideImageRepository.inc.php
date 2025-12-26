<?php

/* --------------------------------------------------------------
  SlideImageRepository.inc.php 2016-10-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('SlideImageRepositoryInterface');

/**
 * Class SliderRepository
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideImageRepository implements SlideImageRepositoryInterface
{
    /**
     * @var SlideImageRepositoryWriterInterface
     */
    protected $writer;
    
    /**
     * @var SlideImageRepositoryReaderInterface
     */
    protected $reader;
    
    /**
     * @var SlideImageRepositoryDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var SlideImageAreaRepositoryInterface
     */
    protected $slideImageAreaRepository;
    
    
    /**
     * SlideImageRepository constructor.
     *
     * @param SlideImageRepositoryWriterInterface  $writer
     * @param SlideImageRepositoryReaderInterface  $reader
     * @param SlideImageRepositoryDeleterInterface $deleter
     * @param SlideImageAreaRepositoryInterface    $slideImageAreaRepository
     */
    public function __construct(
        SlideImageRepositoryWriterInterface $writer,
        SlideImageRepositoryReaderInterface $reader,
        SlideImageRepositoryDeleterInterface $deleter,
        SlideImageAreaRepositoryInterface $slideImageAreaRepository
    ) {
        $this->writer                   = $writer;
        $this->reader                   = $reader;
        $this->deleter                  = $deleter;
        $this->slideImageAreaRepository = $slideImageAreaRepository;
    }
    
    
    /**
     * Returns a SlideImageCollection instance by the given slide ID.
     *
     * @param IdType $slideId ID of the slide to get.
     *
     * @return SlideImageCollection
     */
    public function getBySlideId(IdType $slideId)
    {
        $slideImageCollection = $this->reader->getBySlideId($slideId);
        
        /** @var SlideImageInterface $slideImage */
        foreach ($slideImageCollection->getArray() as $slideImage) {
            $slideImage->setSlideImageAreaCollection($this->_getSlideImageAreaCollection(new IdType($slideImage->getId())));
        }
        
        return $slideImageCollection;
    }
    
    
    /**
     * Returns a SlideImage instance by the given slide image ID.
     *
     * @param IdType $slideImageId ID of the slide image to get.
     *
     * @return SlideImageInterface
     * @throws UnexpectedValueException
     *
     * @throws InvalidArgumentException If no slide image could be found by the provided ID.
     */
    public function getById(IdType $slideImageId)
    {
        $slideImage = $this->reader->getById($slideImageId);
        $slideImage->setSlideImageAreaCollection($this->_getSlideImageAreaCollection($slideImageId));
        
        return $slideImage;
    }
    
    
    /**
     * Returns a SlideImageCollection with all existing SlideImage objects by the given slide ID and language ID.
     *
     * @param IdType $slideId    ID of the slide to receive the slide image collection from.
     * @param IdType $languageId Language ID.
     *
     * @return SlideImageCollection
     * @throws UnexpectedValueException
     *
     * @throws InvalidArgumentException If no slide could be found by the provided slide ID.
     */
    public function getBySlideIdAndLanguageId(IdType $slideId, IdType $languageId)
    {
        $slideImageCollection = $this->reader->getBySlideIdAndLanguageId($slideId, $languageId);
        
        /** @var SlideImageInterface $slideImage */
        foreach ($slideImageCollection->getArray() as $slideImage) {
            $slideImage->setSlideImageAreaCollection($this->_getSlideImageAreaCollection(new IdType($slideImage->getId())));
        }
        
        return $slideImageCollection;
    }
    
    
    /**
     * Stores a SlideImage to the database.
     *
     * @param IdType              $slideId    ID of the Slide to to save the slide image to.
     * @param SlideImageInterface $slideImage Slide image to save.
     *
     * @return SlideImageRepositoryInterface Same instance for method chaining.
     * @throws InvalidArgumentException If no slider could be found by the given slide ID.
     *
     */
    public function store(IdType $slideId, SlideImageInterface $slideImage)
    {
        $slideImageId = new IdType($this->writer->store($slideId, $slideImage));
        $slideImage->setId($slideImageId);
        
        /** @var SlideImageAreaInterface $slideImageArea */
        foreach ($slideImage->getSlideImageAreaCollection()->getArray() as $slideImageArea) {
            $this->slideImageAreaRepository->store($slideImageId, $slideImageArea);
        }
        
        return $this;
    }
    
    
    /**
     * Deletes a SlideImage by the given slide image ID.
     *
     * @param IdType $slideImageId ID of the slide image to delete.
     *
     * @return SlideImageRepositoryInterface Same instance for method chaining.
     */
    public function deleteSlideImageById(IdType $slideImageId)
    {
        $this->deleter->deleteById($slideImageId);
        $this->slideImageAreaRepository->deleteSlideImageAreasBySlideImageId($slideImageId);
        
        return $this;
    }
    
    
    /**
     * Check if an image file is used by another slide image entry.
     *
     * @param FilenameStringType $filename Slide image filename.
     * @param IdType             $slideImageId
     *
     * @return bool
     */
    public function isSlideImageFileUsed(FilenameStringType $filename, IdType $slideImageId)
    {
        return $this->reader->isSlideImageFileUsed($filename, $slideImageId);
    }
    
    
    /**
     * Unset the image filename references in other slide image entry by the given filename.
     *
     * @param FilenameStringType $filename Slide image filename.
     *
     * @return SlideImageRepositoryInterface Same instance for method chaining.
     */
    public function unsetSlideImageReference(FilenameStringType $filename)
    {
        $this->writer->unsetSlideImageReference($filename);
        
        return $this;
    }
    
    
    /**
     * Returns a SlideImageAreaCollection with all existing SlideImageArea objects by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageAreaCollection
     */
    protected function _getSlideImageAreaCollection(IdType $slideImageId)
    {
        $slideImageAreaCollection = $this->slideImageAreaRepository->getBySlideImageId($slideImageId);
        
        return $slideImageAreaCollection;
    }
}
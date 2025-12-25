<?php

/* --------------------------------------------------------------
  SlideRepository.inc.php 2016-09-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('SlideRepositoryInterface');

/**
 * Class SliderRepository
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideRepository implements SlideRepositoryInterface
{
    /**
     * @var SlideRepositoryWriterInterface
     */
    protected $writer;
    
    /**
     * @var SlideRepositoryReaderInterface
     */
    protected $reader;
    
    /**
     * @var SlideRepositoryDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var SlideImageRepositoryInterface
     */
    protected $slideImageRepository;
    
    
    /**
     * SlideRepository constructor.
     *
     * @param SlideRepositoryWriterInterface  $writer
     * @param SlideRepositoryReaderInterface  $reader
     * @param SlideRepositoryDeleterInterface $deleter
     * @param SlideImageRepositoryInterface   $slideImageRepository
     */
    public function __construct(
        SlideRepositoryWriterInterface $writer,
        SlideRepositoryReaderInterface $reader,
        SlideRepositoryDeleterInterface $deleter,
        SlideImageRepositoryInterface $slideImageRepository
    ) {
        $this->writer               = $writer;
        $this->reader               = $reader;
        $this->deleter              = $deleter;
        $this->slideImageRepository = $slideImageRepository;
    }
    
    
    /**
     * Returns a SlideCollection instance by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SlideCollection
     */
    public function getBySliderId(IdType $sliderId)
    {
        $slideCollection = $this->reader->getBySliderId($sliderId);
        
        /** @var SlideInterface $slide */
        foreach ($slideCollection->getArray() as $slide) {
            $slide->setSlideImageCollection($this->_getSlideImageCollection(new IdType($slide->getId())));
        }
        
        return $slideCollection;
    }
    
    
    /**
     * Returns a Slide instance by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideInterface
     */
    public function getById(IdType $slideId)
    {
        $slide = $this->reader->getById($slideId);
        $slide->setSlideImageCollection($this->_getSlideImageCollection($slideId));
        
        return $slide;
    }
    
    
    /**
     * Returns a SlideCollection with all existing Slide objects by the given slider ID and language ID.
     *
     * @param IdType $sliderId
     * @param IdType $languageId
     *
     * @return SlideCollection
     */
    public function getBySliderIdAndLanguageId(IdType $sliderId, IdType $languageId)
    {
        $slideCollection = $this->reader->getBySliderIdAndLanguageId($sliderId, $languageId);
        
        /** @var SlideInterface $slide */
        foreach ($slideCollection->getArray() as $slide) {
            $slide->setSlideImageCollection($this->_getSlideImageCollection(new IdType($slide->getId())));
        }
        
        return $slideCollection;
    }
    
    
    /**
     * Stores a Slide to the database.
     *
     * @param IdType         $sliderId
     * @param SlideInterface $slide
     *
     * @return SlideRepositoryInterface Same instance for method chaining.
     */
    public function store(IdType $sliderId, SlideInterface $slide)
    {
        $slideId = new IdType($this->writer->store($sliderId, $slide));
        $slide->setId($slideId);
        
        /** @var SlideImageInterface $slideImage */
        foreach ($slide->getSlideImageCollection()->getArray() as $slideImage) {
            $this->slideImageRepository->store($slideId, $slideImage);
        }
        
        return $this;
    }
    
    
    /**
     * Deletes a Slide by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideRepositoryInterface Same instance for method chaining.
     */
    public function deleteSlideById(IdType $slideId)
    {
        $slide = $this->getById($slideId);
        $this->deleter->deleteById($slideId);
        
        /** @var SlideImageInterface $slideImage */
        foreach ($slide->getSlideImageCollection()->getArray() as $slideImage) {
            $this->slideImageRepository->deleteSlideImageById(new IdType($slideImage->getId()));
        }
        
        return $this;
    }
    
    
    /**
     * Check if an image file is used by another slide entry.
     *
     * @param FilenameStringType $filename Slide thumbnail image filename.
     * @param IdType             $slideId
     *
     * @return bool
     */
    public function isSlideThumbnailImageFileUsed(FilenameStringType $filename, IdType $slideId)
    {
        return $this->reader->isSlideThumbnailImageFileUsed($filename, $slideId);
    }
    
    
    /**
     * Unset the thumbnail filename references in other slide thumbnail entry by the given filename.
     *
     * @param FilenameStringType $filename Slide image filename.
     *
     * @return SlideRepositoryInterface Same instance for method chaining.
     */
    public function unsetSlideThumbnailReference(FilenameStringType $filename)
    {
        $this->writer->unsetSlideThumbnailReference($filename);
        
        return $this;
    }
    
    
    /**
     * Returns a SlideImageCollection with all existing SlideImage objects by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideImageCollection
     */
    protected function _getSlideImageCollection(IdType $slideId)
    {
        $slideImageCollection = $this->slideImageRepository->getBySlideId($slideId);
        
        return $slideImageCollection;
    }
}
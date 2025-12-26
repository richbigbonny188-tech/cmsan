<?php

/* --------------------------------------------------------------
  SliderRepository.inc.php 2016-08-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('SliderRepositoryInterface');

/**
 * Class SliderRepository
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SliderRepository implements SliderRepositoryInterface
{
    /** @var  SliderRepositoryWriterInterface */
    protected $writer;
    
    /** @var  SliderRepositoryReaderInterface */
    protected $reader;
    
    /** @var  SliderRepositoryDeleterInterface */
    protected $deleter;
    
    /** @var  SlideRepositoryInterface */
    protected $slideRepository;
    
    
    /**
     * SliderRepository constructor.
     *
     * @param SliderRepositoryWriterInterface  $writer
     * @param SliderRepositoryReaderInterface  $reader
     * @param SliderRepositoryDeleterInterface $deleter
     * @param SlideRepositoryInterface         $slideRepository
     */
    public function __construct(
        SliderRepositoryWriterInterface $writer,
        SliderRepositoryReaderInterface $reader,
        SliderRepositoryDeleterInterface $deleter,
        SlideRepositoryInterface $slideRepository
    ) {
        $this->writer          = $writer;
        $this->reader          = $reader;
        $this->deleter         = $deleter;
        $this->slideRepository = $slideRepository;
    }
    
    
    /**
     * Returns a SliderCollection with all existing Slider objects.
     *
     * @return SliderCollection
     */
    public function getAll()
    {
        $sliderCollection = $this->reader->getAll();
        
        /** @var SliderInterface $slider */
        foreach ($sliderCollection->getArray() as $slider) {
            $slider->setSlideCollection($this->_getSlideCollection(new IdType($slider->getId())));
        }
        
        return $sliderCollection;
    }
    
    
    /**
     * Returns a Slider instance by the given slider ID.
     *
     * @param IdType $sliderId ID of the slider to get.
     *
     * @return SliderInterface
     */
    public function getById(IdType $sliderId)
    {
        $slider = $this->reader->getById($sliderId);
        $slider->setSlideCollection($this->_getSlideCollection(new IdType($slider->getId())));
        
        return $slider;
    }
    
    
    /**
     * Stores a Slider to the database.
     *
     * @param SliderInterface $slider Slider to store.
     *
     * @return SliderRepositoryInterface Same instance for method chaining.
     * @throws InvalidArgumentException If no slider was found by the given ID.
     *
     */
    public function store(SliderInterface $slider)
    {
        if ($slider->getId() !== 0) {
            $this->deleteSliderById(new IdType($slider->getId()));
        }
        
        $sliderId = new IdType($this->writer->store($slider));
        $slider->setId($sliderId);
        
        /** @var SlideInterface $slide */
        foreach ($slider->getSlideCollection()->getArray() as $slide) {
            $this->slideRepository->store($sliderId, $slide);
        }
        
        return $this;
    }
    
    
    /**
     * Set the Slider for the start page.
     *
     * @param IdType $sliderId ID of the slider to set as the start page slider.
     *
     * @return SliderRepositoryInterface Same instance for method chaining.
     */
    public function setStartPageSlider(IdType $sliderId)
    {
        $this->writer->setStartPageSlider($sliderId);
        
        return $this;
    }
    
    
    /**
     * Deletes a Slider by the given slider ID.
     *
     * @param IdType $sliderId ID of the slider to delete.
     *
     * @return SliderRepositoryInterface Same instance for method chaining.
     * @throws InvalidArgumentException If no slider was found by the given ID.
     *
     */
    public function deleteSliderById(IdType $sliderId)
    {
        $slider = $this->getById($sliderId);
        $this->deleter->deleteById($sliderId);
        
        /** @var SlideInterface $slide */
        foreach ($slider->getSlideCollection()->getArray() as $slide) {
            $this->slideRepository->deleteSlideById(new IdType($slide->getId()));
        }
        
        return $this;
    }
    
    
    /**
     * Get the Slider for the start page.
     *
     * @return SliderInterface|null Returns the start page slider instance or null if no record was found.
     */
    public function getStartPageSlider()
    {
        return $this->reader->getStartPageSlider();
    }
    
    
    /**
     * Returns a SlideCollection with all existing Slide objects by the given slider ID.
     *
     * @param IdType $sliderId ID of the slider to fetch the slide collection from.
     *
     * @return SlideCollection
     */
    protected function _getSlideCollection(IdType $sliderId)
    {
        $slideCollection = $this->slideRepository->getBySliderId($sliderId);
        
        return $slideCollection;
    }
}
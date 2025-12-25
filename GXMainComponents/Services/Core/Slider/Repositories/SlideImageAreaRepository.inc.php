<?php

/* --------------------------------------------------------------
  SlideImageAreaRepository.inc.php 2016-10-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('SlideImageAreaRepositoryInterface');

/**
 * Class SlideImageAreaRepository
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SlideImageAreaRepository implements SlideImageAreaRepositoryInterface
{
    /**
     * @var SlideImageAreaRepositoryWriterInterface
     */
    protected $writer;
    
    /**
     * @var SlideImageAreaRepositoryReaderInterface
     */
    protected $reader;
    
    /**
     * @var SlideImageAreaRepositoryDeleterInterface
     */
    protected $deleter;
    
    
    /**
     * SlideImageAreaRepository constructor.
     *
     * @param SlideImageAreaRepositoryWriterInterface  $writer
     * @param SlideImageAreaRepositoryReaderInterface  $reader
     * @param SlideImageAreaRepositoryDeleterInterface $deleter
     */
    public function __construct(
        SlideImageAreaRepositoryWriterInterface $writer,
        SlideImageAreaRepositoryReaderInterface $reader,
        SlideImageAreaRepositoryDeleterInterface $deleter
    ) {
        $this->writer  = $writer;
        $this->reader  = $reader;
        $this->deleter = $deleter;
    }
    
    
    /**
     * Returns a SlideImageAreaCollection instance by the given slide image ID.
     *
     * @param IdType $slideImageId ID of the slide image to get.
     *
     * @return SlideImageAreaCollection
     */
    public function getBySlideImageId(IdType $slideImageId)
    {
        $slideImageAreaCollection = $this->reader->getBySlideImageId($slideImageId);
        
        return $slideImageAreaCollection;
    }
    
    
    /**
     * Returns a SlideImageArea instance by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SlideImageAreaInterface
     */
    public function getById(IdType $slideImageAreaId)
    {
        $slideImageArea = $this->reader->getById($slideImageAreaId);
        
        return $slideImageArea;
    }
    
    
    /**
     * Stores a SlideImageArea to the database.
     *
     * @param IdType                  $slideImageId
     * @param SlideImageAreaInterface $slideImageArea
     *
     * @return SlideImageAreaRepositoryInterface Same instance for method chaining.
     */
    public function store(IdType $slideImageId, SlideImageAreaInterface $slideImageArea)
    {
        $slideImageAreaId = new IdType($this->writer->store($slideImageId, $slideImageArea));
        
        $slideImageArea->setId($slideImageAreaId);
        
        return $this;
    }
    
    
    /**
     * Deletes a SlideImageArea by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SlideImageAreaRepositoryInterface Same instance for method chaining.
     */
    public function deleteSlideImageAreaById(IdType $slideImageAreaId)
    {
        $this->deleter->deleteById($slideImageAreaId);
        
        return $this;
    }
    
    
    /**
     * Deletes SlideImageAreas by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageAreaRepositoryInterface Same instance for method chaining.
     */
    public function deleteSlideImageAreasBySlideImageId(IdType $slideImageId)
    {
        $this->deleter->deleteBySlideImageId($slideImageId);
        
        return $this;
    }
}
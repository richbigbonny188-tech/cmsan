<?php

/* --------------------------------------------------------------
   SliderImage.inc.php 2016-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Class SlideImage
 *
 * Represents the default settings of a single image in a slide.
 *
 * @category   System
 * @package    Slider
 * @subpackage Entities
 */
class SlideImage implements SlideImageInterface
{
    /**
     * Slide image ID
     *
     * @var int
     */
    protected $id = 0;
    
    /**
     * Language ID
     *
     * @var int
     */
    protected $languageId = 0;
    
    /**
     * Breakpoint
     *
     * @var string
     */
    protected $breakpoint = '';
    
    /**
     * Image
     *
     * @var string
     */
    protected $image = '';
    
    /**
     * Collection of slide image areas
     *
     * @var SlideImageAreaCollection
     */
    protected $slideImageAreaCollection;
    
    
    /**
     * SlideImage constructor.
     */
    public function __construct()
    {
        // Set SlideImageAreaCollection items.
        $slideImageAreaCollection = MainFactory::create('SlideImageAreaCollection', []);
        $this->setSlideImageAreaCollection($slideImageAreaCollection);
    }
    
    
    /**
     * Set the slide image ID.
     *
     * @param IdType $id Slide image ID.
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the ID of the slide image.
     *
     * @return int Slide image ID.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Set the language ID for the slide image.
     *
     * @param IdType $languageId Language ID.
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setLanguageId(IdType $languageId)
    {
        $this->languageId = $languageId->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the language ID of the slide image.
     *
     * @return int Slide image language ID.
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
    
    
    /**
     * Set the breakpoint for the slide image.
     *
     * @param NonEmptyStringType $breakpoint
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setBreakpoint(NonEmptyStringType $breakpoint)
    {
        $this->breakpoint = $breakpoint->asString();
        
        return $this;
    }
    
    
    /**
     * Return the breakpoint of the slide image.
     *
     * @return string Breakpoint of the slide image.
     */
    public function getBreakpoint()
    {
        return $this->breakpoint;
    }
    
    
    /**
     * Set the image for the image slider.
     *
     * @param StringType $image The image to set.
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setImage(StringType $image)
    {
        $this->image = $image->asString();
        
        return $this;
    }
    
    
    /**
     * Return the image of the image slider.
     *
     * @return string Image of the image slider.
     */
    public function getImage()
    {
        return $this->image;
    }
    
    
    /**
     * Set the slide image area collection for the slide image.
     *
     * @param SlideImageAreaCollection $slideImageAreaCollection
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setSlideImageAreaCollection(SlideImageAreaCollection $slideImageAreaCollection)
    {
        $this->slideImageAreaCollection = $slideImageAreaCollection;
        
        return $this;
    }
    
    
    /**
     * Return the slide image area collection of the slide image.
     *
     * @return SlideImageAreaCollection Slide image area collection.
     */
    public function getSlideImageAreaCollection()
    {
        return $this->slideImageAreaCollection;
    }
    
    
    /**
     * Adds a slide image area to the slide image area collection
     *
     * @param SlideImageAreaInterface $slideImageArea
     *
     * @return SlideImageInterface Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    public function addSlideImageArea(SlideImageAreaInterface $slideImageArea)
    {
        $this->slideImageAreaCollection->addItem($slideImageArea);
        
        return $this;
    }
}
<?php

/* --------------------------------------------------------------
   Slider.inc.php 2016-08-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Class Slider
 *
 * Represents the default settings of a slider. Has got the slide settings as collection for every
 * slide of a slider.
 *
 * @category   System
 * @package    Slider
 * @subpackage Entities
 */
class Slider implements SliderInterface
{
    /**
     * Slider ID
     *
     * @var int
     */
    protected $id = 0;
    
    /**
     * Slider Name
     *
     * @var string
     */
    protected $name = '';
    
    /**
     * Animation speed of the slider
     *
     * @var double
     */
    protected $speed = 3;
    
    /**
     * Is it shown on start page?
     *
     * @var bool
     */
    protected $startPage = false;
    
    /**
     * Collection of slides
     *
     * @var SlideCollection
     */
    protected $slideCollection;
    
    
    public function __construct()
    {
        // Set SlideCollection items.
        $slideCollection = MainFactory::create('SlideCollection', []);
        $this->setSlideCollection($slideCollection);
    }
    
    
    /**
     * Set the ID for the slider.
     *
     * @param IdType $id Slider ID.
     *
     * @return SliderInterface Same instance for chained method calls.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the ID of the slider.
     *
     * @return int Slider ID.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Set the name for the slider.
     *
     * @param StringType $name Name for the slider.
     *
     * @return SliderInterface Same instance for chained method calls.
     */
    public function setName(StringType $name)
    {
        $this->name = $name->asString();
        
        return $this;
    }
    
    
    /**
     * Return the Name of the slider.
     *
     * @return string Name.
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Set the speed for the slider.
     *
     * @param DecimalType $speed The speed in seconds for the slider.
     *
     * @return SliderInterface Same instance for chained method calls.
     */
    public function setSpeed(DecimalType $speed)
    {
        $this->speed = $speed->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Return the speed of the slider.
     *
     * @return double Speed.
     */
    public function getSpeed()
    {
        return $this->speed;
    }
    
    
    /**
     * Return true when the slider is displayed on the start page, false otherwise.
     *
     * @return bool
     */
    public function showOnStartPage()
    {
        return $this->startPage;
    }
    
    
    /**
     * Show or hides a slider on the start page.
     *
     * @param BoolType $status True when it should be displayed, false otherwise.
     *
     * @return SliderInterface Same instance for chained method calls.
     */
    public function setShowOnStartPage(BoolType $status)
    {
        $this->startPage = $status->asBool();
        
        return $this;
    }
    
    
    /**
     * Set the slide collection for the slider.
     *
     * @param SlideCollection $slideCollection
     *
     * @return SliderInterface Same instance for chained method calls.
     */
    public function setSlideCollection(SlideCollection $slideCollection)
    {
        $this->slideCollection = $slideCollection;
        
        return $this;
    }
    
    
    /**
     * Return the slide collection of the slider.
     *
     * @return SlideCollection Slide collection.
     */
    public function getSlideCollection()
    {
        return $this->slideCollection;
    }
    
    
    /**
     * Adds a slide to the slide collection
     *
     * @param SlideInterface $slide
     *
     * @return SliderInterface Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    public function addSlide(SlideInterface $slide)
    {
        $this->slideCollection->addItem($slide);
        
        return $this;
    }
}
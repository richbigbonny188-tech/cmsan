<?php

/* --------------------------------------------------------------
   Slide.inc.php 2016-10-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Slide
 *
 * Represents the default settings of a single slide in a slider. Has got the slider image settings as collection for
 * every image of a slide.
 *
 * @category   System
 * @package    Slider
 * @subpackage Entities
 */
class Slide implements SlideInterface
{
    /**
     * Slide ID
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
     * Thumbnail
     *
     * @var string
     */
    protected $thumbnail = '';
    
    /**
     * Title
     *
     * @var string
     */
    protected $title = '';
    
    /**
     * Image alt text
     *
     * @var string
     */
    protected $altText = '';
    
    /**
     * Link url
     *
     * @var string
     */
    protected $url = '';
    
    /**
     * Link target (e.g. _blank or _self)
     *
     * @var string
     */
    protected $urlTarget = '';
    
    
    /**
     * Collection of Slide Images
     *
     * @var SlideImageCollection
     */
    protected $slideImageCollection;
    
    
    /**
     * Sort order
     *
     * @var int
     */
    protected $sortOrder = 0;
    
    
    public function __construct()
    {
        // Set SlideImageCollection items.
        $slideImageCollection = MainFactory::create('SlideImageCollection', []);
        $this->setSlideImageCollection($slideImageCollection);
    }
    
    
    /**
     * Set the slide ID for the slide.
     *
     * @param IdType $id Slide ID.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the slide ID.
     *
     * @return int Slide ID.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Set the language ID for the slide.
     *
     * @param IdType $languageId Language ID.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setLanguageId(IdType $languageId)
    {
        $this->languageId = $languageId->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the language ID.
     *
     * @return int Language ID.
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
    
    
    /**
     * Set the thumbnail for the slide.
     *
     * @param StringType $thumbnail Thumbnail to set.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setThumbnail(StringType $thumbnail)
    {
        $this->thumbnail = $thumbnail->asString();
        
        return $this;
    }
    
    
    /**
     * Return the thumbnail of the slide.
     *
     * @return string Thumbnail.
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }
    
    
    /**
     * Set the title for the slide.
     *
     * @param StringType $title Title for the slide.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setTitle(StringType $title)
    {
        $this->title = $title->asString();
        
        return $this;
    }
    
    
    /**
     * Return the title of the slide.
     *
     * @return string Title.
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    
    /**
     * Set the alt text for the slide.
     *
     * @param StringType $altText .
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setAltText(StringType $altText)
    {
        $this->altText = $altText->asString();
        
        return $this;
    }
    
    
    /**
     * Return the alt text of the slide.
     *
     * @return string Alt text of the slide.
     */
    public function getAltText()
    {
        return $this->altText;
    }
    
    
    /**
     * Set the URL for the slide.
     *
     * @param StringType $url URL.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setUrl(StringType $url)
    {
        $this->url = $url->asString();
        
        return $this;
    }
    
    
    /**
     * Return the URL of the slide.
     *
     * @return string URL.
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    
    /**
     * Set the url target property for the slide.
     *
     * @param StringType $urlTarget URL target e.g. '_blank'.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setUrlTarget(StringType $urlTarget)
    {
        $validUrlTargets = ['_self', '_blank'];
        if (!in_array($urlTarget->asString(), $validUrlTargets)) {
            throw new InvalidArgumentException('Slide: Unsupported link target. ' . 'Supported link targets are: "'
                                               . implode('","', $validUrlTargets) . '". ' . 'Got "'
                                               . $urlTarget->asString() . '".');
        }
        
        $this->urlTarget = $urlTarget->asString();
        
        return $this;
    }
    
    
    /**
     * Return the url target property.
     *
     * @return string URL target e.g. '_blank'.
     */
    public function getUrlTarget()
    {
        return $this->urlTarget;
    }
    
    
    /**
     * Set the slide image collection for the slide.
     *
     * @param SlideImageCollection $slideImageCollection
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setSlideImageCollection(SlideImageCollection $slideImageCollection)
    {
        $this->slideImageCollection = $slideImageCollection;
        
        return $this;
    }
    
    
    /**
     * Return the slide image collection of the slide.
     *
     * @return SlideImageCollection Slide image collection.
     */
    public function getSlideImageCollection()
    {
        return $this->slideImageCollection;
    }
    
    
    /**
     * Adds a slide image to the slide image collection
     *
     * @param SlideImageInterface $slideImage
     *
     * @return SlideInterface Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    public function addSlideImage(SlideImageInterface $slideImage)
    {
        $this->slideImageCollection->addItem($slideImage);
        
        return $this;
    }
    
    
    /**
     * Set the sort order for the slide.
     *
     * @param IntType $sortOrder Slide sort order.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setSortOrder(IntType $sortOrder)
    {
        $this->sortOrder = $sortOrder->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the slide sort order.
     *
     * @return int sort order.
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
<?php

/* --------------------------------------------------------------
   SlideImageArea.inc.php 2016-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Class SlideImageArea
 *
 * Represents the default settings of a single image area in a slide.
 *
 * @category   System
 * @package    Slider
 * @subpackage Entities
 */
class SlideImageArea implements SlideImageAreaInterface
{
    /**
     * Slide image area ID
     *
     * @var int
     */
    protected $id = 0;
    
    /**
     * Link title
     *
     * @var string
     */
    protected $linkTitle = '';
    
    /**
     * Link URL
     *
     * @var string
     */
    protected $linkUrl = '';
    
    /**
     * Link target
     *
     * @var string
     */
    protected $linkTarget = '';
    
    /**
     * Image area coordinates
     *
     * @var string
     */
    protected $coordinates = '';
    
    
    /**
     * Set the slide image ID.
     *
     * @param IdType $id Slide image ID.
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
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
     * Set the link title for the slide image area.
     *
     * @param StringType $linkTitle
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
     */
    public function setLinkTitle(StringType $linkTitle)
    {
        $this->linkTitle = $linkTitle->asString();
        
        return $this;
    }
    
    
    /**
     * Return the link title of the slide image area.
     *
     * @return string Link title of the slide image area.
     */
    public function getLinkTitle()
    {
        return $this->linkTitle;
    }
    
    
    /**
     * Set the link URL for the slide image area.
     *
     * @param StringType $linkUrl
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
     */
    public function setLinkUrl(StringType $linkUrl)
    {
        $this->linkUrl = $linkUrl->asString();
        
        return $this;
    }
    
    
    /**
     * Return the link URL of the slide image area.
     *
     * @return string Link URL of the slide image area.
     */
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }
    
    
    /**
     * Set the link target for the slide image area.
     *
     * @param NonEmptyStringType $linkTarget
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
     * @throws InvalidArgumentException If the provided coordinates is not supported.
     *
     */
    public function setLinkTarget(NonEmptyStringType $linkTarget)
    {
        $validLinkTargets = ['_self', '_blank'];
        if (!in_array($linkTarget->asString(), $validLinkTargets)) {
            throw new InvalidArgumentException('SlideImageArea: Unsupported link target. '
                                               . 'Supported link targets are: "' . implode('","',
                                                                                           $validLinkTargets) . '". '
                                               . 'Got "' . $linkTarget->asString() . '".');
        }
        
        $this->linkTarget = $linkTarget->asString();
        
        return $this;
    }
    
    
    /**
     * Return the link target of the slide image area.
     *
     * @return string Link target of the slide image area.
     */
    public function getLinkTarget()
    {
        return $this->linkTarget;
    }
    
    
    /**
     * Set the coordinates for the slide image area.
     *
     * @param NonEmptyStringType $coordinates
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
     * @throws InvalidArgumentException If the provided coordinates are not valid.
     *
     */
    public function setCoordinates(NonEmptyStringType $coordinates)
    {
        $coords = str_replace(' ', '', $coordinates->asString());
        
        $pattern = '/[^0-9,]/';
        if (preg_match($pattern, $coords)) {
            throw new InvalidArgumentException('SlideImageArea: Invalid coordinates given: "' . $coordinates->asString()
                                               . '". Coordinates must only contain numbers separated by comma.');
        }
        
        $pattern = '/^[0-9]+,[0-9]+,[0-9]+,[0-9]+,[0-9]+,[0-9]+[0-9,]*/';
        if (!preg_match($pattern, $coords)) {
            throw new InvalidArgumentException('SlideImageArea: Invalid coordinates given: "' . $coordinates->asString()
                                               . '". There are at least six comma separated numbers needed to represent a shape.');
        }
        
        $this->coordinates = $coords;
        
        return $this;
    }
    
    
    /**
     * Return the coordinates of the slide image area.
     *
     * @return string Coordinates of the slide image area.
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }
}
<?php

/* --------------------------------------------------------------
   SlideImageAreaInterface.inc.php 2016-11-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SlideImageAreaInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageAreaInterface
{
    /**
     * Set the slide image ID.
     *
     * @param IdType $id Slide image ID.
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
     */
    public function setId(IdType $id);
    
    
    /**
     * Return the ID of the slide image.
     *
     * @return int Slide image ID.
     */
    public function getId();
    
    
    /**
     * Set the link title for the slide image area.
     *
     * @param StringType $linkTitle
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
     */
    public function setLinkTitle(StringType $linkTitle);
    
    
    /**
     * Return the link title of the slide image area.
     *
     * @return string Link title of the slide image area.
     */
    public function getLinkTitle();
    
    
    /**
     * Set the link URL for the slide image area.
     *
     * @param StringType $linkUrl
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
     */
    public function setLinkUrl(StringType $linkUrl);
    
    
    /**
     * Return the link URL of the slide image area.
     *
     * @return string Link URL of the slide image area.
     */
    public function getLinkUrl();
    
    
    /**
     * Set the link target for the slide image area.
     *
     * @param NonEmptyStringType $linkTarget
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
     */
    public function setLinkTarget(NonEmptyStringType $linkTarget);
    
    
    /**
     * Return the link target of the slide image area.
     *
     * @return string Link target of the slide image area.
     */
    public function getLinkTarget();
    
    
    /**
     * Set the coordinates for the slide image area.
     *
     * @param NonEmptyStringType $coordinates
     *
     * @return SlideImageAreaInterface Same instance for chained method calls.
     */
    public function setCoordinates(NonEmptyStringType $coordinates);
    
    
    /**
     * Return the coordinates of the slide image area.
     *
     * @return string Coordinates of the slide image area.
     */
    public function getCoordinates();
}
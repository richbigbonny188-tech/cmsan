<?php

/* --------------------------------------------------------------
   SliderJsonSerializer.inc.php 2020-04-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class SliderJsonSerializer
 *
 * This class will serialize and deserialize a Slider entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communications.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class SliderJsonSerializer extends AbstractJsonSerializer
{
    /**
     * Serialize a Slider object to a JSON string.
     *
     * @param Slider $object                   Object instance to be serialized.
     * @param bool   $encode                   (optional) Whether to json_encode the result of the method (default
     *                                         true).
     *
     * @return string|array Returns the json encoded slider (string) or an array that can be easily encoded
     *                      into a JSON string.
     * @throws InvalidArgumentException If the provided object type is invalid.
     */
    public function serialize($object, $encode = true)
    {
        if (!is_a($object, 'SliderInterface')) {
            throw new InvalidArgumentException('Invalid argument provided, SliderInterface object required: '
                                               . get_class($object));
        }
        
        $slider = [
            'id'              => $object->getId(),
            'name'            => $object->getName(),
            'speed'           => $object->getSpeed(),
            'showOnStartPage' => $object->showOnStartPage(),
            'slides'          => $this->_serializeSlides($object)
        ];
        
        return ($encode) ? $this->jsonEncode($slider) : $slider;
    }
    
    
    /**
     * Deserialize method is not used by the api.
     *
     * @param string $string     JSON string that contains the data of the slider.
     * @param object $baseObject (optional) If provided, this will be the base object to be updated
     *                           and no new instance will be created.
     *
     * @return Slider
     *
     * @throws InvalidArgumentException If the argument is not a string or is empty.
     */
    public function deserialize($string, $baseObject = null)
    {
        if (!is_string($string) || empty($string)) {
            throw new InvalidArgumentException('Invalid argument provided for deserialization: ' . gettype($string));
        }
        
        $json = json_decode($string); // error for malformed json strings
        
        if ($json === null && json_last_error() > 0) {
            throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: ' . $string);
        }
        
        if (!$baseObject) {
            $slider = MainFactory::create('Slider');
        } else {
            $slider = $baseObject;
        }
        
        // Deserialize Json String
        
        if ($json->id !== null) {
            $slider->setId(new IdType((int)$json->id));
        }
        
        if ($json->name !== null) {
            $slider->setName(new StringType((string)$json->name));
        }
        
        if ($json->speed !== null) {
            $slider->setSpeed(new DecimalType((double)$json->speed));
        }
        
        if ($json->showOnStartPage !== null) {
            $slider->setShowOnStartPage(new BoolType((bool)$json->showOnStartPage));
        }
        
        if ($json->slides !== null) {
            $slider->setSlideCollection($this->_deserializeSlides($json));
        }
        
        return $slider;
    }
    
    
    /**
     * Serialize the slides of a slider.
     *
     * @param Slider $slider
     *
     * @return array
     */
    protected function _serializeSlides($slider)
    {
        $slides = [];
        
        /** @var SlideInterface $slide */
        foreach ($slider->getSlideCollection()->getArray() as $slide) {
            $slides[] = [
                'id'         => $slide->getId(),
                'languageId' => $slide->getLanguageId(),
                'thumbnail'  => rawurlencode($slide->getThumbnail()),
                'title'      => $slide->getTitle(),
                'altText'    => $slide->getAltText(),
                'url'        => $slide->getUrl(),
                'urlTarget'  => $slide->getUrlTarget(),
                'images'     => $this->_serializeSlideImages($slide),
                'sortOrder'  => $slide->getSortOrder()
            ];
        }
        
        return $slides;
    }
    
    
    /**
     * Serialize the slide images of a slide.
     *
     * @param Slide $slide
     *
     * @return array
     */
    protected function _serializeSlideImages($slide)
    {
        $images = [];
        
        /** @var SlideImageInterface $slideImage */
        foreach ($slide->getSlideImageCollection()->getArray() as $slideImage) {
            $images[] = [
                'id'         => $slideImage->getId(),
                'languageId' => $slideImage->getLanguageId(),
                'breakpoint' => $slideImage->getBreakpoint(),
                'image'      => rawurlencode($slideImage->getImage()),
                'areas'      => $this->_serializeSlideImageAreas($slideImage)
            ];
        }
        
        return $images;
    }
    
    
    /**
     * Serialize the slide image areas of a slide image.
     *
     * @param SlideImage $slideImage
     *
     * @return array
     */
    public function _serializeSlideImageAreas($slideImage)
    {
        $areas = [];
        
        /** @var SlideImageAreaInterface $slideImageArea */
        foreach ($slideImage->getSlideImageAreaCollection()->getArray() as $slideImageArea) {
            $areas[] = [
                'id'          => $slideImageArea->getId(),
                'coordinates' => $slideImageArea->getCoordinates(),
                'linkTitle'   => $slideImageArea->getLinkTitle(),
                'linkUrl'     => $slideImageArea->getLinkUrl(),
                'linkTarget'  => $slideImageArea->getLinkTarget(),
            ];
        }
        
        return $areas;
    }
    
    
    /**
     * Deserialize slides from slider JSON object.
     *
     * @param object $json
     *
     * @return SlideCollection
     */
    protected function _deserializeSlides($json)
    {
        $slideCollectionArray = [];
        
        foreach ($json->slides as $jsonSlide) {
            $slide = MainFactory::create('Slide');
            
            if ($jsonSlide->id !== null) {
                $slide->setId(new IdType((int)$jsonSlide->id));
            }
            
            if ($jsonSlide->languageId !== null) {
                $slide->setLanguageId(new IdType((int)$jsonSlide->languageId));
            }
            
            if ($jsonSlide->thumbnail !== null) {
                $slide->setThumbnail(new StringType((string)$jsonSlide->thumbnail));
            }
            
            if ($jsonSlide->title !== null) {
                $slide->setTitle(new StringType((string)$jsonSlide->title));
            }
            
            if ($jsonSlide->altText !== null) {
                $slide->setAltText(new StringType((string)$jsonSlide->altText));
            }
            
            if ($jsonSlide->url !== null) {
                $slide->setUrl(new StringType((string)$jsonSlide->url));
            }
            
            if ($jsonSlide->urlTarget !== null) {
                $slide->setUrlTarget(new StringType((string)$jsonSlide->urlTarget));
            }
            
            if ($jsonSlide->images !== null) {
                $slide->setSlideImageCollection($this->_deserializeSlideImages($jsonSlide));
            }
            
            if ($jsonSlide->sortOrder !== null) {
                $slide->setSortOrder(new IdType((int)$jsonSlide->sortOrder));
            }
            
            $slideCollectionArray[] = $slide;
        }
        
        return MainFactory::create('SlideCollection', $slideCollectionArray);
    }
    
    
    /**
     * Deserialize slide images from slide JSON object.
     *
     * @param object $json
     *
     * @return SlideImageCollection
     */
    protected function _deserializeSlideImages($json)
    {
        $slideImageCollectionArray = [];
        
        foreach ($json->images as $jsonImage) {
            $slideImage = MainFactory::create('SlideImage');
            
            if ($jsonImage->id !== null) {
                $slideImage->setId(new IdType((int)$jsonImage->id));
            }
            
            if ($jsonImage->languageId !== null) {
                $slideImage->setLanguageId(new IdType((int)$jsonImage->languageId));
            }
            
            if ($jsonImage->breakpoint !== null) {
                $slideImage->setBreakpoint(new NonEmptyStringType((string)$jsonImage->breakpoint));
            }
            
            if ($jsonImage->image !== null) {
                $slideImage->setImage(new StringType((string)$jsonImage->image));
            }
            
            if ($jsonImage->areas !== null) {
                $slideImage->setSlideImageAreaCollection($this->_deserializeSlideImageAreas($jsonImage));
            }
            
            $slideImageCollectionArray[] = $slideImage;
        }
        
        return MainFactory::create('SlideImageCollection', $slideImageCollectionArray);
    }
    
    
    /**
     * Deserialize slide image areas from slide image JSON object.
     *
     * @param object $json
     *
     * @return SlideImageAreaCollection
     */
    protected function _deserializeSlideImageAreas($json)
    {
        $slideImageAreaCollectionArray = [];
        
        foreach ($json->areas as $jsonImageArea) {
            $slideImageArea = MainFactory::create('SlideImageArea');
            
            if ($jsonImageArea->id !== null) {
                $slideImageArea->setId(new IdType((int)$jsonImageArea->id));
            }
            
            if ($jsonImageArea->coordinates !== null) {
                $slideImageArea->setCoordinates(new NonEmptyStringType((string)$jsonImageArea->coordinates));
            }
            
            if ($jsonImageArea->linkTitle !== null) {
                $slideImageArea->setLinkTitle(new NonEmptyStringType((string)$jsonImageArea->linkTitle));
            }
            
            if ($jsonImageArea->linkUrl !== null) {
                $slideImageArea->setLinkUrl(new StringType((string)$jsonImageArea->linkUrl));
            }
            
            if ($jsonImageArea->linkTarget !== null) {
                $slideImageArea->setLinkTarget(new NonEmptyStringType((string)$jsonImageArea->linkTarget));
            }
            
            $slideImageAreaCollectionArray[] = $slideImageArea;
        }
        
        return MainFactory::create('SlideImageAreaCollection', $slideImageAreaCollectionArray);
    }
}
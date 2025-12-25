<?php
/* --------------------------------------------------------------
   SlidersDetailsController.inc.php 2022-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class SlidersDetailsController
 *
 * Bootstraps the teaser slider edit page.
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class SlidersDetailsController extends AdminHttpViewController
{
    /**
     * @var SliderReadService
     */
    protected $sliderReadService;
    
    /**
     * @var SliderWriteService
     */
    protected $sliderWriteService;
    
    /**
     * @var LanguageProvider
     */
    protected $languageProvider;
    
    /**
     * @var Slider
     */
    protected $slider;
    
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * @var UserConfigurationService
     */
    protected $userCfgService;
    
    
    /**
     * Initializes the controller.
     */
    public function init()
    {
        $this->sliderReadService   = StaticGXCoreLoader::getService('SliderRead');
        $this->sliderWriteService  = StaticGXCoreLoader::getService('SliderWrite');
        $this->userCfgService      = StaticGXCoreLoader::getService('UserConfiguration');
        $this->languageProvider    = MainFactory::create('LanguageProvider',
                                                         StaticGXCoreLoader::getDatabaseQueryBuilder());
        $this->languageTextManager = MainFactory::create('LanguageTextManager',
                                                         'sliders',
                                                         (int)($_SESSION['languages_id'] ?? null));
    }
    
    
    /**
     * Renders the sliders detail page.
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     *
     * @returns AdminLayoutHttpControllerResponse
     */
    public function actionDefault()
    {
        $title = new NonEmptyStringType($this->languageTextManager->get_text('PAGE_TITLE'));
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN . '/html/content/sliders/details.html'));
        
        $sliderId = (int)$this->_getQueryParameter('id');
        
        $this->slider = $this->_getSlider($sliderId);
        
        $data = MainFactory::create('KeyValueCollection',
                                    [
                                        'id'                 => $this->slider->getId(),
                                        'name'               => $this->slider->getName(),
                                        'speed'              => $this->slider->getSpeed(),
                                        'slides'             => $this->_getSliderSlidesData($this->slider),
                                        'empty_slide'        => $this->_getSlideTemplateData(),
                                        'breakpoints'        => $this->_getBreakpoints(),
                                        'user_configuration' => [
                                            'master_data_panel' => $this->userCfgService->getUserConfiguration(new IdType((int)$_SESSION['customer_id']),
                                                                                                               'slider-master_data_panel_collapse')
                                        ],
                                        'selections'         => [
                                            'thumbnail' => $this->sliderReadService->getAllSlideThumbnailImagesFromStorage(),
                                            'images'    => $this->sliderReadService->getAllSlideImagesFromStorage()
                                        ]
                                    ]);
        
        $assets = MainFactory::create('AssetCollection', [MainFactory::create('Asset', 'sliders.lang.inc.php')]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }
    
    
    /**
     * Saves a slider.
     *
     * @return JsonHttpControllerResponse.
     * @throws InvalidArgumentException
     *
     */
    public function actionSave()
    {
        $data = $this->_getPostDataCollection()->getArray();
        
        $this->slider = $this->_getSlider((int)$data['id']);
        
        $this->slider->setName(new StringType(stripslashes($data['name'])))->setSpeed(new DecimalType($data['speed']));
        
        $slides = [];
        
        foreach ($data['slides'] as $languageCode => $slideListData) {
            $sortOrder  = 1;
            $languageId = new IdType($this->languageProvider->getIdByCode(new LanguageCode(new StringType($languageCode))));
            
            foreach ($slideListData as $slideData) {
                $slide = $this->_createSlideObject($languageId, $slideData);
                $slide->setSortOrder(new IdType($sortOrder++));
                $slides[] = $slide;
            }
        }
        $this->slider->setSlideCollection(MainFactory::create('SlideCollection', $slides));
        
        $slider = $this->sliderWriteService->saveSlider($this->slider);
        
        $this->_addSuccessMessage();
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true, 'id' => $slider->getId()]);
    }
    
    
    /**
     * Fetches a slider.
     *
     * If no ID has been passed, a new slider will be created.
     *
     * @param int|null $id Slider ID (optional).
     *
     * @return Slider Fetched slider.
     */
    protected function _getSlider($id)
    {
        return $id ? $this->sliderReadService->getSliderById(new IdType($id)) : MainFactory::create('Slider');
    }
    
    
    /**
     * Returns the slides data.
     * If no ID is set, new slides for each language will be created.
     *
     * @param SliderInterface $slider Slider.
     *
     * @return array Slides data.
     */
    protected function _getSliderSlidesData(SliderInterface $slider)
    {
        if ($slider->getId() === 0) {
            $slides = [];
            
            foreach ($this->languageProvider->getActiveCodes()->getArray() as $languageCode) {
                $languageId = new IdType($this->languageProvider->getIdByCode($languageCode));
                
                $slide = MainFactory::create('Slide');
                $slide->setLanguageId($languageId)
                    ->setSlideImageCollection($this->_getDefaultSlideImageCollection($languageId))
                    ->setTitle(new StringType($this->languageTextManager->get_text('NEW_SLIDE') . ' 1'));
                
                $slides[] = $slide;
            }
            
            $this->slider->setSlideCollection(new SlideCollection($slides));
        }
        
        $slidesData = [];
        
        foreach ($this->slider->getSlideCollection()->getArray() as $slide) {
            $slidesData[] = $this->_getSlideData($slide);
        }
        
        return $slidesData;
    }
    
    
    /**
     * Returns the slide data.
     *
     * @param SlideInterface $slide Slide.
     *
     * @return array Extracted data from slide object.
     */
    protected function _getSlideData(SlideInterface $slide)
    {
        $sliderLanguageId = new IdType($slide->getLanguageId());
        
        $slideData = [
            'id'            => $slide->getId(),
            'language_code' => $this->languageProvider->getCodeById($sliderLanguageId)->asString(),
            'title'         => $slide->getTitle(),
            'alt_text'      => $slide->getAltText(),
            'link'          => $slide->getUrl(),
            'link_target'   => $slide->getUrlTarget(),
            'images'        => $this->_getSlideImagesData($slide->getSlideImageCollection(), $sliderLanguageId),
            'thumbnail'     => $slide->getThumbnail()
        ];
        
        return $slideData;
    }
    
    
    /**
     * Returns the slide images data (iterating over the slide image collection).
     *
     * @param SlideImageCollection $slideImageCollection Slide image collection.
     * @param IdType               $languageId           Language ID.
     *
     * @return array
     */
    protected function _getSlideImagesData(SlideImageCollection $slideImageCollection, IdType $languageId)
    {
        $slideImagesData = [];
        
        /** @var SlideImage $slideImage */
        foreach ($slideImageCollection->getArray() as $slideImage) {
            if ($slideImage->getLanguageId() === $languageId->asInt()) {
                $slideImagesData[] = $this->_getSlideImageData($slideImage);
            }
        }
        
        return $slideImagesData;
    }
    
    
    /**
     * Returns the slide image data.
     *
     * @param SlideImageInterface $slideImage Slide image.
     *
     * @return array Slide image data.
     */
    protected function _getSlideImageData(SlideImageInterface $slideImage)
    {
        $slideImageData = [
            'breakpoint'  => $slideImage->getBreakpoint(),
            'id'          => $slideImage->getId(),
            'image'       => $slideImage->getImage(),
            'language_id' => $slideImage->getLanguageId(),
            'areas'       => $this->_getSlideImageAreasData($slideImage->getSlideImageAreaCollection())
        ];
        
        return $slideImageData;
    }
    
    
    /**
     * Returns the slide image areas data (iterating over the slide image area collection).
     *
     * @param SlideImageAreaCollection $slideImageAreaCollection Slide image area collection.
     *
     * @return array
     */
    protected function _getSlideImageAreasData(SlideImageAreaCollection $slideImageAreaCollection)
    {
        $slideImageAreasData = [];
        
        /** @var SlideImageArea $slideImageArea */
        foreach ($slideImageAreaCollection->getArray() as $slideImageArea) {
            $slideImageAreasData[] = $this->_getSlideImageAreaData($slideImageArea);
        }
        
        return $slideImageAreasData;
    }
    
    
    /**
     * Returns the slide image area data.
     *
     * @param SlideImageAreaInterface $slideImageArea Slide image area.
     *
     * @return array Slide image area data.
     */
    protected function _getSlideImageAreaData(SlideImageAreaInterface $slideImageArea)
    {
        $slideImageAreaData = [
            'id'          => $slideImageArea->getId(),
            'coordinates' => $slideImageArea->getCoordinates(),
            'link_title'  => $slideImageArea->getLinkTitle(),
            'link_url'    => $slideImageArea->getLinkUrl(),
            'link_target' => $slideImageArea->getLinkTarget(),
        ];
        
        return $slideImageAreaData;
    }
    
    
    /**
     * Returns a slide object.
     *
     * @param IdType $languageId    Language ID.
     * @param array  $slideFormData Slide data.
     *
     * @return SlideInterface
     */
    protected function _createSlideObject(IdType $languageId, array $slideData)
    {
        $slide = MainFactory::create('Slide');
        
        if (array_key_exists('id', $slideData)) {
            $slide->setId(new IdType($slideData['id']));
        }
        
        $slide->setTitle(new StringType($slideData['title']))
            ->setAltText(new StringType($slideData['alt_text']))
            ->setLanguageId($languageId)
            ->setThumbnail(new StringType($slideData['thumbnail']))
            ->setUrl(new StringType($slideData['link']))
            ->setUrlTarget(new StringType($slideData['link_target']));
        
        $slideImages = [];
        
        foreach ($slideData['images'] as $slideImageData) {
            $slideImages[] = $this->_createSlideImageObject($languageId, $slideImageData);
        }
        
        $slide->setSlideImageCollection(MainFactory::create('SlideImageCollection', $slideImages));
        
        return $slide;
    }
    
    
    /**
     * Returns a slide image object.
     *
     * @param IdType $languageId         Language ID.
     * @param array  $slideImageFormData Slide image data.
     *
     * @return SlideImageInterface
     */
    protected function _createSlideImageObject(IdType $languageId, array $slideImageFormData)
    {
        $slideImage = MainFactory::create('SlideImage');
        
        if (array_key_exists('id', $slideImageFormData)) {
            $slideImage->setId(new IdType($slideImageFormData['id']));
        }
        
        $slideImage->setLanguageId($languageId)
            ->setBreakpoint(new NonEmptyStringType($slideImageFormData['breakpoint']))
            ->setImage(new StringType($slideImageFormData['image']));
        
        $slideImageAreas = [];
        
        if (array_key_exists('areas', $slideImageFormData)) {
            foreach ($slideImageFormData['areas'] as $slideImageAreaData) {
                $slideImageAreas[] = $this->_createSlideImageAreaObject($slideImageAreaData);
            }
        }
        
        $slideImage->setSlideImageAreaCollection(MainFactory::create('SlideImageAreaCollection', $slideImageAreas));
        
        return $slideImage;
    }
    
    
    /**
     * Returns a slide image area object.
     *
     * @param array $slideImageAreaFormData Slide image area data.
     *
     * @return SlideImageAreaInterface
     */
    protected function _createSlideImageAreaObject(array $slideImageAreaFormData)
    {
        $slideImageArea = MainFactory::create('SlideImageArea');
        
        $slideImageArea->setId(new IdType($slideImageAreaFormData['id']))
            ->setCoordinates(new NonEmptyStringType((string)$slideImageAreaFormData['coordinates']))
            ->setLinkTitle(new StringType((string)$slideImageAreaFormData['linkTitle']))
            ->setLinkUrl(new StringType((string)$slideImageAreaFormData['linkUrl']))
            ->setLinkTarget(new NonEmptyStringType((string)$slideImageAreaFormData['linkTarget']));
        
        return $slideImageArea;
    }
    
    
    /**
     * Returns a default slide image collection.
     *
     * @param IdType $languageId Language ID.
     *
     * @return SlideImageCollection
     */
    protected function _getDefaultSlideImageCollection(IdType $languageId)
    {
        $slideImages = [];
        
        foreach ($this->_getBreakpoints() as $breakpoint) {
            $slideImages[] = MainFactory::create('SlideImage')
                ->setLanguageId($languageId)
                ->setBreakpoint(new NonEmptyStringType($breakpoint));
        }
        
        return MainFactory::create('SlideImageCollection', $slideImages);
    }
    
    
    /**
     * Returns all available breakpoints.
     *
     * @return array
     */
    protected function _getBreakpoints()
    {
        return ['xs', 'sm', 'md', 'lg'];
    }
    
    
    /**
     * Returns the slide template data.
     *
     * @return array Slide template data.
     */
    protected function _getSlideTemplateData()
    {
        $languageId = new IdType((int)$_SESSION['languages_id']);
        
        $slide = MainFactory::create('Slide');
        
        $slide->setLanguageId($languageId)
            ->setSlideImageCollection($this->_getDefaultSlideImageCollection($languageId));
        
        $slideData = $this->_getSlideData($slide);
        
        return $slideData;
    }
    
    
    /**
     * Adds a new success message.
     */
    protected function _addSuccessMessage()
    {
        $languageCode = $this->languageProvider->getCodeById(new IdType((int)$_SESSION['languages_id']));
        
        $messageSource      = 'adminAction';
        $messageIdentifier  = uniqid('adminActionSuccess-', true);
        $messageStatus      = 'new';
        $messageType        = 'success';
        $messageVisibility  = 'removable';
        $messageButtonLink  = '';
        $messageText        = $this->languageTextManager->get_text('GM_LANGUAGE_CONFIGURATION_SUCCESS', 'languages');
        $messageHeadLine    = $this->languageTextManager->get_text('success', 'messages');
        $messageButtonLabel = '';
        
        $message = MainFactory::create('InfoBoxMessage');
        
        $message->setSource(new StringType($messageSource))
            ->setIdentifier(new StringType($messageIdentifier))
            ->setStatus(new StringType($messageStatus))
            ->setType(new StringType($messageType))
            ->setVisibility(new StringType($messageVisibility))
            ->setButtonLink(new StringType($messageButtonLink))
            ->setCustomerId(new IdType((int)$_SESSION['customer_id']))
            ->setMessage(new StringType($messageText), $languageCode)
            ->setHeadLine(new StringType($messageHeadLine), $languageCode)
            ->setButtonLabel(new StringType($messageButtonLabel), $languageCode);
        
        /** @var InfoBoxService $service */
        $service = StaticGXCoreLoader::getService('InfoBox');
        $service->addMessage($message);
    }
}
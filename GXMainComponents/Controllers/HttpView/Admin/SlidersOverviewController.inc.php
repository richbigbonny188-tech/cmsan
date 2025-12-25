<?php

/* --------------------------------------------------------------
   SlidersOverviewController.inc.php 2016-09-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class SliderOverviewController
 *
 * Bootstraps the teaser slider overview page.
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class SlidersOverviewController extends AdminHttpViewController
{
    /**
     * @var SliderReadService
     */
    protected $sliderReadService;
    
    
    /**
     * Initialize Controller
     */
    public function init()
    {
        $this->sliderReadService = StaticGXCoreLoader::getService('SliderRead');
    }
    
    
    /**
     * Renders the main sliders overview page.
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function actionDefault()
    {
        $languageTextManager = MainFactory::create('LanguageTextManager', 'sliders', $_SESSION['languages_id']);
        $title               = new NonEmptyStringType($languageTextManager->get_text('PAGE_TITLE'));
        
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_ADMIN . '/html/content/sliders/overview.html'));
        
        $slidersArray = [];
        
        /** @var Slider $slider */
        foreach ($this->sliderReadService->getAllSlider() as $slider) {
            $slidersArray[] = [
                'name'       => $slider->getName(),
                'start_page' => $slider->showOnStartPage(),
                'slider_id'  => $slider->getId()
            ];
        }
        
        $data   = MainFactory::create('KeyValueCollection', ['sliders' => $slidersArray]);
        $assets = MainFactory::create('AssetCollection', [MainFactory::create('Asset', 'sliders.lang.inc.php')]);
        
        return MainFactory::create('AdminLayoutHttpControllerResponse', $title, $template, $data, $assets);
    }
}
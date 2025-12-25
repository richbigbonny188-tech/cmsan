<?php
/* --------------------------------------------------------------
   ProductsSwiperThemeContentView.inc.php 2022-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductsSwiperThemeContentView
 */
class ProductsSwiperThemeContentView extends ThemeContentView
{
    protected $swiperDefaults = [];
    
    protected $customParameters = [];
    
    protected $slidesPerViewArray = [
        1  => 12,
        2  => 6,
        3  => 4,
        4  => 3,
        6  => 2,
        12 => 1,
    ];
    
    protected $internalDefaults = [
        'engineAttrEnabled'  => 'data-gambio-widget',
        'engineAttrDisabled' => 'data-_gambio-widget',
    ];
    
    
    public function __construct($customParameters)
    {
        parent::__construct();
        
        $this->customParameters = $customParameters;
    }
    
    
    public function prepare_data()
    {
        $this->setDefaults();
        
        $swiperOptions = array_merge($this->swiperDefaults, $this->customParameters);
        
        // Get the aditional widgets as string.
        $widgets                     = ($swiperOptions['hoverable']) ? 'product_hover' : '';
        $swiperOptions['engineAttr'] = ($swiperOptions['startWidget']) ? $this->internalDefaults['engineAttrEnabled'] : $this->internalDefaults['engineAttrDisabled'];
        $swiperOptions['widgets']    = ($widgets !== '') ? $swiperOptions['engineAttr'] . '="' . $widgets . '"' : '';
        
        // Add the button classes
        $swiperOptions['next']       = 'js-' . $swiperOptions['id'] . '-button-next';
        $swiperOptions['prev']       = 'js-' . $swiperOptions['id'] . '-button-prev';
        $swiperOptions['pagination'] = 'js-' . $swiperOptions['id'] . '-pagination';
        
        // Generate an options string for the swiper
        $swiperOptions['swiperOptions']['nextButton'] = '.' . $swiperOptions['next'];
        $swiperOptions['swiperOptions']['prevButton'] = '.' . $swiperOptions['prev'];
        $swiperOptions['swiperOptions']['pagination'] = '.' . $swiperOptions['pagination'];
        
        $swiperOptions['configuration'] = '';
        $swiperOptions['configuration'] .= ($swiperOptions['target']) ? 'data-swiper-target="'
                                                                        . $swiperOptions['target'] . '" ' : '';
        $swiperOptions['configuration'] .= ($swiperOptions['controls']) ? 'data-swiper-controls="'
                                                                          . $swiperOptions['controls'] . '" ' : '';
        $swiperOptions['configuration'] .= 'data-swiper-auto-off="' . $swiperOptions['autoOff']
                                           . '" data-swiper-slider-options="' . str_replace('"',
                                                                                            '&quot;',
                                                                                            json_encode($swiperOptions['swiperOptions']))
                                           . '" ';
        
        // Add misc options
        $swiperOptions['maxHeight'] = ($swiperOptions['maxHeight']) ? 'style="height: ' . $swiperOptions['maxHeight']
                                                                      . ' px;"' : '';
        
        // IMAGE SWIPER FALLBACK
        $swiperOptions['popup']  = '';
        $swiperOptions['images'] = [];
        
        // Shorten product descriptions
        foreach ($swiperOptions['products'] AS $index => $product) {
            $swiperOptions['products'][$index]['showManufacturerImages']         = $this->customParameters['showManufacturerImages'] ?? null;
            $swiperOptions['products'][$index]['showProductRibbons']             = $this->customParameters['showProductRibbons'] ?? null;
            $swiperOptions['products'][$index]['DELIVERY']                       = $product['PRODUCTS_SHIPPING_NAME'];
            
            if (!empty($product['PRODUCTS_SHIPPING_RANGE'])) {
    
                $swiperOptions['products'][$index]['SHIPPING_RANGE'] = $product['PRODUCTS_SHIPPING_RANGE'];
            }
            
            $swiperOptions['products'][$index]['SHORTENED_PRODUCTS_DESCRIPTION'] = $product['PRODUCTS_SHORT_DESCRIPTION'];
            
            if (!empty($product['PRODUCTS_META_DESCRIPTION'])
                && preg_match('/^.{1,77}\b/s', $product['PRODUCTS_META_DESCRIPTION'], $shortened)) {
                $extend = (strlen($product['PRODUCTS_META_DESCRIPTION']) > 77) ? '...' : '';
                
                $swiperOptions['products'][$index]['SHORTENED_META_DESCRIPTION'] = str_replace('"',
                                                                                               '&quot;',
                                                                                               $shortened[0]) . $extend;
            } else {
                $swiperOptions['products'][$index]['SHORTENED_META_DESCRIPTION'] = $product['PRODUCTS_NAME'];
            }
            
            mb_regex_encoding('UTF-8');
            mb_regex_set_options('m');
            $pattern = '^.{1,' . $swiperOptions['truncate'] . '}\b[^\s]+';
            
            if (mb_ereg($pattern, html_entity_decode_wrapper($product['PRODUCTS_NAME']), $shortened)) {
                $extend                                        = (strlen($product['PRODUCTS_NAME'])
                                                                  > $swiperOptions['truncate']) ? '...' : '';
                $swiperOptions['products'][$index]['HEADLINE'] = htmlspecialchars_wrapper($shortened[0]) . $extend;
            } else {
                $swiperOptions['products'][$index]['HEADLINE'] = $product['PRODUCTS_NAME'];
            }
            
            $swiperOptions['products'][$index]['PRODUCTS_IMAGE']     = (!empty($product['PRODUCTS_IMAGE'])) ? $product['PRODUCTS_IMAGE'] : '';
            $swiperOptions['products'][$index]['PRODUCTS_IMAGE_ALT'] = (!empty($product['PRODUCTS_IMAGE_ALT'])) ? $product['PRODUCTS_IMAGE_ALT'] : $product['PRODUCTS_NAME'];
            $swiperOptions['products'][$index]['PRODUCTS_IMAGE_ALT'] = str_replace('"',
                                                                                   '&quot;',
                                                                                   $swiperOptions['products'][$index]['PRODUCTS_IMAGE_ALT']);
            $swiperOptions['products'][$index]['INDEX']              = $swiperOptions['id'] . '-'
                                                                       . $product['PRODUCTS_ID'];
            $swiperOptions['products'][$index]['PRODUCTS_VPE']       = $product['PRODUCTS_VPE'];
        }
        
        $swiperOptions['_TYPE'] = 'PRODUCTS';
        
        $this->set_content_template($swiperOptions['template']);
        $this->set_content_data('SWIPER_DATA', $swiperOptions);
    }
    
    
    protected function setDefaults()
    {
        $key             = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-listing-col-xs');
        $xsSlidesPerView = 2;
        if (array_key_exists($key, $this->slidesPerViewArray)) {
            $xsSlidesPerView = $this->slidesPerViewArray[$key];
        }
        
        $key             = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-listing-col-sm');
        $smSlidesPerView = 3;
        if (array_key_exists($key, $this->slidesPerViewArray)) {
            $smSlidesPerView = $this->slidesPerViewArray[$key];
        }
        
        $key             = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-listing-col-md');
        $mdSlidesPerView = 2;
        if (array_key_exists($key, $this->slidesPerViewArray)) {
            $mdSlidesPerView = $this->slidesPerViewArray[$key];
        }
        
        $this->swiperDefaults = [
            'products'        => [],
            'truncate'        => 80,
            'id'              => 'swiper_' . mt_rand(),
            'template'        => 'product_listing_swiper.html',
            'productTemplate' => StaticGXCoreLoader::getThemeControl()->getThemeHtmlPath()
                                 . 'product_listing_product.html',
            'startWidget'     => true,
            'hoverable'       => false,
            'itemProperties'  => '',
            'target'          => null,
            'controls'        => null,
            'maxHeight'       => null,
            'autoOff'         => 'true',
            'swiperOptions'   => [
                'spaceBetween'  => 0,
                'loop'          => true,
                'slidesPerView' => 4,
                'autoplay'      => null,
                'breakpoints'   => [
                    [
                        'breakpoint'        => 40,
                        'usePreviewBullets' => true,
                        'slidesPerView'     => $xsSlidesPerView,
                        'centeredSlides'    => true
                    ],
                    [
                        'breakpoint'        => 60,
                        'usePreviewBullets' => true,
                        'slidesPerView'     => $smSlidesPerView
                    ],
                    [
                        'breakpoint'        => 80,
                        'usePreviewBullets' => true,
                        'slidesPerView'     => $mdSlidesPerView
                    ],
                ],
            ],
        ];
        
        if (empty($this->customParameters['fullscreenPage'])) {
            $key = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-listing-col-lg');
            
            $slidesPerView = 4;
            if (array_key_exists($key, $this->slidesPerViewArray)) {
                $slidesPerView = $this->slidesPerViewArray[$key];
            }
            
            $this->swiperDefaults['swiperOptions']['breakpoints'][] = [
                'breakpoint'        => 100,
                'usePreviewBullets' => true,
                'slidesPerView'     => $slidesPerView
            ];
        }
    }
}

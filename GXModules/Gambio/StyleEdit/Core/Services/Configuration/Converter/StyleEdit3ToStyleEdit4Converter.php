<?php
/* --------------------------------------------------------------
  StyleEdit3ToStyleEdit4Converter.php 2021-11-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Converter;

use Gambio\StyleEdit\Core\Services\Configuration\Converter\Interfaces\StyleEdit3ToStyleEdit4ConverterInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Configuration;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\ConfigurationInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\OptionInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Option;
use Gambio\StyleEdit\Core\Services\Configuration\Factories\ConfigurationFactory;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;
use stdClass;

/**
 * Class StyleEdit3ToStyleEdit4Converter
 */
class StyleEdit3ToStyleEdit4Converter implements StyleEdit3ToStyleEdit4ConverterInterface
{
    protected const CHANGED_VARIABLE_NAMES = [
        'body-bg'                         => 'gx-body-bg-color',
        'gx-wrapper-bg'                   => 'gx-wrapper-bg-color',
        'gx-table-bg'                     => 'gx-table-bg-color',
        'navbar-default-bg'               => 'gx-cat-navbar-bg-color',
        'gx-megadropdown-bg'              => 'gx-megadropdown-bg-color',
        'gx-enter-category-bg'            => 'gx-enter-category-bg-color',
        'gx-cat-left-bg'                  => 'gx-cat-left-bg-color',
        'gx-cat-left-bg-hover'            => 'gx-cat-left-bg-hover-color',
        'gx-cat-left-bg-active'           => 'gx-cat-left-bg-active-color',
        'gx-cat-left-bg-open'             => 'gx-cat-left-bg-open-color',
        'gx-navbar-topbar-bg'             => 'gx-navbar-topbar-bg-color',
        'gx-navbar-topbar-link-bg'        => 'gx-navbar-topbar-link-bg-color',
        'gx-navbar-topbar-link-hover-bg'  => 'gx-navbar-topbar-link-hover-bg-color',
        'gx-navbar-topbar-link-active-bg' => 'gx-navbar-topbar-link-active-bg-color',
        'gx-dropdown-bg'                  => 'gx-dropdown-bg-color',
        'gx-dropdown-footer-bg'           => 'gx-dropdown-footer-bg-color',
        'gx-dropdown-hover-bg'            => 'gx-dropdown-hover-bg-color',
        'gx-footer-header-bg'             => 'gx-footer-header-bg-color',
        'gx-footer-bg'                    => 'gx-footer-bg-color',
        'gx-product-info-img-bg'          => 'gx-product-info-img-bg-color',
        'gx-product-bg'                   => 'gx-product-bg-color',
        'gx-gallery-bg'                   => 'gx-gallery-bg-color',
        'gx-product-info-details-bg'      => 'gx-product-info-details-bg-color',
        'gx-tab-bg'                       => 'gx-tab-bg-color',
        'gx-tab-bg-active'                => 'gx-tab-bg-active-color',
        'gx-total-box-top-bg'             => 'gx-total-box-top-bg-color',
        'gx-total-box-bottom-bg'          => 'gx-total-box-bottom-bg-color',
        'gx-checkout-table-bg'            => 'gx-checkout-table-bg-color',
        'gx-list-group-item-bg'           => 'gx-list-group-item-bg-color',
        'gx-list-group-item-bg-active'    => 'gx-list-group-item-bg-active-color',
        'gx-checkout-form-bg'             => 'gx-checkout-form-bg-color',
        'gx-checkbox-bg'                  => 'gx-checkbox-bg-color',
        'gx-checkbox-active-bg'           => 'gx-checkbox-active-bg-color',
        'gx-panel-header-bg'              => 'gx-panel-header-bg-color',
        'gx-panel-bg'                     => 'gx-panel-bg-color',
        'gx-header-bg'                    => 'gx-header-bg-color',
        'gx-header-bg-mobile'             => 'gx-header-bg-mobile-color',
        'gx-header-bg-sticky'             => 'gx-header-bg-sticky-color',
        'gx-modal-bg'                     => 'gx-modal-bg-color',
        'gx-alert-danger-bg'              => 'gx-alert-danger-bg-color',
        'gx-alert-success-bg'             => 'gx-alert-success-bg-color',
        'gx-alert-warning-bg'             => 'gx-alert-warning-bg-color',
        'gx-alert-info-bg'                => 'gx-alert-info-bg-color',
        'gx-btn-primary-bg'               => 'gx-btn-primary-bg-color',
        'gx-btn-default-bg'               => 'gx-btn-default-bg-color',
        'gx-btn-calltoaction-bg'          => 'gx-btn-calltoaction-bg-color',
        'gx-cart-basket-sticky-color'     => 'gx-cart-sticky-icon-color',
        'gx-cart-basket-mobile-color'     => 'gx-cart-mobile-icon-color',
        'grey'                            => 'gray',
        'gx-googleplus'                   => ['#DD4B39'],
        'gx-cart-basket-color'            => 'gx-cart-icon-color',
    ];
    
    protected const CHANGED_TYPES = [
        'switch' => 'checkbox',
        'text'   => 'textbox',
        'color'  => 'colorpicker',
        'select' => 'dropdownselect',
        'image'  => 'imageupload',
    ];
    
    protected const CHANGED_GROUPS = [
        'navbar-default-link-hover-bg-color'  => 'template',
        'navbar-default-link-active-bg-color' => 'template',
        'gx-custom-bg-color-hover'            => 'template',
        'gx-custom-bg-color-active'           => 'template',
        'gx-cat-navbar-bg-color'              => 'template',
    ];
    
    
    /**
     * @param StyleEdit3ConfigurationInterface $styleEdit3Configuration
     *
     * @return ConfigurationInterface
     */
    public function convert(StyleEdit3ConfigurationInterface $styleEdit3Configuration): ConfigurationInterface
    {
        $settings = $styleEdit3Configuration->settings();
        $options  = [];
        
        if (count($settings)) {
            
            foreach ($settings as $setting) {
                
                foreach ($this->createOptions($setting) as $option) {
                    if (!empty($option->value()) && $option->type() === 'imageupload') {
                        if ($styleEdit3Configuration->type() === 'Theme') {
                            $imageRelativePath = 'themes/' . $styleEdit3Configuration->name() . '/images/custom/' . $option->value();
                        } else {
                            $imageRelativePath = 'templates/' . $styleEdit3Configuration->name() . '/assets/images/custom/' . $option->value();
                        }
                        
                        $imageAbsolutePath = DIR_FS_CATALOG . $imageRelativePath;

                        if (!file_exists($imageAbsolutePath)) {
                            continue;
                        }

                        $parsedOption = $option->jsonSerialize();
                        $parsedOption->value = (ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER). DIR_WS_CATALOG . $imageRelativePath;
                        $parsedOption->type = 'url';
                        $option = $this->createOption($parsedOption);

                        $options[] = $option;

                        $parsedOption = $option->jsonSerialize();
                        $parsedOption->name = substr($parsedOption->name, 0, -3) . 'enabled';
                        $parsedOption->value = true;
                        $parsedOption->type = 'checkbox';
                        $option = $this->createOption($parsedOption);
                    }

                    $options[] = $option;
                }
            }
        }
        
        $customCss = $styleEdit3Configuration->customCss();
        $options[] = $this->createCustomCssOption($customCss);
        
        return new Configuration($options);
    }
    
    
    /**
     * @param stdClass $setting
     *
     * @return OptionInterface[]
     */
    protected function createOptions(stdClass $setting): array
    {
        $result = [];
        
        if (isset($setting->name) && $setting->name === 'boxes') {
            
            return $this->createBoxesOptions($setting);
        }
        
        if (isset($setting->entries) && is_array($setting->entries)) {
            
            foreach ($setting->entries as $option) {
                
                if ($this->optionIsValid($option)) {
                    
                    $options = $this->renameOptionValues($option);
                    
                    foreach ($options as $newOption) {
                        
                        $result[] = $this->createOption($newOption);
                    }
                }
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param stdClass $setting
     *
     * @return OptionInterface[]
     */
    protected function createBoxesOptions(stdClass $setting): array
    {
        $result = [];
        
        foreach ($setting->entries as $menuBox) {
            
            [$name, $type, $boxActive, $position] = array_values((array)$menuBox);
            
            $option                  = new stdClass;
            $option->name            = $name;
            $option->group           = 'box';
            $option->type            = 'ordainedlistitem';
            $option->value           = new stdClass;
            $option->value->status   = $boxActive;
            $option->value->position = $position;
            
            if ($this->optionIsValid($option)) {
                
                $result[] = $this->createOption($option);
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param stdClass $option
     *
     * @return bool
     */
    protected function optionIsValid(stdClass $option): bool
    {
        foreach (ConfigurationFactory::REQUIRED_OPTION_PROPERTIES as $property) {
            
            if (!property_exists($option, $property)) {
                
                return false;
            }
        }
        
        return true;
    }
    
    
    /**
     * @param string $customCss
     *
     * @return OptionInterface
     */
    protected function createCustomCssOption(string $customCss): OptionInterface
    {
        $option = new stdClass;
        $option->name = 'custom-scss-css-style';
        $option->value = $customCss;
        $option->type = 'code-editor';
        $option->group = 'custom';
        $optionArray = $this->renameOptionValues($option);
        return $this->createOption(end($optionArray));
    }
    
    
    /**
     * Some variable names where changed for StyleEdit4
     * the configuration names from StyleEdit3 are renamed here.
     *
     * @param stdClass $option
     *
     * @return stdClass[]
     */
    protected function renameOptionValues(stdClass $option): array
    {
        if (array_key_exists($option->name, self::CHANGED_VARIABLE_NAMES)) {
            
            $option->name = self::CHANGED_VARIABLE_NAMES[$option->name];
        }
        
        // In SE3, font families had the type "text" but in SE4 we cannot change them to "textbox".
        // So we need to check if the option name contains "font-family" in its name and change it to "fontfamily"
        if (false !== strpos(strtolower($option->name), "font-family")) {
            $option->type = 'fontfamily';
        }
        
        if (array_key_exists($option->type, self::CHANGED_TYPES)) {
            
            $option->type = self::CHANGED_TYPES[$option->type];
        }
        
        // changing old variables that are used as values for another var
        foreach (self::CHANGED_VARIABLE_NAMES as $orgVarName => $newVarName) {

            $pattern = '/' . preg_quote($orgVarName, '/') . '($|\,|\)|\;)/';
            if (is_string($option->value) && preg_match($pattern, $option->value)) {
                if (is_array($newVarName)) {
                    $option->value = str_replace('$' . $orgVarName, implode('', $newVarName), $option->value);
                } else {
                    $option->value = str_replace($orgVarName, $newVarName, $option->value);
                }
            }
        }

        if (array_key_exists($option->name, self::CHANGED_GROUPS)) {

            $option->group = self::CHANGED_GROUPS[$option->name];
        }

        return [$option];
    }
    
    
    /**
     * @param stdClass $option
     *
     * @return OptionInterface
     */
    protected function createOption(stdClass $option): OptionInterface
    {
        return new Option($option->name, $option->group, $option->type, $option->value);
    }
}

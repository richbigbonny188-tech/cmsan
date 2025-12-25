<?php
/* --------------------------------------------------------------
  ConvertSettingsToDefaultValueThemeExtensions.php 2019-10-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Helpers;

use InvalidArgumentException;
use RuntimeException;
use stdClass;

/**
 * Class ConvertSettingsToDefaultValueThemeExtensions
 */
class ConvertSettingsToDefaultValueThemeExtensions
{
    /**
     * @var stdClass
     */
    protected $themeJson;
    
    /**
     * @var array|stdClass[]
     */
    protected $settings;
    
    /**
     * @var array
     */
    protected $overwrites = [];
    
    /**
     * @var string[]
     */
    protected const OPTION_GROUPS = [
        'background',
        'background-gradient',
        'background-image',
        'border',
        'font',
        'margin',
        'padding',
    ];
    
    protected const GROUP_PROPERTIES = [
        'background-gradient' => [
            'enabled'      => '-enabled',
            'color1'       => '-color1',
            'color2'       => '-color2',
            'gradientType' => '-type',
            'angle'        => '-angle',
        ],
        'background-image'    => [
            'enabled'    => '-enabled',
            'url'        => '-url',
            'position'   => '-position',
            'repeat'     => '-repeat',
            'size'       => '-size',
            'attachment' => '-attachment',
        ],
        'border'              => [
            'top'    => '-top',
            'right'  => '-right',
            'bottom' => '-bottom',
            'left'   => '-left',
            'style'  => '-style',
            'color'  => '-color',
        ],
        'font'                => [
            'family'                  => '-family',
            'color'                   => '-color',
            'size'                    => '-size',
            'style'                   => '-style',
            'textAlign'               => '-text-align',
            'textDecorationUnderline' => '-text-decoration-underline',
            'textTransformUppercase'  => '-text-transform-uppercase',
            'enableCustomization'     => '-enable-customization',
        ],
        'margin'              => [
            'top'    => '-top',
            'right'  => '-right',
            'bottom' => '-bottom',
            'left'   => '-left',
        ],
        'padding'             => [
            'top'    => '-top',
            'right'  => '-right',
            'bottom' => '-bottom',
            'left'   => '-left',
        ]
    ];
    
    
    /**
     * ConvertSettingsToDefaultValueThemeExtensions constructor.
     *
     * @param stdClass   $themeJson
     * @param stdClass[] $settings
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     *
     */
    public function __construct(stdClass $themeJson, array $settings)
    {
        $this->themeJson = $themeJson;
        $this->mapSettingsArray($settings);
    }
    
    
    /**
     * @param array $settings
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function mapSettingsArray(array $settings): void
    {
        $result = [];
        
        if (count($settings)) {
            
            foreach ($settings as $option) {
                
                if (!is_object($option) || get_class($option) !== 'stdClass') {
                    
                    throw new InvalidArgumentException('The settings array can only contain stdClass options');
                }
                
                if (!property_exists($option, 'name')) {
                    
                    throw new RuntimeException('Invalid settings supplied. Every Option needs tp have a name');
                }
                
                $result[$option->name] = $option;
            }
        }
        $this->settings = $result;
    }
    
    
    /**
     * @return stdClass[]
     */
    public function convert(): array
    {
        if (isset($this->themeJson->config->basics)) {
            
            $this->loopThroughCategories($this->themeJson->config->basics);
        }
        
        if (isset($this->themeJson->config->areas)) {
            
            $this->loopThroughCategories($this->themeJson->config->areas);
        }
        
        return $this->overwrites;
    }
    
    
    /**
     * @param stdClass $category
     */
    protected function loopThroughCategories(stdClass $category): void
    {
        if (isset($category->fieldsets)) {
            
            foreach ($category->fieldsets as $fieldset) {
                
                if (isset($fieldset->options)) {
                    
                    foreach ($fieldset->options as $option) {
                        
                        $this->setOptionValues($option);
                    }
                }
            }
        } elseif (isset($category->categories)) {
            
            foreach ($category->categories as $subCategory) {
                
                $this->loopThroughCategories($subCategory);
            }
        }
    }
    
    
    /**
     * @param stdClass $option
     */
    protected function setOptionValues(stdClass $option): void
    {
        $id   = $option->id;
        $type = $option->type;
        
        if (in_array($type, self::OPTION_GROUPS, true)) {
            
            $default = $this->getOptionValuesGroup($id, $type);
            
            if ($default !== null && (array)$default !== (array)$option->default) {
                
                $this->overwrites[$id] = $default;
            }
        } elseif (array_key_exists($id, $this->settings)) {
            
            $default = $this->getOptionValue($id);
            if ($default !== null && $default !== $option->default) {
                
                $this->overwrites[$id] = $default;
            }
        }
    }
    
    
    /**
     * @param string $id
     * @param string $type
     *
     * @return stdClass|null
     */
    protected function getOptionValuesGroup(string $id, string $type): ?stdClass
    {
        //  background groups are the only groups that contain other groups.
        if ($type === 'background') {
            
            $color    = $this->getOptionValue($id . '-color');
            $image    = $this->getOptionValuesGroup($id . '-image', 'background-image');
            $gradient = $this->getOptionValuesGroup($id . '-gradient', 'background-gradient');
            
            $result = new stdClass;
            
            if ($color !== null) {
                $result->color = $color;
            }
            
            if ($image !== null && count((array)$image) !== 0) {
                $result->image = $image;
            }
            
            if ($gradient !== null && count((array)$gradient) !== 0) {
                $result->gradient = $gradient;
            }
            
            return count((array)$result) === 0 ? null : $result;
        }
        
        if (!array_key_exists($type, self::GROUP_PROPERTIES)) {
            
            return null;
        }
        
        $result = new stdClass;
        
        foreach (self::GROUP_PROPERTIES[$type] as $property => $propertyId) {
            
            $storedValue = $this->getOptionValue($id . $propertyId);
            if ($storedValue !== null) {
                
                $result->$property = $storedValue;
            }
        }
        
        return count((array)$result) === 0 ? null : $result;
    }
    
    
    /**
     * @param string $id
     *
     * @return mixed
     */
    protected function getOptionValue(string $id)
    {
        return array_key_exists($id, $this->settings) ? $this->settings[$id]->value : null;
    }
    
}
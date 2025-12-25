<?php
/* --------------------------------------------------------------
  ConfigurationFactory.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Factories;

use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\ConfigurationInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\OptionInterface;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Option;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Configuration;
use Gambio\StyleEdit\Core\Services\Configuration\Factories\Interfaces\ConfigurationFactoryInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;
use InvalidArgumentException;
use RuntimeException;
use stdClass;

/**
 * Class ConfigurationFactory
 */
class ConfigurationFactory implements ConfigurationFactoryInterface
{
    /**
     * @var string[]
     */
    public const REQUIRED_OPTION_PROPERTIES = [
        'name',
        'group',
        'type',
        'value'
    ];
    
    
    /**
     * @param array $settingsJson
     *
     * @return ConfigurationInterface
     */
    public function createSettings(array $settingsJson): ConfigurationInterface
    {
        $options = [];
        
        foreach ($settingsJson as $option) {
            
            foreach (self::REQUIRED_OPTION_PROPERTIES as $requirement) {
                
                if (!property_exists($option, $requirement)) {
                    
                    throw new InvalidArgumentException('One of the options in the settings.json is missing a required property ('
                                                       . $requirement . ')');
                }
            }
            
            $options[] = new Option($option->name, $option->group, $option->type, $option->value);
        }
        
        return new Configuration($options);
    }
}
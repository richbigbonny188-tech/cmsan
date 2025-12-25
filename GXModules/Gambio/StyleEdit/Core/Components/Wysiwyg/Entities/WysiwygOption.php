<?php
/* --------------------------------------------------------------
  WysiwygOption.php 2019-08-02
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\Wysiwyg\Entities;

use ContentNotFoundException;
use ContentReadServiceInterface;
use ContentText;
use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;
use Gambio\StyleEdit\Core\Repositories\Entities\Configuration;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\Components\ContentManager\Entities\AbstractContentManagerOption;
use InfoElementContent;
use ReflectionException;

/**
 * Class WysiwygOption
 */
class WysiwygOption extends AbstractContentManagerOption
{
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function parseValue($value)
    {
        $result = null;
        
        if ($this->translatable()) {
            
            $result = [];
            
            foreach ($value as $language => $optionValue) {
                
                $result[$language] = WysiwygOptionValue::createFromJsonObject($optionValue);
            }
        } else {
            
            $result = WysiwygOptionValue::createFromJsonObject($value);
        }
        
        return $result;
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'wysiwyg';
    }
}
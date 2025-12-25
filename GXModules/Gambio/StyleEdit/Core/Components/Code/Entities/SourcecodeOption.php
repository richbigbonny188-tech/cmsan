<?php
/* --------------------------------------------------------------
  CodeOption.php 2019-08-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\Code\Entities;

use Gambio\StyleEdit\Core\Components\ContentManager\Entities\AbstractContentManagerOption;

/**
 * Class CodeOption
 */
class SourcecodeOption extends AbstractContentManagerOption
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
                
                $result[$language] = SourcecodeOptionValue::createFromJsonObject($optionValue);
            }
        } else {
            
            $result = SourcecodeOptionValue::createFromJsonObject($value);
        }
        
        return $result;
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'sourcecode';
    }
}
<?php
/*--------------------------------------------------------------
   AbstractAssociativeCollection.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Collections;

/**
 * Class AbstractAssociativeCollection
 * @package Gambio\Admin\Modules\SetupWizard\Collections
 */
abstract class AbstractAssociativeCollection extends AbstractCollection
{
    /**
     * @return int
     */
    public function key(): int
    {
        return array_keys($this->values)[$this->position];
    }
    
    
    /**
     * @inheritDoc
     */
    public function currentValue()
    {
        $key = array_keys($this->values)[$this->position];
        
        return $this->values[$key];
    }
    
    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        $keys = array_keys($this->values);
        
        if (isset($keys[$this->position]) === false) {
            
            return false;
        }
        
        $key = $keys[$this->position];
        
        return isset($this->values[$key]);
    }
}
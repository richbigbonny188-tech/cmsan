<?php
/* --------------------------------------------------------------
   SettingsController.php 2019-04-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Components\Option\Entities;

use EditableCollection;
use Exception;
use Gambio\StyleEdit\Core\Options\Entities\AbstractOption;
use Gambio\StyleEdit\Core\Options\Entities\CollectionTrait;
use JsonSerializable;

/**
 * Class OptionCollection
 * @package Gambio\StyleEdit\Core
 */
class OptionCollection extends EditableCollection implements JsonSerializable
{
    use CollectionTrait;
    
    
    /**
     * @param mixed $value
     *
     * @return OptionCollection
     * @throws Exception
     */
    public function addItem($value): self
    {
        if ($value instanceof self) {
            return $this->addCollection($value);
        } elseif ($value instanceof AbstractOption) {
            return parent::addItem($value);
        }
        throw new Exception('Collection must be an instance of ' . Self::class . ' or ' . AbstractOption::class);
    }
    
    
    /**
     * @param EditableCollection $collection
     *
     * @return OptionCollection
     * @throws Exception
     */
    public function addCollection(EditableCollection $collection)
    {
        if ($collection instanceof OptionCollection) {
            return parent::addCollection($collection);
        }
        throw new Exception('Collection must be an instance of ' . Self::class);
    }
    
    
    /**
     * Get valid type.
     *
     * This method must be implemented in the child-collection classes.
     *
     * @return string
     */
    protected function _getValidType(): string
    {
        return AbstractOption::class;
    }
}
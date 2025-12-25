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

namespace Gambio\StyleEdit\Core\Options\Entities;

use EditableCollection;
use JsonSerializable;

/**
 * Class FieldSetCollection
 * @package Gambio\StyleEdit\Core\Components\Entities
 */
class FieldSetCollection extends EditableCollection implements JsonSerializable
{
    use CollectionTrait;
    
    
    /**
     * Get valid type.
     *
     * This method must be implemented in the child-collection classes.
     *
     * @return string
     */
    protected function _getValidType()
    {
        return FieldSet::class;
    }
    
}
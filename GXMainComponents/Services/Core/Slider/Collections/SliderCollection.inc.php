<?php

/* --------------------------------------------------------------
   SliderCollection.php 2016-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SliderCollection
 *
 * @category   System
 * @package    Slider
 * @subpackage Collections
 */
class SliderCollection extends EditableCollection
{
    /**
     * Returns a valid type for the SliderCollection.
     *
     * @return string Valid type.
     */
    protected function _getValidType()
    {
        return 'SliderInterface';
    }
}
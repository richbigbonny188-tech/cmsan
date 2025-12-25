<?php

/* --------------------------------------------------------------
   SlideImageCollection.php 2016-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SlideImageCollection
 *
 * @category   System
 * @package    Slider
 * @subpackage Collections
 */
class SlideImageCollection extends EditableCollection
{
    /**
     * Returns a valid type for the SlideImageCollection.
     *
     * @return string Valid type.
     */
    protected function _getValidType()
    {
        return 'SlideImageInterface';
    }
}
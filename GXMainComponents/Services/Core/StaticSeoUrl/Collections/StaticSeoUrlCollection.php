<?php

/* --------------------------------------------------------------
   StaticSeoUrlCollection.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StaticSeoUrlCollection
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Collections
 */
class StaticSeoUrlCollection extends EditableCollection
{
    /**
     * Returns a valid type for the StaticSeoUrlCollection.
     *
     * @return string Valid type.
     */
    protected function _getValidType()
    {
        return 'StaticSeoUrlInterface';
    }
}
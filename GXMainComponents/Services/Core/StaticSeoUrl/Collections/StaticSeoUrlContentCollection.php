<?php

/* --------------------------------------------------------------
   StaticSeoUrlContentCollection.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StaticSeoUrlContentCollection
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Collections
 *
 * @method StaticSeoUrlContent[] getArray
 */
class StaticSeoUrlContentCollection extends EditableCollection
{
    /**
     * Returns a valid type for the StaticSeoUrlContentCollection.
     *
     * @return string Valid type.
     */
    protected function _getValidType()
    {
        return 'StaticSeoUrlContentInterface';
    }
}
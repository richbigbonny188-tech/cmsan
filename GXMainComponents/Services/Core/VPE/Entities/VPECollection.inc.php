<?php

/* --------------------------------------------------------------
   VPECollection.inc.php 2017-07-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VPECollection
 *
 * @category   System
 * @package    VPE
 * @subpackage Entities
 *
 * @method VPEInterface[] getArray
 */
class VPECollection extends EditableCollection
{
    /**
     * Valid type for VPECollections is VPEInterface.
     *
     * @return string
     */
    public function _getValidType()
    {
        return '\VPEInterface';
    }
}
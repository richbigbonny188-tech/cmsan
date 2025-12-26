<?php
/* --------------------------------------------------------------
   ManufacturerCollection.inc.php 2017-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ManufacturerCollection
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Entities
 */
class ManufacturerCollection extends EditableCollection
{
    /**
     * Get valid type.
     *
     * @return string
     */
    public function _getValidType()
    {
        return '\ManufacturerInterface';
    }
}
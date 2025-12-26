<?php
/* --------------------------------------------------------------
   ManufacturerFactory.inc.php 2017-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ManufacturerFactory
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Factories
 */
class ManufacturerFactory
{
    /**
     * Returns new instances of manufacturer entities.
     *
     * @return \Manufacturer
     */
    public function createEntity()
    {
        return new Manufacturer(new EditableKeyValueCollection([]));
    }
    
    
    /**
     * Returns new instances of manufacturerCollections.
     *
     * @return \ManufacturerCollection
     */
    public function createCollection()
    {
        return new ManufacturerCollection();
    }
    
}
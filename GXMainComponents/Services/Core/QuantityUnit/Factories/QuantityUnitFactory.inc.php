<?php

/* --------------------------------------------------------------
   QuantityUnitFactory.inc.php 2017-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuantityUnitFactory
 *
 * @category   System
 * @package    QuantityUnit
 * @subpackage Factories
 */
class QuantityUnitFactory
{
    /**
     * Returns new instances of QuantityUnit entities.
     *
     * @return \GXEngineQuantityUnit
     */
    public function createEntity()
    {
        return new GXEngineQuantityUnit(new EditableKeyValueCollection([]));
    }
    
    
    /**
     * Returns new instances of QuantityUnitCollections.
     *
     * @return \QuantityUnitCollection
     */
    public function createCollection()
    {
        return new QuantityUnitCollection();
    }
}
<?php

/* --------------------------------------------------------------
   VPEFactory.inc.php 2017-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VPEFactory
 *
 * @category   System
 * @package    VPE
 * @subpackage Factories
 */
class VPEFactory
{
    /**
     * @return \VPE
     */
    public function createEntity()
    {
        return new VPE(new EditableKeyValueCollection([]));
    }
    
    
    /**
     * @return \VPECollection
     */
    public function createCollection()
    {
        return new VPECollection();
    }
    
}
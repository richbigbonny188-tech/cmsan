<?php
/* --------------------------------------------------------------
   SharedShoppingCartInterface.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SharedShoppingCartInterface
 *
 * @category   System
 * @package    ShoppingCart
 * @subpackage Interfaces
 */
interface SharedShoppingCartInterface
{
    /**
     * @return string
     */
    public function getHash();
    
    
    /**
     * @return string
     */
    public function getShoppingCartJson();
    
    
    /**
     * @return \DateTime
     */
    public function getCreationDate();
    
    
    /**
     * @return int
     */
    public function getCustomerId();
}
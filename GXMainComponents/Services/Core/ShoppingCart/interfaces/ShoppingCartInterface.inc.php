<?php
/* --------------------------------------------------------------
   ShoppingCartInterface.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ShoppingCartInterface
 *
 * @category   System
 * @package    ShoppingCart
 * @subpackage Interfaces
 */
interface ShoppingCartInterface
{
    /**
     * @return int
     */
    public function getId();
    
    
    /**
     * @return int
     */
    public function getCustomerId();
    
    
    /**
     * @return string
     */
    public function getProductId();
    
    
    /**
     * @return float
     */
    public function getQuantity();
    
    
    /**
     * @return float
     */
    public function getFinalPrice();
    
    
    /**
     * @return string
     */
    public function getCreationDate();
}
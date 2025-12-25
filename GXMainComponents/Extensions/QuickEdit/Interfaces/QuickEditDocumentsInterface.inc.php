<?php

/* --------------------------------------------------------------
   QuickEditDocumentsInterface.inc.php 2017-03-09
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditDocumentsInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
interface QuickEditDocumentsInterface
{
    /**
     * Creates a list of products that have been passed as parameters.
     *
     * @param array $products Returns the list of products that have been passed as parameters.
     */
    public function getProductsById(array $products);
    
    
    /**
     * Creates a list of all products needed to create the inventory list. The list is then generated.
     */
    public function getProducts();
    
    
    /**
     * Returns the link to the last created inventory list.
     *
     * @return array Returns the link to the last created inventory list.
     */
    public function getLink();
}
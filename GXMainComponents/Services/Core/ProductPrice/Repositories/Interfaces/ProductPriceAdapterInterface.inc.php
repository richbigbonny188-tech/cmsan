<?php
/* --------------------------------------------------------------
   ProductPriceAdapterInterface.inc.php 2018-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductPriceAdapterInterface
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage Repositories
 */
interface ProductPriceAdapterInterface
{
    /**
     * Returns an instance that can fetch product price data from a storage.
     *
     * @return \ProductPriceReaderInterface
     */
    public function reader();
    
    
    /**
     * Returns an instance that can write product price data to a storage.
     *
     * @return \ProductPriceWriterInterface
     */
    public function writer();
}
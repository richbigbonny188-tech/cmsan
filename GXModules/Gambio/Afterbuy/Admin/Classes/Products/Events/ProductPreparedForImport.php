<?php
/* --------------------------------------------------------------
   ProductPreparedForImport.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products\Events;

use GXEngineProduct;
use GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects\AfterbuyProduct;

/**
 * Class ProductPreparedForImport
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\Events
 */
class ProductPreparedForImport
{
    /**
     * @var GXEngineProduct
     */
    private GXEngineProduct $product;
    
    
    /**
     * @var AfterbuyProduct
     */
    private AfterbuyProduct $afterbuyProduct;
    
    
    /**
     * @param GXEngineProduct $product
     * @param AfterbuyProduct $afterbuyProduct
     */
    private function __construct(GXEngineProduct $product, AfterbuyProduct $afterbuyProduct)
    {
        $this->product         = $product;
        $this->afterbuyProduct = $afterbuyProduct;
    }
    
    
    /**
     * @param GXEngineProduct $product
     * @param AfterbuyProduct $afterbuyProduct
     *
     * @return static
     */
    public static function create(GXEngineProduct $product, AfterbuyProduct $afterbuyProduct): static
    {
        return new static($product, $afterbuyProduct);
    }
    
    
    /**
     * @return GXEngineProduct
     */
    public function gxEngineProduct(): GXEngineProduct
    {
        return $this->product;
    }
    
    
    /**
     * @return AfterbuyProduct
     */
    public function afterbuyProduct(): AfterbuyProduct
    {
        return $this->afterbuyProduct;
    }
}

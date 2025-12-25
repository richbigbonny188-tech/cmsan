<?php
/* --------------------------------------------------------------
   ProductMapping.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects;

/**
 * Class ProductMapping
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects
 */
class ProductMapping
{
    
    public const MAPPING_NONE = 'none';
    
    public const MAPPING_PRODUCT = 'product';
    
    public const MAPPING_COMBI = 'combi';
    
    
    /**
     * @var int|null
     */
    private ?int $productsId = null;
    
    
    /**
     * @var int|null
     */
    private ?int $combiId = null;
    
    
    /**
     * @var string
     */
    private string $type;
    
    
    /**
     * @var int|null
     */
    private ?int $afterbuyProductId;
    
    
    /**
     * @param int|null $productsId
     * @param int|null $combiId
     * @param int|null $afterbuyProductId
     */
    public function __construct(?int $productsId = null, ?int $combiId = null, int $afterbuyProductId = null)
    {
        if ($productsId === null || $productsId === 0) {
            $this->type = static::MAPPING_NONE;
        } else {
            $this->productsId = $productsId;
            if ($combiId === null || $combiId === 0) {
                $this->type = self::MAPPING_PRODUCT;
            } else {
                $this->combiId = $combiId;
                $this->type    = self::MAPPING_COMBI;
            }
            $this->afterbuyProductId = $afterbuyProductId;
        }
    }
    
    
    /**
     * @return int
     */
    public function getProductsId(): int
    {
        return $this->productsId;
    }
    
    
    /**
     * @return int|null
     */
    public function getCombiId(): ?int
    {
        return $this->combiId;
    }
    
    
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    
    /**
     * @param string $type
     *
     * @return bool
     */
    public function isType(string $type): bool
    {
        return $this->type === $type;
    }
    
    
    /**
     * @return int|null
     */
    public function getAfterbuyProductId(): ?int
    {
        return $this->afterbuyProductId;
    }
    
}
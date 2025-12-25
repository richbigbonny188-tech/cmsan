<?php
/*--------------------------------------------------------------
   ProductDTO.php 2022-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Data\DTO;

/**
 * Class ProductDTO
 *
 * @package Gambio\Admin\Modules\Customer\App\Data\DTO
 */
class ProductDTO
{
    private int    $productId;
    private string $name;
    private string $image;
    
    
    /**
     * @param int    $productId
     * @param string $name
     * @param string $image
     */
    public function __construct(
        int    $productId,
        string $name,
        string $image
    ) {
        $this->productId = $productId;
        $this->name      = $name;
        $this->image     = $image;
    }
    
    
    /**
     * @return int
     */
    public function productId(): int
    {
        return $this->productId;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return string
     */
    public function image(): string
    {
        return $this->image;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'productId' => $this->productId(),
            'name'      => $this->name(),
            'image'     => $this->image(),
        ];
    }
}
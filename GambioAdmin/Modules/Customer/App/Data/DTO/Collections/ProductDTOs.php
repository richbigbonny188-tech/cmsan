<?php
/*--------------------------------------------------------------
   ProductDTOs.php 2022-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Data\DTO\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\App\Data\DTO\ProductDTO;
use IteratorAggregate;
use Traversable;

/**
 * Class ProductDTOs
 *
 * @package Gambio\Admin\Modules\Customer\App\Data\DTO\Collections
 */
class ProductDTOs implements IteratorAggregate
{
    /**
     * @var ProductDTO[]
     */
    private array $dtos;
    
    
    /**
     * ProductDTO s constructor.
     *
     * @param ProductDTO[] $dtos
     */
    public function __construct(ProductDTO ...$dtos)
    {
        $this->dtos = $dtos;
    }
    
    
    /**
     * @return Traversable|ProductDTO[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->dtos);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
    
        foreach ($this->dtos as $dto) {
        
            $result[$dto->productId()] = $dto->toArray();
        }
        
        return $result;
        
        //return array_map(fn(ProductDTO $dto): array => $dto->toArray(), $this->dtos);
    }
}
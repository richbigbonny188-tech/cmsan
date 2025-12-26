<?php
/*--------------------------------------------------------------------------------------------------
    ImageDtoCollection.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use JsonSerializable;

/**
 * Class ImageDtoCollection
 * @package Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO
 */
class ImageDtoCollection implements Countable, Iterator, ArrayAccess, JsonSerializable
{
    /**
     * @var ImageDto[]
     */
    protected $values = [];
    
    /**
     * @var int
     */
    protected $position = 0;
    
    /**
     * SellingUnitImagesCollection constructor.
     *
     * @param ImageDto[] $images
     */
    public function __construct(array $images = [])
    {
        if (count($images)) {
            
            foreach ($images as $image) {
                
                $this[] = $image;
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->values[$this->position];
    }
    
    
    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->position++;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }
    
    
    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return isset($this->values[$this->position]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->position = 0;
    }
    
    
    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->values[$offset]);
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }
    
    
    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof ImageDto) {
            
            throw new InvalidArgumentException(static::class . ' only accepts ' . ImageDto::class);
        }
        
        if (empty($offset)) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->values[$offset]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->values);
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->values;
    }
}
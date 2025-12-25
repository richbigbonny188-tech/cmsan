<?php
/* --------------------------------------------------------------
   ImageListsCollection.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\ProductImageList\Collections;

use ArrayAccess;
use Countable;
use Gambio\ProductImageList\Exceptions\ImageListDoesNotExistsException;
use Gambio\ProductImageList\Image\ValueObjects\AbstractText;
use Gambio\ProductImageList\ImageList\Collections\ImageList;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use InvalidArgumentException;
use Iterator;
use JsonSerializable;

/**
 * Class ImageListsCollection
 * @package Gambio\ProductImageList\Collections
 */
class ImageListsCollection implements ArrayAccess, Countable, Iterator, JsonSerializable
{
    /**
     * @var ImageList[]
     */
    protected $values = [];
    
    /**
     * @var int
     */
    protected $position = 0;
    
    
    /**
     * ImageListsCollection constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        if (count($values)) {
    
            foreach ($values as $imageList) {
                
                $this->addImageList($imageList);
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
        if (!$value instanceof ImageList) {
    
            throw new InvalidArgumentException(static::class . ' only accepts ' . ImageList::class);
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
     * @param ImageList $imageList
     */
    public function addImageList(ImageList $imageList): void
    {
        $this->values[] = $imageList;
    }
    
    /**
     * @param ListId $id
     *
     * @return ImageList
     * @throws ImageListDoesNotExistsException
     */
    public function getImageListById(ListId $id) : ImageList
    {
        foreach ($this as $imageList) {
            
            if ($imageList->listId() !== null && $imageList->listId()->value() === $id->value()) {
                
                return $imageList;
            }
        }
        
        throw new ImageListDoesNotExistsException('No text was found with the ListId "' . $id->value() . '"');
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
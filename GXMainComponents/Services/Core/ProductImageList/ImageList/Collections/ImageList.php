<?php
/* --------------------------------------------------------------
  ImageList.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ImageList\Collections;

use ArrayAccess;
use Countable;
use Gambio\ProductImageList\Image\Interfaces\ImageInterface;
use Gambio\ProductImageList\Image\ValueObjects\Id;
use Gambio\ProductImageList\ImageList\Exceptions\ImageNotFoundException;
use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\ImageList\ValueObjects\ListName;
use InvalidArgumentException;
use Iterator;
use JsonSerializable;

/**
 * Class ImageList
 * @package Gambio\ProductImageList\ImageList\Collections
 */
class ImageList implements Countable, Iterator, ArrayAccess, JsonSerializable
{
    /**
     * @var ImageInterface[]
     */
    protected $values = [];
    
    /**
     * @var int
     */
    protected $position = 0;
    
    /**
     * @var ListId|null
     */
    protected $listId;
    
    /**
     * @var ListName
     */
    protected $listName;
    
    
    /**
     * ImageList constructor.
     *
     * @param ListName         $listName
     * @param ImageInterface[] $values
     * @param ListId           $listId
     */
    public function __construct(ListName $listName, array $values = [], ?ListId $listId = null)
    {
        if (count($values)) {
    
            foreach ($values as $image) {
                
                $this->addImage($image);
            }
        }
    
        $this->listId   = $listId;
        $this->listName = $listName;
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
        if (!$value instanceof ImageInterface) {
    
            throw new InvalidArgumentException(static::class . ' only accepts ' . ImageInterface::class);
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
     * @param ImageInterface $image
     */
    public function addImage(ImageInterface $image): void
    {
        $this->values[] = $image;
    }
    
    
    /**
     * @param Id $id
     *
     * @return ImageInterface
     * @throws ImageNotFoundException
     */
    public function getImageById(Id $id): ImageInterface
    {
        if ($this->count()) {
    
            foreach ($this->values as $image) {
                
                if ($image->id()->value() === $id->value()) {
                    
                    return $image;
                }
            }
        }
        
        throw new ImageNotFoundException('No image was found with the Id ' . $id->value());
    }
    
    
    /**
     * @return ListId|null
     */
    public function listId(): ?ListId
    {
        return $this->listId;
    }
    
    
    /**
     * @return ListName
     */
    public function listName(): ListName
    {
        return $this->listName;
    }
    
    
    /**
     * @return ImageInterface[]
     */
    public function toArray(): array
    {
        return $this->values;
    }
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return (object)[
            'listId'   => $this->listId(),
            'listName' => $this->listName(),
            'images'   => $this->values
        ];
    }
}
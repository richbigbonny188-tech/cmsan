<?php
/* --------------------------------------------------------------
  TitleCollection.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\Collections;

use ArrayAccess;
use Countable;
use Gambio\ProductImageList\Image\Exceptions\TextNotFoundException;
use Gambio\ProductImageList\Image\ValueObjects\AbstractText;
use Gambio\ProductImageList\Image\ValueObjects\LanguageCode;
use InvalidArgumentException;
use Iterator;
use JsonSerializable;

/**
 * Class TitleCollection
 * @package Gambio\ProductImageList\Image\Collections
 */
class TextCollection implements Countable, Iterator, ArrayAccess, JsonSerializable
{
    /**
     * @var AbstractText[]
     */
    protected $values = [];
    
    /**
     * @var int
     */
    protected $position = 0;
    
    
    /**
     * TitleCollection constructor.
     *
     * @param AbstractText[] $values
     */
    public function __construct(array $values = [])
    {
        if (count($values)) {
    
            foreach ($values as $text) {
                
                $this->addText($text);
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
        if (!$value instanceof AbstractText) {
            
            throw new InvalidArgumentException(static::class . ' only accepts ' . AbstractText::class);
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
     * @param AbstractText $title
     */
    public function addText(AbstractText $title): void
    {
        $this->values[] = $title;
    }
    
    
    /**
     * @param LanguageCode $languageCode
     *
     * @return AbstractText
     * @throws TextNotFoundException
     */
    public function getTextByLanguageCode(LanguageCode $languageCode): AbstractText
    {
        foreach ($this as $text) {
        
            if ($text->languageCode()->value() === $languageCode->value()) {
                
                return $text;
            }
        }
        
        throw new TextNotFoundException('No text was found with the LanguageCode "' . $languageCode->value() . '"');
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
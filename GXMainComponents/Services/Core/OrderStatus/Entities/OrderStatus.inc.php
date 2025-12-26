<?php

/* --------------------------------------------------------------
   OrderStatus.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderStatus
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Entities
 */
class OrderStatus implements OrderStatusInterface
{
    /**
     * @var int|null
     */
    protected $id;
    
    /**
     * @var array
     */
    protected $names = [];
    
    /**
     * @var string
     */
    protected $color;
    
    
    /**
     * OrderStatus constructor.
     *
     * @param \IntType|null $id
     */
    public function __construct(IntType $id = null)
    {
        $this->id = $id ? $id->asInt() : null;
    }
    
    
    /**
     * Returns the order status id.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Returns the order status names.
     *
     * @return array Order status names.
     */
    public function getNames()
    {
        return $this->names;
    }
    
    
    /**
     * Returns the order status name by the given language id.
     *
     * @param LanguageCode $languageCode Expected language code.
     *
     * @return string Expected status name by given language id.
     * @throws Exception If no name exists by the given language id.
     */
    public function getName(LanguageCode $languageCode)
    {
        if (!array_key_exists($languageCode->asString(), $this->names)) {
            throw new InvalidArgumentException('Order status name by language code "' . $languageCode->asString()
                                               . '" not found.');
        }
        
        return $this->names[$languageCode->asString()];
    }
    
    
    /**
     * Sets the order status name.
     *
     * @param \LanguageCode $languageCode Language code of order status name.
     * @param \StringType   $name         Order status name.
     *
     * @return $this|OrderStatus Same instance for chained method calls.
     */
    public function setName(LanguageCode $languageCode, StringType $name)
    {
        $this->names[$languageCode->asString()] = $name->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the order status label-color.
     *
     * @return string Color of order status label.
     */
    public function getColor()
    {
        return $this->color;
    }
    
    
    /**
     * Sets the order status label-color.
     *
     * @param StringType $color New color of order status label.
     *
     * @return $this|OrderStatus Same instance for chained method calls.
     */
    public function setColor(StringType $color)
    {
        $this->color = $color->asString();
        
        return $this;
    }
}
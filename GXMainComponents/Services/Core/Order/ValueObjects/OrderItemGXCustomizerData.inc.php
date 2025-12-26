<?php
/* --------------------------------------------------------------
   OrderItemGXCustomizerData.inc.php 2022-02-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderItemGXCustomizerData
 *
 * @category   System
 * @package    Order
 * @subpackage ValueObjects
 */
class OrderItemGXCustomizerData
{
    /**
     * Set of the element data
     *
     * @var string
     */
    protected $set = '';
    
    /**
     * Area of the element data
     *
     * @var string
     */
    protected $area = '';
    
    /**
     * Type of the element data
     *
     * @var string
     */
    protected $type = '';
    
    /**
     * Name of the element data
     *
     * @var string
     */
    protected $name = '';
    
    /**
     * Value of the element data
     *
     * @var string
     */
    protected $value = '';
    
    /**
     * File of the element data
     *
     * @var string
     */
    protected      $file = '';
    
    /**
     * Id of the customer set
     *
     * @var int|null
     */
    protected $setId;
    
    /**
     * Id of the element
     *
     * @var int|null
     */
    protected $elementId;
    
    /**
     * Id of the area
     *
     * @var int|null
     */
    protected $areaId;
    
    
    /**
     * OrderItemGXCustomizerData constructor.
     *
     * @param \StringType $set
     * @param \StringType $area
     * @param \StringType $type
     * @param \StringType $name
     * @param \StringType $value
     * @param \StringType $file
     * @param IdType|null $setId
     * @param IdType|null $elementId
     * @param IdType|null $areaId
     */
    public function __construct(
        StringType $set,
        StringType $area,
        StringType $type,
        StringType $name,
        StringType $value,
        StringType $file,
        ?IdType $setId = null,
        ?IdType $elementId = null,
        ?IdType $areaId = null
    ) {
        $this->set       = $set->asString();
        $this->area      = $area->asString();
        $this->type      = $type->asString();
        $this->name      = $name->asString();
        $this->value     = $value->asString();
        $this->file      = $file->asString();
        $this->setId     = $setId;
        $this->elementId = $elementId;
        $this->areaId    = $areaId;
    }
    
    
    /**
     * Returns the set of the element data.
     *
     * @return string
     */
    public function getSet()
    {
        return $this->set;
    }
    
    
    /**
     * Returns the set of the element data.
     *
     * @return int|null
     */
    public function getSetId()
    {
        return $this->setId === null ? null : $this->setId->asInt();
    }
    
    
    /**
     * Returns the id of the element.
     *
     * @return int|null
     */
    public function getElementId()
    {
        return $this->elementId === null ? null : $this->elementId->asInt();
    }
    
    /**
     * Returns the if of the area.
     *
     * @return int|null
     */
    public function getAreaId()
    {
        return $this->areaId === null ? null : $this->areaId->asInt();
    }
    
    
    /**
     * Returns the area of the element data.
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }
    
    
    /**
     * Returns the type of the element data.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    
    /**
     * Returns the name of the element data.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Returns the value of the element data.
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
    
    
    /**
     * Returns the file of the element data.
     *
     * @return int
     */
    public function getFile()
    {
        return $this->file;
    }
}
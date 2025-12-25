<?php

/* --------------------------------------------------------------
  SliderAssignmentRepository.inc.php 2016-11-01
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('SliderAssignmentRepositoryInterface');

/**
 * Class SliderAssignmentRepository
 *
 * @category   System
 * @package    Slider
 * @subpackage Repositories
 */
class SliderAssignmentRepository implements SliderAssignmentRepositoryInterface
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'slider_assignments';
    
    /**
     * Query builder.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SliderAssignmentRepository constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Get the Slider ID for the provided entity type (category, content, product) and entity ID.
     *
     * @param NonEmptyStringType $entityType
     * @param IdType             $entityId
     *
     * @return int|null Returns the ID of the slider which is assigned for the given content id or null if no record
     *                  was found.
     * @throws InvalidArgumentException If the given entity type is not valid.
     *
     */
    public function findAssignedSliderIdForEntityTypeAndEntityId(NonEmptyStringType $entityType, IdType $entityId)
    {
        $this->_validateEntityType($entityType);
        
        $sliderId = null;
        
        $result = $this->db->select('slider_id')->get_where($this->table,
                                                            [
                                                                'entity_type' => $entityType->asString(),
                                                                'entity_id'   => $entityId->asInt()
                                                            ])->row_array();
        
        if ($result !== null) {
            $sliderId = (int)$result['slider_id'];
        }
        
        return $sliderId;
    }
    
    
    /**
     * Inserts a slider assignment into the database.
     *
     * @param IdType             $sliderId
     * @param NonEmptyStringType $entityType
     * @param IdType             $entityId
     *
     * @return SliderAssignmentRepositoryInterface Same instance for method chaining.
     * @throws InvalidArgumentException If the given entity type is not valid.
     *
     */
    public function store(IdType $sliderId, NonEmptyStringType $entityType, IdType $entityId)
    {
        $this->_validateEntityType($entityType);
        
        $this->db->replace($this->table,
                           [
                               'slider_id'   => $sliderId->asInt(),
                               'entity_id'   => $entityId->asInt(),
                               'entity_type' => $entityType->asString()
                           ]);
        
        return $this;
    }
    
    
    /**
     * Deletes all slider assignments by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderAssignmentRepositoryInterface Same instance for method chaining.
     */
    public function deleteBySliderId(IdType $sliderId)
    {
        $this->db->delete($this->table, ['slider_id' => $sliderId->asInt()]);
        
        return $this;
    }
    
    
    /**
     * Deletes a slider assignment by the given entity type and entity ID.
     *
     * @param NonEmptyStringType $entityType
     * @param IdType             $entityId
     *
     * @return SliderAssignmentRepositoryInterface Same instance for method chaining.
     * @throws InvalidArgumentException If the given entity type is not valid.
     *
     */
    public function deleteByEntityTypeAndEntityId(NonEmptyStringType $entityType, IdType $entityId)
    {
        $this->_validateEntityType($entityType);
        
        $this->db->delete($this->table, ['entity_id' => $entityId->asInt(), 'entity_type' => $entityType->asString()]);
        
        return $this;
    }
    
    
    /**
     * Validates the given entity type. Valid types are: category, content, product.
     *
     * @param NonEmptyStringType $entityType
     *
     * @throws InvalidArgumentException If the given entity type is not valid.
     *
     */
    protected function _validateEntityType(NonEmptyStringType $entityType)
    {
        $validEntityTypes = ['category', 'content', 'product'];
        if (!in_array($entityType->asString(), $validEntityTypes)) {
            throw new InvalidArgumentException('SliderAssignmentRepository: Unsupported entity type. '
                                               . 'Supported entity types are: "' . implode('","',
                                                                                           $validEntityTypes) . '". '
                                               . 'Got "' . $entityType->asString() . '".');
        }
    }
}
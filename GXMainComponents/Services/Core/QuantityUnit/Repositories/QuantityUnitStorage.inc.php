<?php

/* --------------------------------------------------------------
   QuantityUnitStorage.inc.php 2017-08-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuantityUnitStorage
 *
 * @category   System
 * @package    QuantityUnit
 * @subpackage Repositories
 */
class QuantityUnitStorage implements QuantityUnitStorageInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * QuantityUnitStorage constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     * @param \LanguageProvider    $languageProvider
     */
    public function __construct(CI_DB_query_builder $queryBuilder, LanguageProvider $languageProvider)
    {
        $this->queryBuilder     = $queryBuilder;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Returns quantity unit entity data by the given id.
     *
     * @param \IdType $quantityUnitId
     *
     * @return array
     * @throws \EntityNotFoundException If no entity was found by provided id.
     */
    public function getById(IdType $quantityUnitId)
    {
        $rawData = $this->queryBuilder->select()
            ->from('quantity_unit')
            ->join('quantity_unit_description',
                   'quantity_unit.quantity_unit_id = quantity_unit_description.quantity_unit_id')
            ->where('quantity_unit.quantity_unit_id', $quantityUnitId->asInt())
            ->get()
            ->result_array() ? : [];
        
        if (count($rawData) === 0) {
            throw new EntityNotFoundException('Customer group entity was not found with provided id "'
                                              . $quantityUnitId->asInt() . '"');
        }
        
        $result     = ['id' => $quantityUnitId->asInt()];
        $namesArray = [];
        
        foreach ($rawData as $data) {
            $namesArray[$data['language_id']] = $data['unit_name'];
        }
        $result['names'] = $namesArray;
        
        return $result;
    }
    
    
    /**
     * Returns all quantity unit entities data as array.
     *
     * @return array
     */
    public function getAll()
    {
        $rawData = $this->queryBuilder->select()
            ->from('quantity_unit')
            ->join('quantity_unit_description',
                   'quantity_unit.quantity_unit_id = quantity_unit_description.quantity_unit_id')
            ->order_by('quantity_unit.quantity_unit_id')
            ->get()
            ->result_array();
        
        $formattedArray = [];
        $names          = [];
        
        foreach ($rawData as $data) {
            if (isset($oldId) && $oldId !== (int)$data['quantity_unit_id']) {
                $formattedArray[] = $names;
                $names            = [];
            }
            $oldId = (int)$data['quantity_unit_id'];
            
            $names['id']                          = $oldId;
            $names['names'][$data['language_id']] = $data['unit_name'];
        }
        if (count($rawData) > 0) {
            $formattedArray[] = $names;
        }
        
        return $formattedArray;
    }
    
    
    /**
     * Saves quantity unit entity data in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be saved.
     *
     * @return \QuantityUnitStorageInterface Same instance for chained method calls.
     */
    public function save(QuantityUnitInterface $quantityUnit)
    {
        $this->queryBuilder->insert('quantity_unit', ['quantity_unit_id' => null]);
        $quantityUnitId = $this->queryBuilder->insert_id();
        
        foreach ($quantityUnit->getNames() as $languageCode => $name) {
            $this->queryBuilder->insert('quantity_unit_description',
                                        [
                                            'quantity_unit_id' => $quantityUnitId,
                                            'language_id'      => $this->languageProvider->getIdByCode(MainFactory::create('LanguageCode',
                                                                                                                           new StringType($languageCode))),
                                            'unit_name'        => $name
                                        ]);
        }
        
        $quantityUnit->setId(new IdType($quantityUnitId));
        
        return $this;
    }
    
    
    /**
     * Updates quantity unit entity data in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be updated.
     *
     * @return \QuantityUnitStorageInterface Same instance for chained method calls.
     */
    public function update(QuantityUnitInterface $quantityUnit)
    {
        foreach ($quantityUnit->getNames() as $languageCode => $name) {
            $languageId = $this->languageProvider->getIdByCode(new LanguageCode(new StringType($languageCode)));
            
            $this->queryBuilder->update('quantity_unit_description',
                                        ['unit_name' => $name],
                                        ['quantity_unit_id' => $quantityUnit->getId(), 'language_id' => $languageId]);
        }
        
        return $this;
    }
    
    
    /**
     * Deletes quantity unit entity data in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be delete.
     *
     * @return \QuantityUnitStorageInterface Same instance for chained method calls.
     */
    public function delete(QuantityUnitInterface $quantityUnit)
    {
        $this->queryBuilder->delete('quantity_unit', ['quantity_unit_id' => $quantityUnit->getId()]);
        $this->queryBuilder->delete('quantity_unit_description', ['quantity_unit_id' => $quantityUnit->getId()]);
        
        return $this;
    }
}

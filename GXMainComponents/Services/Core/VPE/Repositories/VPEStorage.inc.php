<?php
/* --------------------------------------------------------------
   VPEStorage.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VPEStorage
 *
 * @category   System
 * @package    VPE
 * @subpackage Repositories
 */
class VPEStorage implements VPEStorageInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    public function __construct(CI_DB_query_builder $queryBuilder, LanguageProvider $languageProvider)
    {
        $this->queryBuilder     = $queryBuilder;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Returns VPE entity data by the given id.
     *
     * @param \IdType $vpeId
     *
     * @return array
     */
    public function getById(IdType $vpeId)
    {
        $rawData = $this->queryBuilder->select()
            ->from('products_vpe')
            ->where('products_vpe_id', $vpeId->asInt())
            ->get()
            ->result_array();
        
        $result     = ['id' => $vpeId->asInt()];
        $namesArray = [];
        
        foreach ($rawData as $data) {
            $namesArray[$data['language_id']] = $data['products_vpe_name'];
        }
        $result['names']   = $namesArray;
        $result['default'] = $this->_isDefault($vpeId);
        
        return $result;
    }
    
    
    /**
     * Returns all VPE entities data as array.
     *
     * @return array
     */
    public function getAll()
    {
        $rawData = $this->queryBuilder->select()
            ->from('products_vpe')
            ->order_by('products_vpe_id', 'asc')
            ->order_by('language_id',
                       'asc')
            ->get()
            ->result_array();
        
        $formattedArray = [];
        $names          = [];
        
        foreach ($rawData as $data) {
            if (isset($oldId) && $oldId !== (int)$data['products_vpe_id']) {
                $formattedArray[] = $names;
                $names            = [];
            }
            $oldId                                = (int)$data['products_vpe_id'];
            $names['id']                          = $oldId;
            $names['default']                     = $this->_isDefault(new IdType($oldId));
            $names['names'][$data['language_id']] = $data['products_vpe_name'];
        }
        
        if (count($rawData) > 0) {
            $formattedArray[] = $names;
        }
        
        return $formattedArray;
    }
    
    
    /**
     * Saves VPE entity data in database.
     *
     * @param \VPEInterface $vpe VPE entity to be saved.
     *
     * @return \VPEStorageInterface Same instance for chained method calls.
     */
    public function save(VPEInterface $vpe)
    {
        
        $lastVpeId = $this->queryBuilder->select('products_vpe_id')
                         ->from('products_vpe')
                         ->order_by('products_vpe_id',
                                    'DESC')
                         ->get()
                         ->row_array()['products_vpe_id'];
        $vpeId     = (int)$lastVpeId + 1;
        foreach ($vpe->getNames() as $languageCode => $name) {
            $this->queryBuilder->set([
                                         'products_vpe_id'   => $vpeId,
                                         'language_id'       => $this->languageProvider->getIdByCode(MainFactory::create('LanguageCode',
                                                                                                                         new StringType($languageCode))),
                                         'products_vpe_name' => $name
                                     ]);
            $this->queryBuilder->insert('products_vpe');
        }
        $vpe->setId(new IdType($vpeId));
        $this->_updateDefault($vpe);
        
        return $this;
    }
    
    
    /**
     * Deletes VPE entity data in database.
     *
     * @param \VPEInterface $vpe VPE entity to be delete.
     *
     * @return \VPEStorageInterface Same instance for chained method calls.
     */
    public function delete(VPEInterface $vpe)
    {
        $this->queryBuilder->delete('products_vpe ', ['products_vpe_id' => $vpe->getId()]);
        $this->queryBuilder->update('products',
                                    ['products_vpe' => null],
                                    ['products_vpe' => $vpe->getId()]);
        
        return $this;
    }
    
    
    /**
     * Updates VPE entity data in database.
     *
     * @param \VPEInterface $vpe VPE entity to be updated.
     *
     * @return \VPEStorageInterface Same instance for chained method calls.
     */
    public function update(VPEInterface $vpe)
    {
        foreach ($vpe->getNames() as $languageCode => $name) {
            $languageId = $this->languageProvider->getIdByCode(new LanguageCode(new StringType($languageCode)));
            
            $this->queryBuilder->update('products_vpe',
                                        ['products_vpe_name' => $name],
                                        ['products_vpe_id' => $vpe->getId(), 'language_id' => $languageId]);
        }
        $this->_updateDefault($vpe);
        
        return $this;
    }
    
    
    /**
     * Checks if the vpe entity of given id is shops default.
     *
     * @param \IdType $vpeId Id of expected vpe default entity.
     *
     * @return bool True if entity is default, false otherwise.
     */
    protected function _isDefault(IdType $vpeId)
    {
        return $this->queryBuilder->select('value')->from('gx_configurations')->where([
                                                                                          'value' => $vpeId->asInt(),
                                                                                          'key'   => 'configuration/DEFAULT_PRODUCTS_VPE_ID'
                                                                                      ])->get()->num_rows() === 1;
    }
    
    
    /**
     * Checks if vpe entity is default. If true, the id will set to the vpe default value in the configuration table.
     *
     * @param \VPEInterface $vpe Vpe entity to check and set as shops default.
     *
     * @return \VPEStorageInterface Same instance for chained method calls.
     */
    protected function _updateDefault(VPEInterface $vpe)
    {
        if ($vpe->isDefault()) {
            $this->queryBuilder->update('gx_configurations',
                                        ['value' => $vpe->getId()],
                                        ['key' => 'configuration/DEFAULT_PRODUCTS_VPE_ID']);
        }
        
        return $this;
    }
}
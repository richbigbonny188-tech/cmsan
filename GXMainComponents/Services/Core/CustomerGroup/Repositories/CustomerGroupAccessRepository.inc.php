<?php
/* --------------------------------------------------------------
   CustomerGroupAccessRepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerGroupAccessRepository
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
class CustomerGroupAccessRepository implements CustomerGroupAccessRepositoryInterface
{
    
    /**
     * @var \CustomerGroupFactory
     */
    protected $factory;
    
    
    /**
     * @var \CustomerGroupReaderInterface
     */
    protected $reader;
    
    
    /**
     * @var \LanguageProvider
     */
    protected $languageProvider;
    
    
    /**
     * CustomerGroupAccessRepository constructor.
     *
     * @param \CustomerGroupFactory         $factory
     * @param \CustomerGroupReaderInterface $reader
     * @param \LanguageProvider             $languageProvider To convert language id's to language codes.
     */
    public function __construct(
        CustomerGroupFactory $factory,
        CustomerGroupReaderInterface $reader,
        LanguageProvider $languageProvider
    ) {
        $this->factory          = $factory;
        $this->reader           = $reader;
        $this->languageProvider = $languageProvider;
    }
    
    
    /**
     * Returns all customer group as collection.
     *
     * @return \CustomerGroupCollection Customer group collection.
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function getAll()
    {
        $rawData    = $this->reader->getAll();
        $collection = $this->factory->createCollection();
        
        foreach ($rawData as $dataSet) {
            $customerGroup = $this->factory->createEntity();
            
            $customerGroup->setId(new IntType($dataSet['id']))
                ->setMembers(new IntType($dataSet['members']))
                ->setDefault(new BoolType($dataSet['default']))
                ->setSettings($this->_createSettings($dataSet['settings']))
                ->setConfigurations($this->_createConfigurations($dataSet['configurations']));
            
            foreach ($dataSet['names'] as $languageId => $customerGroupName) {
                $languageCode = $this->languageProvider->getCodeById(new IdType($languageId));
                $customerGroup->setName(new StringType($customerGroupName), $languageCode);
            }
            
            $collection->addItem($customerGroup);
        }
        
        return $collection;
    }
    
    
    /**
     * Returns customer group entity by given id.
     *
     * @param \IntType $id IdType of entity to be returned.
     *
     * @return \CustomerGroup Customer group collection.
     */
    public function getById(IntType $id)
    {
        $dataSet = $this->reader->getById($id);
        
        $customerGroup = $this->factory->createEntity();
        
        $customerGroup->setId($id)
            ->setMembers(new IntType($dataSet['members']))
            ->setDefault(new BoolType($dataSet['default']))
            ->setSettings($this->_createSettings($dataSet['settings']))
            ->setConfigurations($this->_createConfigurations($dataSet['configurations']));
        
        foreach ($dataSet['names'] as $languageId => $customerGroupName) {
            $languageCode = $this->languageProvider->getCodeById(new IdType($languageId));
            $customerGroup->setName(new StringType($customerGroupName), $languageCode);
        }
        
        return $customerGroup;
    }
    
    
    /**
     * creates customer group entity.
     *
     * @return \CustomerGroup New customer group entity.
     */
    public function create()
    {
        return $this->factory->createEntity();
    }
    
    
    /**
     * Creates with given array an CustomerGroupSetting
     *
     * @param array $dataSet
     *
     * @return \CustomerGroupSettings
     */
    protected function _createSettings(array $dataSet)
    {
        $true  = new BoolType(true);
        $false = new BoolType(false);
        
        $public             = $dataSet['public'] ? $true : $false;
        $otDiscount         = $dataSet['otDiscount'] ? $true : $false;
        $graduatedPrices    = $dataSet['graduatedPrices'] ? $true : $false;
        $showPrice          = $dataSet['showPrice'] ? $true : $false;
        $showPriceTax       = $dataSet['showPriceTax'] ? $true : $false;
        $addTaxOt           = $dataSet['addTaxOt'] ? $true : $false;
        $discountAttributes = $dataSet['discountAttributes'] ? $true : $false;
        $fsk18Purchasable   = $dataSet['fsk18Purchasable'] ? $true : $false;
        $fsk18Display       = $dataSet['fsk18Display'] ? $true : $false;
        $writeReviews       = $dataSet['writeReviews'] ? $true : $false;
        $readReviews        = $dataSet['readReviews'] ? $true : $false;
        
        return $this->factory->createSettings($public,
                                              $otDiscount,
                                              $graduatedPrices,
                                              $showPrice,
                                              $showPriceTax,
                                              $addTaxOt,
                                              $discountAttributes,
                                              $fsk18Purchasable,
                                              $fsk18Display,
                                              $writeReviews,
                                              $readReviews);
    }
    
    
    /**
     * Creates with given array an CustomerGroupConfiguration
     *
     * @param array $dataSet
     *
     * @return \CustomerGroupConfigurations
     */
    protected function _createConfigurations(array $dataSet)
    {
        return $this->factory->createConfigurations(new DecimalType($dataSet['discount']),
                                                    new DecimalType($dataSet['otDiscount']),
                                                    $dataSet['minOrder'] ? new DecimalType($dataSet['minOrder']) : null,
                                                    $dataSet['maxOrder'] ? new DecimalType($dataSet['maxOrder']) : null,
                                                    $dataSet['unallowedPaymentModules'],
                                                    $dataSet['unallowedShippingModules']);
    }
}

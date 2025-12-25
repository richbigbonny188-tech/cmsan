<?php
/* --------------------------------------------------------------
   ParcelServiceMapper.inc.php 2018-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceMapper
 */
class ParcelServiceMapper implements ParcelServiceMapperInterface
{
    /**
     * @var \ParcelServiceDataAdapterInterface
     */
    protected $dataAdapter;
    
    
    /**
     * ParcelServiceDataMapper constructor.
     *
     * @param \ParcelServiceDataAdapterInterface $dataAdapter
     */
    public function __construct(\ParcelServiceDataAdapterInterface $dataAdapter)
    {
        $this->dataAdapter = $dataAdapter;
    }
    
    
    /**
     * Fetches all parcel services limited by the pager.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection|null
     */
    public function findAll(\Pager $pager = null, array $sorters = [])
    {
        $reader             = $this->dataAdapter->reader();
        $parcelServicesData = $reader->fetchAll($pager, $sorters);
        
        if (!count($parcelServicesData)) {
            return null;
        }
        
        $collection = $this->createParcelServiceCollection($parcelServicesData);
        
        return $collection;
    }
    
    
    /**
     * Fetches all parcel services found by given limited search condition.
     *
     * @param \ParcelServiceSearchCondition $searchCondition
     * @param \Pager|null                   $pager   (Optional) Pager object with pagination information
     * @param array                         $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection|null
     */
    public function findBy(\ParcelServiceSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        $reader             = $this->dataAdapter->reader();
        $parcelServicesData = $reader->fetchBy(new StringType($searchCondition->buildSql()), $pager, $sorters);
        
        if (!count($parcelServicesData)) {
            return null;
        }
        
        $collection = $this->createParcelServiceCollection($parcelServicesData);
        
        return $collection;
    }
    
    
    /**
     * Fetches a parcel service found by given ID.
     *
     * @param \ParcelServiceId $parcelServiceId
     *
     * @return \ParcelServiceInterface|null
     */
    public function findById(\ParcelServiceId $parcelServiceId)
    {
        $reader            = $this->dataAdapter->reader();
        $parcelServiceData = $reader->fetchById($parcelServiceId);
        
        if (!count($parcelServiceData)) {
            return null;
        }
        
        $parcelService = $this->createParcelService($parcelServiceData);
        
        return $parcelService;
    }
    
    
    /**
     * Inserts a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return \ParcelServiceInterface
     */
    public function insert(\ParcelServiceInterface $parcelService)
    {
        $parcelServiceInsertId = $this->dataAdapter->writer()
            ->insert($this->createParcelServiceDataArray($parcelService));
        
        $parcelService = $this->findById(\ParcelServiceId::create($parcelServiceInsertId));
        
        return $parcelService;
    }
    
    
    /**
     * Updates a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return \ParcelServiceInterface
     */
    public function update(\ParcelServiceInterface $parcelService)
    {
        $this->dataAdapter->writer()->update($this->createParcelServiceDataArray($parcelService),
                                             new IdType($parcelService->id()));
        
        return $parcelService;
    }
    
    
    /**
     * Deletes a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     */
    public function delete(\ParcelServiceInterface $parcelService)
    {
        $this->dataAdapter->writer()->delete(new \IdType($parcelService->id()));
    }
    
    
    /**
     * Creates a ParcelServiceCollection from given parcelServicesData array.
     *
     * @param $parcelServicesData
     *
     * @return \ParcelServiceCollection
     */
    protected function createParcelServiceCollection($parcelServicesData)
    {
        $parcelServices = [];
        
        foreach ($parcelServicesData as $parcelServiceData) {
            $parcelServices[] = $this->createParcelService($parcelServiceData);
        }
        
        $collection = ParcelServiceCollection::collect($parcelServices);
        
        return $collection;
    }
    
    
    /**
     * Creates a GXParcelService object from given parcelServiceData.
     *
     * @param $parcelServiceData
     *
     * @return \GXParcelService
     */
    protected function createParcelService($parcelServiceData)
    {
        $parcelServiceDescriptions = [];
        
        foreach ($parcelServiceData['descriptions'] as $description) {
            $parcelServiceDescriptions[] = ParcelServiceDescription::create(ParcelServiceDescriptionId::create(ParcelServiceId::create($description['parcel_service_id']),
                                                                                                               $description['language_id']),
                                                                            $description['url'],
                                                                            $description['comment']);
        }
        
        $parcelService = GXParcelService::create(ParcelServiceId::create($parcelServiceData['parcel_service_id']),
                                                 $parcelServiceData['name'],
                                                 $parcelServiceData['default'],
                                                 ParcelServiceDescriptionCollection::collect($parcelServiceDescriptions));
        
        return $parcelService;
    }
    
    
    /**
     * Creates a parcelServiceData array from given parcelService object.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return array
     */
    protected function createParcelServiceDataArray(\ParcelServiceInterface $parcelService)
    {
        $parcelServiceDescriptions = $parcelService->parcelServiceDescriptions()->getArray();
        
        $descriptions = [];
        
        /** @var \ParcelServiceDescriptionInterface $parcelServiceDescription */
        foreach ($parcelServiceDescriptions as $parcelServiceDescription) {
            $descriptions[] = [
                'parcel_service_id' => $parcelServiceDescription->parcelServiceId(),
                'language_id'       => $parcelServiceDescription->languageId(),
                'url'               => $parcelServiceDescription->url(),
                'comment'           => $parcelServiceDescription->comment()
            ];
        }
        
        $parcelServiceData = [
            'parcel_service_id' => $parcelService->id(),
            'name'              => $parcelService->name(),
            'default'           => (int)$parcelService->isDefault(),
            'descriptions'      => $descriptions
        ];
        
        return $parcelServiceData;
    }
}
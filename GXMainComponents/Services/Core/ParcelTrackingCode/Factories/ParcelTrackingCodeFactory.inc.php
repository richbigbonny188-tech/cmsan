<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeFactory.inc.php 2023-03-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeFactory
 */
class ParcelTrackingCodeFactory
{
    /**
     * @var \ParcelTrackingCodeRepository
     */
    protected $repository;
    
    /**
     * @var \ParcelTrackingCodeDeleteService
     */
    protected $deleteService;
    
    /**
     * @var \ParcelTrackingCodeFinder
     */
    protected $finder;
    
    
    /**
     * Creates an active record version of the parcel tracking code entity.
     *
     * @param \ParcelTrackingCodeServiceId  $parcelServiceId Id of related parcel service.
     * @param \ParcelTrackingCodeOrderId    $orderId         Id of related order.
     * @param \OrderParcelTrackingCode      $trackingCode    Parcel tracking code of order.
     * @param \ParcelTrackingCodeLanguageId $languageId      Language id, used to fetch language specific descriptions.
     *
     * @return \ActiveRecordParcelTrackingCode New parcel tracking code entity with active record functionality.
     */
    public function create(
        ParcelTrackingCodeServiceId $parcelServiceId,
        ParcelTrackingCodeOrderId $orderId,
        OrderParcelTrackingCode $trackingCode,
        ParcelTrackingCodeLanguageId $languageId
    ) {
        return MainFactory::create('ActiveRecordParcelTrackingCode',
                                   $this->trackingCodeRepository(),
                                   GXParcelTrackingCode::create($parcelServiceId,
                                                                $orderId,
                                                                $trackingCode,
                                                                $languageId));
    }
    
    
    /**
     * Creates an active record version of the parcel tracking code entity with lightweight data.
     *
     * @param \ParcelTrackingCodeOrderId      $orderId Id of related order.
     * @param \ParcelTrackingCodeServiceName  $name    Name of parcel service.
     * @param \ParcelTrackingCodeUrl          $url     Parcel tracking code url.
     * @param \ParcelTrackingCodeComment|null $comment Optional comment for parcel tracking code.
     *
     * @return \ActiveRecordParcelTrackingCode New parcel tracking code entity with active record functionality.
     */
    public function createLightweight(
        ParcelTrackingCodeOrderId $orderId,
        ParcelTrackingCodeServiceName $name,
        ParcelTrackingCodeUrl $url,
        ParcelTrackingCodeComment $comment = null
    ) {
        return MainFactory::create('ActiveRecordParcelTrackingCode',
                                   $this->trackingCodeRepository(),
                                   GXParcelTrackingCode::createLightweight($orderId, $name, $url, $comment));
    }
    
    
    /**
     * Creates an active record version of the given parcel tracking code entity.
     *
     * @param \GXParcelTrackingCode $trackingCode Entity to be enriched with AR functionality.
     *
     * @return \ActiveRecordParcelTrackingCode New parcel tracking code entity with active record functionality.
     */
    public function createFrom(GXParcelTrackingCode $trackingCode)
    {
        return MainFactory::create('ActiveRecordParcelTrackingCode', $this->trackingCodeRepository(), $trackingCode);
    }
    
    
    /**
     * Creates and returns a delete service for parcel tracking codes.
     * The service will be cached in memory.
     *
     * @return \ParcelTrackingCodeDeleteService Service for delete operations in the parcel tracking code domain.
     */
    public function deleteService()
    {
        if (null === $this->deleteService) {
            $this->deleteService = MainFactory::create('ParcelTrackingCodeDeleteService',
                                                       $this->trackingCodeRepository());
        }
        
        return $this->deleteService;
    }
    
    
    /**
     * Creates and returns a parcel tracking code finder.
     * The finder will be cached in memory.
     *
     * @return \ParcelTrackingCodeFinder Search component to find parcel tracking codes.
     */
    public function finder()
    {
        if (null === $this->finder) {
            $this->finder = MainFactory::create('ParcelTrackingCodeFinder',
                                                StaticGXCoreLoader::getDatabaseQueryBuilder());
        }
        
        return $this->finder;
    }
    
    
    /**
     * Creates and returns the parcel tracking code repository.
     * The repository will be cached in memory.
     *
     * @return \ParcelTrackingCodeRepository Access to data layer of domain.
     */
    protected function trackingCodeRepository()
    {
        if (null === $this->repository) {
            $db      = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $writer  = new GXParcelTrackingCodeWriter($db);
            $deleter = new ParcelTrackingCodeDeleter($db);
            
            $this->repository = new ParcelTrackingCodeRepository($writer, $deleter);
        }
        
        return $this->repository;
    }
}
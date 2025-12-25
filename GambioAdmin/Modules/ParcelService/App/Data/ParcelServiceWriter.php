<?php
/* --------------------------------------------------------------
   ParcelServiceWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceDescriptions;
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\CreationOfParcelServicesFailedException;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\DeletionOfParcelServicesFailedException;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\StorageOfParcelServicesFailedException;
use Gambio\Core\Language\Services\LanguageService;

/**
 * Class ParcelServiceWriter
 *
 * @package Gambio\Admin\Modules\ParcelService\App\Data
 */
class ParcelServiceWriter
{
    /**
     * @var Connection
     */
    private $db;
    
    /**
     * @var LanguageService
     */
    private $languageService;
    
    
    /**
     * ParcelServiceWriter constructor.
     *
     * @param Connection      $db
     * @param LanguageService $languageService
     */
    public function __construct(Connection $db, LanguageService $languageService)
    {
        $this->db              = $db;
        $this->languageService = $languageService;
    }
    
    
    /**
     * @param string                    $name
     * @param ParcelServiceDescriptions $descriptions
     * @param bool                      $isDefault
     * @param string                    $shipmentType
     *
     * @return int
     *
     * @throws CreationOfParcelServicesFailedException
     */
    public function createParcelService(
        string                    $name,
        ParcelServiceDescriptions $descriptions,
        bool                      $isDefault = false,
        string                    $shipmentType = ''
    ): int {
        try {
            if ($isDefault) {
                $this->db->createQueryBuilder()->update('parcel_services')->set('`default`', '0')->executeQuery();
            }
            
            $this->db->createQueryBuilder()
                ->insert('parcel_services')
                ->setValue('`name`', ':name')
                ->setValue('`default`', ':default')
                ->setValue('`shipment_type`', ':shipmentType')
                ->setParameter('name', $name)
                ->setParameter('default', (int)$isDefault)
                ->setParameter('shipmentType', $shipmentType)
                ->executeQuery();
            $id = $this->db->lastInsertId();
            
            foreach ($this->languageService->getAvailableLanguages() as $language) {
                $this->db->createQueryBuilder()
                    ->insert('parcel_services_description')
                    ->setValue('parcel_service_id', ':parcel_service_id')
                    ->setValue('language_id', ':language_id')
                    ->setValue('url', ':url')
                    ->setValue('comment', ':comment')
                    ->setParameter('parcel_service_id', $id)
                    ->setParameter('language_id', $language->id())
                    ->setParameter('url', $descriptions->url($language->code()))
                    ->setParameter('comment', $descriptions->comment($language->code()))
                    ->executeQuery();
            }
            
            return (int)$id;
        } catch (Exception $exception) {
            throw CreationOfParcelServicesFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param array ...$creationArguments Provided array must contain arguments like they are used in the single
     *                                    creation method. Provide multiple arrays for multi creation.
     *
     * @return int[]
     *
     * @throws CreationOfParcelServicesFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createMultipleParcelServices(array ...$creationArguments): array
    {
        try {
            $this->db->beginTransaction();
            $ids = [];
            foreach ($creationArguments as $creationArgument) {
                $ids[] = $this->createParcelService($creationArgument[0],
                                                    $creationArgument[1],
                                                    $creationArgument[2] ?? false,
                                                    $creationArgument[3] ?? '');
            }
            $this->db->commit();
        } catch (Exception $exception) {
            if (!($exception instanceof CreationOfParcelServicesFailedException)) {
                $exception = CreationOfParcelServicesFailedException::becauseOfException($exception);
            }
            
            $this->db->rollBack();
            throw $exception;
        }
        
        return $ids;
    }
    
    
    /**
     * @param ParcelService $parcelService
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function updateParcelService(ParcelService $parcelService): void
    {
        if ($parcelService->isDefault()) {
            $this->db->createQueryBuilder()->update('parcel_services')->set('`default`', '0')->executeQuery();
        }
        
        $this->db->createQueryBuilder()
            ->update('parcel_services')
            ->set('`name`', ':name')
            ->set('`default`', ':default')
            ->set('`shipment_type`', ':shipmentType')
            ->where('parcel_service_id = :id')
            ->setParameter('name', $parcelService->name())
            ->setParameter('default', (int)$parcelService->isDefault())
            ->setParameter('shipmentType', $parcelService->shipmentType())
            ->setParameter('id', $parcelService->id())
            ->executeQuery();
        
        $this->db->createQueryBuilder()
            ->delete('parcel_services_description')
            ->where('parcel_service_id = :id')
            ->setParameter('id', $parcelService->id())
            ->executeQuery();
        
        foreach ($this->languageService->getAvailableLanguages() as $language) {
            $this->db->createQueryBuilder()
                ->insert('parcel_services_description')
                ->setValue('parcel_service_id', ':id')
                ->setValue('language_id', ':language_id')
                ->setValue('url', ':url')
                ->setValue('comment', ':comment')
                ->setParameter('url', $parcelService->url($language->code()))
                ->setParameter('comment', $parcelService->comment($language->code()))
                ->setParameter('id', $parcelService->id())
                ->setParameter('language_id', $language->id())
                ->executeQuery();
        }
    }
    
    
    /**
     * @param ParcelService ...$parcelServices
     *
     * @throws StorageOfParcelServicesFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateParcelServices(ParcelService ...$parcelServices): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($parcelServices as $parcelService) {
                $this->updateParcelService($parcelService);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw StorageOfParcelServicesFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param ParcelServiceId $id
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function deleteParcelService(ParcelServiceId $id): void
    {
        $this->db->createQueryBuilder()
            ->delete('parcel_services')
            ->where('parcel_service_id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery();
        
        $this->db->createQueryBuilder()
            ->delete('parcel_services_description')
            ->where('parcel_service_id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery();
    }
    
    
    /**
     * @param ParcelServiceId ...$ids
     *
     * @throws DeletionOfParcelServicesFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteParcelServices(ParcelServiceId ...$ids): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($ids as $id) {
                $this->deleteParcelService($id);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw DeletionOfParcelServicesFailedException::becauseOfException($exception);
        }
    }
}
<?php
/* --------------------------------------------------------------
   TrackingCodeWriter.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\App\Data;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\ParcelServiceDetails;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\CreationOfTrackingCodesFailedException;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\DeletionOfTrackingCodesFailedException;
use Gambio\Core\Language\Services\LanguageService;

/**
 * Class TrackingCodeWriter
 *
 * @package Gambio\Admin\Modules\TrackingCode\App\Data
 */
class TrackingCodeWriter
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
     * TrackingCodeWriter constructor.
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
     * @param OrderId              $orderId
     * @param string               $code
     * @param ParcelServiceDetails $parcelServiceDetails
     * @param bool                 $isReturnDelivery
     *
     * @return int
     *
     * @throws CreationOfTrackingCodesFailedException
     */
    public function createTrackingCode(
        OrderId              $orderId,
        string               $code,
        ParcelServiceDetails $parcelServiceDetails,
        bool                 $isReturnDelivery
    ): int {
        try {
            $language = $this->languageService->getLanguageByCode($parcelServiceDetails->languageCode());
            
            $this->db->createQueryBuilder()
                ->insert('orders_parcel_tracking_codes')
                ->setValue('order_id', ':orderId')
                ->setValue('tracking_code', ':code')
                ->setValue('is_return_delivery', ':isReturnDelivery')
                ->setValue('language_id', ':languageId')
                ->setValue('parcel_service_id', ':parcelServiceId')
                ->setValue('parcel_service_name', ':parcelServiceName')
                ->setValue('url', ':parcelServiceUrl')
                ->setValue('comment', ':parcelServiceComment')
                ->setValue('shipment_type', ':shipmentType')
                ->setValue('creation_date', 'now()')
                ->setParameter('orderId', $orderId->value())
                ->setParameter('code', $code)
                ->setParameter('isReturnDelivery', $isReturnDelivery ? '1' : '0')
                ->setParameter('languageId', $language->id())
                ->setParameter('parcelServiceId', $parcelServiceDetails->parcelServiceId())
                ->setParameter('parcelServiceName', $parcelServiceDetails->name())
                ->setParameter('parcelServiceUrl', $parcelServiceDetails->url())
                ->setParameter('parcelServiceComment', $parcelServiceDetails->comment())
                ->setParameter('shipmentType', $parcelServiceDetails->shipmentType())
                ->executeQuery();
            
            return (int)$this->db->lastInsertId();
        } catch (Exception $exception) {
            throw CreationOfTrackingCodesFailedException::becauseOfException($exception);
        }
    }
    
    
    /**
     * @param array ...$creationArguments Provided array must contain arguments like they are used in the single
     *                                    creation method. Provide multiple arrays for multi creation.
     *
     * @return int[]
     *
     * @throws CreationOfTrackingCodesFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function createMultipleTrackingCodes(array ...$creationArguments): array
    {
        try {
            $this->db->beginTransaction();
            $ids = [];
            foreach ($creationArguments as $creationArgument) {
                $ids[] = $this->createTrackingCode($creationArgument[0],
                                                   $creationArgument[1],
                                                   $creationArgument[2],
                                                   $creationArgument[3]);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            if (!($exception instanceof CreationOfTrackingCodesFailedException)) {
                $exception = CreationOfTrackingCodesFailedException::becauseOfException($exception);
            }
            
            $this->db->rollBack();
            throw $exception;
        }
        
        return $ids;
    }
    
    
    /**
     * @param TrackingCodeId $id
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function deleteTrackingCode(TrackingCodeId $id): void
    {
        $this->db->createQueryBuilder()
            ->delete('orders_parcel_tracking_codes')
            ->where('orders_parcel_tracking_code_id = :id')
            ->setParameter('id', $id->value())
            ->executeQuery();
    }
    
    
    /**
     * @param TrackingCodeId ...$ids
     *
     * @throws DeletionOfTrackingCodesFailedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteTrackingCodes(TrackingCodeId ...$ids): void
    {
        try {
            $this->db->beginTransaction();
            foreach ($ids as $id) {
                $this->deleteTrackingCode($id);
            }
            $this->db->commit();
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw DeletionOfTrackingCodesFailedException::becauseOfException($exception);
        }
    }
}
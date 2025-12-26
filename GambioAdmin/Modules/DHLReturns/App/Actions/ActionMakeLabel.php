<?php
/* --------------------------------------------------------------
   ActionMakeLabel.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\App\Actions;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Gambio\Admin\Modules\DHLReturns\App\Data\ReturnOrderMapper;
use Gambio\Admin\Modules\DHLReturns\Services\DHLConfigurationService;
use Gambio\Admin\Modules\DHLReturns\Services\DHLReturnsService;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\ParcelServiceNotFoundException;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceReadService;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\CreationOfTrackingCodesFailedException;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeFactory;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeWriteService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Path;
use function Gambio\Core\Logging\logger;

class ActionMakeLabel extends AbstractAction
{
    private const LOGGING_NAMESPACE = 'dhlreturns';
    /**
     * @var DHLConfigurationService
     */
    private $dhlConfigurationService;
    /**
     * @var DHLReturnsService
     */
    private $DHLReturnsService;
    /**
     * @var Path
     */
    private $path;
    
    /**
     * @var TrackingCodeWriteService
     */
    private $trackingCodeWriteService;
    
    /**
     * @var TrackingCodeFactory
     */
    private $trackingCodeFactory;
    
    /**
     * @var ParcelServiceReadService
     */
    private $parcelServiceReadService;
    
    /**
     * @var Connection
     */
    private $connection;
    
    
    /**
     * @param DHLConfigurationService  $dhlConfigurationService
     * @param DHLReturnsService        $DHLReturnsService
     * @param Path                     $path
     * @param TrackingCodeWriteService $trackingCodeWriteService
     * @param TrackingCodeFactory      $trackingCodeFactory
     * @param ParcelServiceReadService $parcelServiceReadService
     * @param Connection               $connection
     */
    public function __construct(
        DHLConfigurationService $dhlConfigurationService,
        DHLReturnsService $DHLReturnsService,
        Path $path,
        TrackingCodeWriteService $trackingCodeWriteService,
        TrackingCodeFactory $trackingCodeFactory,
        ParcelServiceReadService $parcelServiceReadService,
        Connection $connection
    ) {
        $this->dhlConfigurationService  = $dhlConfigurationService;
        $this->DHLReturnsService        = $DHLReturnsService;
        $this->path                     = $path;
        $this->trackingCodeWriteService = $trackingCodeWriteService;
        $this->trackingCodeFactory      = $trackingCodeFactory;
        $this->parcelServiceReadService = $parcelServiceReadService;
        $this->connection               = $connection;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws ParcelServiceNotFoundException
     * @throws CreationOfTrackingCodesFailedException
     * @throws DBALException
     */
    public function handle(Request $request, Response $response): Response
    {
        $username = $this->dhlConfigurationService->get('returns/user');
        logger(static::LOGGING_NAMESPACE)->debug('MakeLabel handle() called, user = ' . $username);
        
        $body = $request->getParsedBody();
        logger(static::LOGGING_NAMESPACE)->debug(print_r($body, true));
        
        if (!isset($body['returnOrder'])) {
            $jsonResponse = [
                'status'  => 'ERROR',
                'message' => 'Required returnOrder property missing'
            ];
            
            return $response->withJson($jsonResponse);
        }
        
        $orderId               = (int)($body['orderId'] ?? 0);
        $inputReturnOrderArray = $body['returnOrder'];
        $originCountry         = $inputReturnOrderArray['senderAddress']['country'];
        $receiverId            = $this->dhlConfigurationService->get('returns/receiver_id/'
                                                                     . strtolower($originCountry));
        if ($receiverId === '') {
            $jsonResponse = [
                'status'  => 'ERROR',
                'message' => 'No receiver_id configured for country ' . $originCountry,
            ];
            
            return $response->withJson($jsonResponse);
        }
        
        $email           = $this->dhlConfigurationService->get('returns/email');
        $telephoneNumber = $this->dhlConfigurationService->get('returns/telephone');
        
        $returnOrder = ReturnOrderMapper::mapFromArrayToObject($inputReturnOrderArray,
                                                               $receiverId,
                                                               $email,
                                                               $telephoneNumber);
        logger(static::LOGGING_NAMESPACE)->debug(print_r($returnOrder, true));
        
        try {
            $returnOrderConfirmation = $this->DHLReturnsService->createReturnLabel($returnOrder);
            
            $timestamp = date('YmdHis');
            $hash      = substr(sha1((string)mt_rand()), 0, 16);
            $fileName  = "{$orderId}_{$returnOrderConfirmation->getShipmentNumber()}_{$timestamp}_{$hash}.pdf";
            $filePath  = "{$this->path->base()}/export/dhlreturns/{$fileName}";
            file_put_contents($filePath, $returnOrderConfirmation->getLabelData());
            
            $labelUrl = "{$this->url->base()}/export/dhlreturns/{$fileName}";
    
            $parcelServiceId = (int)$this->dhlConfigurationService->get('returns/parcel_service_id');
            if ($parcelServiceId > 0) {
                $languageCode  = $this->_getLanguageCodeByOrderId((int)$orderId);
                logger(static::LOGGING_NAMESPACE)->notice("adding tracking number {$returnOrderConfirmation->getShipmentNumber()} to order {$orderId} ({$languageCode})");
                $parcelService = $this->parcelServiceReadService->getParcelServiceById((int)$parcelServiceId);
                $trackingUrl = strtr($parcelService->url($languageCode),
                                     ['{TRACKING_NUMBER}' => rawurlencode($returnOrderConfirmation->getShipmentNumber())]);
                $shipmentType = 'Retoure';
                
                $parcelServiceDetails = $this->trackingCodeFactory->createParcelServiceDetails($parcelService->id(),
                                                                                               $languageCode,
                                                                                               $parcelService->name(),
                                                                                               $trackingUrl,
                                                                                               $parcelService->comment($languageCode),
                                                                                               $shipmentType);
    
                $this->trackingCodeWriteService->createTrackingCode((int)$orderId,
                                                               $returnOrderConfirmation->getShipmentNumber(),
                                                               $parcelServiceDetails,
                                                               true);
            }
    
            $data = [
                'status'         => 'OK',
                'shipmentNumber' => $returnOrderConfirmation->getShipmentNumber(),
                'file'           => $filePath,
                'labelUrl'       => $labelUrl,
            ];
            
            return $response->withJson($data);
        } catch (\RuntimeException $e) {
            logger(static::LOGGING_NAMESPACE)->error('Could not create label: ' . $e->getMessage());
            $data = [
                'status'  => 'ERROR',
                'message' => $e->getMessage(),
            ];
            
            return $response->withJson($data);
        }
    }
    
    
    /**
     * @param int $orderId
     *
     * @return string
     * @throws DBALException
     */
    protected function _getLanguageCodeByOrderId(int $orderId): string
    {
        $query  = 'SELECT l.code
					FROM
						orders o
                    JOIN
						languages l ON 
                            o.orders_id = :orders_id AND
                            o.language = l.directory
					ORDER BY l.status DESC
					LIMIT 1';
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':orders_id', $orderId);
        try {
            $row = $stmt->executeQuery()->fetchAssociative();
            $languageCode = $row['code'];
        } catch (DBALException $e) {
            $languageCode = 'de';
        }
        return $languageCode;
    }
}

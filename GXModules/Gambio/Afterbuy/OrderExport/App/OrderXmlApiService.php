<?php
/* --------------------------------------------------------------
   OrderXmlApiService.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\AfterbuyLogger;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotEnabledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotInstalledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyResponseException;
use GXModules\Gambio\Afterbuy\OrderExport\Model\OrderIds;
use GXModules\Gambio\Afterbuy\OrderExport\Service\AfterbuyOrderXmlApiService;
use GXModules\Gambio\Afterbuy\OrderExport\Service\Data\AfterbuyOrderXmlApiRepository;
use Throwable;

/**
 * Class OrderXmlApiService
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data
 */
class OrderXmlApiService implements AfterbuyOrderXmlApiService
{
    /**
     * @var AfterbuyOrderXmlApiRepository
     */
    private AfterbuyOrderXmlApiRepository $repository;
    
    
    /**
     * @var AfterbuyLogger
     */
    private AfterbuyLogger $logger;
    
    
    /**
     * OrderXmlApiService constructor.
     *
     * @param AfterbuyOrderXmlApiRepository $repository
     * @param AfterbuyLogger                $logger
     */
    public function __construct(AfterbuyOrderXmlApiRepository $repository, AfterbuyLogger $logger)
    {
        $this->repository = $repository;
        $this->logger     = $logger;
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateOrderViaXmlApi(OrderId $orderId): void
    {
        try {
            $request = $this->repository->getRequest(new OrderIds($orderId));
        } catch (AfterbuyNotEnabledException|AfterbuyNotInstalledException $e) {
            $message = "The afterbuy module is not installed or not enabled.\nError: {$e->getMessage()}";
            $context = array_merge(['orderId' => $orderId->orderId()], $this->getContextFromThrowable($e));
            $this->logger->notice($message, $context);
            
            return;
        }
        try {
            $this->repository->send($request, $orderId);
        } catch (AfterbuyResponseException $e) {
            $message               = "Failed to send the xml request to the Afterbuy XML-API.\nError: {$e->getMessage()}";
            $context               = array_merge(['orderId' => $orderId->orderId()],
                                                 $this->getContextFromThrowable($e));
            $context['xmlRequest'] = $request->toXmlString();
            $this->logger->error($message, $context);
        }
    }
    
    
    /**
     * Creates a context array from any throwable.
     * Serializes the throwable to an array.
     *
     * @param Throwable $throwable
     *
     * @return array
     */
    private function getContextFromThrowable(Throwable $throwable): array
    {
        return [
            'message' => $throwable->getMessage(),
            'code'    => $throwable->getCode(),
            'file'    => $throwable->getFile(),
            'line'    => $throwable->getLine(),
            'trace'   => $throwable->getTrace(),
        ];
    }
}
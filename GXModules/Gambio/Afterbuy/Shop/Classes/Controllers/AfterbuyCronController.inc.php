<?php
/* --------------------------------------------------------------
   AfterbuyCronController.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Psr\Log\LoggerInterface;

/**
 * Class AfterbuyCronController
 *
 * @package GXModules\Gambio\Afterbuy\Shop\Classes\Controllers
 */
class AfterbuyCronController extends HttpViewController
{
    
    const MAX_ORDERS_PER_RUN = 20;
    
    
    /**
     * @var ConfigurationStorage
     */
    protected ConfigurationStorage $configuration;
    
    
    /**
     * @var int
     */
    protected int $maxOrdersPerRun;
    
    
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;
    
    
    /**
     * @return HttpControllerResponseInterface
     */
    public function actionDefault(): HttpControllerResponseInterface
    {
        return parent::actionDefault();
    }
    
    
    /**
     * @throws AfterbuyException
     */
    public function init(): void
    {
        $this->logger    = MainFactory::create('AfterbuyLogger');
        $moduleInstalled = (bool)gm_get_conf('MODULE_CENTER_GAMBIOAFTERBUY_INSTALLED');
        if ($moduleInstalled === false) {
            $this->logger->notice('Cron endpoint called, but module is not installed!');
            throw new AfterbuyException('Afterbuy module not installed');
        }
        
        $this->configuration = MainFactory::create('GambioAfterbuyConfigurationStorage');
        $moduleActive        = (bool)$this->configuration->get('active');
        if ($moduleActive === false) {
            $this->logger->notice('Cron endpoint called, but module is not active!');
            throw new AfterbuyException('Afterbuy module not active');
        }
        
        $this->maxOrdersPerRun = self::MAX_ORDERS_PER_RUN;
    }
    
    
    /**
     * @return JsonHttpControllerResponse
     * @throws Exception
     */
    public function actionSendOrders(): JsonHttpControllerResponse
    {
        $allAsPaid    = $this->configuration->get('order_status_paid') === '-1';
        $responseData = [];
        $headers      = [];
        
        if ($this->_getQueryParameter('key') !== LogControl::get_secure_token()) {
            $responseData['error'] = [
                'message' => 'unauthorized access',
            ];
            $headers[]             = 'HTTP/1.1 403 Not Authorized';
            $response              = MainFactory::create('JsonHttpControllerResponse', $responseData, $headers);
            $this->logger->notice('Cron endpoint blocking unauthorized access');
            
            return $response;
        }
        
        $this->logger->notice('Cron endpoint sending up to ' . $this->maxOrdersPerRun . ' orders');
        $db                = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $queuedOrdersQuery = $db->select('orders_id')
            ->where('afterbuy_success', '0')
            ->order_by('orders_id DESC')
            ->limit($this->maxOrdersPerRun)
            ->get('orders');
        $queuedOrdersIds   = [];
        foreach ($queuedOrdersQuery->result() as $queuedOrder) {
            $queuedOrdersIds[] = $queuedOrder->orders_id;
        }
        sort($queuedOrdersIds);
        $responseData['processedOrders'] = [];
        $responseData['unpaidOrders']    = [];
        $responseData['errors']          = [];
        foreach ($queuedOrdersIds as $orderId) {
            $afterbuyOrderSender = MainFactory::create('AfterbuyOrderSender', $orderId);
            if ($allAsPaid || $afterbuyOrderSender->orderIsPaid()) {
                try {
                    $afterbuyOrderSender->processOrder();
                    $responseData['processedOrders'][] = $orderId;
                } catch (AfterbuyException $e) {
                    $responseData['errors'][$orderId] = $e->getMessage();
                }
            } else {
                $responseData['unpaidOrders'][] = $orderId;
            }
        }
        
        $resultLog = sprintf("Cron job summary:\nProcessed orders: %s\nUnpaid orders (skipped): %s",
                             implode(', ', $responseData['processedOrders']),
                             implode(', ', $responseData['unpaidOrders']));
        if (!empty($responseData['errors'])) {
            $resultLog .= sprintf("\nOrders with errors in transmission:\n");
            foreach ($responseData['errors'] as $errorOrder => $errorMessage) {
                $resultLog .= sprintf("%d - %s\n", $errorOrder, $errorMessage);
            }
        }
        $this->logger->notice($resultLog);
        
        $headers[] = 'Content-Type: application/json';
        $response  = MainFactory::create('JsonHttpControllerResponse', $responseData, $headers);
        
        return $response;
    }
}

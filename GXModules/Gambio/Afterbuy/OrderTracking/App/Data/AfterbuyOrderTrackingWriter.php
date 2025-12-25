<?php
/* --------------------------------------------------------------
   AfterbuyOrderTrackingWriter.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderTracking\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use GXModules\Gambio\Afterbuy\OrderTracking\Exceptions\SyncTrackingLinksFailedException;

/**
 * Class AfterbuyOrderTrackingWriter
 *
 * @package GXModules\Gambio\Afterbuy\OrderTracking\App\Data
 */
class AfterbuyOrderTrackingWriter
{
    private const CONFIGURATION_NAMESPACE = 'modules/gambio/afterbuy';
    
    
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * @var ConfigurationStorageRepository
     */
    private ConfigurationStorageRepository $storage;
    
    
    /**
     * AfterbuyOrderTrackingWriter constructor.
     *
     * @param Connection                            $connection
     * @param ConfigurationStorageRepositoryBuilder $builder
     */
    public function __construct(
        Connection                            $connection,
        ConfigurationStorageRepositoryBuilder $builder
    ) {
        $this->connection = $connection;
        $this->storage    = $builder->build(self::CONFIGURATION_NAMESPACE);
    }
    
    
    /**
     * Updates the order status to the afterbuy configuration 'order_status_tracking_sync'.
     *
     * Additionally, updates the 'orders_status_history' table. By using the raw
     * database connection, only the database records are updated, but nothing else, like events, are triggered.
     *
     * @param int $orderId
     *
     * @throws SyncTrackingLinksFailedException
     */
    public function updateOrderStatusWithTrackingConfiguration(int $orderId): void
    {
        $configuration = $this->storage->get('order_status_tracking_sync');
        if (empty($configuration) || $configuration === '-1') {
            return;
        }
        $data  = ['orders_status' => $configuration];
        $where = ['orders_id' => $orderId];
        
        try {
            $this->connection->update('orders', $data, $where);
            
            $data = [
                'orders_id'        => $orderId,
                'orders_status_id' => $configuration,
                'date_added'       => date('Y-m-d H:i:s'),
                'comments'         => 'Tracking-Code Synchronization',
            ];
            $this->connection->insert('orders_status_history', $data);
        } catch (Exception $e) {
            $message = "Database error when updating the order status to '$configuration' for the order with id '$orderId'.\nError: {$e->getMessage()}";
            throw new SyncTrackingLinksFailedException($message, $e->getCode(), $e);
        }
    }
}
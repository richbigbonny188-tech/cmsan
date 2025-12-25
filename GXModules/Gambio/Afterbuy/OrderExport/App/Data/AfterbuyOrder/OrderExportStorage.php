<?php
/* --------------------------------------------------------------
   OrderExportStorage.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder;

use DateTimeImmutable;
use Exception;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use GXModules\Gambio\Afterbuy\OrderTracking\App\Data\AfterbuyOrderTrackingRepository;

/**
 * Class OrderExportStorage
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class OrderExportStorage
{
    private const DATE_FORMAT             = 'd.m.Y H:i:s';
    private const CONFIGURATION_NAMESPACE = 'modules/gambio/afterbuy';
    
    /**
     * @var ConfigurationStorageRepository
     */
    private ConfigurationStorageRepository $storage;
    
    
    /**
     * OrderExportWriter constructor.
     *
     * @param ConfigurationStorageRepositoryBuilder $builder
     */
    public function __construct(ConfigurationStorageRepositoryBuilder $builder)
    {
        $this->storage = $builder->build(self::CONFIGURATION_NAMESPACE);
    }
    
    
    /**
     * Updates the afterbuy configuration 'last_tracking_sync' to now.
     *
     * @return void
     */
    public function updateLastTrackingSyncTimeToNow(): void
    {
        $now   = new DateTimeImmutable();
        $value = $now->format('c');
        
        $this->storage->set('last_tracking_sync', $value);
    }
    
    
    /**
     * Returns the last tracking synchronization date as string in format 'd.m.Y H:i:s'.
     *
     * @return string
     */
    public function getLastTrackingSyncTime(): string
    {
        $lastTrackingSyncConfig = $this->storage->get(AfterbuyOrderTrackingRepository::CONFIGURATION_AB_TRACKING_SYNC);
        try {
            return (new DateTimeImmutable($lastTrackingSyncConfig))->format(self::DATE_FORMAT);
        } catch (Exception $e) {
            return date(self::DATE_FORMAT);
        }
    }
}
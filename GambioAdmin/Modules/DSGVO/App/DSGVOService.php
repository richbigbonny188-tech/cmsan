<?php
/* --------------------------------------------------------------
 DSGVOService.php 2021-05-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DSGVO\App;

use Gambio\Admin\Modules\DSGVO\Services\DSGVOLogger;
use Gambio\Admin\Modules\DSGVO\Services\DSGVORepository;
use Gambio\Admin\Modules\DSGVO\Services\DSGVOService as Service;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\UserConfiguration\Services\UserConfigurationService;

/**
 * Class DSGVOService
 * @package Gambio\Admin\Modules\DSGVO\App
 */
class DSGVOService implements Service
{
    /**
     * @var DSGVORepository
     */
    private $repository;
    
    /**
     * @var DSGVOLogger
     */
    private $logger;
    
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    /**
     * @var UserConfigurationService
     */
    private $userConfigurationService;
    
    
    /**
     * DSGVOService constructor.
     *
     * @param DSGVORepository          $repository
     * @param DSGVOLogger              $logger
     * @param UserPreferences          $userPreferences
     * @param UserConfigurationService $userConfigurationService
     */
    public function __construct(
        DSGVORepository $repository,
        DSGVOLogger $logger,
        UserPreferences $userPreferences,
        UserConfigurationService $userConfigurationService
    ) {
        $this->repository               = $repository;
        $this->logger                   = $logger;
        $this->userPreferences          = $userPreferences;
        $this->userConfigurationService = $userConfigurationService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function logAdminActivity(): void
    {
        $log = $this->repository->getAdminActivityLog();
        $this->logger->logAdminActivity($log);
    }
    
    
    /**
     * @inheritDoc
     */
    public function isAdminLoggingEnabled(): bool
    {
        if (!$this->userPreferences->isAuthenticated()) {
            return false;
        }
        
        $configuration = $this->userConfigurationService->getValue($this->userPreferences->userId(),
                                                                   'admin_activity_status');
        
        return (bool)$configuration;
    }
}
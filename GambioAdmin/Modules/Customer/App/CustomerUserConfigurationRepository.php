<?php
/*--------------------------------------------------------------
   CustomerUserConfigurationRepository.php 2022-09-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\UserConfiguration\Services\UserConfigurationService;

/**
 * Class CustomerUserConfigurationRepository
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class CustomerUserConfigurationRepository
{
    private const PROMPT_CONFIGURATION_KEY = 'SHOW_WARNING_ON_LOGIN_AS_CUSTOMER';
    private const CUSTOMERS_PER_PAGE_CONFIGURATION_KEY = 'CUSTOMERS_PER_PAGE';
    
    private UserConfigurationService $service;
    private int                      $userId;
    
    
    /**
     * @param UserConfigurationService $service
     * @param UserPreferences          $preferences
     */
    public function __construct(
        UserConfigurationService $service,
        UserPreferences          $preferences
    ) {
        $this->service = $service;
        $this->userId  = $preferences->userId();
    }
    
    
    /**
     * @return bool
     */
    public function getShowWarningOnLoginAsCustomerValue(): bool
    {
        return $this->service->getValue($this->userId, static::PROMPT_CONFIGURATION_KEY, 'true') === 'true';
    }
    
    
    /**
     * @param bool $value
     *
     * @return void
     */
    public function setShowWarningOnLoginAsCustomerValue(bool $value): void
    {
        $this->service->storeConfiguration($this->userId, static::PROMPT_CONFIGURATION_KEY, $value ? 'true' : 'false');
    }
    
    
    /**
     * @return int
     */
    public function getCustomersPerPageValue(): int
    {
        return (int)$this->service->getValue($this->userId, static::CUSTOMERS_PER_PAGE_CONFIGURATION_KEY, '20');
    }
    
    
    /**
     * @param int $value
     *
     * @return void
     */
    public function setCustomersPerPageValue(int $value): void
    {
        $this->service->storeConfiguration($this->userId, static::CUSTOMERS_PER_PAGE_CONFIGURATION_KEY, (string)$value);
    }
}
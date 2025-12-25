<?php
/*------------------------------------------------------------------------------
  DashboardData.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\ValueObjects;

use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PreferredCategory;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PreferredPeriod;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Result;
use Gambio\Admin\Modules\SetupWizard\Collections\SetupWizardStepCollection;

/**
 * Class DashboardData
 * @package Gambio\Admin\Modules\Dashboard\ValueObjects
 */
class DashboardData implements \JsonSerializable
{
    /**
     * @var string
     */
    private $shopKey;
    /**
     * @var string
     */
    private $shopUrl;
    
    /**
     * @var string
     */
    private $shopVersion;
    
    /**
     * @var Result
     */
    protected $statistics;
    
    /**
     * @var SetupWizardStepCollection
     */
    protected $steps;
    
    /**
     * @var bool
     */
    private $isExternalSocialMediaEmbedsAllowed;
    
    /**
     * @var PreferredCategory
     */
    private $userPreferredCategory;
    
    /**
     * @var PreferredPeriod
     */
    private $userPreferredPeriod;
    
    /**
     * @var ShopOrigin
     */
    private $shopOrigin;
    
    
    /**
     * DashboardData constructor.
     *
     * @param string                    $shopKey
     * @param string                    $shopUrl
     * @param string                    $shopVersion
     * @param Result                    $statistics
     * @param SetupWizardStepCollection $steps
     * @param bool                      $isExternalSocialMediaEmbedsAllowed
     * @param PreferredCategory         $userPreferredCategory
     * @param PreferredPeriod           $userPreferredPeriod
     * @param ShopOrigin                $shopOrigin
     */
    public function __construct(
        string $shopKey,
        string $shopUrl,
        string $shopVersion,
        Result $statistics,
        SetupWizardStepCollection $steps,
        bool $isExternalSocialMediaEmbedsAllowed,
        PreferredCategory $userPreferredCategory,
        PreferredPeriod $userPreferredPeriod,
        ShopOrigin $shopOrigin
    ) {
        $this->statistics                         = $statistics;
        $this->steps                              = $steps;
        $this->shopKey                            = $shopKey;
        $this->shopUrl                            = $shopUrl;
        $this->shopVersion                        = $shopVersion;
        $this->isExternalSocialMediaEmbedsAllowed = $isExternalSocialMediaEmbedsAllowed;
        $this->userPreferredCategory              = $userPreferredCategory;
        $this->userPreferredPeriod                = $userPreferredPeriod;
        $this->shopOrigin                         = $shopOrigin;
    }
    
    
    /**
     * @return PreferredCategory
     */
    public function userPreferredCategory(): PreferredCategory
    {
        return $this->userPreferredCategory;
    }
    
    
    /**
     * @return PreferredPeriod
     */
    public function userPreferredPeriod(): PreferredPeriod
    {
        return $this->userPreferredPeriod;
    }
    
    
    /**
     * @return string
     */
    public function shopKey(): string
    {
        return $this->shopKey;
    }
    
    
    /**
     * @return string
     */
    public function shopUrl(): string
    {
        return $this->shopUrl;
    }
    
    
    /**
     * @return string
     */
    public function shopVersion(): string
    {
        return $this->shopVersion;
    }
    
    
    /**
     * @return Result
     */
    public function statistics(): Result
    {
        return $this->statistics;
    }
    
    
    /**
     * @return ShopOrigin
     */
    public function shopOrigin(): ShopOrigin
    {
        return $this->shopOrigin;
    }
    
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'shopKey'               => $this->shopKey(),
            'shopUrl'               => $this->shopUrl(),
            'shopVersion'           => $this->shopVersion(),
            'categories'            => $this->statistics->categories(),
            'userPreferredCategory' => $this->userPreferredCategory->jsonSerialize(),
            'userPreferredPeriod'   => $this->userPreferredPeriod->jsonSerialize(),
            'statistics'            => $this->statistics->statistics(),
            'setupData'             => $this->steps,
            'loadExternalContents'  => $this->isExternalSocialMediaEmbedsAllowed,
            'shopOrigin'            => $this->shopOrigin(),
        ];
    }
}
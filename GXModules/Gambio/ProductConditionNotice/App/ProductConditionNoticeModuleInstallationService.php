<?php
/* --------------------------------------------------------------
   ModuleInstallationService.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\ProductConditionNotice\App;

use GXModules\Gambio\ProductConditionNotice\Services\ConditionNoticeConfigurationService;
use GXModules\Gambio\ProductConditionNotice\Services\ProductConditionNoticeModuleInstallationService as ProductConditionNoticeModuleInstallationServiceInterface;
use GXModules\Gambio\ProductConditionNotice\Services\WarrantyNoticeConfigurationService;

/**
 * Class ModuleInstallationService
 *
 * @package GXModules\Gambio\ProductConditionNotice\App
 */
class ProductConditionNoticeModuleInstallationService
    implements ProductConditionNoticeModuleInstallationServiceInterface
{
    /**
     * @var ConditionNoticeConfigurationService
     */
    private $conditionNoticeService;
    
    
    /**
     * @var WarrantyNoticeConfigurationService
     */
    private $warrantyNoticeService;
    
    
    /**
     * @param ConditionNoticeConfigurationService $conditionNoticeService
     * @param WarrantyNoticeConfigurationService  $warrantyNoticeService
     */
    public function __construct(
        ConditionNoticeConfigurationService $conditionNoticeService,
        WarrantyNoticeConfigurationService  $warrantyNoticeService
    ) {
        $this->conditionNoticeService = $conditionNoticeService;
        $this->warrantyNoticeService  = $warrantyNoticeService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function installModule(): void
    {
        $this->conditionNoticeService->disableConditionNotice();
        $this->conditionNoticeService->setConditionNoticeAsNotMandatory();
        
        $defaultProductConditionNoticeTexts = require __DIR__ . '/Data/DefaultProductConditionNoticeTexts.php';
        foreach ($defaultProductConditionNoticeTexts as $languageCode => $defaultText) {
            $this->conditionNoticeService->setConditionNoticeText($languageCode, $defaultText);
        }
        
        $this->warrantyNoticeService->disableWarrantyNotice();
        $this->warrantyNoticeService->setWarrantyNoticeAsNotMandatory();
        
        $defaultProductWarrantyNoticeTexts = require __DIR__ . '/Data/DefaultProductWarrantyNoticeTexts.php';
        foreach ($defaultProductWarrantyNoticeTexts as $languageCode => $defaultText) {
            $this->warrantyNoticeService->setWarrantyNoticeText($languageCode, $defaultText);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function uninstallModule(): void
    {
        $this->conditionNoticeService->deleteStoredConfiguration();
        $this->warrantyNoticeService->deleteStoredConfiguration();
    }
}
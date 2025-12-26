<?php
/* --------------------------------------------------------------
   ProductConditionNoticeStaticServiceFactory.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\ProductConditionNotice\Services;

use ConfigurationStorage;
use GXModules\Gambio\ProductConditionNotice\App\ConditionNoticeConfigurationService as ConditionNoticeService;
use GXModules\Gambio\ProductConditionNotice\App\ProductConditionNoticeModuleInstallationService as ModuleInstallationService;
use GXModules\Gambio\ProductConditionNotice\App\ProductConditionNoticeTextPhrasePhraseService as TextPhraseService;
use GXModules\Gambio\ProductConditionNotice\App\ProductConditionNoticeUsedProductService as UsedProductService;
use GXModules\Gambio\ProductConditionNotice\App\WarrantyNoticeConfigurationService as WarrantyNoticeService;
use MainFactory;
use StaticGXCoreLoader;

/**
 * Class ProductConditionNoticeStaticServiceFactory
 *
 * @package GXModules\Gambio\ProductConditionNotice\Services
 */
class ProductConditionNoticeStaticServiceFactory
{
    /**
     * @var ConditionNoticeService
     */
    private static $conditionNoticeService;
    
    /**
     * @var WarrantyNoticeService
     */
    private static $warrantyNoticeService;
    
    /**
     * @var ModuleInstallationService
     */
    private static $moduleInstallationService;
    
    /**
     * @var UsedProductService
     */
    private static $usedProductService;
    
    /**
     * @var TextPhraseService
     */
    private static $textPhraseService;
    
    
    /**
     * Builds and returns the condition notice service.
     *
     * @return ConditionNoticeConfigurationService
     */
    public static function createConditionNoticeService(): ConditionNoticeConfigurationService
    {
        if (static::$conditionNoticeService === null) {
            $configurationStorage = MainFactory::create(ConfigurationStorage::class,
                                                        'modules/Gambio/ProductConditionNotice');
            
            static::$conditionNoticeService = new ConditionNoticeService($configurationStorage);
        }
        
        return static::$conditionNoticeService;
    }
    
    
    /**
     * Builds and returns the warranty notice service.
     *
     * @return WarrantyNoticeService
     */
    public static function createWarrantyNoticeService(): WarrantyNoticeService
    {
        if (static::$warrantyNoticeService === null) {
            $configurationStorage = MainFactory::create(ConfigurationStorage::class,
                                                        'modules/Gambio/ProductConditionNotice');
            
            static::$warrantyNoticeService = new WarrantyNoticeService($configurationStorage);
        }
        
        return static::$warrantyNoticeService;
    }
    
    
    /**
     * Builds and returns the module installation service.
     *
     * @return ProductConditionNoticeModuleInstallationService
     */
    public static function createModuleInstallationService(): ProductConditionNoticeModuleInstallationService
    {
        if (static::$moduleInstallationService === null) {
            $conditionNoticeService = self::createConditionNoticeService();
            $warrantyNoticeService  = self::createWarrantyNoticeService();
            
            static::$moduleInstallationService = new ModuleInstallationService($conditionNoticeService,
                                                                               $warrantyNoticeService);
        }
        
        return static::$moduleInstallationService;
    }
    
    
    /**
     * Builds and returns the used products service.
     *
     * @return ProductConditionNoticeUsedProductService
     */
    public static function createUsedProductService(): ProductConditionNoticeUsedProductService
    {
        if (static::$usedProductService === null) {
            $queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();
            
            static::$usedProductService = new UsedProductService($queryBuilder);
        }
        
        return static::$usedProductService;
    }
    
    
    /**
     * Builds and returns the text phrase service.
     *
     * @return ProductConditionNoticeTextPhraseService
     */
    public static function createTextPhraseService(): ProductConditionNoticeTextPhraseService
    {
        if (static::$textPhraseService === null) {
            $textManager = MainFactory::create_object('LanguageTextManager');
            
            static::$textPhraseService = new TextPhraseService($textManager);
        }
        
        return static::$textPhraseService;
    }
}
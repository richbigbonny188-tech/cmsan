<?php
/* --------------------------------------------------------------
   ConfigurationService.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\ProductConditionNotice\App;

use ConfigurationStorage;
use GXModules\Gambio\ProductConditionNotice\Services\WarrantyNoticeConfigurationService as ConfigurationServiceInterface;

/**
 * Class ConfigurationService
 *
 * @package GXModules\Gambio\ProductWarrantyNotice\App
 */
class WarrantyNoticeConfigurationService implements ConfigurationServiceInterface
{
    private const SHOW_PRODUCT_CONDITION_NOTICE         = 'show_product_warranty_notice';
    private const PRODUCT_CONDITION_NOTICE_IS_MANDATORY = 'product_warranty_notice_is_mandatory';
    private const PRODUCT_CONDITION_NOTICE_TEXT         = 'product_warranty_notice_text/';
    
    
    /**
     * @var ConfigurationStorage
     */
    private $configurationStorage;
    
    
    /**
     * @param ConfigurationStorage $configurationStorage
     */
    public function __construct(ConfigurationStorage $configurationStorage)
    {
        $this->configurationStorage = $configurationStorage;
    }
    
    
    /**
     * @inheritDoc
     */
    public function enableWarrantyNotice(): void
    {
        $this->configurationStorage->set(self::SHOW_PRODUCT_CONDITION_NOTICE, '1');
    }
    
    
    /**
     * @inheritDoc
     */
    public function disableWarrantyNotice(): void
    {
        $this->configurationStorage->set(self::SHOW_PRODUCT_CONDITION_NOTICE, '0');
    }
    
    
    /**
     * @inheritDoc
     */
    public function setWarrantyNoticeAsMandatory(): void
    {
        $this->configurationStorage->set(self::PRODUCT_CONDITION_NOTICE_IS_MANDATORY, '1');
    }
    
    
    /**
     * @inheritDoc
     */
    public function setWarrantyNoticeAsNotMandatory(): void
    {
        $this->configurationStorage->set(self::PRODUCT_CONDITION_NOTICE_IS_MANDATORY, '0');
    }
    
    
    /**
     * @inheritDoc
     */
    public function setWarrantyNoticeText(string $languageCode, string $text): void
    {
        $this->configurationStorage->set(self::PRODUCT_CONDITION_NOTICE_TEXT . strtoupper($languageCode),
                                         $text);
    }
    
    
    /**
     * @inheritDoc
     */
    public function setWarrantyNoticeTexts(array $texts): void
    {
        foreach ($texts as $languageCode => $text) {
            $this->setWarrantyNoticeText($languageCode, $text);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function isWarrantyNoticeEnabled(): bool
    {
        return $this->configurationStorage->get(self::SHOW_PRODUCT_CONDITION_NOTICE) === '1';
    }
    
    
    /**
     * @inheritDoc
     */
    public function isWarrantyNoticeMandatory(): bool
    {
        return $this->configurationStorage->get(self::PRODUCT_CONDITION_NOTICE_IS_MANDATORY) === '1';
    }
    
    
    /**
     * @inheritDoc
     */
    public function getWarrantyNoticeText(string $languageCode): string
    {
        $text = $this->configurationStorage->get(self::PRODUCT_CONDITION_NOTICE_TEXT . strtoupper($languageCode));
        
        return ($text !== false) ? $text : '';
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllWarrantyNoticeTexts(): array
    {
        $noticeTexts = [];
        foreach ($this->configurationStorage->get_all(self::PRODUCT_CONDITION_NOTICE_TEXT) as $configKey => $noticeText) {
            $languageCode                           = substr($configKey, strlen(self::PRODUCT_CONDITION_NOTICE_TEXT));
            $noticeTexts[strtoupper($languageCode)] = $noticeText;
        }
        
        return $noticeTexts;
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteStoredConfiguration(): void
    {
        $this->configurationStorage->delete(self::SHOW_PRODUCT_CONDITION_NOTICE);
        $this->configurationStorage->delete(self::PRODUCT_CONDITION_NOTICE_IS_MANDATORY);
        $this->configurationStorage->delete_all(self::PRODUCT_CONDITION_NOTICE_TEXT);
    }
}
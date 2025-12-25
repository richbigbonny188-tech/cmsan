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
use GXModules\Gambio\ProductConditionNotice\Services\ConditionNoticeConfigurationService as ConfigurationServiceInterface;

/**
 * Class ConfigurationService
 *
 * @package GXModules\Gambio\ProductConditionNotice\App
 */
class ConditionNoticeConfigurationService implements ConfigurationServiceInterface
{
    private const SHOW_PRODUCT_CONDITION_NOTICE         = 'show_product_condition_notice';
    private const PRODUCT_CONDITION_NOTICE_IS_MANDATORY = 'product_condition_notice_is_mandatory';
    private const PRODUCT_CONDITION_NOTICE_TEXT         = 'product_condition_notice_text/';
    
    
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
    public function enableConditionNotice(): void
    {
        $this->configurationStorage->set(self::SHOW_PRODUCT_CONDITION_NOTICE, '1');
    }
    
    
    /**
     * @inheritDoc
     */
    public function disableConditionNotice(): void
    {
        $this->configurationStorage->set(self::SHOW_PRODUCT_CONDITION_NOTICE, '0');
    }
    
    
    /**
     * @inheritDoc
     */
    public function setConditionNoticeAsMandatory(): void
    {
        $this->configurationStorage->set(self::PRODUCT_CONDITION_NOTICE_IS_MANDATORY, '1');
    }
    
    
    /**
     * @inheritDoc
     */
    public function setConditionNoticeAsNotMandatory(): void
    {
        $this->configurationStorage->set(self::PRODUCT_CONDITION_NOTICE_IS_MANDATORY, '0');
    }
    
    
    /**
     * @inheritDoc
     */
    public function setConditionNoticeText(string $languageCode, string $text): void
    {
        $this->configurationStorage->set(self::PRODUCT_CONDITION_NOTICE_TEXT . strtoupper($languageCode),
                                         $text);
    }
    
    
    /**
     * @inheritDoc
     */
    public function setConditionNoticeTexts(array $texts): void
    {
        foreach ($texts as $languageCode => $text) {
            $this->setConditionNoticeText($languageCode, $text);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function isConditionNoticeEnabled(): bool
    {
        return $this->configurationStorage->get(self::SHOW_PRODUCT_CONDITION_NOTICE) === '1';
    }
    
    
    /**
     * @inheritDoc
     */
    public function isConditionNoticeMandatory(): bool
    {
        return $this->configurationStorage->get(self::PRODUCT_CONDITION_NOTICE_IS_MANDATORY) === '1';
    }
    
    
    /**
     * @inheritDoc
     */
    public function getConditionNoticeText(string $languageCode): string
    {
        $text = $this->configurationStorage->get(self::PRODUCT_CONDITION_NOTICE_TEXT . strtoupper($languageCode));
        
        return ($text !== false) ? $text : '';
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllConditionNoticeTexts(): array
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
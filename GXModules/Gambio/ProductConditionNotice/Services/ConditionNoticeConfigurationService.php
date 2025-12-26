<?php
/* --------------------------------------------------------------
   ConditionNoticeConfigurationService.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\ProductConditionNotice\Services;

/**
 * Interface ConditionNoticeConfigurationService
 *
 * @package GXModules\Gambio\ProductConditionNotice\Services
 */
interface ConditionNoticeConfigurationService
{
    /**
     * Enables the product condition notice in the checkout.
     */
    public function enableConditionNotice(): void;
    
    
    /**
     * Disables the product condition notice in the checkout.
     */
    public function disableConditionNotice(): void;
    
    
    /**
     * Marks the product condition notice as mandatory.
     */
    public function setConditionNoticeAsMandatory(): void;
    
    
    /**
     * Marks the product condition notice as not mandatory.
     */
    public function setConditionNoticeAsNotMandatory(): void;
    
    
    /**
     * Sets the product condition notice text shown in the checkout process.
     *
     * @param string $languageCode
     * @param string $text
     */
    public function setConditionNoticeText(string $languageCode, string $text): void;
    
    
    /**
     * Sets the product condition notice texts shown in the checkout process.
     * The provided array keys need to be language codes.
     *
     * @param array<string, string> $texts
     */
    public function setConditionNoticeTexts(array $texts): void;
    
    
    /**
     * Returns the "product condition notice is enabled" state.
     *
     * @return bool
     */
    public function isConditionNoticeEnabled(): bool;
    
    
    /**
     * Returns the "product condition notice is mandatory" state.
     *
     * @return bool
     */
    public function isConditionNoticeMandatory(): bool;
    
    
    /**
     * Returns the product condition notice text shown in the checkout process.
     *
     * @param string $languageCode
     *
     * @return string
     */
    public function getConditionNoticeText(string $languageCode): string;
    
    
    /**
     * Returns all product condition notice texts.
     *
     * @return string[]
     */
    public function getAllConditionNoticeTexts(): array;
    
    
    /**
     * Deletes all stored module configurations.
     */
    public function deleteStoredConfiguration(): void;
}
<?php
/* --------------------------------------------------------------
   WarrantyNoticeConfigurationService.php 2021-12-06
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
 * Interface WarrantyNoticeConfigurationService
 *
 * @package GXModules\Gambio\ProductWarrantyNotice\Services
 */
interface WarrantyNoticeConfigurationService
{
    /**
     * Enables the product warranty notice in the checkout.
     */
    public function enableWarrantyNotice(): void;
    
    
    /**
     * Disables the product warranty notice in the checkout.
     */
    public function disableWarrantyNotice(): void;
    
    
    /**
     * Marks the product warranty notice as mandatory.
     */
    public function setWarrantyNoticeAsMandatory(): void;
    
    
    /**
     * Marks the product warranty notice as not mandatory.
     */
    public function setWarrantyNoticeAsNotMandatory(): void;
    
    
    /**
     * Sets the product warranty notice text shown in the checkout process.
     *
     * @param string $languageCode
     * @param string $text
     */
    public function setWarrantyNoticeText(string $languageCode, string $text): void;
    
    
    /**
     * Sets the product warranty notice texts shown in the checkout process.
     * The provided array keys need to be language codes.
     *
     * @param array<string, string> $texts
     */
    public function setWarrantyNoticeTexts(array $texts): void;
    
    
    /**
     * Returns the "product warranty notice is enabled" state.
     *
     * @return bool
     */
    public function isWarrantyNoticeEnabled(): bool;
    
    
    /**
     * Returns the "product warranty notice is mandatory" state.
     *
     * @return bool
     */
    public function isWarrantyNoticeMandatory(): bool;
    
    
    /**
     * Returns the product warranty notice text shown in the checkout process.
     *
     * @param string $languageCode
     *
     * @return string
     */
    public function getWarrantyNoticeText(string $languageCode): string;
    
    
    /**
     * Returns all product warranty notice texts.
     *
     * @return string[]
     */
    public function getAllWarrantyNoticeTexts(): array;
    
    
    /**
     * Deletes all stored module configurations.
     */
    public function deleteStoredConfiguration(): void;
}
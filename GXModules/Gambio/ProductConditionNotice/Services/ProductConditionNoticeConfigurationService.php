<?php
/* --------------------------------------------------------------
   ProductConditionNoticeConfigurationService.php 2021-12-06
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
 * Interface ProductConditionNoticeConfigurationService
 *
 * @package GXModules\Gambio\ProductConditionNotice\Services
 */
interface ProductConditionNoticeConfigurationService
{
    /**
     * Enables this module and shows the product condition notice in the checkout.
     */
    public function enableModule(): void;
    
    
    /**
     * Disables this module and hides the product condition notice in the checkout.
     */
    public function disableModule(): void;
    
    
    /**
     * Marks the confirmation of the product condition notice as mandatory.
     */
    public function setConfirmationAsMandatory(): void;
    
    
    /**
     * Marks the confirmation of the product condition notice as not mandatory.
     */
    public function setConfirmationAsNotMandatory(): void;
    
    
    /**
     * Sets the product condition notice text shown in the checkout process.
     *
     * @param string $languageCode
     * @param string $text
     */
    public function setNoticeText(string $languageCode, string $text): void;
    
    
    /**
     * Sets the product condition notice texts shown in the checkout process.
     * The provided array keys need to be language codes.
     *
     * @param array<string, string> $texts
     */
    public function setNoticeTexts(array $texts): void;
    
    
    /**
     * Returns the module-is-enabled state.
     *
     * @return bool
     */
    public function isModuleEnabled(): bool;
    
    
    /**
     * Returns the confirmation-is-mandatory state.
     *
     * @return bool
     */
    public function isConfirmationMandatory(): bool;
    
    
    /**
     * Returns the product condition notice text shown in the checkout process.
     *
     * @param string $languageCode
     *
     * @return string
     */
    public function getNoticeText(string $languageCode): string;
    
    
    /**
     * Returns all product condition notice texts.
     *
     * @return string[]
     */
    public function getAllNoticeTexts(): array;
    
    
    /**
     * Deletes all stored module configurations.
     */
    public function deleteStoredConfiguration(): void;
}
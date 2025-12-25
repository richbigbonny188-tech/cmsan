<?php
/* --------------------------------------------------------------
   CheckoutSessionTrait.php 2021-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

trait CheckoutSessionTrait
{
    /**
     * @param string $value
     *
     * @return void
     */
    protected function setConditionNoticeSession(string $value): void
    {
        $_SESSION['condition_notice'] = $value;
    }
    
    
    /**
     * @return string|null
     */
    protected function getConditionNoticeSessionValue(): ?string
    {
        return $_SESSION['condition_notice'];
    }
    
    
    /**
     * @return void
     */
    protected function unsetConditionNoticeSession(): void
    {
        unset($_SESSION['condition_notice']);
    }
    
    
    /**
     * @return bool
     */
    protected function issetConditionNoticeSession(): bool
    {
        return array_key_exists('condition_notice', $_SESSION);
    }
    
    
    /**
     * @param string $value
     *
     * @return void
     */
    protected function setWarrantyNoticeSession(string $value): void
    {
        $_SESSION['warranty_notice'] = $value;
    }
    
    
    /**
     * @return string|null
     */
    protected function getWarrantyNoticeSessionValue(): ?string
    {
        return $_SESSION['warranty_notice'];
    }
    
    
    /**
     * @return void
     */
    protected function unsetWarrantyNoticeSession(): void
    {
        unset($_SESSION['warranty_notice']);
    }
    
    
    /**
     * @return bool
     */
    protected function issetWarrantyNoticeSession(): bool
    {
        return array_key_exists('warranty_notice', $_SESSION);
    }
}
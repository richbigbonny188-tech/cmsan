<?php
/* --------------------------------------------------------------
   VatNumberValidator.inc.php 2024-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('VatNumberValidatorInterface');

use Gambio\Core\VatValidation\Services\VatValidationService;

/**
 * Class VatNumberValidator
 *
 * This class provides methods for validating VAT numbers
 *
 * @category   System
 * @package    Customer
 * @subpackage Validation
 * @implements VatNumberValidatorInterface
 */
class VatNumberValidator implements VatNumberValidatorInterface
{
    protected VatValidationService $vatValidationService;
    
    
    /**
     * Initialize the VAT number validator.
     */
    public function __construct()
    {
        $this->vatValidationService = LegacyDependencyContainer::getInstance()->get(VatValidationService::class);
    }
    
    
    /**
     * Returns the VAT number status code ID.
     *
     * @param string $p_vatNumber VAT number.
     *
     * @return int VAT number status code ID.
     */
    public function getVatNumberStatusCodeId($p_vatNumber): int
    {
        $isValid = $this->vatValidationService->validateVatId($p_vatNumber);
        if ($isValid === true) {
            return 1;
        } else {
            return 0;
        }
    }
    
    
    /**
     * Returns the customer status ID.
     *
     * @param string $p_vatNumber VAT number.
     * @param int    $p_countryId Country ID.
     * @param bool   $p_isGuest   Is customer a guest?
     *
     * @return int Customer status ID.
     */
    public function getCustomerStatusId($p_vatNumber, $p_countryId, $p_isGuest)
    {
        $isValid = $this->vatValidationService->validateVatId($p_vatNumber);
        
        return $this->determineCustomerStatusId($isValid, $p_countryId, $p_isGuest);
    }
    
    
    /**
     * Determine the statusId of a new Registree or Guest.
     *
     * @param bool $isVatValid
     * @param int  $countryId
     * @param bool $isGuest
     */
    protected function determineCustomerStatusId($isVatValid, $countryId, $isGuest)
    {
        if ($isGuest === true) {
            if ($isVatValid === true) {
                if ($countryId == STORE_COUNTRY) {
                    if (ACCOUNT_COMPANY_VAT_GROUP) {
                        return DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL;
                    } else {
                        return DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
                    }
                } else {
                    if (ACCOUNT_COMPANY_VAT_GROUP) {
                        return DEFAULT_CUSTOMERS_VAT_STATUS_ID;
                    } else {
                        return DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
                    }
                }
            } else {
                return DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
            }
        } else {
            if ($isVatValid === true) {
                if ($countryId == STORE_COUNTRY) {
                    if (ACCOUNT_COMPANY_VAT_GROUP == 'true') {
                        return DEFAULT_CUSTOMERS_VAT_STATUS_ID_LOCAL;
                    } else {
                        return DEFAULT_CUSTOMERS_STATUS_ID;
                    }
                } else {
                    if (ACCOUNT_COMPANY_VAT_GROUP == 'true') {
                        return DEFAULT_CUSTOMERS_VAT_STATUS_ID;
                    } else {
                        return DEFAULT_CUSTOMERS_STATUS_ID;
                    }
                }
            } else {
                return DEFAULT_CUSTOMERS_STATUS_ID;
            }
        }
    }
    
    
    /**
     * Returns the error status
     *
     * @param string $p_vatNumber VAT number.
     *
     * @return bool Error status.
     */
    public function getErrorStatus($p_vatNumber): bool
    {
        if ($this->vatValidationService->isVatIdEmpty($p_vatNumber)) {
            return false;
        }
        
        $isValid = $this->vatValidationService->validateVatId($p_vatNumber);
        if ($isValid === false && ACCOUNT_VAT_BLOCK_ERROR == 'true') {
            return true;
        }
        
        return false;
    }
}
 
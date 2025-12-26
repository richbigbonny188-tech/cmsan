<?php
/*--------------------------------------------------------------
   CustomerFilters.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Data\Filter;

use Gambio\Admin\Modules\Customer\Model\Filter\CustomerFilters as CustomerFiltersInterface;
use Gambio\Core\Filter\SqlFilters;

/**
 * Class CustomerFilters
 *
 * @package Gambio\Admin\Modules\Customer\App\Data\Filter
 * @codeCoverageIgnore
 */
class CustomerFilters extends SqlFilters implements CustomerFiltersInterface
{
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'                                 => 'c.customers_id',
            'customerGroup'                      => 'c.customers_status',
            'isGuestAccount'                     => 'c.account_type',
            'isFavorite'                         => 'c.customers_is_favorite',
            'personalInformation.gender'         => 'c.customers_gender',
            'personalInformation.firstName'      => 'c.customers_firstname',
            'personalInformation.lastName'       => 'c.customers_lastname',
            'personalInformation.dateOfBirth'    => 'c.customers_dob',
            'personalInformation.customerNumber' => 'c.customers_cid',
            'contactInformation.email'           => 'c.customers_email_address',
            'contactInformation.phoneNumber'     => 'c.customers_telephone',
            'contactInformation.faxNumber'       => 'c.customers_fax',
            'businessInformation.companyName'    => 'c.customers_company',
            'businessInformation.vatId'          => 'c.customers_vat_id',
            'businessInformation.vatIdStatus'    => 'c.customers_vat_id_status',
            'businessInformation.isTradesperson' => 'c.customers_is_tradesperson',
            'credit'                             => 'gv.amount',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForLikeOperation(): array
    {
        return [
            'personalInformation.gender',
            'personalInformation.firstName',
            'personalInformation.lastName',
            'personalInformation.dateOfBirth',
            'personalInformation.customerNumber',
            'contactInformation.email',
            'contactInformation.phoneNumber',
            'contactInformation.faxNumber',
            'businessInformation.companyName',
            'businessInformation.vatId',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForNumericOperations(): array
    {
        return ['id', 'customerGroup', 'credit'];
    }
}
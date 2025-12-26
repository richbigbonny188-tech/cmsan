<?php
/*--------------------------------------------------------------
   CustomerSearch.php 2023-06-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Data\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerSearch as CustomerSearchInterface;
use Gambio\Core\Filter\SqlSearch;

/**
 * Class CustomerSearchByKeyword
 *
 * @package Gambio\Admin\Modules\Customer\App\Data\Filter
 */
class CustomerSearch extends SqlSearch implements CustomerSearchInterface
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
            'personalInformation.fullName',
            'personalInformation.invertFullName',
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
     * @inheritcDoc
     */
    public function applyToQuery(QueryBuilder $query): void
    {
        parent::applyToQuery($query);
        $this->searchForCustomColumns($query);
    }
    
    
    /**
     * Extends base logic for searching to include the
     * possibility to search for a full name or reverted full name
     *
     * @param QueryBuilder $query
     */
    private function searchForCustomColumns(QueryBuilder $query): void
    {
        $customColumns = [
            'full_name'          => 'CONCAT(c.customers_firstname, " ", c.customers_lastname)',
            'full_name_reverted' => 'CONCAT(c.customers_lastname, " ", c.customers_firstname)',
        ];
    
        foreach ($customColumns as $parameterName => $column) {
        
            $value     = '%' . $this->keyword() . '%';
            $parameter = ':criteria_search_' . $parameterName;
            $expr      = $query->expr()->like($column, $parameter);
        
            $query->orWhere($expr)->setParameter(ltrim($parameter, ':'), $value);
        }
    }
}
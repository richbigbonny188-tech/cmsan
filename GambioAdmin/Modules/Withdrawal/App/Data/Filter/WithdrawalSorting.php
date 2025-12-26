<?php
/* --------------------------------------------------------------
   WithdrawalSorting.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\App\Data\Filter;

use Gambio\Core\Filter\SqlSorting;

/**
 * Class WithdrawalSorting
 *
 * @package Gambio\Admin\Modules\Withdrawal\App\Data\Filter
 * @codeCoverageIgnore
 */
class WithdrawalSorting extends SqlSorting
{
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'                        => 'withdrawal_id',
            'order.id'                  => 'order_id',
            'order.creationDate'        => 'order_date',
            'order.deliveryDate'        => 'delivery_date',
            'customer.id'               => 'customer_id',
            'customer.gender'           => 'customer_gender',
            'customer.firstName'        => 'customer_firstname',
            'customer.lastName'         => 'customer_lastname',
            'customer.address.street'   => 'customer_street_address',
            'customer.address.postcode' => 'customer_postcode',
            'customer.address.city'     => 'customer_city',
            'customer.address.country'  => 'customer_country',
            'customer.email'            => 'customer_email',
            'date'                      => 'withdrawal_date',
            'content'                   => 'withdrawal_content',
            'createdByAdmin'            => 'created_by_admin',
            'createdOn'                 => 'date_created',
        ];
    }
}
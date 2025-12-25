<?php
/* --------------------------------------------------------------
   AfterbuyOrderReaderFields.php 2023-02-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder;

/**
 * Class AfterbuyOrderReaderFields
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class AfterbuyOrderReaderFields
{
    public const AFTERBUY_ORDER_FIELDS = [
        'o.orders_id',
        'ao.afterbuy_order_id',
        'o.orders_status',
        'o.delivery_firstname',
        'o.delivery_lastname',
        'o.delivery_postcode',
        'o.delivery_city',
        'o.delivery_country',
        'o.delivery_state',
        'o.delivery_country_iso_code_2',
        'o.delivery_street_address',
        'o.delivery_house_number',
        'o.delivery_additional_info',
        'o.customers_telephone',
        'o.delivery_suburb',
        'o.delivery_company',
        'o.shipping_method',
        'o.shipping_class',
        'o.payment_method',
        'o.payment_class',
        'o.gambio_hub_module_title',
        'o.gambio_hub_module',
        'o.customers_status',
        'o.comments',
    ];
}
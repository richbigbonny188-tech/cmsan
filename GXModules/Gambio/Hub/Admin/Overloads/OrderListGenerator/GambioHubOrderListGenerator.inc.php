<?php
/* --------------------------------------------------------------
   GambioHubOrderListGenerator.inc.php 2018-01-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubOrderListGenerator
 *
 * Enables order filtering with Gambio Hub modules and the output of gambio hub payment data.
 */
class GambioHubOrderListGenerator extends GambioHubOrderListGenerator_parent
{
    /**
     * Filter the order records.
     *
     * This method contains the filtering logic. It can be overloaded in order to provide a custom filtering logic.
     *
     * @param array       $filterParameters
     * @param \Pager|null $pager
     * @param array       $sorters
     *
     * @return mixed
     */
    protected function _setFilterArguments(array $filterParameters, \Pager $pager = null, array $sorters = [])
    {
        $paymentMethodsArray = isset($filterParameters['paymentMethod']) ? $filterParameters['paymentMethod'] : [];
        unset($filterParameters['paymentMethod']);
        
        if (! $paymentMethodsArray) {
            return parent::_setFilterArguments($filterParameters, $pager, $sorters);
        }
        
        $hubPaymentMethods = $paymentMethods = [];
        
        foreach($paymentMethodsArray as $paymentMethod) {
            if(strpos($paymentMethod, 'Hub') !== false) {
                $hubPaymentMethods[] = $paymentMethod;
            } else {
                $paymentMethods[] = $paymentMethod;
            }
        }
        
        $this->db->group_start();
        
        foreach($hubPaymentMethods as $paymentMethod) {
            $this->db->or_group_start()->where([
                'orders.payment_class'     => 'gambio_hub',
                'orders.gambio_hub_module' => $paymentMethod
            ])->group_end();
        }
        
        foreach($paymentMethods as $paymentMethod) {
            $paymentMethod = (self::FILTER_NO_VALUE === $paymentMethod) ? '' : $paymentMethod;
            $this->db->or_where('orders.payment_class', $paymentMethod);
        }
        
        $this->db->group_end();
        
        return parent::_setFilterArguments($filterParameters, $pager, $sorters);
    }

    /**
     * Returns a string for the ::_select() method which contains column names of the orders table.
     *
     * @return string
     */
    protected function _ordersColumns()
    {
        $additionalColumns = ', orders.gambio_hub_module, orders.gambio_hub_module_title';

        return parent::_ordersColumns() . $additionalColumns;
    }

    /**
     * Creates and returns whether an order shipping or payment type instance by the given row data and type argument.
     *
     * @param string $type Whether 'shipping' or 'payment', used to determine the expected order type class.
     * @param array  $row  Row array with data about the order type.
     *
     * @return OrderShippingType|OrderPaymentType
     *
     * @throws InvalidArgumentException
     */
    protected function _createOrderType($type, array $row)
    {
        if ($type === 'payment' && $row['gambio_hub_module']) {
            $paymentTitle = new StringType($row['gambio_hub_module_title']);
            $paymentModule = new StringType($row['gambio_hub_module']);

            return MainFactory::create('OrderPaymentType', $paymentTitle, $paymentModule);
        }

        return parent::_createOrderType($type, $row);
    }
}

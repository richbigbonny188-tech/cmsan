<?php
/* --------------------------------------------------------------
   KlarnaHubOrderExtender.inc.php 2023-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'inc/xtc_format_price.inc.php';

/**
 * Class KlarnaHubOrderExtender
 */
class KlarnaHubOrderExtender extends KlarnaHubOrderExtender_parent
{
    /**
     * @var \HubAssetHelper
     */
    protected $hubAssetHelper;

    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;

    /**
     * @var array
     */
    protected $order;

    /**
     * @var array
     */
    protected $klarnaHubModuleCodes = [
        'KlarnaHub',
        'KlarnaPaylaterHub',
        'KlarnaPaynowHub',
        'KlarnaSliceitHub',
        'KlarnaBanktransferHub'
    ];


    /**
     * Proceed with the execution of the extender.
     */
    public function proceed()
    {
        parent::proceed();

        $this->queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();

        $this->order = $this->queryBuilder->get_where('orders', ['orders_id' => $_GET['oID']])->row_array();

        if (empty($this->order) || !in_array($this->order['gambio_hub_module'], $this->klarnaHubModuleCodes, false)) {
            return;
        }

        $this->hubAssetHelper = MainFactory::create('HubAssetHelper', gm_get_conf('INSTALLED_VERSION'));
        $this->_addKlarnaHubOrderDetailsScript()->_recalculateOrderAmount()->addContent();
    }


    /**
     * Loads the Gambio Hub order details JavaScript file.
     *
     * The script will adjust the order details page for Gambio Hub compatibility. Check the order_details.js for
     * further information.
     *
     * @return KlarnaHubOrderExtender Returns same instance for chained method calls.
     */
    protected function _addKlarnaHubOrderDetailsScript()
    {
        $debug   = file_exists(DIR_FS_CATALOG . '.dev-environment');
        $postfix = $debug ? '' : '.min';
        $baseUrl = HTTP_SERVER . DIR_WS_CATALOG . $this->hubAssetHelper->getScriptsBaseUrl();

        $queryParams = [
            'appUrl'      => DIR_WS_CATALOG,
            'moduleCode'  => $this->order['gambio_hub_module'],
            'orderNumber' => $this->order['orders_id']
        ];

        $this->v_output_buffer['order_status'] .= '
            <script src="' . $baseUrl . '/vendor/fetch' . $postfix . '.js"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/klarna_hub' . $postfix . '.js?'
                                                  . http_build_query($queryParams, '', '&') . '"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/klarna_hub' . $postfix . '.js?'
                                                  . http_build_query($queryParams, '', '&') . '"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/klarna_hub_api' . $postfix . '.js"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/klarna_hub_lib' . $postfix . '.js"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/order_details/disable_edit_address_button' . $postfix . '.js"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/order_details/disable_edit_button_dropdown' . $postfix . '.js"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/order_details/extend_cancel_order_action' . $postfix . '.js"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/order_details/extend_order_status_modal' . $postfix . '.js"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/order_details/extend_order_status_modal_entry_selection'
                                                  . $postfix . '.js"></script>
            <script src="' . $baseUrl . '/extenders/klarna_hub/order_details/extend_tracking_codes_button' . $postfix . '.js"></script>
        ';

        return $this;
    }


    /**
     * Recalculates order amount.
     *
     * KlarnaHub orders that are marked for recalculation need to send their new amounts to Klarna.
     *
     * @return KlarnaHubOrderExtender Returns same instance for chained method calls.
     */
    protected function _recalculateOrderAmount()
    {
        try {
            $clientKey = MainFactory::create('HubClientKeyConfiguration')->getClientKey();
        } catch (Exception $exception) {
            return $this; // The client is not connected to Hub so do not proceed with the recalculation.
        }

        $languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_hub_klarna_hub',
            $_SESSION['languages_id']);

        $additionalAmounts = $this->_getAdditionalAmounts();

        if ($additionalAmounts === null) {
            return $this; // Skip, no recalculation required.
        }

        $moduleCode  = new NonEmptyStringType($this->order['gambio_hub_module']);
        $orderNumber = new NonEmptyStringType($this->order['orders_id']);

        $klarnaHubFactory        = MainFactory::create('KlarnaHubFactory');
        $klarnaHubCallbackClient = $klarnaHubFactory->createCallbackClient($moduleCode, $orderNumber);
        $klarnaHubConfiguration  = $klarnaHubFactory->createConfiguration();

        $subtotal = $this->_getOrderSubtotal();
        $this->_processShippingChanges($additionalAmounts['newShippingCosts']);

        $klarnaHubConfigurationArray = $klarnaHubConfiguration->asArray($clientKey, $moduleCode, $orderNumber);

        $klarnaHubConfigurationArray = $this->_processDiscountChanges('ot_coupon',
            $languageTextManager->get_text('COUPON_ORDER_TOTAL_TITLE'), $additionalAmounts['newCouponAmount'],
            $klarnaHubConfigurationArray);
        $klarnaHubConfigurationArray = $this->_processDiscountChanges('ot_discount',
            $languageTextManager->get_text('DISCOUNT_ORDER_TOTAL_TITLE'), $additionalAmounts['newDiscountAmount'],
            $klarnaHubConfigurationArray);
        $klarnaHubConfigurationArray = $this->_processDiscountChanges('ot_gv',
            $languageTextManager->get_text('VOUCHER_ORDER_TOTAL_TITLE'), $additionalAmounts['newVoucherAmount'],
            $klarnaHubConfigurationArray);

        $klarnaHubOrder          = $this->_createKlarnaHubOrder($klarnaHubConfigurationArray);
        $surchargeTotal          = $this->_calculateSurchargeTotal($klarnaHubOrder);
        $discountTotal           = $this->_calculateDiscountTotal($klarnaHubOrder);
        $reduceInvoiceAmountData = $this->_prepareReduceInvoiceAmountData($klarnaHubOrder, $subtotal, $surchargeTotal,
            $discountTotal, $additionalAmounts);

        try {
            $klarnaHubCallbackClient->executeReduceInvoiceAmount($reduceInvoiceAmountData);

            $this->_renderRecalculationSuccess();
        } catch (Exception $exception) {
            $this->_renderRecalculationFailure();
            
            // Always show the updated order data, as it has always been stored in the database.
            $this->_renderRecalculationSuccess();
        }

        $this->_unsetAdditionalAmounts();

        return $this;
    }


    /**
     * Prepares and returns the reduce invoice amount request payload.
     *
     * @param \KlarnaHubOrder $klarnaHubOrder    Contains Klarna related logic.
     * @param float           $subtotal          Current shop order subtotal value.
     * @param float           $surchargeTotal    Surcharge total required for the calculation.
     * @param float           $discountTotal     Discount total required for the calculation.
     * @param array           $additionalAmounts Klarna order additional amounts such as shipping and voucher.
     *
     * @return array
     */
    protected function _prepareReduceInvoiceAmountData(
        \KlarnaHubOrder $klarnaHubOrder,
        $subtotal,
        $surchargeTotal,
        $discountTotal,
        array $additionalAmounts
    ) {
        return [
            'newInvoiceAmount' => KlarnaHubPrice::sanitize(($subtotal - (float)$discountTotal + (float)$surchargeTotal
                                                            + (float)$additionalAmounts['newShippingCosts']) * 100),
            'comment'          => '',
            'orderLines'       => $klarnaHubOrder->getUpdatedOrderLines()
        ];
    }


    /**
     * Returns the order subtotal value.
     *
     * @return float
     */
    protected function _getOrderSubtotal()
    {
        return (float)$this->queryBuilder->get_where('orders_total', [
            'orders_id' => $this->order['orders_id'],
            'class'     => 'ot_subtotal'
        ])->row()->value;
    }


    /**
     * Process any shipping changes.
     *
     * @param float $newShippingCosts
     */
    protected function _processShippingChanges($newShippingCosts)
    {
        $this->queryBuilder->update('orders_total', [
            'text'  => xtc_format_price($newShippingCosts, '1', false),
            'value' => $newShippingCosts
        ], [
            'orders_id' => $this->order['orders_id'],
            'class'     => 'ot_shipping'
        ]);
    }


    /**
     * Processes discount changes and returns the updated configuration if needed.
     *
     * @param string $orderReference              The order reference in the database.
     * @param string $referenceName               The name of the discount.
     * @param float  $newDiscountAmount           New voucher amount.
     * @param array  $klarnaHubConfigurationArray Klarna hub configuration data.
     *
     * @return array
     */
    protected function _processDiscountChanges(
        $orderReference,
        $referenceName,
        $newDiscountAmount,
        array $klarnaHubConfigurationArray
    ) {
        if ($newDiscountAmount > 0) {
            $newDiscountAmount *= -1;
        }
        
        $newDiscountAmount = (float)$newDiscountAmount;

        $voucherExists = (bool)$this->queryBuilder->get_where('orders_total', [
            'orders_id' => $this->order['orders_id'],
            'class'     => $orderReference
        ])->num_rows();

        if (!$voucherExists) {
            if ($newDiscountAmount === 0.0) {
                return $klarnaHubConfigurationArray;
            }
            
            $shopOrderVoucher = $this->getOrderTotalData($orderReference, $referenceName, $newDiscountAmount);
            
            $this->queryBuilder->insert('orders_total', $shopOrderVoucher);
            
            $klarnaHubConfigurationArray['klarnaOrder']['order_lines'][] = $this->buildOrderLine($referenceName,
                                                                                         $orderReference,
                                                                                         $newDiscountAmount);
            
            $klarnaHubConfigurationArray['order']['totals'][] = $shopOrderVoucher;
        } else {
            $this->queryBuilder->update('orders_total', [
                'text'  => xtc_format_price($newDiscountAmount, '1', false),
                // The shop handles all discounts as negative numbers except ot_gv, it HAS to be positive! Blame the
                // shop for this weird line :-)
                'value' => $orderReference === 'ot_gv' ? -1 * $newDiscountAmount : $newDiscountAmount
            ], [
                'orders_id' => $this->order['orders_id'],
                'class'     => $orderReference
            ]);
            
            $orderTotalLineFound = false;
            foreach ($klarnaHubConfigurationArray['order']['totals'] as &$orderTotalLine) {
                if ($orderTotalLine['class'] === $orderReference) {
                    $orderTotalLineFound = true;
                    $orderTotalLine['text']  = xtc_format_price($newDiscountAmount, '1', false);
                    $orderTotalLine['value'] = $orderReference === 'ot_gv' ? -1
                                                                             * $newDiscountAmount : $newDiscountAmount;
                }
            }
            
            // if a discount was added the first time, add missing data
            
            if (!$orderTotalLineFound) {
                $klarnaHubConfigurationArray['order']['totals'][] = $this->getOrderTotalData($orderReference, $referenceName, $newDiscountAmount);
            }
            
            $klarnaOrderLine = array_filter($klarnaHubConfigurationArray['klarnaOrder']['order_lines'],
                function ($orderLine) use ($orderReference) {
                    return $orderLine['reference'] === $orderReference;
                });
            
            if (empty($klarnaOrderLine)) {
                $klarnaHubConfigurationArray['klarnaOrder']['order_lines'][] = $this->buildOrderLine($referenceName,
                                                                                                     $orderReference,
                                                                                                     $newDiscountAmount);
            }
        }

        return $klarnaHubConfigurationArray;
    }


    /**
     * Renders recalculation success content.
     */
    protected function _renderRecalculationSuccess()
    {
        // Trigger shop order lines recalculation and update the order lines table element.
        $debug = file_exists(DIR_FS_CATALOG . '.dev-environment');

        $postfix = $debug ? '' : '.min';

        $this->v_output_buffer['order_status'] .= '
		            <script src="' . DIR_WS_CATALOG . $this->hubAssetHelper->getScriptsBaseUrl()
                                                  . '/extenders/klarna_hub/order_details/update_order_lines' . $postfix
                                                  . '.js"></script>
        ';
    }


    /**
     * Renders recalculation failure content.
     */
    protected function _renderRecalculationFailure()
    {
        $languageTextManager = MainFactory::create('LanguageTextManager', 'gambio_hub_klarna_hub',
            $_SESSION['languages_id']);

        // Append the error in the message stack manually (cause message stack contents are already rendered).
        $html = '
			<div class="alert alert-danger">
				' . $languageTextManager->get_text('KLARNA_RECALCULATION_FAILED') . '
			</div>
		';

        $this->v_output_buffer['order_status'] .= '
            <script>
                $(function() {
                    $(".message_stack_container").append(' . json_encode($html) . ');
                });
            </script>
        ';
    }


    /**
     * Returns additional amounts cached in gm configuration table.
     *
     * @return array|null
     */
    protected function _getAdditionalAmounts()
    {
        $recalculate = json_decode((string)gm_get_conf('GAMBIO_HUB_KLARNA_HUB_RECALCULATE'), true);

        return isset($recalculate[$this->order['orders_id']]) ? $recalculate[$this->order['orders_id']] : null;
    }


    /**
     * Removes additional amounts cached in gm configuration table.
     */
    protected function _unsetAdditionalAmounts()
    {
        $recalculate = json_decode((string)gm_get_conf('GAMBIO_HUB_KLARNA_HUB_RECALCULATE'), true);

        unset($recalculate[$this->order['orders_id']]);

        gm_set_conf('GAMBIO_HUB_KLARNA_HUB_RECALCULATE', json_encode($recalculate));
    }


    /**
     * Calculates total sum of surcharge order lines.
     *
     * @param \KlarnaHubOrder $klarnaHubOrder Klarna Hub order
     *
     * @return float
     */
    protected function _calculateSurchargeTotal(\KlarnaHubOrder $klarnaHubOrder)
    {
        $surchargesTotal = 0;

        foreach ($klarnaHubOrder->getUpdatedOrderLines() as $orderLine) {
            if ($orderLine['type'] !== 'surcharge') {
                continue;
            }

            $surchargesTotal += $orderLine['totalAmount'] / 100; // Convert to float as Klarna uses no decimal points.
        }

        return $surchargesTotal;
    }


    /**
     * Calculates total sum of discount order lines.
     *
     * @param \KlarnaHubOrder $klarnaHubOrder Klarna Hub order.
     *
     * @return float
     */
    protected function _calculateDiscountTotal(\KlarnaHubOrder $klarnaHubOrder)
    {
        $discount = 0;

        foreach ($klarnaHubOrder->getUpdatedOrderLines() as $orderLine) {
            if ($orderLine['type'] !== 'discount') {
                continue;
            }

            $discount += abs($orderLine['totalAmount']) / 100; // Convert to float as Klarna uses no decimal points.
        }

        return $discount;
    }


    /**
     * Returns a Klarna order instance.
     *
     * @return \KlarnaHubOrder
     */
    protected function _createKlarnaHubOrder(array $klarnaHubConfigurationArray)
    {
        return MainFactory::create('KlarnaHubOrder', StaticGXCoreLoader::getDatabaseQueryBuilder(),
            $klarnaHubConfigurationArray['order'], $klarnaHubConfigurationArray['klarnaOrder']);
    }
    
    
    /**
     * @param string $orderReference
     * @param string $referenceName
     * @param float  $newDiscountAmount
     *
     * @return array
     */
    protected function getOrderTotalData(string $orderReference, string $referenceName, float $newDiscountAmount): array
    {
        $sortOrder = '0';
        switch ($orderReference) {
            case 'ot_coupon':
                $sortOrder = defined('MODULE_ORDER_TOTAL_COUPON_SORT_ORDER') ? MODULE_ORDER_TOTAL_COUPON_SORT_ORDER : '29';
                break;
            case 'ot_discount':
                $sortOrder = defined('MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER') ? MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER : '20';
                break;
            case 'ot_gv':
                $sortOrder = defined('MODULE_ORDER_TOTAL_GV_SORT_ORDER') ? MODULE_ORDER_TOTAL_GV_SORT_ORDER : '80';
                break;
        }
        
        return [
            'orders_id'  => $this->order['orders_id'],
            'title'      => $referenceName . ':',
            'text'       => xtc_format_price($newDiscountAmount, '1', false),
            // The shop handles all discounts as negative numbers except ot_gv, it HAS to be positive! Blame the
            // shop for this weird line :-)
            'value'      => $orderReference === 'ot_gv' ? -1 * $newDiscountAmount : $newDiscountAmount,
            'class'      => $orderReference,
            'sort_order' => $sortOrder,
        ];
    }
    
    
    /**
     * @param string $referenceName
     * @param string $orderReference
     * @param float  $newDiscountAmount
     *
     * @return array
     */
    protected function buildOrderLine(string $referenceName, string $orderReference, float $newDiscountAmount): array
    {
        return [
            'images_url'            => null,
            'merchant_data'         => null,
            'name'                  => $referenceName . ':',
            'product_identifiers'   => [
                'brand'                    => null,
                'category_path'            => null,
                'global_trade_item_number' => null,
                'manufacturer_part_number' => null
            ],
            'product_url'           => null,
            'quantity'              => 1,
            'quantity_unit'         => null,
            'reference'             => $orderReference,
            'tax_rate'              => 0,
            'total_amount'          => $newDiscountAmount * 100,
            'total_discount_amount' => 0,
            'total_tax_amount'      => 0,
            'type'                  => 'discount',
            'unit_price'            => $newDiscountAmount * 100
        ];
    }
}

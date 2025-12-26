<?php
/* --------------------------------------------------------------
   GambioHubOrderTotal.inc.php 2023-05-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubOrderTotal extends GambioHubOrderTotal_parent {
    public function process()
    {
        $order_total_array = [];
        foreach($this->modules as $moduleFile)
        {
            $class = basename($moduleFile, '.php');
            if ($GLOBALS[$class]->enabled) {
                $preProcessOrder = clone $GLOBALS['order'];
                $GLOBALS[$class]->process();
                $summaryOfChanges = [
                    'subtotal'      => $GLOBALS['order']->info['subtotal'] - $preProcessOrder->info['subtotal'],
                    'total'         => $GLOBALS['order']->info['total']    - $preProcessOrder->info['total'],
                    'tax'           => ($GLOBALS['order']->info['tax'] ?? 0.0) - ($preProcessOrder->info['tax'] ?? 0.0),
                    'shipping_cost' => $GLOBALS['order']->info['shipping_cost']
                                       - $preProcessOrder->info['shipping_cost'],
                    'tax_groups'    => []
                ];
                if(is_array($GLOBALS['order']->info['tax_groups'] ?? null))
                {
                    foreach($GLOBALS['order']->info['tax_groups'] as $taxGroup => $taxGroupValue) {
                        $summaryOfChanges['tax_groups'][$taxGroup] = ($GLOBALS['order']->info['tax_groups'][$taxGroup] ?? 0.0) -
                                                                     ($preProcessOrder->info['tax_groups'][$taxGroup] ?? 0.0);
                    }
                }
                $processingResult = $GLOBALS[$class]->output;
                $processingResult = is_array($processingResult) ? $processingResult : [];
                foreach($processingResult as $otOutput) {
                    if((xtc_not_null($otOutput['title']) && xtc_not_null($otOutput['text'])) ||
                       $GLOBALS[$class]->code === 'ot_gm_tax_free')
                    {
                        if($class === 'ot_discount')
                        {
                            $summaryOfChanges['total'] += $otOutput['value'];
                        }
                        $order_total_array[] = array_merge(
                            [
                                'title' => 'not set',
                                'text'  => 'not set',
                                'value' => 0.00,
                            ],
                            $otOutput,
                            [
                                'code'       => $GLOBALS[$class]->code,
                                'sort_order' => $GLOBALS[$class]->sort_order,
                                'changes'    => $summaryOfChanges,
                            ]
                        );
                        
                        if ($class === 'ot_total' && strpos($_SERVER['REQUEST_URI'], 'checkout_process.php') !== false) {
                            $sessionKeyExists = array_key_exists('checkout_interest', $_SESSION)
                                                && is_array($_SESSION['checkout_interest'])
                                                && array_key_exists($_SESSION['gambio_hub_session_key'], $_SESSION['checkout_interest'])
                                                && !empty($_SESSION['checkout_interest'][$_SESSION['gambio_hub_session_key']]);
                            $isCheckoutConfirmation = strpos($_SERVER['REQUEST_URI'] ?? '', 'checkout_confirmation') !== false;
                            $isCheckoutProcess = strpos($_SERVER['REQUEST_URI'] ?? '', 'checkout_process') !== false;
                            if ($sessionKeyExists && ($isCheckoutConfirmation || $isCheckoutProcess)) {
                                $totalWithoutInterest = $otOutput['value'];
                                $interestValue = $GLOBALS['xtPrice']->xtcFormat($_SESSION['checkout_interest'][$_SESSION['gambio_hub_session_key']]['amount'], false);
                                $totalWithInterest = $totalWithoutInterest + $interestValue;
                                $order_total_array[] = [
                                    'code'       => $GLOBALS[$class]->code . '_interest',
                                    'sort_order' => $GLOBALS[$class]->sort_order + 1,
                                    'title' => MODULE_ORDER_TOTAL_TOTAL_INTEREST_AMOUNT . ':',
                                    'text'  => $GLOBALS['xtPrice']->xtcFormat($_SESSION['checkout_interest'][$_SESSION['gambio_hub_session_key']]['amount'],
                                        true),
                                    'value' => $interestValue,
                                ];
                                $order_total_array[] = [
                                    'code'       => $GLOBALS[$class]->code . '_interest_total',
                                    'sort_order' => $GLOBALS[$class]->sort_order + 2,
                                    'title' => MODULE_ORDER_TOTAL_TOTAL_TITLE_INCL_INTEREST . ':',
                                    'text'  => '<b>' . $GLOBALS['xtPrice']->xtcFormat($totalWithInterest, true) . '</b>',
                                    'value' => $GLOBALS['xtPrice']->xtcFormat($totalWithInterest, false),
                                ];
                            }
                        }
                    }
                }
            }
        }
        
        return $order_total_array;
    }
    
    
    public function output_array()
    {
        $outputArray = parent::output_array();
        
        foreach (debug_backtrace() as $trace) {
            if (strpos($trace['function'], 'selection') !== false) {
                return $outputArray;
            }
        }
        
        if (isset($GLOBALS['ot_total']) && $GLOBALS['ot_total']->enabled) {
            $totalOutput = $GLOBALS['ot_total']->output;
        } else {
            return $outputArray;
        }
        
        $sessionKeyExists = array_key_exists('checkout_interest', $_SESSION)
                            && is_array($_SESSION['checkout_interest'])
                            && array_key_exists($_SESSION['gambio_hub_session_key'], $_SESSION['checkout_interest'])
                            && !empty($_SESSION['checkout_interest'][$_SESSION['gambio_hub_session_key']]);
        $isCheckoutConfirmation = strpos($_SERVER['REQUEST_URI'] ?? '', 'checkout_confirmation') !== false;
        $isCheckoutProcess = strpos($_SERVER['REQUEST_URI'] ?? '', 'checkout_process') !== false;
        if ($sessionKeyExists && ($isCheckoutConfirmation || $isCheckoutProcess)) {
            $totalWithoutInterest = $totalOutput[0]['value'];
            $interestValue        = $GLOBALS['xtPrice']->xtcFormat($_SESSION['checkout_interest'][$_SESSION['gambio_hub_session_key']]['amount'],
                false);
            $totalWithInterest    = $totalWithoutInterest + $interestValue;
            $outputArray[]  = [
                'title' => MODULE_ORDER_TOTAL_TOTAL_INTEREST_AMOUNT . ':',
                'text'  => $GLOBALS['xtPrice']->xtcFormat($_SESSION['checkout_interest'][$_SESSION['gambio_hub_session_key']]['amount'],
                    true),
            ];
            $outputArray[]  = [
                'title' => MODULE_ORDER_TOTAL_TOTAL_TITLE_INCL_INTEREST . ':',
                'text'  => '<b>' . $GLOBALS['xtPrice']->xtcFormat($totalWithInterest, true) . '</b>',
            ];
        }
        
        return $outputArray;
    }
    
}

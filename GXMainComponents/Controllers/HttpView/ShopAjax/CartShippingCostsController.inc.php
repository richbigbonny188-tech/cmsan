<?php
/* --------------------------------------------------------------
   CartShippingCostsController.inc.php 2016-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CartShippingCostsController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class CartShippingCostsController extends HttpViewController
{
    /**
     * @var array
     */
    protected $status = [];
    
    /**
     * @var array
     */
    protected $shippingCostsInformation = [];
    
    /**
     * @var CartShippingCostsAjaxHandler
     */
    protected $cartShippingCostsAjaxHandler;
    
    
    /**
     * @return HttpControllerResponse
     * @todo use GET and POST REST-API like
     *
     * @todo get rid of old AjaxHandler
     */
    public function actionDefault()
    {
        $this->cartShippingCostsAjaxHandler = MainFactory::create('CartShippingCostsAjaxHandler');
        
        $this->_setStatusAndShippingInformation();
        $this->_setShippingModulesSelection();
        $this->_setShippingWeightInformation();
        
        $result = $this->_getResponseArray();
        
        return MainFactory::create('JsonHttpControllerResponse', $result);
    }
    
    
    protected function _setStatusAndShippingInformation()
    {
        $getData = ['action' => 'get_shipping_costs'];
        $this->cartShippingCostsAjaxHandler->set_data('GET', $getData);
        $this->cartShippingCostsAjaxHandler->set_data('POST', $this->_getPostDataCollection()->getArray());
        $this->cartShippingCostsAjaxHandler->proceed();
        $result = json_decode($this->cartShippingCostsAjaxHandler->get_response(), true);
        
        $this->status['success']       = true;
        $this->status['error_message'] = $this->status['success'] ? '' : $result['error_message'];
        
        $this->shippingCostsInformation['cart_ot_gambioultra_costs'] = isset($result['cart_ot_gambioultra_costs']) ? strip_tags($result['cart_ot_gambioultra_costs']) : '';
        $this->shippingCostsInformation['cart_shipping_costs']       = $result['status']
                                                                       === 'success' ? $result['cart_shipping_costs'] : '';
    }
    
    
    protected function _setShippingModulesSelection()
    {
        $getData = ['action' => 'get_shipping_modules'];
        $this->cartShippingCostsAjaxHandler->set_data('GET', $getData);
        $this->cartShippingCostsAjaxHandler->proceed();
        $result = json_decode($this->cartShippingCostsAjaxHandler->get_response(), true);
        
        $this->shippingCostsInformation['cart_shipping_modules'] = $result['html'];
    }
    
    
    protected function _setShippingWeightInformation()
    {
        $getData = ['action' => 'get_shipping_weight'];
        $this->cartShippingCostsAjaxHandler->set_data('GET', $getData);
        $this->cartShippingCostsAjaxHandler->proceed();
        $result = json_decode($this->cartShippingCostsAjaxHandler->get_response(), true);
        
        $this->shippingCostsInformation['cart_shipping_weight'] = $result['html'];
    }
    
    
    /**
     * @return array
     */
    protected function _getResponseArray()
    {
        $result = [
            'success'     => $this->status['success'],
            'status_code' => 1,
            'content'     => [
                'gambioultra' => [
                    'selector' => 'gambioUltraCosts',
                    'type'     => 'text',
                    'value'    => $this->shippingCostsInformation['cart_ot_gambioultra_costs']
                ],
                'weight'      => [
                    'selector' => 'shippingWeight',
                    'type'     => 'replace',
                    'value'    => $this->shippingCostsInformation['cart_shipping_weight']
                ],
                'costs'       => [
                    'selector' => 'shippingCost',
                    'type'     => 'text',
                    'value'    => $this->shippingCostsInformation['cart_shipping_costs']
                ],
                'modules'     => [
                    'selector' => 'shippingCalculator',
                    'type'     => 'replace',
                    'value'    => $this->shippingCostsInformation['cart_shipping_modules']
                ],
                'error'       => [
                    'selector' => 'invalidCombinationError',
                    'type'     => 'text',
                    'value'    => $this->status['error_message']
                ]
            ]
        ];
        
        return $result;
    }
}
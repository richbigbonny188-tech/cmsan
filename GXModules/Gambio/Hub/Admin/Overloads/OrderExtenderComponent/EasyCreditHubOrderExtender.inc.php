<?php
/* --------------------------------------------------------------
   EasyCreditHubOrderExtender.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'inc/xtc_format_price.inc.php';

/**
 * Class EasyCreditHubOrderExtender
 */
class EasyCreditHubOrderExtender extends EasyCreditHubOrderExtender_parent
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
    protected $EasyCreditHubModuleCodes = [
        'EasyCreditHub',
    ];


    /**
     * Proceed with the execution of the extender.
     */
    public function proceed()
    {
        parent::proceed();

        $this->queryBuilder = StaticGXCoreLoader::getDatabaseQueryBuilder();

        $this->order = $this->queryBuilder->get_where('orders', ['orders_id' => $_GET['oID']])->row_array();

        if (empty($this->order) || !in_array($this->order['gambio_hub_module'], $this->EasyCreditHubModuleCodes, false)) {
            return;
        }

        $this->hubAssetHelper = MainFactory::create('HubAssetHelper', gm_get_conf('INSTALLED_VERSION'));
        $this->_addEasyCreditHubOrderDetailsScript();
        $this->addContent();
    }


    /**
     * Loads the Gambio Hub order details JavaScript file.
     *
     * The script will adjust the order details page for Gambio Hub compatibility. Check the order_details.js for
     * further information.
     *
     * @return EasyCreditHubOrderExtender Returns same instance for chained method calls.
     */
    protected function _addEasyCreditHubOrderDetailsScript()
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
            <script src="' . $baseUrl . '/extenders/easycredit_hub/easycredit_hub' . $postfix . '.js?'
                                                  . http_build_query($queryParams, '', '&') . '"></script>
            <script src="' . $baseUrl . '/extenders/easycredit_hub/order_details/disable_edit_address_button' . $postfix . '.js"></script>
            <script src="' . $baseUrl . '/extenders/easycredit_hub/order_details/disable_edit_button_dropdown' . $postfix . '.js"></script>
        ';
    }
}

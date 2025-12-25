<?php
/* --------------------------------------------------------------
   RecreateOrder.php 2023-06-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'gm/inc/gm_save_order.inc.php';

class RecreateOrder
{
	/**
	 * @var int order id
	 */
	var $v_order_id = 0;

	/**
	 * @var string html of the order
	 */
	var $v_html = '';
    
    /**
     * @var string txt order mail body
     */
    protected $txtMailBody = '';

	/**
     * constructor
	 *
	 * check order exists
	 *
	 * @access public
	 * @param int $p_orders_id order id
	 * @return bool OK:true | ERROR:false
     */
    function __construct($p_orders_id)
    {
        // manage params
        $this->v_order_id = (int)$p_orders_id;

        // get send order status and orders id
        $t_query = xtc_db_query("
								SELECT
									orders_id,
									gm_send_order_status,
									abandonment_download,
									abandonment_service
								FROM " .
									TABLE_ORDERS . "
								WHERE
									orders_id= '" . $this->v_order_id . "'
								LIMIT 1
		");

		// if order status exists
		if(xtc_db_num_rows($t_query) <= 0) {
			return false;
		}

		$t_order_status = xtc_db_fetch_array($t_query);
		$this->createOrder($t_order_status);

		return true;
    }

	/**
	 * create the order
	 *
	 * @access private
	 * @return bool OK:true | Error:false
	 */
	public function createOrder($t_row)
	{
        $orderMailBuilder = MainFactory::create('OrderMailBuilder');
        $orderMailBuilder->build($t_row);
        
        // GET HTML MAIL CONTENT
        $this->v_html = $orderMailBuilder->getHtmlMailBody();
        
        // GET TXT MAIL CONTENT
        $this->txtMailBody = $orderMailBuilder->getTxtMailBody();

		// update send order status in DB
		gm_save_order($t_row['orders_id'], $this->v_html, $this->txtMailBody, $t_row['gm_send_order_status']);

		return true;
	}

	/**
	 * get html of the order
	 *
	 * @access public
	 * @return string $this->v_html html of the order
	 */
	public function getHtml()
	{
		return $this->v_html;
	}
    
    /**
     * @access public
     * @return string
     */
    function getTxtMailBody(): string
    {
        return $this->txtMailBody;
    }
}

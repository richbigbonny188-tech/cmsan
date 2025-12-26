<?php
/* --------------------------------------------------------------
   GambioHubSendOrderThemeContentView.inc.php 2022-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'inc/get_transfer_charge_text.inc.php'; // Required in older shop versions.

/**
 * Class GambioHubSendOrderThemeContentView
 */
class GambioHubSendOrderThemeContentView extends GambioHubSendOrderThemeContentView_parent
{
	/**
	 * @var string
	 */
	protected $cashOnDeliveryModuleCode = 'CashOnDeliveryHub';
	
	/**
	 * @var string
	 */
	protected $moneyOrderModuleCode = 'MoneyOrderHub';
	
	/**
	 * @var string
	 */
	protected $hubModuleCode = '';
	
	/**
	 * @var string
	 */
	protected $hubModuleTitle = '';
	
	
	/**
	 * @return array
	 */
	public function get_mail_content_array()
	{
		if($this->order->info['payment_method'] === 'gambio_hub')
		{
			if($this->_getHubModuleCode() === $this->moneyOrderModuleCode)
			{
				$this->_assignPaymentInfoText();
			}
			elseif($this->_getHubModuleCode() === $this->cashOnDeliveryModuleCode)
			{
				$this->_assignTransferChargeText();
			}
		}
		
		return parent::get_mail_content_array();
	}
	
	
	/**
	 * @param string $contenName
	 * @param mixed  $contentValue
	 * @param int    $deprecationLevel
	 */
	public function set_content_data($contenName, $contentValue, $deprecationLevel = 0)
	{
		if($this->order->info['payment_method'] === 'gambio_hub')
		{
			switch($contenName)
			{
				case 'PAYMENT_METHOD':
					$contentValue = $this->_getHubModuleTitle();
					break;
				case 'PAYMENT_MODUL':
					$contentValue = $this->_getHubModuleCode();
					break;
			}
		}
		
		parent::set_content_data($contenName, $contentValue, $deprecationLevel);
	}
	
	
	/**
	 * @return string
	 */
	protected function _getHubModuleCode()
	{
		if($this->hubModuleCode === '')
		{
			$this->_initHubData();
		}
		
		return $this->hubModuleCode;
	}
	
	
	/**
	 * @return string
	 */
	protected function _getHubModuleTitle()
	{
		if($this->hubModuleTitle === '')
		{
			$this->_initHubData();
		}
		
		return $this->hubModuleTitle;
	}
	
	
	/**
	 * Init module code and module title values
	 */
	protected function _initHubData()
	{
		$query  = 'SELECT
							`gambio_hub_module`,
							`gambio_hub_module_title`
						FROM `orders`
						WHERE `orders_id` = ' . (int)$this->order_id;
		$result = xtc_db_query($query);
		$row    = xtc_db_fetch_array($result);
		$title  = $row['gambio_hub_module_title'];
		
		if(strpos($title, 'Klarna') === false && preg_match('/^Klarna.*Hub$/', $row['gambio_hub_module']))
		{
			$title = 'Klarna ' . $title;
		}
		
		$this->hubModuleCode  = $row['gambio_hub_module'];
		$this->hubModuleTitle = $title;
	}
	
	
	/**
	 * Assign "Pay to" information for money order module
	 */
	protected function _assignPaymentInfoText()
	{
		$hubMoneyOrderPayToInfo = gm_get_conf('GAMBIO_HUB_MONEY_ORDER_PAY_TO');
		if($hubMoneyOrderPayToInfo !== false)
		{
			$languageTextManager    = MainFactory::create_object('LanguageTextManager', [], true);
			$hubMoneyOrderPayToInfo = $languageTextManager->get_text('hubMoneyOrderPayTo', 'gambio_hub') . "\n"
			                          . $hubMoneyOrderPayToInfo;
			
			$this->payment_info_html = nl2br($hubMoneyOrderPayToInfo);
			$this->payment_info_text = $hubMoneyOrderPayToInfo;
		}
	}
	
	
	/**
	 * Assign transfer charge info text for cash on delivery module
	 */
	protected function _assignTransferChargeText()
	{
        if (!isset($this->order->info['language'])) {
            return '';
        }
        
		$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
		$languageTextManager->init_from_lang_file('lang/' . $this->order->info['language']
		                                          . '/modules/order_total/ot_cod_fee.php');
		
		$codInfo = get_transfer_charge_text($this->order->info['shipping_class'],
		                                    $this->order->delivery['country_iso_2'], $this->order->customer['status'],
		                                    $this->order->info['currency']);
		$this->set_content_data('COD_INFO', $codInfo);
	}
}

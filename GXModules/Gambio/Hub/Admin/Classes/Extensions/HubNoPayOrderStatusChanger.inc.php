<?php
/* --------------------------------------------------------------
   HubNoPayOrderStatusChanger.inc.php 2021-04-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class HubNoPayOrderStatusChanger
 *
 * @package    Extensions
 */
class HubNoPayOrderStatusChanger
{
    /**
     * Changes all payment status from gambio hub open to not validated.
     */
    public function changeStatus()
    {
        $notValidOrderStatus = xtc_db_fetch_array(xtc_db_query('SELECT `orders_status_id` FROM `orders_status`where `orders_status_name` = "Not validated" OR `orders_status_name` = "Nicht bestätigt"'));
        $notValidOrderStatusId = isset($notValidOrderStatus['orders_status_id']) ? $notValidOrderStatus['orders_status_id'] : false;
        
        if($notValidOrderStatusId === false){
            $notValidOrderStatusId = $this->getFirstAvailableIdFromOrdersStatus();
            xtc_db_query('INSERT INTO `orders_status` (`orders_status_id`, `language_id`, `orders_status_name`, `color`) VALUES ('.$notValidOrderStatusId .', 1, "Not validated", "e0412c"), ('.$notValidOrderStatusId .', 2, "Nicht bestätigt", "e0412c")');
        }
        
        $openStatusSubQuery = '(SELECT `orders_status_id` FROM `orders_status` WHERE `orders_status_name` = "open" OR `orders_status_name` = "offen" LIMIT 1)';
        xtc_db_query('UPDATE `orders` SET `orders_status`='.$notValidOrderStatusId.' WHERE `payment_class` = "gambio_hub" AND `gambio_hub_module` = "" AND`orders_status` = ('.$openStatusSubQuery.')');
    }
    
    
    /**
     * Since the oders status has no auto increment in the id,
     * we need to check whats the next free id is to avoid overwriting existing once.
     *
     * @return int
     */
    protected function getFirstAvailableIdFromOrdersStatus()
    {
        $firstFreeOderStatusId = xtc_db_fetch_array(xtc_db_query('SELECT (`orders_status_id` + 1) as id  FROM `orders_status` a WHERE NOT EXISTS(SELECT * FROM `orders_status` b WHERE b.orders_status_id = a.orders_status_id + 1) ORDER BY `orders_status_id` LIMIT 1'));
        
        return $firstFreeOderStatusId['id'];
    }
}
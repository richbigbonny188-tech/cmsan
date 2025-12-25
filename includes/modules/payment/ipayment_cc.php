<?php
/* --------------------------------------------------------------
   ipayment_cc.php 2020-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once __DIR__ . '/ipayment/ipayment.php';

class ipayment_cc extends ipayment
{
    public function __construct()
    {
        $this->code = 'ipayment_cc';
        parent::__construct();
        if (defined('MODULE_PAYMENT_IPAYMENT_CC_STATUS') && !defined('MODULE_PAYMENT_IPAYMENT_CC_CARDS_ENABLED')) {
            $query = "insert into `gx_configurations` ( `key`, `value`, `sort_order`, `type`, `last_modified`) "
                     . "values ('configuration/MODULE_PAYMENT_IPAYMENT_CC_CARDS_ENABLED', 'master,visa,amex,diners,jcb,solo,discover,maestro', '20', '', '', now())";
            xtc_db_query($query);
        }
    }
    
    
    public function _configuration()
    {
        $config                  = parent::_configuration();
        $config['CARDS_ENABLED'] = [
            '`value`' => 'master,visa,amex,diners,jcb,solo,discover,maestro',
        ];
        
        return $config;
    }
}

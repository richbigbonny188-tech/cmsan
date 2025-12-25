<?php
/* -----------------------------------------------------------------------------------------
   $Id: postfinanceag_mastercard.php, v.2.1 swisswebXperts GmbH
   2023-04-28 swisswebXperts GmbH

	 Copyright (c) 2023 swisswebXperts GmbH www.swisswebxperts.ch
	 Released under the GNU General Public License (Version 2)
	 [http://www.gnu.org/licenses/gpl-2.0.html]
   ---------------------------------------------------------------------------------------*/
include_once('postfinanceag/postfinance.php');

class postfinanceag_mastercard_ORIGIN extends postfinance
{
    var $title, $description, $enabled, $orderid, $productive;
    
    public $images;

    public function __construct()
    {
        $this->code = 'postfinanceag_mastercard';
        $this->images = array('master');

        $this->paymentMethod = 'CreditCard';
        $this->paymentBrand  = 'MasterCard';

        parent::__construct();
    }

    function install()
    {
        $configSQL = "INSERT INTO `gx_configurations`
            (
                `key`,
                `value`,
                `sort_order`,
                `type`,
                `last_modified`
            ) VALUES
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_STATUS',
                'True',
                10,
                'switcher',
                now()
            ),
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_SORT_ORDER',
                '2',
                20,
                null,
                now()
            ),
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_ALLOWED',
                '',
                80,
                null,
                now()
            ),
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_CURRENCY',
                'Selected Currency',
                90,
                'chf-eur-usd',
                now()
            ),
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_ZONE',
                '0',
                100,
                'geo-zone',
                now()
            )
        ";

        xtc_db_query($configSQL);
    }
}
MainFactory::load_origin_class('postfinanceag_mastercard');
<?php
/* --------------------------------------------------------------
   OrderShippingOptionsContentView.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class OrderShippingOptionsContentView extends ContentView
{
    /**
     * @var OrderShippingOptionCollection
     */
    protected $shippingOptionCollection;
    
    
    public function __construct(OrderShippingOptionCollection $shippingOptionCollection)
    {
        parent::__construct();
        $this->shippingOptionCollection = $shippingOptionCollection;
        $this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/order_shipping_options/');
        $this->set_content_template('order_shipping_options.html');
    }
    
    
    public function prepare_data()
    {
        $this->set_content_data('shipping_options', $this->shippingOptionCollection);
    }
}

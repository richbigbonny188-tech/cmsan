<?php
/*
 * 888888ba                 dP  .88888.                    dP
 * 88    `8b                88 d8'   `88                   88
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b.
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P'
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * (c) 2010 - 2021 RedGecko GmbH -- http://www.redgecko.de
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 * -----------------------------------------------------------------------------
 */

class MagnalisterOrderExtender extends MagnalisterOrderExtender_parent
{
    public function proceed()
    {
        if ((bool)gm_get_conf('MODULE_CENTER_MAGNALISTER_INSTALLED') === true) {
            $result = magnaExecute('magnaRenderOrderDetails',
                                   ['oID' => $this->v_data_array['GET']['oID'], 'separate' => true],
                                   ['order_details.php']);
            
            if (!empty($result) && is_array($result)) {
                $this->v_output_buffer['below_order_info_heading'] = $result['headline'];
                $this->v_output_buffer['below_order_info']         = $result['body'];
                $this->addContent();
            }
        }
        
        parent::proceed();
    }
}

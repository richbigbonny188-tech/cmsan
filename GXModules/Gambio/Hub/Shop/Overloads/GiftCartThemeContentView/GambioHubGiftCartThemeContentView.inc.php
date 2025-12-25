<?php
/* --------------------------------------------------------------
   GambioHubGiftCartThemeContentView.inc.php 2017-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubGiftCartThemeContentView extends GambioHubGiftCartThemeContentView_parent
{
    protected function _setFormData()
    {
        parent::_setFormData();
        $this->set_content_data('GV_LINK_DONOTUSEBALANCE', xtc_href_link('shop.php', 'do=Cart/DoNotUseBalance', 'SSL'));
        $this->set_content_data('GV_LINK_USEBALANCE', xtc_href_link('shop.php', 'do=Cart/UseBalance', 'SSL'));
    }
}

<?php
/* --------------------------------------------------------------
   HermesHSIOrderExtender.inc.php 2019-10-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);


class HermesHSIOrderExtender extends HermesHSIOrderExtender_parent
{
    public function proceed()
    {
        parent::proceed();
        if ((bool)gm_get_conf('MODULE_CENTER_HERMESHSI_INSTALLED') === true) {
            $scriptFileUrl = DIR_WS_ADMIN . 'html/assets/javascript/modules/hermeshsi/hermeshsi-orderdetails.min.js';
            $scriptTag     = sprintf('<div class="hermeshsi_orderdetails"><script src="%s"></script></div>',
                                     $scriptFileUrl);
            $this->addContentToCollection('below_history', $scriptTag, '');
        }
    }
}

<?php
/* --------------------------------------------------------------
   DynamicShopMessages.inc.php 2018-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class DynamicShopMessages
 *
 * This controller fetches the dynamic shop messages from the shop portal through a CURL request. It will only perform
 * the request once a day and use the data cache for this reason (performance).
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class DynamicShopMessagesController extends AdminHttpViewController
{
    /**
     * Default controller callback.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionDefault()
    {
        $adminFeedFacade = MainFactory::create(AdminFeedFacade::class);
        
        return $adminFeedFacade->dynamicShopMessages();
    }
}
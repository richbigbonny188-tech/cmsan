<?php
/* --------------------------------------------------------------
   AdminFeedFacade.inc.php 2018-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminFeedFacade
 */
class AdminFeedFacade
{
    /**
     * Returns the admin news as html.
     *
     * @return string
     */
    public function adminNews()
    {
        $adminNews = MainFactory::create(AdminNews::class);
        
        return $adminNews->news();
    }
    
    
    /**
     * Returns the messages as a json response for the dynamic shop messages system.
     *
     * @return JsonHttpControllerResponse
     */
    public function dynamicShopMessages()
    {
        $externalSnippets = MainFactory::create(DynamicShopMessages::class);
        
        return $externalSnippets->messages();
    }
    
    
    /**
     * Returns the messages as a json response for the dynamic shop messages system.
     *
     * @return array
     */
    public function adminInfoboxMessages()
    {
        $adminInfobox = MainFactory::create(AdminInfobox::class);
        
        return $adminInfobox->messages();
    }
    
    
    /**
     * Sends the given server info and comment to Gambio.
     *
     * @param string $serverInfo
     * @param string $comment
     *
     * @return bool
     */
    public function sendServerInfo($serverInfo, $comment)
    {
        $serverInfoControl = MainFactory::create(ServerInfo::class);
        
        return $serverInfoControl->send($serverInfo, $comment);
    }
}
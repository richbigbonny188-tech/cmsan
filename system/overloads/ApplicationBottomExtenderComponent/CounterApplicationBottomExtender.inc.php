<?php
/* --------------------------------------------------------------
   CounterApplicationBottomExtender.inc.php 2022-11-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Jaybizzle\CrawlerDetect\CrawlerDetect;

class CounterApplicationBottomExtender extends CounterApplicationBottomExtender_parent
{
    public function proceed()
    {
        if ($this->isBot()) {
            
            parent::proceed();
            
            return;
        }
        
        $_SESSION['gm_tracking'] = $_SESSION['gm_tracking'] ?? MainFactory::create('GMC');
        $_SESSION['gm_tracking']->gmc_record($this->v_data_array['products_id'], $this->v_data_array['cPath']);
        $_SESSION['gm_tracking']->gmc_delete_old_ip();
        
        // track user
        $this->v_output_buffer['SCRIPT_COUNTER'] = $_SESSION['gm_tracking']->gmc_set_current_user(false);
        
        parent::proceed();
    }
    
    
    /**
     * @return bool
     */
    protected function isBot(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUEST_BY']) && strtolower($_SERVER['HTTP_X_REQUEST_BY']) === 'monitoring')
               || (new CrawlerDetect)->isCrawler();
    }
}

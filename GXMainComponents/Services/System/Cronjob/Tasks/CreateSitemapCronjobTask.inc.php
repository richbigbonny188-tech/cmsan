<?php
/* --------------------------------------------------------------
   CreateSitemapCronjobTask.inc.php 2019-10-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'inc/xtc_category_link.inc.php';
require_once DIR_FS_CATALOG . 'inc/xtc_product_link.inc.php';
require_once DIR_FS_CATALOG . 'inc/xtc_cleanName.inc.php';
require_once DIR_FS_CATALOG . 'gm/inc/gm_xtc_href_link.inc.php';

/**
 * Class CreateSitemapCronjobTask
 */
class CreateSitemapCronjobTask extends AbstractCronjobTask
{
    const MAX_EXECUTION_TIME_IN_SECONDS = 20;
    
    
    /**
     * Returns a closure for execution in Jobby. If contains the logic for creating XML sitemaps.
     *
     * @param float $cronjobStartAsMicrotime
     *
     * @return \Closure
     */
    public function getCallback($cronjobStartAsMicrotime)
    {
        return function () use ($cronjobStartAsMicrotime) {
            
            $this->logger->lastRun();
            
            if (file_exists(DIR_FS_CATALOG . 'cache/cronjobs/sitemap_paused')) {
                unlink(DIR_FS_CATALOG . 'cache/cronjobs/sitemap_paused');
            }
            
            /** @var GMSitemapXML_ORIGIN $sitemap */
            $sitemap = $this->dependencies->getDependencies()['GMSitemapXML'];
            
            /** @var \DataCache $dataCache */
            $dataCache = $this->dependencies->getDependencies()['DataCache'];
            
            $sitemap->setExportUrlOnlyOnce(true);
            
            while ($categories = !$sitemap->prepare()) {
                // pause sitemap creation if timeout is reached
                if (microtime(true) - $cronjobStartAsMicrotime > self::MAX_EXECUTION_TIME_IN_SECONDS) {
                    touch(DIR_FS_CATALOG . 'cache/cronjobs/sitemap_paused');
                    
                    break;
                }
            }
            
            $dataCache->set_data('sitemap_categories', true, true);
            
            if (!file_exists(DIR_FS_CATALOG . 'cache/cronjobs/sitemap_paused')) {
                $sitemap->generate(true);
                $this->logger->log(['Sitemap successfully created.']);
            } else {
                $this->logger->log(['Sitemap creation in progress.']);
            }
            
            $this->logger->lastSuccess();
            
            return true;
        };
        
        return $this->closure;
    }
    
    
    /**
     * Returns the cronjob schedule.
     *
     * @return string
     */
    public function getSchedule()
    {
        if (file_exists(DIR_FS_CATALOG . 'cache/cronjobs/sitemap_paused')) {
            return '* * * * *';
        }
        
        return parent::getSchedule();
    }
}

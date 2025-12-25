<?php
/* --------------------------------------------------------------
   CreateSitemapCronjobDependencies.inc.php 2018-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CreateSitemapCronjobDependencies extends AbstractCronjobDependencies
{
    /**
     * Returns an array of instantiated dependencies for cronjob services.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'GMSitemapXML' => MainFactory::create('GMSitemapXML'),
            'DataCache'    => DataCache::get_instance()
        ];
    }
    
}
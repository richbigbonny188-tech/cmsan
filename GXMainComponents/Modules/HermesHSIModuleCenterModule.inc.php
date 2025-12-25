<?php
/* --------------------------------------------------------------
   HermesHSIModuleCenterModule.inc.php 2019-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HermesHSIModuleCenterModule extends AbstractModuleCenterModule
{
    
    /**
     * Initialize the module e.g. set title, description, sort order etc.
     *
     * Function will be called in the constructor
     */
    protected function _init(): void
    {
        $this->title       = $this->languageTextManager->get_text('hermeshsi_title');
        $this->description = $this->languageTextManager->get_text('hermeshsi_description');
        $this->sortOrder   = 20031;
    }
    
    /**
     * Installs the module
     */
    public function install()
    {
        $query = 'CREATE TABLE IF NOT EXISTS `hermeshsi_shipments` (
              `hermeshsi_shipments_id` int(11) NOT NULL AUTO_INCREMENT,
              `orders_id` int(11) NOT NULL,
              `shipment_id` varchar(16) NOT NULL,
              `test_mode` tinyint(1) NOT NULL,
              `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`hermeshsi_shipments_id`),
              KEY `orders_id_idx` (`orders_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
        $db    = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->query($query);
        
        parent::install();
    }

}

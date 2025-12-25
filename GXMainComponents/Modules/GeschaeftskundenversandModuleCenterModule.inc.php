<?php
/* --------------------------------------------------------------
	GeschaeftskundenversandModuleCenterModule.inc.php 2022-07-12
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2022 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class GeschaeftskundenversandModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class GeschaeftskundenversandModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * Initializes Geschäftskundenversand module center module
     *
     * @return void
     */
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('geschaeftskundenversand_title');
        $this->description = $this->languageTextManager->get_text('geschaeftskundenversand_description');
        $this->sortOrder   = 26963;
    }
    
    
    /**
     * Installs the module
     */
    public function install()
    {
        $query = 'CREATE TABLE IF NOT EXISTS `gkv_shipments` (
					`gkv_shipments_id` INT          NOT NULL AUTO_INCREMENT ,
					`orders_id`        INT          NOT NULL ,
					`shipmentnumber`   VARCHAR(39)  NOT NULL ,
                    `returnshipmentnumber` VARCHAR(39) NOT NULL DEFAULT \'\',
					`labelurl`         TEXT         NOT NULL ,
					`returnlabelurl`   TEXT         NOT NULL ,
					`exportlabelurl`   TEXT         NOT NULL ,
					`codlabelurl`      TEXT         NOT NULL ,
					`last_modified`    TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
					PRIMARY KEY (`gkv_shipments_id`),
					INDEX `orders_id` (`orders_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $db    = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->query($query);
        
        parent::install();
    }
}

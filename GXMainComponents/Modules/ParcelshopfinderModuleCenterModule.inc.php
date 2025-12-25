<?php
/* --------------------------------------------------------------
	ParcelshopfinderModuleCenterModule.inc.php 2017-04-04
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ParcelshopfinderModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class ParcelshopfinderModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * Initializes Parcelshopfinder module center module
     *
     * @return void
     */
    protected function _init()
    {
        $text              = MainFactory::create('LanguageTextManager', 'parcelshopfinder', $_SESSION['languages_id']);
        $this->title       = $text->get_text('module_title');
        $this->description = $text->get_text('module_description');
        $this->sortOrder   = 28475;
    }
}

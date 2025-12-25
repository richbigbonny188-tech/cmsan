<?php

/* --------------------------------------------------------------
	ResponsiveFileManagerModuleCenterModule.inc.php 2017-09-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2017 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ResponsiveFileManagerModuleCenterModule.
 *
 * Class representing the module center module for the responsive file manager.
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class ResponsiveFileManagerModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * Initializes the responsive file manager module center module.
     *
     * @return void
     */
    protected function _init()
    {
        $text              = MainFactory::create('LanguageTextManager',
                                                 'responsivefilemanager',
                                                 $_SESSION['languages_id']);
        $this->title       = $text->get_text('module_title');
        $this->description = $text->get_text('module_description');
        $this->sortOrder   = 28476;
    }
    
    
    /**
     * Returns true, if the module should be displayed in module center.
     *
     * @return bool
     */
    public function isVisible()
    {
        return file_exists(DIR_FS_CATALOG . 'ResponsiveFilemanager');
    }
}

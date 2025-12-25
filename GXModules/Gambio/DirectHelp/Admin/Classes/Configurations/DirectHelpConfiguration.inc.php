<?php

/* --------------------------------------------------------------
   DirectHelpConfiguration.inc.php 2018-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the general direct help module configuration
 */
class DirectHelpConfiguration
{
    /**
     * Build assets directory path
     */
    const ASSETS_BASE = DIR_WS_CATALOG . 'GXModules/Gambio/DirectHelp/Build';
    
    /**
     * Link provider proxy URL
     */
    const PROXY_URL = 'admin.php?do=DirectHelpProxy';
}
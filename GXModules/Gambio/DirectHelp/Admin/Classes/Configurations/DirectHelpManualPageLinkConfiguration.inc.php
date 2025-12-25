<?php

/* --------------------------------------------------------------
   DirectHelpManualPageLinkConfiguration.inc.php 2018-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the online manual page link configuration
 */
class DirectHelpManualPageLinkConfiguration
{
    /**
     * Maximum mapping file cache time (1 day)
     *
     * @var int
     */
    const MAX_LOCAL_MAPPING_FILE_TTL = 86400;
    
    /**
     * Remote mapping file location
     *
     * @var string
     */
    const REMOTE_MAPPING_FILE_LOCATION = 'https://developers.gambio.de/manual/links.php';
    
    /**
     * Local mapping file location
     *
     * @var string
     */
    const LOCAL_MAPPING_FILE_LOCATION = DIR_FS_CATALOG . 'cache/online_manual_links.json';
}
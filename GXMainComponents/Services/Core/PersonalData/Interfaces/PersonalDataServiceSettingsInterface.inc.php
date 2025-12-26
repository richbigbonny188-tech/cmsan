<?php
/* --------------------------------------------------------------
   PersonalDataServiceSettingsInterface.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface PersonalDataServiceSettingsInterface
 */
interface PersonalDataServiceSettingsInterface
{
    /**
     * Returns the path to export zip file directory.
     *
     * @return string
     */
    public function getExportZipFilePath();
    
    
    /**
     * Returns the path to export file directory.
     *
     * @return string
     */
    public function getExportFilePath();
    
    
    /**
     * Returns the path to export zip file directory from shop root.
     *
     * @return string
     */
    public function getZipDownloadFilePath();
}
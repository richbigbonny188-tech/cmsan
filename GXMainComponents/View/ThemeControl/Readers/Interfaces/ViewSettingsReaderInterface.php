<?php
/*--------------------------------------------------------------------------------------------------
    ViewSettingsReaderInterface.php 2019-09-12
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

/**
 * Interface ViewSettingsReaderInterface
 */
interface ViewSettingsReaderInterface
{
    /**
     * @return ViewSettings
     */
    public function get(): ViewSettings;
}
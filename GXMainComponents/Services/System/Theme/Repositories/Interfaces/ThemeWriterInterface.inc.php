<?php
/* --------------------------------------------------------------
   ThemeWriterInterface.inc.php 2018-11-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ThemeWriterInterface
 */
interface ThemeWriterInterface
{
    /**
     * Saves the given them to the destination.
     *
     * @param \ThemeInterface              $theme       Theme to be saved.
     * @param \ThemeDirectoryRootInterface $destination Destination directory.
     *
     * @return void
     */
    public function save(ThemeInterface $theme, ThemeDirectoryRootInterface $destination);
}

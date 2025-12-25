<?php
/* --------------------------------------------------------------
  InstallationStatusInterface.php 2019-12-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface InstallationStatusInterface
 */
interface InstallationStatusInterface
{
    /**
     * @return bool
     */
    public function isInstalled(): bool;
}
<?php
/* --------------------------------------------------------------
  GmConfigurationReaderInterface.php 2019-08-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface GmConfigurationReaderInterface
 */
interface GmConfigurationReaderInterface
{
    /**
     * @param string $key
     *
     * @return string[]
     * @throws GmConfigurationNotFoundException
     */
    public function getConfigurationByKey(string $key): array;
}
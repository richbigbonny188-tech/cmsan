<?php
/* --------------------------------------------------------------
  GmConfigurationFactoryInterface.php 2019-08-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface GmConfigurationFactoryInterface
 */
interface GmConfigurationFactoryInterface
{
    /**
     * @param int    $id
     * @param string $key
     * @param string $value
     * @param int    $groupId
     * @param int    $sortOrder
     *
     * @return GmConfigurationInterface
     */
    public function createGmConfiguration(
        int $id,
        string $key,
        string $value,
        int $groupId,
        int $sortOrder
    ): GmConfigurationInterface;
}
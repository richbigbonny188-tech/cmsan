<?php
/* --------------------------------------------------------------
  GmConfigurationWriterInterface.php 2019-08-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface GmConfigurationWriterInterface
 */
interface GmConfigurationWriterInterface
{
    /**
     * @param GmConfigurationInterface $configuration
     */
    public function updateGmConfiguration(GmConfigurationInterface $configuration): void;
}
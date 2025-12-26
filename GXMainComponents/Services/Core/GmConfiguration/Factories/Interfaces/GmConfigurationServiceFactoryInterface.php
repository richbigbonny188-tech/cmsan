<?php
/* --------------------------------------------------------------
  GmConfigurationServiceFactoryInterface.php 2019-08-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface GmConfigurationServiceFactoryInterface
 */
interface GmConfigurationServiceFactoryInterface
{
    /**
     * @return GmConfigurationReaderInterface
     */
    public function reader(): GmConfigurationReaderInterface;
    
    
    /**
     * @return GmConfigurationRepositoryInterface
     */
    public function repository(): GmConfigurationRepositoryInterface;
    
    
    /**
     * @return GmConfigurationFactoryInterface
     */
    public function factory(): GmConfigurationFactoryInterface;
    
    
    /**
     * @return GmConfigurationServiceInterface
     */
    public function service(): GmConfigurationServiceInterface;
}
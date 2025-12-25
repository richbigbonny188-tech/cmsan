<?php
/* --------------------------------------------------------------
  ThemeSettingsServiceFactoryInterface.php 2019-08-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Factories\Interfaces;

use Gambio\GX\Services\System\ThemeSettings\Interfaces\ThemeSettingsServiceInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsReaderInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsRepositoryInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsWriterInterface;

/**
 * Interface ThemeSettingsServiceFactoryInterface
 */
interface ThemeSettingsServiceFactoryInterface
{
    /**
     * @return ThemeSettingsRepositoryInterface
     */
    public function repository(): ThemeSettingsRepositoryInterface;
    
    
    /**
     * @return ThemeSettingsFactoryInterface
     */
    public function factory(): ThemeSettingsFactoryInterface;
    
    
    /**
     * @return ThemeSettingsReaderInterface
     */
    public function reader(): ThemeSettingsReaderInterface;
    
    
    /**
     * @return ThemeSettingsWriterInterface
     */
    public function writer(): ThemeSettingsWriterInterface;
    
    
    /**
     * @return ThemeSettingsServiceInterface
     */
    public function service(): ThemeSettingsServiceInterface;
}
<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationInterface.php 2019-09-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces;

use stdClass;

/**
 * Interface StyleEdit3ConfigurationInterface
 */
interface StyleEdit3ConfigurationInterface
{
    /**
     * Is this configuration for a template or a theme
     *
     * @return string
     */
    public function type(): string;
    
    
    /**
     * Users chosen name for the configuration
     *
     * @return string
     */
    public function name(): string;
    
    
    /**
     * @return bool
     */
    public function isActive(): bool;
    
    
    /**
     * @return string path to the configuration file
     */
    public function path(): string;
    
    
    /**
     * @return stdClass[]
     */
    public function settings(): array;
    
    
    /**
     * @return string
     */
    public function style(): string;
    
    
    /**
     * @return string
     */
    public function customCss(): string;
}
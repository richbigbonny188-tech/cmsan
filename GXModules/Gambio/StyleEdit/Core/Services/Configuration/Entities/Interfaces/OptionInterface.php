<?php
/* --------------------------------------------------------------
  OptionInterface.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces;

/**
 * Interface OptionInterface
 */
interface OptionInterface
{
    /**
     * @return string|null
     */
    public function type(): ?string;
    
    
    /**
     * @return string
     */
    public function name(): string;
    
    
    /**
     * @return mixed
     */
    public function value();
    
    
    /**
     * @return string
     */
    public function group(): string;
}
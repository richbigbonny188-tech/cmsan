<?php
/* --------------------------------------------------------------
   OptionInterface.php 2023-05-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio05-25
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Options\Entities;

use Gambio\StyleEdit\Core\Language\Entities\Language;

/**
 * Interface OptionInterface
 * @package Gambio\StyleEdit\Core\Options\Entities
 */
interface OptionInterface
{
    /**
     * @return string|null
     */
    public function id(): ?string;
    
    
    /**
     * @return string|null
     */
    public function type(): ?string;
    
    
    /**
     * @return mixed
     */
    public function defaultValue();
    
    
    /**
     * @param Language|null $language
     *
     * @return mixed
     */
    public function value(?Language $language = null);
    
    
    /**
     * @return string|null
     */
    public function for(): ?string;


    /**
     * @return bool
     */
    public function translatable(): bool;


    /**
     * @return bool
     */
    public function hidden(): bool;
}
<?php
/* --------------------------------------------------------------
   SingletonPrototype.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Command;

use Gambio\StyleEdit\Core\Options\Entities\OptionInterface;

/**
 * Interface CommandInterface
 * @package Gambio\StyleEdit\Core\Command
 */
interface CommandInterface
{
    
    /**
     * Execute the command
     */
    public function execute(): void;
    
    
    /**
     * Execute the command
     */
    public function rollback(): void;
    
    
    /**
     * @param \Gambio\StyleEdit\Core\Options\Entities\OptionInterface $option
     */
    public function setOption(OptionInterface $option): void;
    
}
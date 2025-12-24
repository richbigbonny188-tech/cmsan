<?php
/* --------------------------------------------------------------
   DeterminesFqn.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data\Processors;

use Gambio\Core\Application\ValueObjects\Path;

trait DeterminesFqn
{
    /**
     * @var Path
     */
    private $path;
    
    
    /**
     * Translates the path to a GXModules namespace.
     *
     * @param string $path
     *
     * @return string
     */
    private function determineFqn(string $path): string
    {
        return str_replace(["{$this->path->base()}/", '.php', '/'], ['', '', '\\'], $path);
    }
}
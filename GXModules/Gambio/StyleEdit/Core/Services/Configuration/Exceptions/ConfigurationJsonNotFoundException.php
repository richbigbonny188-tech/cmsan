<?php
/* --------------------------------------------------------------
  ConfigurationJsonNotFoundException.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Exceptions;

use Exception;
use Throwable;

/**
 * Class ConfigurationJsonNotFoundException
 */
class ConfigurationJsonNotFoundException extends Exception
{
    /**
     * ConfigurationJsonNotFoundException constructor.
     *
     * @param string         $path
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $path, $code = 0, Throwable $previous = null)
    {
        $message = "File was not found in the path ($path)";
        
        parent::__construct($message, $code, $previous);
    }
}
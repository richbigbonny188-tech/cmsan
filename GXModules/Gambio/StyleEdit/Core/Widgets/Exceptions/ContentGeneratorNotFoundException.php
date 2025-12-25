<?php
/* --------------------------------------------------------------
  ContentGeneratorNotFoundException.php 2019-08-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Widgets\Exceptions;

use Exception;
use Throwable;

/**
 * Class ContentGeneratorNotFoundException
 */
class ContentGeneratorNotFoundException extends Exception
{
    /**
     * ContentGeneratorNotFoundException constructor.
     *
     * @param string         $contentGenerator
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $contentGenerator, int $code = 0, Throwable $previous = null)
    {
        $message = "ContentGenerator ($contentGenerator) was not found!";
        
        parent::__construct($message, $code, $previous);
    }
}
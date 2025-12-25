<?php
/* --------------------------------------------------------------
  AliasNotFoundException.php 2019-12-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Mapper\Exceptions;

use Exception;

/**
 * Class AliasNotFoundException
 * @package Gambio\StyleEdit\Core\Mapper\Exceptions
 */
class AliasNotFoundException extends Exception
{
    /**
     * AliasNotFoundException constructor.
     *
     * @param int            $contentGroup
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(int $contentGroup, $code = 0, Throwable $previous = null)
    {
        $message = 'No alias was found with the ContentGroup ' . $contentGroup;
        parent::__construct($message, $code, $previous);
    }
}
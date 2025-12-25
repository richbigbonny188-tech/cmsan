<?php
/* --------------------------------------------------------------
  ContentGeneratorDoesNotImplementStyleEditApiDataProviderInterface.php 2019-08-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Widgets\Exceptions;

use Exception;
use Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces\StyleEditApiDataProviderInterface;
use Throwable;

/**
 * Class ContentGeneratorDoesNotImplementStyleEditApiDataProviderInterface
 */
class ContentGeneratorDoesNotImplementStyleEditApiDataProviderInterface extends Exception
{
    /**
     * ContentGeneratorDoesNotImplementProvidesApiDataInterface constructor.
     *
     * @param string         $contentGenerator
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $contentGenerator, $code = 0, Throwable $previous = null)
    {
        $message = "ContentGenerator ($contentGenerator) does not implement "
                   . StyleEditApiDataProviderInterface::class;
        
        parent::__construct($message, $code, $previous);
    }
}
<?php
/*--------------------------------------------------------------------------------------------------
    NotFoundException.php 2019-10-24
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\StyleEdit\Core\BuildStrategies\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class NotFoundException
 * @package Gambio\StyleEdit\Core\BuildStrategies\Exceptions
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
    
}
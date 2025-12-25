<?php
/* --------------------------------------------------------------
   InvalidPictureException.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products\Exceptions;

use RuntimeException;

/**
 * Class ImageRetrievalException
 *
 * Thrown if something is wrong with a ProductPicture.
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\Exceptions
 */
class InvalidPictureException extends RuntimeException
{
    
}
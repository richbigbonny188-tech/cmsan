<?php
/* --------------------------------------------------------------
   LanguageNotFoundException.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\Model\Exceptions;

use Exception;

/**
 * Class LanguageNotFoundException
 *
 * @package Gambio\Admin\Modules\Language\Model\Exceptions
 */
class LanguageNotFoundException extends Exception
{
    /**
     * Creates a new LanguageNotFoundException instance for a provided ID.
     *
     * @param int $id
     *
     * @return static
     */
    public static function forId(int $id): self
    {
        return new self('Could no found language with ID "' . $id . '".');
    }
    
    
    /**
     * Creates a new LanguageNotFoundException instance for a provided code.
     *
     * @param string $code
     *
     * @return static
     */
    public static function forCode(string $code): self
    {
        return new self('Could no found language with code "' . $code . '".');
    }
}
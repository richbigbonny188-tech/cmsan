<?php
/* --------------------------------------------------------------
   SecurityToken.php 2021-05-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\Core\Application\ValueObjects;

use Gambio\Core\Application\ValueObjects\Exceptions\InvalidSecurityTokenException;

/**
 * Class SecurityToken
 * @package Gambio\Core\Application\ValueObjects
 */
class SecurityToken
{
    /**
     * @var string
     */
    private $value;


    /**
     * SecurityToken constructor.
     *
     * @param string $value
     *
     * @throws InvalidSecurityTokenException
     */
    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }


    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }


    /**
     * @param string $value
     *
     * @throws InvalidSecurityTokenException
     */
    private function validate(string $value): void
    {
        if (!preg_match('/^[0-9a-f]{32}$/', $value)) {
            throw new InvalidSecurityTokenException("Security token '$value' is not a 32 characters long hexadecimal string.");
        }
    }
}

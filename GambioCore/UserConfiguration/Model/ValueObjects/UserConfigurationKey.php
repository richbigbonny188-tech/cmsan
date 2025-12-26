<?php
/* --------------------------------------------------------------
   UserConfigurationKey.php 2021-05-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\UserConfiguration\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class UserConfigurationKey
 *
 * @package Gambio\Core\UserConfiguration\Model\ValueObjects
 */
class UserConfigurationKey
{
    /**
     * @var string
     */
    private $value;
    
    
    /**
     * UserConfigurationKey constructor.
     *
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param string $value
     *
     * @return UserConfigurationKey
     */
    public static function create(string $value): UserConfigurationKey
    {
        Assert::notWhitespaceOnly($value, 'User configuration key can not be empty.');
        
        return new self($value);
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}
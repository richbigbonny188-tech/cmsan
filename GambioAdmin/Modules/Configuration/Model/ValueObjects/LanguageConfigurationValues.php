<?php
/* --------------------------------------------------------------
 LanguageConfigurationValues.php 2022-08-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Model\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Class LanguageConfigurationValues
 * @package Gambio\Admin\Modules\Configuration\Model\ValueObjects
 */
class LanguageConfigurationValues implements JsonSerializable
{
    /**
     * @var array
     */
    private $values = [];
    
    
    /**
     * LanguageConfigurationValues constructor.
     *
     * @param array $languageValues
     */
    public function __construct(array $languageValues)
    {
        foreach ($languageValues as $languageCode => $value) {
            if (!is_string($languageCode) || strlen($languageCode) !== 2) {
                $this->throwInvalidArgumentException('language code', $languageCode);
            }
            if (!is_string($value)) {
                $this->throwInvalidArgumentException('value', $value);
            }
            $this->values[$languageCode] = $value;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->values;
    }
    
    
    /**
     * Throws an invalid argument exception.
     *
     * @param string $type
     * @param        $value
     */
    private function throwInvalidArgumentException(string $type, $value): void
    {
        $notice = var_export($value, true);
        throw new InvalidArgumentException("Invalid $type ($notice) provided.");
    }
}
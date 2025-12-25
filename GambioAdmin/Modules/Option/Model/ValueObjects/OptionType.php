<?php
/* --------------------------------------------------------------
   OptionType.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class OptionType
 *
 * @package Gambio\Admin\Modules\Option\Model\ValueObjects
 * @codeCoverageIgnore
 */
class OptionType
{
    public const DROPDOWN_TYPE   = 'Dropdown';
    public const IMAGE_TYPE      = 'Image';
    public const RADIO_TYPE      = 'Radio';
    public const TEXT_TYPE       = 'Text';
    public const BOXED_TEXT_TYPE = 'BoxedText';
    public const ALLOWED_TYPES   = [
        self::DROPDOWN_TYPE,
        self::IMAGE_TYPE,
        self::RADIO_TYPE,
        self::TEXT_TYPE,
        self::BOXED_TEXT_TYPE,
    ];
    
    /**
     * @var string
     */
    private $value;
    
    
    /**
     * OptionType constructor.
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
     * @return OptionType
     */
    public static function create(string $value): OptionType
    {
        Assert::oneOf($value,
                      self::ALLOWED_TYPES,
                      'Invalid type given. Need to be one of: ' . implode(', ', self::ALLOWED_TYPES) . '; Got: %s');
        
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
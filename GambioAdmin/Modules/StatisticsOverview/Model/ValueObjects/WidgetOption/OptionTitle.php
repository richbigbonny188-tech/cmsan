<?php
/*--------------------------------------------------------------
   OptionTitle.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption;

use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\StatisticsOverview\Model\Features\WithLanguageCode;
use Webmozart\Assert\Assert;

/**
 * Class representing an option title.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption
 */
class OptionTitle implements WithLanguageCode
{
    /**
     * Value.
     *
     * @var string
     */
    private $value;
    
    /**
     * Language code.
     *
     * @var LanguageCode
     */
    private $languageCode;
    
    
    /**
     * Constructor.
     *
     * @param LanguageCode $languageCode Language code.
     * @param string       $value        Value.
     */
    private function __construct(LanguageCode $languageCode, string $value)
    {
        Assert::notEmpty($value);
        
        $this->value        = $value;
        $this->languageCode = $languageCode;
    }
    
    
    /**
     * Create instance.
     *
     * @param LanguageCode $languageCode Language code.
     * @param string       $title        Value.
     *
     * @return OptionTitle Instance.
     */
    public static function create(LanguageCode $languageCode, string $title): self
    {
        return new self($languageCode, $title);
    }
    
    
    /**
     * Return value.
     *
     * @return string Value.
     */
    public function value(): string
    {
        return $this->value;
    }
    
    
    /**
     * @inheritDoc
     */
    public function languageCode(): LanguageCode
    {
        return $this->languageCode;
    }
}
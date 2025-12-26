<?php
/*--------------------------------------------------------------
   TextData.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TextData\TextDataValue;

/**
 * Class representing text data.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData
 */
class TextData implements WidgetData
{
    /**
     * Type name.
     */
    private const TYPE = "text";
    
    /**
     * Value.
     *
     * @var TextDataValue
     */
    private $text;
    
    /**
     * Type.
     *
     * @var string
     */
    private $type;
    
    
    /**
     * Constructor.
     *
     * @param TextDataValue $text Value.
     */
    private function __construct(TextDataValue $text)
    {
        $this->text = $text;
        $this->type = self::TYPE;
    }
    
    
    /**
     * Create instance.
     *
     * @param TextDataValue $text Value.
     *
     * @return TextData Instance.
     */
    public static function create(TextDataValue $text): self
    {
        return new self($text);
    }
    
    
    /**
     * Return value.
     *
     * @return TextDataValue Value.
     */
    public function text(): TextDataValue
    {
        return $this->text;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }
}
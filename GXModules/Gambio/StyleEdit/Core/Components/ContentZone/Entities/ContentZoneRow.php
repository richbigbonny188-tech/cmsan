<?php
/* --------------------------------------------------------------
  ContentZoneRow.php 2021-10-04
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\ContentZone\Entities;

use Exception;
use Gambio\StyleEdit\Core\Components\BackgroundGroup\Entities\BackgroundGroupOption;
use Gambio\StyleEdit\Core\Components\BorderGroup\Entities\BorderGroupOption;
use Gambio\StyleEdit\Core\Components\Checkbox\Entities\CheckboxOption;
use Gambio\StyleEdit\Core\Components\ContentZone\Entities\Traits\ContentZoneNormalizeTrait;
use Gambio\StyleEdit\Core\Components\MarginGroup\Entities\MarginGroupOption;
use Gambio\StyleEdit\Core\Components\PaddingGroup\Entities\PaddingGroupOption;
use Gambio\StyleEdit\Core\Components\RadioImage\Entities\RadioImageOption;
use Gambio\StyleEdit\Core\Components\ResponsiveGroup\Entities\ResponsiveGroupOption;
use Gambio\StyleEdit\Core\Components\Style\CssGenerator;
use Gambio\StyleEdit\Core\Components\TextBox\Entities\TextBox;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces\ContentGeneratorInterface;
use Gambio\StyleEdit\Core\Components\ContentZone\Interfaces\UpdatableContentZoneContentInterface;
use Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces\PersistableContentInterface;
use InvalidArgumentException;
use JsonSerializable;
use KeyValueCollection;
use stdClass;

/**
 * Class ContentZoneRow
 */
class ContentZoneRow extends KeyValueCollection
    implements ContentGeneratorInterface, JsonSerializable, PersistableContentInterface, UpdatableContentZoneContentInterface
{
    use ContentZoneNormalizeTrait;

    /**
     * @var string
     */
    protected $cssId;

    /**
     * @var string
     */
    protected $cssClass;

    /**
     * @var ResponsiveGroupOption
     */
    protected $responsive;

    /**
     * @var BackgroundGroupOption
     */
    protected $background;

    /**
     * @var BorderGroupOption
     */
    protected $border;


    /**
     * @var string
     */
    protected $columnLayout;

    /**
     * @var array|null
     */
    protected $columnClasses;

    /**
     * @var string
     */
    protected $contentAlign;

    /**
     * @var ?stdClass
     */
    protected $jsonObject;

    /**
     * @var string
     */
    protected const STYLE_TAG_PATTERN = '/\s+<style>([^<]+)<\/style>/m';


    /**
     * ContentZoneRow constructor.
     *
     * @param array $keyValueArray
     * @param RadioImageOption $columnLayout
     * @param CheckboxOption $contentAlign
     * @param ResponsiveGroupOption $responsive
     * @param BackgroundGroupOption $background
     * @param BorderGroupOption $border
     * @param TextBox $cssId
     * @param TextBox $cssClass
     * @param stdClass|null $jsonObject
     */
    public function __construct(
        array $keyValueArray,
        RadioImageOption $columnLayout,
        CheckboxOption $contentAlign,
        ResponsiveGroupOption $responsive,
        BackgroundGroupOption $background,
        BorderGroupOption $border,
        TextBox $cssId,
        TextBox $cssClass,
        ?stdClass $jsonObject
    )
    {
        parent::__construct($keyValueArray);

        $this->cssId = $cssId;
        $this->cssClass = $cssClass;
        $this->columnLayout = $columnLayout;
        $this->contentAlign = $contentAlign;
        $this->responsive = $responsive;
        $this->background = $background;
        $this->border = $border;
        $this->cssId = $cssId;
        $this->cssClass = $cssClass;
        $this->jsonObject = $jsonObject;
    }


    /**
     * @param stdClass $jsonObject
     *
     * @return ContentZoneRow
     * @throws Exception
     */
    public static function createFromJsonObject(stdClass $jsonObject): ContentZoneRow
    {
        if (!isset($jsonObject->cols, $jsonObject->{'columnsLayout'}, $jsonObject->{'contentAlign'}, $jsonObject->responsive, $jsonObject->background, $jsonObject->border, $jsonObject->id)) {

            throw new InvalidArgumentException;
        }

        if (count($jsonObject->cols)) {

            foreach ($jsonObject->cols as $colIndex => &$element) {

                if (!isset($element->id)) {

                    $element->id = $jsonObject->id->id . '-col-' . ($colIndex + 1);
                }

                $element = ContentZoneCol::createFromJsonObject($element);
            }

            unset($col);
        }

        $columnLayout = RadioImageOption::createFromJsonObject($jsonObject->columnsLayout);
        $cssId = TextBox::createFromJsonObject($jsonObject->id);
        $responsive = ResponsiveGroupOption::createFromJsonObject(
            self::setType($jsonObject->responsive, 'Responsive')
        );

        if (!isset($jsonObject->contentAlign->labelId)) {
            $jsonObject->contentAlign->labelId = 'StyleEdit.contentZone.row.alignment.label';
        }
        $contentAlign = CheckboxOption::createFromJsonObject($jsonObject->contentAlign);

        if (!isset($jsonObject->background->labelId)) {
            $jsonObject->background->labelId = 'StyleEdit.contentZone.background.label';
        }
        $background = BackgroundGroupOption::createFromJsonObject(
            self::setType($jsonObject->background, 'Background')
        );
        // Updating background object in order to get the updated image URL
        // if it was parsed, for example: __SHOP_BASE_URL__images/some_image.jpg
        $jsonObject->background = $background->jsonSerialize();

        if (!isset($jsonObject->border->labelId)) {
            $jsonObject->border->labelId = 'StyleEdit.contentZone.border.label';
        }
        $border = BorderGroupOption::createFromJsonObject(self::setType($jsonObject->border, 'Border'));

        if (!isset($jsonObject->class->labelId)) {
            $jsonObject->class->labelId = 'StyleEdit.contentZone.class.label';
        }
        $cssClass = TextBox::createFromJsonObject($jsonObject->class);

        return new static($jsonObject->cols,
            $columnLayout,
            $contentAlign,
            $responsive,
            $background,
            $border,
            $cssId,
            $cssClass,
            $jsonObject);
    }


    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = $this->jsonObject;

        $cols = [];

        if (count($this->getArray())) {
            /** @var ContentZoneCol $col */
            foreach ($this->getArray() as $col) {

                $cols[] = $col->jsonSerialize();
            }
        }

        $result->cols = $cols;

        return $result;
    }


    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     * @throws Exception
     */
    public function htmlContent(?Language $currentLanguage): string
    {
        $rowId = $this->cssId->value($currentLanguage);
        $rowClass = implode(' ',
            array_filter([
                'row',
                $this->cssClass->value($currentLanguage),
                $this->responsive->hiddenLg()
                    ->value($currentLanguage) ? ' hidden-lg' : false,
                $this->responsive->hiddenMd()
                    ->value($currentLanguage) ? ' hidden-md' : false,
                $this->responsive->hiddenSm()
                    ->value($currentLanguage) ? ' hidden-sm' : false,
                $this->responsive->hiddenXs()
                    ->value($currentLanguage) ? ' hidden-xs' : false
            ]));

        $style = (string)CssGenerator::create($rowId)->setBorder($this->border)->setBackground($this->background);

        $html = '<div id="{$user_provided_id}" class="{$user_provided_classes}">' . PHP_EOL;
        $html .= "\t<div class=\"" . ($this->contentAlign->value($currentLanguage) ? 'container' : 'container-fluid') . "\">" . PHP_EOL;
        $html .= "\t\t" . '<div class="gx-content-zone-row">';
        $html = str_replace(['{$user_provided_id}', '{$user_provided_classes}'], [$rowId, $rowClass], $html);

        /** @var ContentZoneCol $col */
        foreach ($this->getArray() as $col) {

            /**
             * setting the layout class to the column
             */
            $columnLayoutClass = $this->getNextColumnClass($currentLanguage);
            $colCssClass = $col->cssClass();
            $colCssClass = $colCssClass === null ? $columnLayoutClass : "$colCssClass $columnLayoutClass";
            $col->setCssClass($colCssClass);

            $colHtml = $col->htmlContent($currentLanguage);
            $colHtml = str_replace(PHP_EOL, PHP_EOL . "\t\t\t", $colHtml);

            $html .= PHP_EOL . "\t" . $colHtml;
        }

        $html .= PHP_EOL . "\t\t</div>";
        $html .= PHP_EOL . "\t</div>";

        return $this->mergeStyleTags($style, $html . PHP_EOL . '</div>');
    }


    /**
     * @param string $rowStyle
     * @param string $rowHtml
     *
     * @return string
     */
    protected function mergeStyleTags(string $rowStyle, string $rowHtml): string
    {
        if (preg_match_all(self::STYLE_TAG_PATTERN, $rowHtml, $matches)) {

            // adding the col style to the row style
            foreach ($matches[1] as $style) {

                // removing first tab from each line
                $style = preg_replace('/^\t\t\t/m', '', $style);

                $rowStyle = str_replace('</style>', $style . '</style>', $rowStyle);
            }

            // removing the original style tags from the html
            foreach ($matches[0] as $styleTag) {

                $rowHtml = str_replace($styleTag, '', $rowHtml);
            }
        }

        $rowStyle = str_replace('</style>', '</style>' . PHP_EOL, $rowStyle);

        return $rowStyle . $rowHtml;
    }


    /**
     * @param Language $currentLanguage
     */
    protected function createColumnClassesArray(Language $currentLanguage): void
    {
        switch ($this->columnLayout->value($currentLanguage)) {

            case 'fifty-fifty' :

                $this->columnClasses = ['col-md-6', 'col-md-6'];
                break;

            case 'thirty-three-sixty-six' :

                $this->columnClasses = ['col-md-4', 'col-md-8'];
                break;

            case 'sixty-six-thirty-three' :

                $this->columnClasses = ['col-md-8', 'col-md-4'];
                break;

            case 'twenty-five-seventy-five' :

                $this->columnClasses = ['col-md-3', 'col-md-9'];
                break;

            case 'seventy-five-twenty-five' :

                $this->columnClasses = ['col-md-9', 'col-md-3'];
                break;

            case 'three-thirty-three' :

                $this->columnClasses = ['col-md-4', 'col-md-4', 'col-md-4'];
                break;

            case 'four-twenty-five' :

                $this->columnClasses = ['col-md-3', 'col-md-3', 'col-md-3', 'col-md-3'];
                break;

            default :

                $this->columnClasses = ['col-md-12'];
                break;
        }
    }


    /**
     * @param Language $currentLanguage
     *
     * @return string next column class in the selected columnLayout
     */
    protected function getNextColumnClass(Language $currentLanguage): string
    {
        if ($this->columnClasses === null) {

            $this->createColumnClassesArray($currentLanguage);
        }

        return count($this->columnClasses) ? array_shift($this->columnClasses) : '';
    }


    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     * @throws Exception
     */
    public function previewContent(?Language $currentLanguage): string
    {
        return $this->htmlContent($currentLanguage);
    }


    /**
     *
     */
    public function persist(): void
    {
        foreach ($this->getIterator() as $col) {

            $col->persist();
        }
    }


    public function update(): void
    {
        foreach ($this->getIterator() as $col) {
            /** @var ContentZoneCol $col */
            $col->update();
        }
    }
}
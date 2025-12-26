<?php
/*--------------------------------------------------------------
   GetWidgets.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\App\Action;

use Exception;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\OverviewWidget;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetCategory;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\MapData\MapDataItem;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\NumberData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataCategory;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataItem;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\SerialData\SerialDataItem\ItemValue;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataColumn\DateColumn;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TableData\TableDataRow\RowField;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData\TextData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption\OptionItem;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Language\App\LanguageService;
use InvalidArgumentException;

/**
 * Class representing the handler for the action of getting all widgets.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\App\Action
 * @codeCoverageIgnore
 */
class GetWidgets extends AbstractAction
{
    /**
     * Fallback language code.
     */
    private const FALLBACK_LANGUAGE_CODE = "de";
    
    /**
     * Service.
     *
     * @var StatisticsOverviewService
     */
    private $service;
    
    /**
     * Current language code.
     *
     * @var LanguageCode
     */
    private $languageCode;
    
    /**
     * Current user's preferences.
     *
     * @var UserPreferences
     */
    private $userPreferences;
    
    /**
     * Language service.
     *
     * @var LanguageService
     */
    private $languageService;
    
    
    /**
     * Constructor.
     *
     * @param StatisticsOverviewService $service         Service.
     * @param LanguageService           $languageService Language service.
     * @param UserPreferences           $userPreferences Current user's preferences.
     */
    public function __construct(
        StatisticsOverviewService $service,
        LanguageService $languageService,
        UserPreferences $userPreferences
    ) {
        $this->service         = $service;
        $this->languageService = $languageService;
        $this->userPreferences = $userPreferences;
        
        try {
            $this->languageCode = $this->languageService->getLanguageById($this->userPreferences->languageId())->code();
        } catch (Exception $exception) {
            $this->languageCode = self::FALLBACK_LANGUAGE_CODE;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
            return $response->withJson(array_map([$this, 'serializeOverviewWidget'],
                                                 iterator_to_array($this->service->getWidgetsByCategory($request->getQueryParam("category",
                                                                                                                                WidgetCategory::ORDERS)))));
        } catch (Exception $exception) {
            return $response->withStatus(400)->withJson($exception->getMessage());
        }
    }
    
    
    /**
     * Return serialized widget.
     *
     * @param OverviewWidget $widget Widget.
     *
     * @return array Serialized widget.
     */
    protected function serializeOverviewWidget(OverviewWidget $widget): array
    {
        return [
            'id'            => $widget->id()->value(),
            'name'          => $widget->names()->getByLanguageCode($this->languageCode)->value(),
            'category'      => $widget->category()->value(),
            'visualization' => $widget->visualization()->value(),
            'options'       => (object)$this->mapOptionsToArray($widget->options()),
            'data'          => $this->mapDataToArray($widget->data())
        ];
    }
    
    
    /**
     * Return data as array.
     *
     * @param WidgetData $data Widget's data.
     *
     * @return array Widget's data as array.
     */
    protected function mapDataToArray(WidgetData $data): array
    {
        if ($data instanceof SerialData) {
            return $this->mapSerialDataToArray($data);
        }
        
        if ($data instanceof MapData) {
            return $this->mapMapDataToArray($data);
        }
        
        if ($data instanceof TableData) {
            return $this->mapTableDataToArray($data);
        }
        
        if ($data instanceof TextData) {
            return $this->mapTextDataToArray($data);
        }
        
        if ($data instanceof NumberData) {
            return $this->mapNumberDataToArray($data);
        }
        
        throw new InvalidArgumentException("Unknown data type");
    }
    
    
    /**
     * Return array containing serial data.
     *
     * @param SerialData $data Widget's serial data.
     *
     * @return array Widget's serial data as array.
     */
    protected function mapSerialDataToArray(SerialData $data): array
    {
        $array = [
            "type" => $data->type()
        ];
        
        $array["categories"] = array_map(function (SerialDataCategory $category) {
            return $category->value();
        },
            iterator_to_array($data->categories()));
        
        $array["series"] = array_map(function (SerialDataItem $series) {
            return [
                "title"  => $series->title()->value(),
                "values" => array_map(function (ItemValue $value) {
                    return $value->value();
                },
                    iterator_to_array($series->values()))
            ];
        },
            iterator_to_array($data->series()));
        
        return $array;
    }
    
    
    /**
     * Return array containing map data.
     *
     * @param MapData $data Widget's map data.
     *
     * @return array Widget's map data as array.
     */
    protected function mapMapDataToArray(MapData $data): array
    {
        $array = [
            "type" => $data->type()
        ];
        
        $array["values"] = array_map(function (MapDataItem $map) {
            return [
                "title" => $map->title()->value(),
                "value" => $map->value()->value()
            ];
        },
            iterator_to_array($data->mapItems()));
        
        return $array;
    }
    
    
    /**
     * Return array containing table data.
     *
     * @param TableData $data Widget's table data.
     *
     * @return array Widget's table data as array.
     */
    protected function mapTableDataToArray(TableData $data): array
    {
        $array = [
            "type" => $data->type()
        ];
        
        $array["columns"] = array_map(function (TableDataColumn $column) {
            $data = [
                "title" => $column->title(),
                "field" => $column->field(),
                "type"  => $column->type()
            ];
            
            if ($column instanceof DateColumn) {
                $data["format"] = [
                    "input"  => $column->inputDateFormat(),
                    "output" => $column->outputDateFormat()
                ];
            }
            
            return $data;
        },
            iterator_to_array($data->columns()));
        
        $array["rows"] = array_map(function (TableDataRow $row) {
            $fields = [];
            
            /**
             * @var RowField $field
             */
            foreach ($row->fields() as $field) {
                $fields[$field->name()] = $field->value();
            }
            
            return $fields;
        },
            iterator_to_array($data->rows()));
        
        return $array;
    }
    
    
    /**
     * Return array containing text data.
     *
     * @param TextData $data Widget's text data.
     *
     * @return array Widget's text data as array.
     */
    protected function mapTextDataToArray(TextData $data): array
    {
        return [
            "type"  => $data->type(),
            "value" => $data->text()->value()
        ];
    }
    
    
    /**
     * Return array containing number data.
     *
     * @param NumberData $data Widget's number data.
     *
     * @return array Widget's number data as array.
     */
    protected function mapNumberDataToArray(NumberData $data): array
    {
        return [
            "type"  => $data->type(),
            "value" => $data->number()->value()
        ];
    }
    
    
    /**
     * Return options as array.
     *
     * @param WidgetOptions $options Widget's options.
     *
     * @return array Widget's options as array.
     */
    protected function mapOptionsToArray(WidgetOptions $options): array
    {
        $array = [];
        
        /**
         * @var WidgetOption $option
         */
        foreach ($options as $option) {
            $itemArray = [
                "type"  => $option->type(),
                "value" => $option->value(),
                "title" => $option->titles()->getByLanguageCode($this->languageCode)->value()
            ];
            
            if ($option instanceof DropdownOption) {
                $dropdownArray = [];
                
                /**
                 * @var OptionItem $item
                 */
                foreach ($option->items() as $item) {
                    $dropdownArray[$item->value()->value()] = $item->titles()
                        ->getByLanguageCode($this->languageCode)
                        ->value();
                }
                
                $itemArray["dropdown"] = $dropdownArray;
            }
            
            $array[$option->id()->value()] = $itemArray;
        }
        
        return $array;
    }
}
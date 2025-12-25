<?php
/*--------------------------------------------------------------
   MapData.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\MapData\MapDataItems;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;

/**
 * Class representing map data.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData
 */
class MapData implements WidgetData
{
    /**
     * Type name.
     */
    private const TYPE = "map";
    
    /**
     * Items.
     *
     * @var MapDataItems
     */
    private $mapItems;
    
    /**
     * Type.
     *
     * @var string
     */
    private $type;
    
    
    /**
     * Constructor.
     *
     * @param MapDataItems $mapItems Items.
     */
    private function __construct(MapDataItems $mapItems)
    {
        $this->mapItems = $mapItems;
        $this->type     = self::TYPE;
    }
    
    
    /**
     * Create instance.
     *
     * @param MapDataItems $mapItems Items.
     *
     * @return MapData Instance.
     */
    public static function create(MapDataItems $mapItems): self
    {
        return new self($mapItems);
    }
    
    
    /**
     * Return map items.
     *
     * @return MapDataItems Items.
     */
    public function mapItems(): MapDataItems
    {
        return $this->mapItems;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }
}
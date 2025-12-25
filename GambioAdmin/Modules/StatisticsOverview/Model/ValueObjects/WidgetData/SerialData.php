<?php
/*--------------------------------------------------------------
   SerialData.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData\SerialDataCategories;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetData\SerialData\SerialDataItems;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;

/**
 * Class representing serial data.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData
 */
class SerialData implements WidgetData
{
    /**
     * Type name.
     */
    private const TYPE = "serial";
    
    /**
     * Categories.
     *
     * @var SerialDataCategories
     */
    private $categories;
    
    /**
     * Series.
     *
     * @var SerialDataItems
     */
    private $series;
    
    /**
     * Type.
     *
     * @var string
     */
    private $type;
    
    
    /**
     * Constructor.
     *
     * @param SerialDataCategories $categories Categories.
     * @param SerialDataItems      $series     Series.
     */
    private function __construct(SerialDataCategories $categories, SerialDataItems $series)
    {
        $this->categories = $categories;
        $this->series     = $series;
        $this->type       = self::TYPE;
    }
    
    
    /**
     * Create instance.
     *
     * @param SerialDataCategories $categories Categories.
     * @param SerialDataItems      $series     Series.
     *
     * @return SerialData
     */
    public static function create(SerialDataCategories $categories, SerialDataItems $series): self
    {
        return new self($categories, $series);
    }
    
    
    /**
     * Return categories.
     *
     * @return SerialDataCategories Categories.
     */
    public function categories(): SerialDataCategories
    {
        return $this->categories;
    }
    
    
    /**
     * Return series.
     *
     * @return SerialDataItems Series.
     */
    public function series(): SerialDataItems
    {
        return $this->series;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }
}
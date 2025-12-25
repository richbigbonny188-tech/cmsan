<?php
/* --------------------------------------------------------------
  PreferredCategory.php 2021-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects;

use Gambio\Admin\Modules\DashboardStatistics\Model\Exceptions\InvalidPreferredCategoryException;
use JsonSerializable;

class PreferredCategory implements JsonSerializable
{
    /**
     * @var string
     */
    private $category;
    
    /**
     * @var string[]
     */
    private $categories = [
        'sales',
        'orders',
        'conversions',
        'visitors',
    ];
    
    
    /**
     * Constructor.
     */
    public function __construct(string $category)
    {
        if (!in_array($category, $this->categories)) {
            throw InvalidPreferredCategoryException::forCategory($category);
        }
        
        $this->category = $category;
    }
    
    
    /**
     * Return value.
     */
    public function value(): string
    {
        return $this->category;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->category;
    }
}
<?php
/*--------------------------------------------------------------
   Category.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Entities;

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects\CategoryId;

/**
 * Class Category
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Entities
 */
class Category
{
    /**
     * Category constructor.
     *
     * @param CategoryId $id
     * @param string     $name
     */
    private function __construct(private CategoryId $id, private string $name) { }
    
    
    /**
     * @param CategoryId $id
     * @param string     $name
     *
     * @return Category
     */
    public static function create(CategoryId $id, string $name): Category
    {
        return new self($id, $name);
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
}
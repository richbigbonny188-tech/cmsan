<?php
/* --------------------------------------------------------------
   ViewedProduct.php 2023-04-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects;

/**
 * Class ViewedProduct
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects
 */
class ViewedProduct
{
    /**
     * ViewedProduct constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $language
     * @param int    $views
     */
    public function __construct(private int $id, private string $name, private string $language, private int $views)
    {
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'language' => $this->language,
            'views'    => $this->views,
        ];
    }
}
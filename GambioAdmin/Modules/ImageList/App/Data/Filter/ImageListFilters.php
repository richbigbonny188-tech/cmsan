<?php
/*--------------------------------------------------------------
   ImageListFilters.php 2021-05-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Data\Filter;

use Gambio\Core\Filter\SqlFilters;

/**
 * Class ImageListFilters
 * @package Gambio\Admin\Modules\ImageList\App\Data\Filter
 * @codeCoverageIgnore
 */
class ImageListFilters extends SqlFilters
{
    
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'         => 'pil.product_image_list_id',
            'name'       => 'pil.product_image_list_name',
            'localPath'  => 'pili.product_image_list_image_local_path',
            'sortOrder'  => 'pili.product_image_list_image_sort_order',
            'textType'   => 'pilit.product_image_list_image_text_type',
            'imageText'  => 'pilit.product_image_list_image_text_value',
            'languageId' => 'pilit.language_id',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForLikeOperation(): array
    {
        return [
            'name',
            'localPath',
            'textType',
            'imageText',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForNumericOperations(): array
    {
        return ['id','sortOrder','languageId'];
    }
}
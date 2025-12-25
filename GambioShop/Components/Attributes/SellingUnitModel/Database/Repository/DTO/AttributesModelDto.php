<?php
/*--------------------------------------------------------------------
 AttributesModelDto.php 2020-2-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\SellingUnitModel\Database\Repository\DTO;

/**
 * Class AttributesModelDto
 * @package Gambio\Shop\Attributes\SellingUnitModel\Database\Repository\DTO
 */
class AttributesModelDto
{
    /**
     * @var string
     */
    protected $model;
    
    
    /**
     * AttributesModelDto constructor.
     *
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }
    
    
    /**
     * @return string
     */
    public function model(): string
    {
        return $this->model;
    }
}
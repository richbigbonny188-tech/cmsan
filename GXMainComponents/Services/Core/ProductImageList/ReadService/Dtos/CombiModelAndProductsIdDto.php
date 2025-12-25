<?php
/* --------------------------------------------------------------
  CombiModelAndProductsIdDto.php 2020-02-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\ReadService\Dtos;

use Gambio\ProductImageList\ReadService\Interfaces\CombiModelAndProductsIdDtoInterface;

/**
 * Class CombiModelAndProductsIdDto
 * @package Gambio\ProductImageList\ReadService\Dtos
 */
class CombiModelAndProductsIdDto implements CombiModelAndProductsIdDtoInterface
{
    /**
     * @var string
     */
    protected $combiModel;
    
    /**
     * @var int
     */
    protected $productsId;
    
    
    /**
     * CombiModelAndProductsIdDto constructor.
     *
     * @param string $combiModel
     * @param int    $productsId
     */
    public function __construct(string $combiModel, int $productsId)
    {
        $this->combiModel = $combiModel;
        $this->productsId = $productsId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function combiModel(): string
    {
        return $this->combiModel;
    }
    
    
    /**
     * @inheritDoc
     */
    public function productsId(): int
    {
        return $this->productsId;
    }
}
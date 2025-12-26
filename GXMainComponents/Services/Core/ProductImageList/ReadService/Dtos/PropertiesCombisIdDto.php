<?php
/* --------------------------------------------------------------
  PropertiesCombisIdDto.php 2020-02-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\ReadService\Dtos;

use Gambio\ProductImageList\ReadService\Interfaces\PropertiesCombisIdDtoInterface;

/**
 * Class PropertiesCombisIdDto
 * @package Gambio\ProductImageList\ReadService\Dtos
 */
class PropertiesCombisIdDto implements PropertiesCombisIdDtoInterface
{
    /**
     * @var int
     */
    protected $combiId;
    
    
    /**
     * PropertiesCombisIdDto constructor.
     *
     * @param int $combiId
     */
    public function __construct(int $combiId)
    {
        $this->combiId = $combiId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function combiId(): int
    {
        return $this->combiId;
    }
}
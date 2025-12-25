<?php
/*--------------------------------------------------------------------------------------------------
    CombisIdDto.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO;

/**
 * Class CombisIdDto
 * @package Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO
 */
class CombisIdDto
{
    /**
     * @var int
     */
    protected $combisId;
    
    
    /**
     * CombisIdDto constructor.
     *
     * @param int $combisId
     */
    public function __construct(int $combisId)
    {
        $this->combisId = $combisId;
    }
    
    
    /**
     * @return int
     */
    public function combisId(): int
    {
        return $this->combisId;
    }
}
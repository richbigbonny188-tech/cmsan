<?php
/*--------------------------------------------------------------------
 AbstractModifierId.php 2020-3-10
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\ValueObjects;

/**
 * Class AbstractModifierId
 * @package Gambio\Shop\SellingUnit\Presentation\ValueObjects
 */
abstract class AbstractModifierId
{
    /**
     * @var int
     */
    protected $id;
    
    
    /**
     * AbstractModifierId constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->id;
    }
}
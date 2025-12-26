<?php
/*--------------------------------------------------------------------
 AttributeIdDto.php 2020-3-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\Id\Repository\DTO;

/**
 * Class AttributeIdDto
 * @package Gambio\Shop\Attributes\Representation\Id\Repository\DTO
 */
class AttributeIdDto
{
    /**
     * @var int
     */
    protected $attributeId;
    
    /**
     * @var int
     */
    protected $attributeValueId;
    
    
    /**
     * AttributeIdDto constructor.
     *
     * @param int $attributeId
     * @param int $attributeValueId
     */
    public function __construct(int $attributeId, int $attributeValueId)
    {
        $this->attributeId      = $attributeId;
        $this->attributeValueId = $attributeValueId;
    }
    
    
    /**
     * @return int
     */
    public function attributeId(): int
    {
        return $this->attributeId;
    }
    
    
    /**
     * @return int
     */
    public function attributeValueId(): int
    {
        return $this->attributeValueId;
    }
}
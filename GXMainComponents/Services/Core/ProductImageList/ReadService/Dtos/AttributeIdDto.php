<?php
/*--------------------------------------------------------------------------------------------------
    AttributeIdDto.php 2020-03-30
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\ProductImageList\ReadService\Dtos;

use Gambio\ProductImageList\ReadService\Interfaces\AttributeIdDtoInterface;

/**
 * Class AttributeIdDto
 * @package Gambio\ProductImageList\ReadService\Dtos
 */
class AttributeIdDto implements AttributeIdDtoInterface
{
    /**
     * @var int
     */
    protected $attributeId;

    /**
     * AttributeIdDto constructor.
     *
     * @param int $attributeId
     */
    public function __construct(int $attributeId)
    {
        $this->attributeId = $attributeId;
    }

    /**
     * @inheritDoc
     */
    public function attributeId(): int
    {
        return $this->attributeId;
    }
}
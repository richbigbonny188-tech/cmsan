<?php
/*------------------------------------------------------------------------------
 Reader.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\SellingUnit\Database\Repository\Readers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Shop\Attributes\ProductModifiers\Database\ValueObjects\AttributeModifierIdentifier;
use Gambio\Shop\Attributes\SellingUnit\Database\Exceptions\AttributeDoesNotExistsException;
use Gambio\Shop\Attributes\SellingUnit\Database\Repository\DTO\AttributeDTO;
use Gambio\Shop\Product\ValueObjects\ProductId;

/**
 * Class Reader
 *
 * @package Gambio\Shop\Attributes\SellingUnit\Database\Repository\Readers
 */
class Reader implements ReaderInterface
{
    /**
     * @var Connection
     */
    protected $connection;
    
    
    /**
     * Reader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getAttributeBy(
        AttributeModifierIdentifier $attributeValueId,
        ProductId                   $productId
    ): AttributeDTO {
        $sql = "
        select `products_attributes`.`products_attributes_id`,
               `products_attributes`.`attributes_model`,
               `products_attributes`.`options_id`,
               `products_attributes`.`sortorder`,
               `products_attributes`.`weight_prefix`,
               `products_attributes`.`options_values_weight`
        from `products_attributes`
        where `options_values_id` ={$attributeValueId->value()}
        and `products_id` = {$productId->value()}";
        
        $result = $this->connection->executeQuery($sql)->fetchAllAssociative();
        
        if (!$result) {
            throw new AttributeDoesNotExistsException($attributeValueId->value() . ' not linked to the product '
                                                      . $productId->value());
        }
        
        return new AttributeDTO((int)$result[0]['products_attributes_id'],
                                (string)$result[0]['attributes_model'],
                                (int)$result[0]['sortorder'],
                                (int)$result[0]['options_id'],
                                (string)$result[0]['weight_prefix'],
                                (float)$result[0]['options_values_weight']);
    }
}
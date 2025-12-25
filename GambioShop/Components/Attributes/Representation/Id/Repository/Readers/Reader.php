<?php
/*--------------------------------------------------------------------
 Reader.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\Id\Repository\Readers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Shop\Attributes\Representation\Id\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\Attributes\Representation\Id\Repository\DTO\AttributeIdDto;

/**
 * Class Reader
 *
 * @package Gambio\Shop\Attributes\Representation\Id\Repository\Readers
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
    public function getAttributeIdFromValueId(array $valueIds): array
    {
        if (count($valueIds) === 0) {
            throw InvalidValueIdsSpecifiedException::missingAttributesIds();
        }
        
        $builder        = $this->connection->createQueryBuilder();
        $optionIdResult = $builder->select('options_id, options_values_id')
            ->distinct()
            ->from('products_attributes')
            ->where('options_values_id IN (' . implode(', ', $valueIds) . ')')
            ->executeQuery();
        
        if ($optionIdResult->rowCount() === 0) {
            throw InvalidValueIdsSpecifiedException::incorrectValues($valueIds);
        }
        
        $result = [];
        
        foreach ($optionIdResult->fetchAllAssociative() as $ids) {
            $result[] = new AttributeIdDto((int)$ids['options_id'], (int)$ids['options_values_id']);
        }
        
        return $result;
    }
}
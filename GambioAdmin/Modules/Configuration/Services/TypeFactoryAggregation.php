<?php
/* --------------------------------------------------------------
   TypeFactoryAggregation.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services;

use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\TypeFactory;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * Class TypeFactoryAggregation
 *
 * @package Gambio\Admin\Modules\Configuration\Services
 */
class TypeFactoryAggregation
{
    /**
     * @var array<string, TypeFactory>
     */
    private $typeFactoryMapping;
    
    
    /**
     * TypeFactoryAggregation constructor.
     *
     * @param array $typeFactoryMapping
     */
    private function __construct(array $typeFactoryMapping)
    {
        $this->typeFactoryMapping = $typeFactoryMapping;
    }
    
    
    /**
     * @param array $typeFactoryMapping
     *
     * @return TypeFactoryAggregation
     */
    public static function create(array $typeFactoryMapping): TypeFactoryAggregation
    {
        Assert::allIsInstanceOf($typeFactoryMapping,
                                TypeFactory::class,
                                'Provided factories need to implement "' . TypeFactory::class . '" interface.');
        Assert::isMap($typeFactoryMapping, 'Provided array needs to map types to factories.');
        
        return new self($typeFactoryMapping);
    }
    
    
    /**
     * @param string $id
     * @param array  $params
     *
     * @return Type
     */
    public function createType(string $id, array $params): Type
    {
        if (array_key_exists($id, $this->typeFactoryMapping) === false) {
            throw new InvalidArgumentException('Unknown type ID provided. Got: ' . $id);
        }
        
        /** @var TypeFactory $factory */
        $factory = $this->typeFactoryMapping[$id];
        
        return $factory->createType($params);
    }
}
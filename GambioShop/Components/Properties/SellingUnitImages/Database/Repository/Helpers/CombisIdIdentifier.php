<?php
/*--------------------------------------------------------------------
 CombisIdIdentifier.php 2020-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Helpers;

use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\Properties\ProductModifiers\Database\ValueObjects\PropertyModifierIdentifier;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifiersCollectionInterface;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\ModifierCollectionDoesNotContainAnyPropertiesException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO\CombisIdDto;
use InvalidArgumentException;
use PropertiesDataAgent;

/**
 * Class CombisIdIdentifier
 * @package Gambio\Shop\Properties\SellingUnitImages\Database\Repository\Helpers
 */
class CombisIdIdentifier implements CombisIdIdentifierInterface
{
    /**
     * @var PropertiesDataAgent
     */
    protected $agent;
    
    
    /**
     * CombisIdIdentifier constructor.
     *
     * @param PropertiesDataAgent $agent
     */
    public function __construct(PropertiesDataAgent $agent)
    {
        $this->agent = $agent;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCombisId(ProductId $identification, ModifierIdentifierCollectionInterface $modifiers): CombisIdDto
    {
        $selectedPropertyValues = [];
        
        foreach ($modifiers as $modifier) {
        
            if ($modifier instanceof PropertyModifierIdentifier) {
                
                $selectedPropertyValues[] = $modifier->value();
            }
        }
        
        if (count($selectedPropertyValues) === 0) {
    
            throw new ModifierCollectionDoesNotContainAnyPropertiesException('ModifierCollection does not hold a '
                                                                             . PropertyModifierIdentifier::class);
        }

        $result = $this->agent->get_available_combis_ids_by_values($identification->value(),
                                                                   $selectedPropertyValues,
                                                                   false);
    
        return new CombisIdDto((int)current($result));
    }
}
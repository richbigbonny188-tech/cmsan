<?php
/*--------------------------------------------------------------
   AdditionalProductFieldFactory.php 2021-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Services;

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalFieldIds;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalFieldValues;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldValue;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\ProductId;

/**
 * Class AdditionalProductFieldFactory
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Services
 */
class AdditionalProductFieldFactory
{
    /**
     * @param int $id
     *
     * @return AdditionalFieldId
     */
    public function createAdditionalFieldId(int $id): AdditionalFieldId
    {
        return AdditionalFieldId::create($id);
    }
    
    
    /**
     * @param AdditionalFieldId ...$ids
     *
     * @return AdditionalFieldIds
     */
    public function createAdditionalFieldIds(AdditionalFieldId ...$ids): AdditionalFieldIds
    {
        return AdditionalFieldIds::create(...$ids);
    }
    
    
    /**
     * @param int $id
     *
     * @return ProductId
     */
    public function createProductId(int $id): ProductId
    {
        return ProductId::create($id);
    }
    
    /**
     * @param string $languageCode
     * @param string $value
     *
     * @return AdditionalFieldValue
     */
    public function createAdditionalFieldValue(
        string $languageCode,
        string $value
    ): AdditionalFieldValue {
        
        return AdditionalFieldValue::create($languageCode, $value);
    }
    
    
    /**
     * @param AdditionalFieldValue ...$values
     *
     * @return AdditionalFieldValues
     */
    public function createAdditionalFieldValues(AdditionalFieldValue ...$values): AdditionalFieldValues
    {
        return AdditionalFieldValues::create(...$values);
    }
}
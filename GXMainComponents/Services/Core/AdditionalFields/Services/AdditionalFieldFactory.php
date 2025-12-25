<?php
/*--------------------------------------------------------------
   AdditionalFieldFactory.php 2021-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Services;

use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\FieldIds;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\Collections\FieldNames;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldId;
use Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects\FieldName;

/**
 * Class AdditionalFieldFactory
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Services
 */
class AdditionalFieldFactory
{
    /**
     * @param int $id
     *
     * @return FieldId
     */
    public function createFieldId(int $id): FieldId
    {
        return FieldId::create($id);
    }
    
    
    /**
     * @param FieldId ...$ids
     *
     * @return FieldIds
     */
    public function createFieldIds(FieldId ...$ids): FieldIds
    {
        return FieldIds::create(...$ids);
    }
    
    
    /**
     * @param string $languageCode
     * @param string $name
     *
     * @return FieldName
     */
    public function createFieldName(string $languageCode, string $name): FieldName
    {
        return FieldName::create($languageCode, $name);
    }
    
    
    /**
     * @param FieldName ...$fieldNames
     *
     * @return FieldNames
     */
    public function createFieldNames(FieldName ...$fieldNames): FieldNames
    {
        return FieldNames::create(...$fieldNames);
    }
}
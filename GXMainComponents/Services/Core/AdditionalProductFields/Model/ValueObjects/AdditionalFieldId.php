<?php
/*--------------------------------------------------------------
   AdditionalFieldId.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects;

use MainFactory;
use Webmozart\Assert\Assert;

/**
 * Class FieldId
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects
 */
class AdditionalFieldId
{
    /**
     * @var int
     */
    protected $id;
    
    
    /**
     * FieldId constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @param int $id
     *
     * @return AdditionalFieldId
     */
    public static function create(int $id): AdditionalFieldId
    {
        Assert::greaterThan($id, 0, 'The additional field ID must be a positive integer. Got: %s');
        
        return MainFactory::create(AdditionalFieldId::class, $id);
    }
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->id;
    }
}
<?php
/*--------------------------------------------------------------
   FieldId.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects;

use MainFactory;
use Webmozart\Assert\Assert;

/**
 * Class FieldId
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects
 */
class FieldId
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
     * @return FieldId
     */
    public static function create(int $id): FieldId
    {
        Assert::greaterThan($id, 0, 'The additional field ID must be a positive integer. Got: %s');
        
        return MainFactory::create(FieldId::class, $id);
    }
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->id;
    }
}
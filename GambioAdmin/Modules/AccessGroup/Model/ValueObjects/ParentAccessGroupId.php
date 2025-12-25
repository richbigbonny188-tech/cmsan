<?php
/* --------------------------------------------------------------
   ParentGroupId.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ParentAccessGroupId
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\ValueObjects
 */
class ParentAccessGroupId
{
    /**
     * @var int
     */
    private $id;
    
    
    /**
     * ParentAccessGroupId constructor.
     *
     * @param int $id
     */
    private function __construct(int $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @param int $id
     *
     * @return ParentAccessGroupId
     */
    public static function create(int $id): ParentAccessGroupId
    {
        Assert::greaterThan($id, 0, 'Parent access group ID need to be greater than 0. Got: %s');
        
        return new self($id);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->id;
    }
}
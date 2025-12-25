<?php
/* --------------------------------------------------------------
   GroupItemType.php 2020-10-21
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
 * Class AccessGroupItemType
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\ValueObjects
 */
class AccessGroupItemType
{
    /**
     * @var string
     */
    private $value;
    
    
    /**
     * AccessGroupItemType constructor.
     *
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @param string $value
     *
     * @return AccessGroupItemType
     */
    public static function create(string $value): AccessGroupItemType
    {
        $allowedTypes = [
            AccessGroupItem::PAGE_TYPE,
            AccessGroupItem::CONTROLLER_TYPE,
            AccessGroupItem::AJAX_HANDLER_TYPE,
            AccessGroupItem::ROUTE_TYPE,
        ];
        Assert::oneOf($value,
                      $allowedTypes,
                      'Invalid group type value provided. Needs to be one of: ' . implode(', ', $allowedTypes)
                      . '; Got: %s');
        
        return new self($value);
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}
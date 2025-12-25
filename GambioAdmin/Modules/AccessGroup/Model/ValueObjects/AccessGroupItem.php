<?php
/* --------------------------------------------------------------
   GroupItem.php 2020-10-21
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
 * Class AccessGroupItem
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\ValueObjects
 */
class AccessGroupItem
{
    /**
     * Type for pages.
     */
    public const PAGE_TYPE = 'PAGE';
    
    /**
     * Type for HTTP controllers.
     */
    public const CONTROLLER_TYPE = 'CONTROLLER';
    
    /**
     * Type for ajax handlers.
     */
    public const AJAX_HANDLER_TYPE = 'AJAX_HANDLER';
    
    /**
     * Type for HTTP routes.
     */
    public const ROUTE_TYPE = 'ROUTE';
    
    
    /**
     * @var AccessGroupItemType
     */
    private $type;
    
    /**
     * @var string
     */
    private $descriptor;
    
    
    /**
     * AccessGroupItem constructor.
     *
     * @param AccessGroupItemType $type
     * @param string              $descriptor
     */
    private function __construct(AccessGroupItemType $type, string $descriptor)
    {
        $this->type       = $type;
        $this->descriptor = $descriptor;
    }
    
    
    /**
     * @param AccessGroupItemType $type
     * @param string              $descriptor
     *
     * @return AccessGroupItem
     */
    public static function create(AccessGroupItemType $type, string $descriptor): AccessGroupItem
    {
        Assert::notWhitespaceOnly($descriptor, 'Descriptor can not be empty.');
        
        return new self($type, $descriptor);
    }
    
    
    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type->value();
    }
    
    
    /**
     * @return string
     */
    public function descriptor(): string
    {
        return $this->descriptor;
    }
}
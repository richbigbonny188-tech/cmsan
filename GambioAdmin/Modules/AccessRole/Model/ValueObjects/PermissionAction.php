<?php
/* --------------------------------------------------------------
   PermissionAction.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class PermissionAction
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\ValueObjects
 */
class PermissionAction
{
    /**
     * String for read action.
     */
    public const READ = 'read';
    
    /**
     * String for write action.
     */
    public const WRITE = 'write';
    
    /**
     * String for delete action.
     */
    public const DELETE = 'delete';
    
    
    /**
     * @var string
     */
    private $value;
    
    
    /**
     * PermissionAction constructor.
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
     * @return PermissionAction
     */
    public static function create(string $value): PermissionAction
    {
        $allowedActions = [self::READ, self::WRITE, self::DELETE];
        Assert::oneOf($value,
                      $allowedActions,
                      'Action must be one of: ' . implode(', ', $allowedActions) . '; Got: %s');
        
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
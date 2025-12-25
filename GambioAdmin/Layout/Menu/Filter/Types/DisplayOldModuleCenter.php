<?php
/* --------------------------------------------------------------
 DisplayOldModuleCenter.php 2020-01-31
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 31 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Filter\Types;

use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Admin\Layout\Menu\Filter\FilterConditionArguments;
use Gambio\Admin\Layout\Menu\Filter\FilterInterface;
use function count;
use function glob;
use function is_array;

/**
 * Class DisplayOldModuleCenter
 * @package Gambio\Admin\Layout\Menu\Filter\Types
 *
 * @codeCoverageIgnore
 */
class DisplayOldModuleCenter implements FilterInterface
{
    public const FILTER_METHOD = 'displayOldModuleCenter';
    
    /**
     * @var Path
     */
    private $path;
    
    
    /**
     * DisplayOldModuleCenter constructor.
     *
     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->path = $path;
    }
    
    
    /**
     * @inheritDoc
     */
    public function check(FilterConditionArguments $condition): bool
    {
        $oldFiles = glob("{$this->path->admin()}/includes/modules/export/*.php");
        
        return is_array($oldFiles) && count($oldFiles) > 0;
    }
}
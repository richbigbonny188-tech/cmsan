<?php
/* --------------------------------------------------------------
   GambioAdminLoader.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer;

use Gambio\Core\TemplateEngine\LayoutData;
use Gambio\Core\TemplateEngine\Loader;
use Throwable;
use function Gambio\Core\Logging\logger;

/**
 * Class GambioAdminLoader
 *
 * @package Gambio\Admin\Layout\Renderer
 */
class GambioAdminLoader
{
    /**
     * @var Loader[]
     */
    private $loaders;
    
    
    /**
     * NewGambioAdminLoader constructor.
     *
     * @param Loader ...$loader
     */
    public function __construct(Loader ...$loader)
    {
        $this->loaders = $loader;
    }
    
    
    /**
     * @param Loader $loader
     */
    public function addLoader(Loader $loader): void
    {
        $this->loaders[get_class($loader)] = $loader;
    }
    
    
    /**
     * @param LayoutData $data
     */
    public function loadLayoutData(LayoutData $data): void
    {
        foreach ($this->loaders as $loader) {
            try {
                $loader->load($data);
            } catch (Throwable $exception) {
                logger()->error('Template loader failed: ' . $exception->getMessage(), ['exception' => $exception]);
            }
        }
    }
}

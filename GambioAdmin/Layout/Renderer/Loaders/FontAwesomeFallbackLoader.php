<?php
/* --------------------------------------------------------------
   FontAwesomeFallbackLoader.php 2020-02-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Core\TemplateEngine\Loader;
use Gambio\Core\TemplateEngine\LayoutData;

/**
 * Class FontAwesomeFallbackLoader
 *
 * @package Gambio\Admin\Layout\Smarty\Loaders
 */
class FontAwesomeFallbackLoader implements Loader
{
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $fontAwesomeFonts   = (array)glob(__DIR__ . '/../../../../admin/html/assets/fonts/fontawesome-free/*.ttf');
        $fontAwesomePresent = !empty($fontAwesomeFonts);
        
        $data->assign('fontawesome_fallback', !$fontAwesomePresent);
    }
}
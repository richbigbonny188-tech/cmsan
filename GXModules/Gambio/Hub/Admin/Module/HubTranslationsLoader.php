<?php
/* --------------------------------------------------------------
 HubTranslationsLoader.php 2020-10-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

namespace GXModules\Gambio\Hub\Admin\Module;

use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Core\TemplateEngine\LayoutData;
use Gambio\Core\TemplateEngine\Loader;

/**
 * Class HubTranslationsLoader
 *
 * @package GXModules\Gambio\Hub\Admin\Module
 */
class HubTranslationsLoader implements Loader
{
    /**
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * GoogleTranslationsLoader constructor.
     *
     * @param TextManager $textManager
     */
    public function __construct(TextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $data->assign('hub_txt', [
                'connected_title'    => $this->textManager->getPhraseText('TEXT_HUB_CONNECTED', 'admin_general'),
                'disconnected_title' => $this->textManager->getPhraseText('TEXT_HUB_DISCONNECTED', 'admin_general'),
            ]);
    }
}

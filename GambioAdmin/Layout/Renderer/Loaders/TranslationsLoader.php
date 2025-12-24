<?php
/* --------------------------------------------------------------
 TranslationsLoader.php 2020-07-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Admin\Layout\Renderer\Translations\FrontendTranslations;
use Gambio\Core\TemplateEngine\Loader;
use Gambio\Core\TemplateEngine\LayoutData;

/**
 * Class TranslationsLoader
 * @package Gambio\Admin\Layout\Renderer\Loaders
 */
class TranslationsLoader implements Loader
{
    /**
     * @var FrontendTranslations
     */
    private $frontendTranslations;
    
    
    /**
     * TranslationsLoader constructor.
     *
     * @param FrontendTranslations $frontendTranslations
     */
    public function __construct(FrontendTranslations $frontendTranslations)
    {
        $this->frontendTranslations = $frontendTranslations;
    }
    
    
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $this->frontendTranslations->addJsTranslation('emptyInfoBox', 'NO_MESSAGES', 'admin_info_boxes');
        $this->frontendTranslations->addJsTranslation('showAllInfoBoxMessages', 'SHOW_ALL', 'admin_info_boxes');
        $this->frontendTranslations->addJsTranslation('successTitle', 'success', 'messages');
        $this->frontendTranslations->addJsTranslation(
            'saveSuccessMessage',
            'GM_LANGUAGE_CONFIGURATION_SUCCESS',
            'languages'
        );
        
        $this->frontendTranslations->addJsEngineSection('admin_labels');
        $this->frontendTranslations->addJsEngineSection('admin_general');
        $this->frontendTranslations->addJsEngineSection('admin_info_boxes');
        $this->frontendTranslations->addJsEngineSection('general');
        $this->frontendTranslations->addJsEngineSection('buttons');
        $this->frontendTranslations->addJsEngineSection('messages');
        
        $vueTranslations = $this->frontendTranslations->hasVueTranslations(
        ) ? $this->frontendTranslations->serializeVueTranslations() : '{}';
        
        $data->assign('jsTranslations', $this->frontendTranslations->serializeJsTranslations());
        $data->assign('vueTranslations', $vueTranslations);
        $data->assign('jseTranslations', $this->frontendTranslations->serializeJsEngineTranslations());
    }
}
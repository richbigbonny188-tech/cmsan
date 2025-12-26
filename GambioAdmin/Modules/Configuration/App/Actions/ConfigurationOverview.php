<?php
/* --------------------------------------------------------------
 ConfigurationOverview.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Actions;

use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class ConfigurationOverview
 *
 * @package Gambio\Admin\Modules\Configuration\App\Actions
 * @codeCoverageIgnore
 */
class ConfigurationOverview extends VuePageAction
{
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $this->enableLargePageLayout();
        
        // [$key, $phraseOrSection, $section|null]
        $translations = [
            ['title', 'configuration_page'],
            ['categories', 'categories_title', 'configuration_page'],
            ['tags', 'tags_title', 'configuration_page'],
            ['save', 'save_btn_title', 'configuration_page'],
            ['search_placeholder', 'configuration_page'],
            ['selected', 'admin_labels'],
            ['select', 'admin_labels'],
            ['deselect', 'admin_labels'],
        ];
        foreach ($translations as $translation) {
            $this->addVuePageTranslation(...$translation);
        }
        
        $template = $this->render($this->translate('page_title', 'configuration_page'),
                                  __DIR__ . '/../../ui/configuration_page.html'
                                  );
        
        return $response->write($template);
    }
    
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return 'configuration';
    }
}
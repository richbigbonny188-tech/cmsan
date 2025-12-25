<?php
/*--------------------------------------------------------------
   Overview.php 2022-09-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\App\Action;

use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class representing the action handler for rendering the page.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\App\Action
 * @codeCoverageIgnore
 */
class Overview extends VuePageAction
{
    /**
     * Entry point name (for Webpack).
     */
    private const JAVASCRIPT_ENTRYPOINT = 'statistics_overview';
    
    /**
     * Language section name.
     */
    private const TRANSLATION_SECTION = 'statistics_overview';
    
    /**
     * All available translations provided to the user interface.
     */
    private const TRANSLATIONS = [
        'language_code',
        'overview_cancel',
        'overview_configure_widget',
        'overview_save',
        'overview_erroneous_widget',
        'overview_refresh',
        'overview_customize',
        'overview_widget_without_data',
        'overview_error_occurred',
        'overview_category_orders',
        'overview_category_customers',
        'overview_category_system'
    ];
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        foreach (self::TRANSLATIONS as $translation) {
            $this->addVuePageTranslation($translation, self::TRANSLATION_SECTION);
        }
        
        return $response->write($this->render($this->translate('overview_page_title', self::TRANSLATION_SECTION),
                                              dirname(__DIR__, 2) . '/ui/overview.html'));
    }
    
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return self::JAVASCRIPT_ENTRYPOINT;
    }
}
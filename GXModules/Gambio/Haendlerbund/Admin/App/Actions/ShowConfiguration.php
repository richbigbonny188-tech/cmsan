<?php
/* --------------------------------------------------------------
   ShowConfiguration.php 2022-03-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Haendlerbund\Admin\App\Actions;

use Gambio\Admin\Application\Http\AdminModuleAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use GXModules\Gambio\Haendlerbund\Admin\Classes\HaendlerbundConfigurationFinder;

class ShowConfiguration extends AdminModuleAction
{
    /**
     * @var HaendlerbundConfigurationFinder
     */
    private $haendlerbundConfigurationFinder;
    
    
    public function __construct(HaendlerbundConfigurationFinder $haendlerbundConfigurationFinder)
    {
        $this->haendlerbundConfigurationFinder = $haendlerbundConfigurationFinder;
    }
    
    
    public function handle(Request $request, Response $response): Response
    {
        $templatePath = __DIR__ . '/../Templates/configuration.html';
        $pageTitle    = $this->translate('module_title', 'haendlerbund');
        $styles = <<<EOS
<style>
.haendlerbund-logo { padding-bottom: 25px; }
.haendlerbund-logo img { width: 380px; height: 60px; }
</style>
EOS;
    
        $pageData = [
            'saveUrl'              => $this->url->admin() . '/haendlerbund/saveConfiguration',
            'updateNowUrl'         => $this->url->admin() . '/haendlerbund/updateNow',
            'txt'                  => $this->textManager->getSectionPhrases('haendlerbund'),
            'configuration'        => [
                'active'                => filter_var($this->haendlerbundConfigurationFinder->get('active'),
                                                      FILTER_VALIDATE_BOOLEAN) ? '1' : '0',
                'accessToken'           => $this->haendlerbundConfigurationFinder->get('accessToken'),
                'mode'                  => $this->haendlerbundConfigurationFinder->get('mode')
                                           !== 'develop' ? 'productive' : 'develop',
                'useTos'                => $this->haendlerbundConfigurationFinder->get('useTos', 'true'),
                'usePrivacy'            => $this->haendlerbundConfigurationFinder->get('usePrivacy', 'true'),
                'useImprint'            => $this->haendlerbundConfigurationFinder->get('useImprint', 'true'),
                'usePaymentAndShipping' => $this->haendlerbundConfigurationFinder->get('usePaymentAndShipping', 'true'),
                'useWithdrawal'         => $this->haendlerbundConfigurationFinder->get('useWithdrawal', 'true'),
            ],
            'affiliate_link'       => 'https://www.haendlerbund.de/de/gambio',
            'dynamic_style_assets' => $styles,
            'message'              => $_SESSION['haendlerbund_message']['message'] ?? '',
            'messageDetail'        => $_SESSION['haendlerbund_message']['message_detail'] ?? '',
        ];
        unset($_SESSION['haendlerbund_message']);
        $content      = $this->render($pageTitle, $templatePath, $pageData);
        
        return $response->write($content);
    }
}

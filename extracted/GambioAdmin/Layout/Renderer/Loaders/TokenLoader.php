<?php
/* --------------------------------------------------------------
 TokenLoader.php 2020-02-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Admin\Application\Token\TokenService;
use Gambio\Core\TemplateEngine\Loader;
use Gambio\Core\TemplateEngine\LayoutData;

/**
 * Class TokenLoader
 * @package Gambio\Admin\Layout\Renderer\Loaders
 */
class TokenLoader implements Loader
{
    /**
     * @var TokenService
     */
    private $tokenService;
    
    
    /**
     * TokenLoader constructor.
     *
     * @param TokenService $tokenService
     */
    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $data->assign('pageToken', $_SESSION['coo_page_token']->generate_token());
        $data->assign('cacheToken', $this->tokenService->getCacheToken());
    }
}
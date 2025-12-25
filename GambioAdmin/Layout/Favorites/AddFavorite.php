<?php
/* --------------------------------------------------------------
 AddFavorite.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Favorites;

use Gambio\Admin\Layout\Favorites\Exceptions\FavoritesPersistenceException;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class AddFavorite
 *
 * @package Gambio\Admin\Layout\Favorites
 * @codeCoverageIgnore
 */
class AddFavorite extends AbstractAction
{
    /**
     * @var FavoritesService
     */
    private $service;
    
    
    /**
     * AddFavorite constructor.
     *
     * @param FavoritesService $service
     */
    public function __construct(FavoritesService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $responseData = ['success' => false];
        $status       = 200;
        $menuItemId   = $request->getParsedBodyParam('menu_item_id');
        if (!$this->service->has($menuItemId)) {
            try {
                $this->service->add($menuItemId);
                $responseData['success'] = true;
                $status                  = 201;
            } catch (FavoritesPersistenceException $e) {
                $status = 500;
            }
        }
        
        return $response->withJson($responseData, $status);
    }
}
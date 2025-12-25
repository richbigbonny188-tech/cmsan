<?php
/*--------------------------------------------------------------
   GetWishlistAction.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Actions;

use DateTimeInterface;
use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistReadService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * @codeCoverageIgnore
 */
class GetWishlistAction
{
    /**
     * @var WishlistReadService
     */
    private WishlistReadService $wishlistReader;
    private int                 $languageId;
    private string              $datetimeFormat;
    
    
    /**
     * @param WishlistReadService $wishlistReader
     * @param UserPreferences     $preferences
     */
    public function __construct(
        WishlistReadService $wishlistReader,
        UserPreferences $preferences
    ) {
        $this->wishlistReader = $wishlistReader;
        $this->languageId     = $preferences->languageId();
        $this->datetimeFormat = DateTimeInterface::ATOM;
    }
    
    
    /**
     * Action Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $customerId = (int)$request->getAttribute('customerId');
        $wishlist   = $this->wishlistReader->getWishlist($customerId, $this->languageId);
        $data       = $wishlist->toArray($this->datetimeFormat);
    
        return $response->withJson($data);
    }
    
}
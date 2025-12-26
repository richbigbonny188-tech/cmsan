<?php
/*--------------------------------------------------------------
   RemoveWishlistAction.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Wishlist\App\Actions;

use Gambio\Admin\Modules\Customer\Submodules\Wishlist\Services\WishlistWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

class RemoveWishlistAction
{
    /**
     * @var WishlistWriteService
     */
    private WishlistWriteService $wishlistWriter;
    
    
    /**
     * Constructor
     */
    public function __construct(WishlistWriteService $wishlistWriter)
    {
        $this->wishlistWriter = $wishlistWriter;
    }
    
    
    /**
     * Action Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $customerId = (int)$request->getAttribute('customerId');
        $this->wishlistWriter->removeWishlist($customerId);
        
        return $response->withStatus(200);
    }
}
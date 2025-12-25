<?php
/*--------------------------------------------------------------
   ProductOptionRequestParser.php 2023-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\App;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionRequestParser;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionFactory;
use Gambio\Core\Application\Http\Request;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ProductOptionApiRequestParser
 *
 * @package    Gambio\Admin\Modules\ProductOption\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionRequestParser
 */
class ProductOptionRequestParser extends AdditionalOptionRequestParser
{
    /**
     * @param Request $request
     * @param array   $errors
     *
     * @return array
     *
     * @deprecated method will be unavailable with GX 4.11 use parseAdditionalOptionsData method instead
     */
    public function parseProductOptionsData(Request $request, array &$errors): array
    {
        return $this->parseAdditionalOptionsData($request, $errors);
    }
}
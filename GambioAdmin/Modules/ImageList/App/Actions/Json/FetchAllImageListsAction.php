<?php
/*--------------------------------------------------------------
   FetchAllImageListsAction.php 2021-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Actions\Json;

use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllImageListsAction
 * @package Gambio\Admin\Modules\ImageList\App\Actions\Json
 */
class FetchAllImageListsAction extends AbstractAction
{
    /**
     * @var ImageListReadServiceInterface
     */
    private $service;
    
    
    /**
     * FetchAllImageListsAction constructor.
     *
     * @param ImageListReadServiceInterface $service
     */
    public function __construct(ImageListReadServiceInterface $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $imageLists   = $this->service->getAllImageLists();
        $responseData = array_map(static function(array $data): array {
            unset($data['newValues']);
            return $data;
        }, $imageLists->toArray());
    
        return $response->withJson(['data' => $responseData,]);
    }
}
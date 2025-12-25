<?php
/*--------------------------------------------------------------
   FetchAllOptionValuesAction.php 2021-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Actions\Json;

use Gambio\Admin\Modules\Option\App\Data\AdminOptionResponseSorter;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllOptionValuesAction
 * @package Gambio\Admin\Modules\Option\App\Actions\Json
 */
class FetchAllOptionValuesAction extends AbstractAction
{
    /**
     * @var OptionReadService
     */
    private $service;
    
    /**
     * @var AdminOptionResponseSorter
     */
    private $sorter;
    
    
    /**
     * FetchAllOptionValuesAction constructor.
     *
     * @param OptionReadService         $service
     * @param AdminOptionResponseSorter $sorter
     */
    public function __construct(OptionReadService $service, AdminOptionResponseSorter $sorter)
    {
        $this->service = $service;
        $this->sorter  = $sorter;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
            $optionId = (int)$request->getAttribute('optionId');
            $option   = $this->service->getOptionById($optionId);
            $option   = $this->sorter->sortOptions([$option->toArray()])[0];
            
            return $response->withJson(['data' => $option['values'],]);
        } catch (OptionDoesNotExistException $exception) {
            return $response->withStatus(404);
        }
    }
}
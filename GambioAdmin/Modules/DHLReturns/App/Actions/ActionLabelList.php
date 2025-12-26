<?php
/* --------------------------------------------------------------
   ActionLabelList.php 2021-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\App\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Path;
use function Gambio\Core\Logging\logger;

class ActionLabelList extends AbstractAction
{
    private const LOGGING_NAMESPACE = 'dhlreturns';
    
    /**
     * @var Path
     */
    private $path;
    
    
    public function __construct(Path $path)
    {
        
        $this->path = $path;
    }
    
    
    public function handle(Request $request, Response $response): Response
    {
        $orderId = (int)$request->getAttribute('orderid');
        if ($orderId === 0) {
            $responseData = [
                'status'  => 'ERROR',
                'message' => 'orderid required',
            ];
            
            return $response->withJson($responseData);
        }
        
        $responseData = [
            'status'    => 'OK',
            'labelList' => $this->buildLabelList($orderId)
        ];
        
        return $response->withHeader('Cache-Control', 'private no-cache must-revalidate')
            ->withJson($responseData);
    }
    
    
    protected function buildLabelList(int $orderId): array
    {
        $labelList = [];
        $labelsDir = "{$this->path->base()}/export/dhlreturns";
        $files     = glob("{$labelsDir}/{$orderId}_*.pdf");
        
        foreach ($files as $file) {
            $baseFile = basename($file);
            $matches  = [];
            if (preg_match("/(?'orderid'\d+)_(?'shipment'\d+)_(?'year'\d{4})(?'month'\d\d)(?'day'\d\d)(?'hour'\d\d)(?'minute'\d\d)(?'second'\d\d)_[0-9a-f]+\.pdf/",
                           $baseFile,
                           $matches) === 1) {
                $creationDate = "{$matches['year']}-{$matches['month']}-{$matches['day']}";
                $creationTime = "{$matches['hour']}:{$matches['minute']}:{$matches['second']}";
                $labelList[]  = [
                    'shipmentNumber' => $matches['shipment'],
                    'creationDate'   => "{$creationDate} {$creationTime}",
                    'filename'       => $baseFile,
                    'labelUrl'       => "{$this->url->base()}/export/dhlreturns/{$baseFile}",
                ];
            }
        }
        
        return $labelList;
    }
}
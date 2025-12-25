<?php
/*--------------------------------------------------------------
   FetchAllOptionsAction.php 2021-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Actions\Json;

use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Admin\Modules\Option\App\Data\AdminOptionResponseSorter;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllOptionsAction
 * @package Gambio\Admin\Modules\Option\App\Actions\Json
 */
class FetchAllOptionsAction extends AbstractAction
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
     * @var LanguageReadService
     */
    private $languageReadService;
    
    
    /**
     * @param OptionReadService         $service
     * @param AdminOptionResponseSorter $sorter
     * @param LanguageReadService       $languageReadService
     */
    public function __construct(
        OptionReadService $service, 
        AdminOptionResponseSorter $sorter,
        LanguageReadService $languageReadService
    ) {
        $this->service             = $service;
        $this->sorter              = $sorter;
        $this->languageReadService = $languageReadService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $options = $this->service->getAllOptions()->toArray();
        $options = array_map([$this, 'addMissingLanguageCodes'], $options);
        $options = $this->sorter->sortOptions($options);
    
        foreach (array_keys($options) as $index) {
            
            unset($options[$index]['newValues']);
        }
        
        return $response->withJson(['data' => $options]);
    }
    
    
    /**
     * @param array $optionData
     *
     * @return array
     */
    private function addMissingLanguageCodes(array $optionData): array
    {
        foreach ($this->languageReadService->getLanguages() as $language) {
        
            $detailLanguageCodes = array_column($optionData['details'], 'languageCode');
            
            if (in_array($language->code(), $detailLanguageCodes) === false) {
            
                $optionData['details'][] = [
                    'languageCode' => $language->code(),
                    'label'        => $optionData['details'][0]['label'],
                    'adminLabel'   => $optionData['details'][0]['adminLabel'],
                    'description'  => $optionData['details'][0]['description'],
                ];
            
            }
        
            foreach ($optionData['values'] as &$value) {
                
                $detailLanguageCodes = array_column($value['details'], 'languageCode');
                
                if (in_array($language->code(), $detailLanguageCodes) === false) {
    
                    $value['details'][] = [
                        'languageCode' => $language->code(),
                        'label'        => $value['details'][0]['label'],
                        'description'  => $value['details'][0]['description'],
                    ];
                }
                
            }
        }
        
        return $optionData;
    }
}
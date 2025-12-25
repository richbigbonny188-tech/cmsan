<?php
/*--------------------------------------------------------------------------------------------------
    ThemeExtenderService.php 2019-12-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Services;

use Exception;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeConfiguration;
use Gambio\StyleEdit\Core\Components\Theme\Repositories\ThemeExtenderRepository;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\StyleEditConfiguration;
use stdClass;

/**
 * Class ThemeExtenderService
 * @package Gambio\StyleEdit\Core\Components\Theme\Services
 */
class ThemeExtenderService
{
    /**
     * @var FileIO
     */
    protected $fileIO;
    /**
     * @var StyleEditConfiguration
     */
    private $configuration;
    /**
     * @var ThemeExtenderRepository
     */
    private $repository;
    
    
    /**
     * ThemeExtenderService constructor.
     *
     * @param ThemeExtenderRepository $repository
     */
    public function __construct(ThemeExtenderRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @param ThemeConfiguration $source
     * @param stdClass           $data
     *
     * @return mixed
     * @throws Exception
     */
    public function extendTheme(ThemeConfiguration $source, stdClass $data)
    {
        $data = $this->prepareThemeSettingsForExtension($source, $data);
        $this->repository->createThemeForData($data);
        $this->repository->copySettings($source->id(), $data->id);

        return $data->id;
    }
    
    
    /**
     * @param ThemeConfiguration $source
     * @param stdClass           $data
     *
     * @return stdClass
     * @throws Exception
     */
    protected function prepareThemeSettingsForExtension(ThemeConfiguration $source, stdClass $data)
    {
        if($data->author === 'Gambio GmbH'){
            unset($data->author);
        }
        $data->author   = $data->author ?? 'CUSTOM';
        $data->version  = $data->version ?? '1.0';
        $data->extends  = $source->id();
        $data->inherits = (object)['from' => 'PARENT'];
        $data->id       = $data->id ?? $source->id() . date('Y-m-d_H-i-s');
        $data->title    = $data->title ?? $this->createTitleForDuplicateTheme($source->title());
        
        //calculated properties must always be null
        unset($data->preview, $data->editable, $data->removable, $data->active, $data->children, $data->language, $data->languages);
        
        return $data;
    }
    
    
    /**
     * @param $name
     *
     * @return string
     * @throws Exception
     */
    protected function createTitleForDuplicateTheme($name): string
    {
        $language = SingletonPrototype::instance()->get(Language::class);
        
        switch ($language->code()) {
            case 'de' :
                
                return $name . ' - Kopie';
            
            case 'en' :
            default   :
                
                return $name . ' - Copy';
        }
    }
    
}
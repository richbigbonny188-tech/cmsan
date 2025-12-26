<?php
/*--------------------------------------------------------------
   HtmlCacheReader.php 2020-12-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\Html;

use Curl\Curl;
use Gambio\Admin\Modules\Dashboard\Html\ValueObjects\CacheFilePath;
use Gambio\Admin\Modules\Dashboard\Html\ValueObjects\EndpointUrl;
use Throwable;

/**
 * Class HtmlCacheReader
 * @package Gambio\Admin\Modules\Dashboard\Html
 */
class HtmlCacheReader extends HtmlProvider
{
    protected const CACHE_FILE_TTL_IN_MINUTES = 15;
    
    /**
     * @var CacheFilePath
     */
    protected $cacheFilePath;
    
    
    /**
     * HtmlCacheReader constructor.
     *
     * @param EndpointUrl   $endpointUrl
     * @param Curl          $curl
     * @param CacheFilePath $cacheFilePath
     */
    public function __construct(
        EndpointUrl $endpointUrl,
        Curl $curl,
        CacheFilePath $cacheFilePath
    ) {
        parent::__construct($endpointUrl, $curl);
        $this->cacheFilePath = $cacheFilePath;
    }
    
    
    /**
     * @return string
     * @throws Throwable
     */
    public function dashboardHtml(): string
    {
        if (file_exists($this->cacheFilePath->value())
            && time() - filemtime($this->cacheFilePath->value()) < self::CACHE_FILE_TTL_IN_MINUTES * 60) {
            
            return file_get_contents($this->cacheFilePath->value());
        }
        
        try {
            $result = parent::dashboardHtml();
            file_put_contents($this->cacheFilePath->value(), $result);
        } catch (Throwable $exception) {
            
            if (file_exists($this->cacheFilePath->value())) {
                
                touch($this->cacheFilePath->value()); // ensuring that on issues at the endpoint not every shop sends a request every time the dashboard is loaded
                
                return file_get_contents($this->cacheFilePath->value());
            }
            
            throw $exception;
        }
        
        return $result;
    }
}
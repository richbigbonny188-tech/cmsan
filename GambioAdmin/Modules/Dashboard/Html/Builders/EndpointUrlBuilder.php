<?php
/*--------------------------------------------------------------
   EndpointUrlBuilder.php 2020-10-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\Html\Builders;

use Gambio\Admin\Modules\Dashboard\Html\Builders\Exceptions\UnfinishedBuildException;
use Gambio\Admin\Modules\Dashboard\Html\ValueObjects\EndpointUrl;

/**
 * Class EndpointUrlBuilder
 * @package Gambio\Admin\Modules\Dashboard\Html\Builders
 */
class EndpointUrlBuilder
{
    /**
     * @var int
     */
    protected $languageId;
    
    /**
     * @var bool
     */
    protected $isCloudShop;
    
    /**
     * @var string[]
     */
    protected $urlMap = [
        'cloud_de'  => 'https://dashboard.gambio.de/cloud-admin-news',
        'cloud_en'  => 'https://dashboard.gambio.com/cloud-admin-news',
        'hosted_de' => 'https://dashboard.gambio.de/admin-news',
        'hosted_en' => 'https://dashboard.gambio.com/admin-news',
    ];
    
    
    /**
     * @return static
     */
    public static function create(): self
    {
        return new static;
    }
    
    
    /**
     * @return EndpointUrl
     * @throws UnfinishedBuildException
     */
    public function build(): EndpointUrl
    {
        $properties = ['languageId', 'isCloudShop'];
        foreach ($properties as $property) {
            
            if ($this->$property === null) {
                
                throw UnfinishedBuildException::missingProperty($property);
            }
        }
        
        $urlMapKey = $this->isCloudShop ? 'cloud_' : 'hosted_';
        $urlMapKey .= $this->languageId === 2 ? 'de' : 'en';
        
        return new EndpointUrl($this->urlMap[$urlMapKey]);
    }
    
    
    /**
     * @param int $languageId
     *
     * @return $this
     */
    public function withLanguageId(int $languageId): self
    {
        $this->languageId = $languageId;
        
        return $this;
    }
    
    
    /**
     * @param bool $isCloudShop
     *
     * @return $this
     */
    public function setIsCloudShop(bool $isCloudShop): self
    {
        $this->isCloudShop = $isCloudShop;
        
        return $this;
    }
}
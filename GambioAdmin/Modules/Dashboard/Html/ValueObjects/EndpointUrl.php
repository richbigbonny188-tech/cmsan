<?php
/*--------------------------------------------------------------
   EndpointUrl.php 2020-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\Html\ValueObjects;

/**
 * Class EndpointUrl
 * @package Gambio\Admin\Modules\Dashboard\Html\ValueObjects
 */
class EndpointUrl
{
    /**
     * @var string
     */
    protected $url;
    
    
    /**
     * EndpointUrl constructor.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->url;
    }
}
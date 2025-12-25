<?php
/*--------------------------------------------------------------
   LogoProperties.php 2020-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\ValueObjects;

use Gambio\Core\Application\ValueObjects\Environment;

/**
 * Class LogoProperties
 * @package Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\ValueObjects
 */
class LogoProperties
{
    /**
     * @var Environment
     */
    private $environment;
    
    
    /**
     * LogoProperties constructor.
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }
    
    
    /**
     * @return string
     */
    public function hashValue(): string
    {
        if ($this->environment->isCloud()) {
            return '7afbf69f02df2a224a09043e3b3eaa4c223fbfb4';
        }
        
        return 'f9c2bd8bc11f4134d381a1e31a42ffebedec59b7';
    }
    
    
    /**
     * @return string
     */
    public function filename(): string
    {
        return 'gx4_shop_logo.svg';
    }
}
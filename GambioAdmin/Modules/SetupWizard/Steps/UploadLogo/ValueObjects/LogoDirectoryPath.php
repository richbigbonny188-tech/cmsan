<?php
/*--------------------------------------------------------------
   LogoDirectoryPath.php 2020-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\ValueObjects;

/**
 * Class LogoDirectoryPath
 * @package Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\ValueObjects
 */
class LogoDirectoryPath
{
    /**
     * @var string
     */
    protected $path;
    
    /**
     * LogoDirectoryPath constructor.
     */
    public function __construct()
    {
        $this->path = dirname(__DIR__, 6) . '/images/logos/';
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->path;
    }
}
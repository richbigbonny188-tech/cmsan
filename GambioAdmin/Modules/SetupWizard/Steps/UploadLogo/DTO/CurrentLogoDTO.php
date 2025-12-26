<?php
/*--------------------------------------------------------------
   CurrentLogoDTO.php 2020-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\DTO;

/**
 * Class CurrentLogoDTO
 * @package Gambio\Admin\Modules\SetupWizard\Steps\UploadLogo\DTO
 */
class CurrentLogoDTO
{
    /**
     * @var string
     */
    protected $filename;
    
    
    /**
     * CurrentLogoDTO constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }
    
    
    /**
     * @return string
     */
    public function filename(): string
    {
        return $this->filename;
    }
}
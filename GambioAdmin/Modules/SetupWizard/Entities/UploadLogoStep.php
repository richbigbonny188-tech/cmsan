<?php
/* --------------------------------------------------------------
  UploadLogoStep.inc.php 2019-05-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Entities;

use Gambio\Admin\Modules\SetupWizard\ValueObjects\Icon;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Url;

/**
 * Class UploadLogoStep
 */
class UploadLogoStep extends AbstractSetupWizardStep
{
    /**
     * @var string
     */
    protected const ICON = 'heart';
    
    /**
     * @var integer
     */
    protected const INDEX = 0;
    
    /**
     * @var string
     */
    protected const LINK = 'gm_logo.php';
    
    
    /**
     * UploadLogoStep constructor.
     *
     * @param Status $status
     * @param Title  $title
     * @param Text   $description
     * @param Key    $key
     */
    public function __construct(Status $status, Title $title, Text $description, Key $key)
    {
        $icon  = new Icon(new Url(self::ICON));
        $index = new Index(self::INDEX);
        $link  = new Url(self::LINK);
        parent::__construct($status, $icon, $index, $link, $title, $description, $key);
    }
}
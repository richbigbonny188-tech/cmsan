<?php
/* --------------------------------------------------------------
  DesignStep.php 2022-10-12
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\Design;

use Gambio\Admin\Modules\SetupWizard\Entities\AbstractSetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Icon;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Url;

/**
 * Class DesignStep
 * @package Gambio\Admin\Modules\SetupWizard\Steps\Design
 */
class DesignStep extends AbstractSetupWizardStep
{
    /**
     * @var string
     */
    const ICON = 'paint-brush';
    
    /**
     * @var string
     */
    const LINK = 'admin.php?do=StyleEdit4Authentication';
    
    
    public function __construct(
        Status $status,
        int $index,
        Title $title,
        Text $description,
        Key $key
    ) {
        $icon = new Icon(
            new Url(self::ICON)
        );
        parent::__construct(
            $status,
            $icon,
            new Index($index),
            new Url(self::LINK),
            $title,
            $description,
            $key
        );
    }
}
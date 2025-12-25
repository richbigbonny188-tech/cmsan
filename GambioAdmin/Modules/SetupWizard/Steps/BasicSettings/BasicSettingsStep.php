<?php
/*--------------------------------------------------------------------------------------------------
    BasicSettingsStep.php 2020-11-02
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\BasicSettings;

use Gambio\Admin\Modules\SetupWizard\Entities\AbstractSetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Icon;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Url;

/**
 * Class BasicSettingsStep
 * @package Gambio\Admin\Modules\SetupWizard\Steps\BasicSettings
 */
class BasicSettingsStep extends AbstractSetupWizardStep
{
    /**
     * BasicSettingsStep constructor.
     *
     * @param Status $status
     * @param Index  $index
     * @param Title  $title
     * @param Text   $description
     * @param Key    $key
     */
    public function __construct(
        Status $status,
        Index $index,
        Title $title,
        Text $description,
        Key $key)
    {
        $iconUrl = new Url('cog');
        $icon    = new Icon($iconUrl);
        $url     = new Url('configurations?query=Shop');
        
        parent::__construct(
            $status,
            $icon,
            $index,
            $url,
            $title,
            $description,
            $key
        );
    }
}
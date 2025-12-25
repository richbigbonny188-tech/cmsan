<?php
/* --------------------------------------------------------------
 ShippingStep.php 2020-08-18
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\Shipping;

use Gambio\Admin\Modules\SetupWizard\Entities\AbstractSetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Icon;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Url;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;

/**
 * Class ShippingStep
 * @package Gambio\Admin\Modules\SetupWizard\Steps\Shipping
 */
class ShippingStep extends AbstractSetupWizardStep
{
    protected const ICON = 'box-open';
    
    /**
     * @var string
     */
    protected const LINK = 'modules.php?set=shipping';
    
    
    /**
     * ShippingStep constructor.
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
        Key $key
    ) {
        parent::__construct($status,
                            new Icon(new Url(self::ICON)),
                            $index,
                            new Url(self::LINK),
                            $title,
                            $description,
                            $key);
    }
}
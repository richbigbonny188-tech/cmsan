<?php
/*------------------------------------------------------------------------------
 SetupWizardStep.php 2020-08-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\Catalog;

use Gambio\Admin\Modules\SetupWizard\Entities\AbstractSetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Icon;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Url;

class CatalogStep extends AbstractSetupWizardStep
{
    public function __construct(
        Status $status,
        Index $index,
        Title $title,
        Text $description,
        Key $key
    ) {
        
        parent::__construct($status,
                            new Icon(new Url('sitemap')),
                            $index,
                            new Url('categories.php'),
                            $title,
                            $description,
                            $key);
    }
}
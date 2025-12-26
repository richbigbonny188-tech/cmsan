<?php
/*--------------------------------------------------------------
   LegalTextStep.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText;

use Gambio\Admin\Modules\SetupWizard\Entities\AbstractSetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Icon;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Url;

/**
 * Class LegalTextStep
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText
 */
class LegalTextStep extends AbstractSetupWizardStep
{
    /**
     * @var string
     */
    protected const KEY = 'SETUP_WIZARD_STEP_LEGAL_TEXT';
    
    /**
     * @var string
     */
    protected const ICON = 'balance-scale';
    
    /**
     * @var integer
     */
    protected const INDEX = 6;
    
    /**
     * @var string
     */
    protected const LINK = 'admin.php?do=ContentManagerPages';
    
    
    /**
     * LegalTextStep constructor.
     *
     * @param Status $status
     * @param Title  $title
     * @param Text   $description
     * @param Key    $key
     */
    public function __construct(
        Status $status,
        Title $title,
        Text $description,
        Key $key
    ) {
        $icon  = new Icon(new Url(self::ICON));
        $index = new Index(self::INDEX);
        $link  = new Url(self::LINK);
        parent::__construct($status, $icon, $index, $link, $title, $description, $key);
    }
}
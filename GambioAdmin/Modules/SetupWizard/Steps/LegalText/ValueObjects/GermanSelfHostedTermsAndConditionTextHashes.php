<?php
/*--------------------------------------------------------------
   GermanTermsAndConditionTextHashes.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects;

/**
 * Class GermanTermsAndConditionTextHashes
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects
 */
class GermanSelfHostedTermsAndConditionTextHashes extends AbstractTermsAndConditionTextHashes
{
    /**
     * GermanTermsAndConditionTextHashes constructor.
     */
    public function __construct()
    {
        $heading = '7273f036e8113718633da52975f19190aaf6f302';
        $text    = 'c28bb190fadcb04fb826ebe78e2711fb76e3bf17';
        parent::__construct($heading, $text);
    }
}
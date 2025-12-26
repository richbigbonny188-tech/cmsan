<?php
/*--------------------------------------------------------------
   GermanCloudTermsAndConditionTextHashes.php 2020-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects;

/**
 * Class GermanCloudTermsAndConditionTextHashes
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects
 */
class GermanCloudTermsAndConditionTextHashes extends AbstractTermsAndConditionTextHashes
{
    /**
     * GermanCloudTermsAndConditionTextHashes constructor.
     */
    public function __construct()
    {
        $heading = '7273f036e8113718633da52975f19190aaf6f302';
        $text    = 'b05f5b754ebe8d69b1cc21efd06747bacc382ec5';
        parent::__construct($heading, $text);
    }
}
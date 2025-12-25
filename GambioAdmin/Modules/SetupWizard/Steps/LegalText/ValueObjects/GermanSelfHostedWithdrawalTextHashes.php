<?php
/*--------------------------------------------------------------
   GermanSelfHostedWithdrawalTextHashes.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects;

/**
 * Class GermanSelfHostedWithdrawalTextHashes
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects
 */
class GermanSelfHostedWithdrawalTextHashes extends AbstractWithdrawalTextHashes
{
    /**
     * GermanSelfHostedWithdrawalTextHashes constructor.
     */
    public function __construct()
    {
        $heading = '1fa416adefec10b684fef2ed8720cd384b95f12a';
        $text    = '0e813dd2e92d8811341edb8b2e311531f24f585d';
        parent::__construct($heading, $text);
    }
}
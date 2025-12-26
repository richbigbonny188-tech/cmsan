<?php
/*--------------------------------------------------------------
   EnglishSelfHostedWithdrawalTextHashes.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects;

/**
 * Class EnglishSelfHostedWithdrawalTextHashes
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects
 */
class EnglishSelfHostedWithdrawalTextHashes extends AbstractWithdrawalTextHashes
{
    /**
     * EnglishSelfHostedWithdrawalTextHashes constructor.
     */
    public function __construct()
    {
        $heading = '478b14424d1063bd15b52bf23d5c4798d738d28a';
        $text    = 'a4ea234d855fea184b96d5db9efbe0202b2b9fa0';
        parent::__construct($heading, $text);
    }
}
<?php
/*--------------------------------------------------------------
   GermanCloudWithdrawalTextHashes.php 2020-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects;

/**
 * Class GermanCloudWithdrawalTextHashes
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects
 */
class GermanCloudWithdrawalTextHashes extends AbstractWithdrawalTextHashes
{
    /**
     * GermanCloudWithdrawalTextHashes constructor.
     */
    public function __construct()
    {
        $heading = '1fa416adefec10b684fef2ed8720cd384b95f12a';
        $text    = 'b05f5b754ebe8d69b1cc21efd06747bacc382ec5';
        parent::__construct($heading, $text);
    }
}
<?php
/*--------------------------------------------------------------
   EnglishCloudWithdrawalTextHashes.php 2020-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects;

/**
 * Class EnglishCloudWithdrawalTextHashes
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects
 */
class EnglishCloudWithdrawalTextHashes extends AbstractWithdrawalTextHashes
{
    /**
     * EnglishCloudWithdrawalTextHashes constructor.
     */
    public function __construct()
    {
        $heading = '478b14424d1063bd15b52bf23d5c4798d738d28a';
        $text    = '87e914c5d244d0164802121ed7d542d2f7bdc1a6';
        parent::__construct($heading, $text);
    }
}
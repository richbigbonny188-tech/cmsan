<?php
/*--------------------------------------------------------------
   EnglishCloudTermsAndConditionTextHashes.php 2020-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects;

/**
 * Class EnglishCloudTermsAndConditionTextHashes
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects
 */
class EnglishCloudTermsAndConditionTextHashes extends AbstractTermsAndConditionTextHashes
{
    /**
     * EnglishCloudTermsAndConditionTextHashes constructor.
     */
    public function __construct()
    {
        $heading = '9a6c44098742b8b58280b2d90786df2b4256e13c';
        $text    = '87e914c5d244d0164802121ed7d542d2f7bdc1a6';
        parent::__construct($heading, $text);
    }
}
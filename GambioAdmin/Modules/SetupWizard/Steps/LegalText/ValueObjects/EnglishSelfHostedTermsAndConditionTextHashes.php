<?php
/*--------------------------------------------------------------
   EnglishSelfHostedTermsAndConditionTextHashes.php 2020-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects;

/**
 * Class EnglishSelfHostedTermsAndConditionTextHashes
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects
 */
class EnglishSelfHostedTermsAndConditionTextHashes extends AbstractTermsAndConditionTextHashes
{
    /**
     * EnglishSelfHostedTermsAndConditionTextHashes constructor.
     */
    public function __construct()
    {
        $heading = '9a6c44098742b8b58280b2d90786df2b4256e13c';
        $text    = '8a2bda9d8c79c5d7b025eba94702b967d6d9d74d';
        parent::__construct($heading, $text);
    }
}
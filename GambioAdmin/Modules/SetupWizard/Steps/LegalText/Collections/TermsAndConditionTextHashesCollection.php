<?php
/*--------------------------------------------------------------
   TermsAndConditionTextHashesCollection.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Collections;

use Gambio\Admin\Modules\SetupWizard\Collections\AbstractAssociativeCollection;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects\AbstractTermsAndConditionTextHashes;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects\EnglishCloudTermsAndConditionTextHashes;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects\EnglishSelfHostedTermsAndConditionTextHashes;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects\GermanCloudTermsAndConditionTextHashes;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects\GermanCloudWithdrawalTextHashes;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\ValueObjects\GermanSelfHostedTermsAndConditionTextHashes;
use Gambio\Core\Application\ValueObjects\Environment;

/**
 * Class TermsAndConditionTextHashesCollection
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Collections
 */
class TermsAndConditionTextHashesCollection extends AbstractAssociativeCollection
{
    /**
     * TermsAndConditionTextHashesCollection constructor.
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        parent::__construct([]);
        $isCloudShop = $environment->isCloud();
        $this[1] = $isCloudShop ? new EnglishCloudTermsAndConditionTextHashes : new EnglishSelfHostedTermsAndConditionTextHashes;
        $this[2] = $isCloudShop ? new GermanCloudTermsAndConditionTextHashes : new GermanSelfHostedTermsAndConditionTextHashes;
    }
    
    
    /**
     * @inheritDoc
     */
    protected function isValid($value): bool
    {
        return $value instanceof AbstractTermsAndConditionTextHashes;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->currentValue();
    }
}
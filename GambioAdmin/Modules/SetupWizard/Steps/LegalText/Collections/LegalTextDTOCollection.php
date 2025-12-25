<?php
/*--------------------------------------------------------------
   LegalTextDTOCollection.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Collections;

use Gambio\Admin\Modules\SetupWizard\Collections\AbstractCollection;
use Gambio\Admin\Modules\SetupWizard\Steps\LegalText\DTO\LegalTextDTO;

/**
 * Class LegalTextDTOCollection
 * @package Gambio\Admin\Modules\SetupWizard\Steps\LegalText\Collections
 */
class LegalTextDTOCollection extends AbstractCollection
{
    
    /**
     * @inheritDoc
     */
    protected function isValid($value): bool
    {
        return $value instanceof LegalTextDTO;
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
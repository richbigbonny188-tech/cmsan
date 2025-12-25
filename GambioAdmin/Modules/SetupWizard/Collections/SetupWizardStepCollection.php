<?php
/* --------------------------------------------------------------
 SetupWizardStepCollection.php 2022-08-05
 Gambio GmbH
 http://www.gambio.de

 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Collections;

use Gambio\Admin\Modules\SetupWizard\Interfaces\SetupWizardStep;

/**
 * Class SetupWizardStepCollection
 * @package Gambio\Admin\Modules\SetupWizard\Collections
 */
class SetupWizardStepCollection extends AbstractCollection
{
    private $visible;
    
    
    /**
     * SetupWizardStepCollection constructor.
     *
     * @param array $values
     * @param       $visible
     */
    public function __construct(array $values, $visible)
    {
        parent::__construct($values);
        $this->visible = $visible;
    }
    
    
    /**
     * @inheritDoc
     */
    protected function isValid($value): bool
    {
        return $value instanceof SetupWizardStep;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->currentValue();
    }
    
    
    /**
     * @return array|mixed|Object[]
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'visible' => $this->visible,
            'steps'   => $this->values
        ];
    }
}
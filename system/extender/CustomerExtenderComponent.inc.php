<?php
/* --------------------------------------------------------------
  CustomerExtenderComponent.inc.php 2021-07-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

/**
 * Class CustomerExtenderComponent
 *
 * An Extender to append new form fields or extra html to the customer page in the Gambio Admin
 *
 * Example overload:
 *
 * class ExampleCustomerExtenderComponent extends ExampleCustomerExtenderComponent_parent
 * {
 *     public function proceed()
 *     {
 *         parent::proceed();
 *
 *         // logic for handling POST data like storing it
 *
 *         // logic for getting data to prefill fields
 *
 *         $this->addPersonalField('Middle name:', '<input type="text" name="middle_name" maxlength="32" />');
 *         $this->addCompanyField('Location:', '<input type="text" name="location" maxlength="32" />');
 *         $this->addAddressField('Floor:', '<input type="text" name="floor" maxlength="32" />');
 *         $this->addContactField('Mobile number:', '<input type="tel" name="mobile_number" />');
 *         $this->addAdditionalField('Reference Code:', '<input type="text" name="reference_code" />');
 *         $this->addExtraHtml('<div><p>Some extra HTML</p></div>');
 *     }
 * }
 *
 * @extends ExtenderComponent
 */
class CustomerExtenderComponent extends ExtenderComponent
{
    /**
     * CustomerExtenderComponent constructor.
     */
    public function __construct()
    {
        $this->v_output_buffer = [
            'personal_fields'   => [],
            'company_fields'    => [],
            'address_fields'    => [],
            'contact_fields'    => [],
            'additional_fields' => [],
            'extra_html'        => [],
        ];
    }
    
    
    protected function addPersonalField(string $label, string $fieldHtml): void
    {
        $this->v_output_buffer['personal_fields'][] = ['label' => $label, 'fieldHtml' => $fieldHtml];
    }
    
    
    protected function addCompanyField(string $label, string $fieldHtml): void
    {
        $this->v_output_buffer['company_fields'][] = ['label' => $label, 'fieldHtml' => $fieldHtml];
    }
    
    
    protected function addAddressField(string $label, string $fieldHtml): void
    {
        $this->v_output_buffer['address_fields'][] = ['label' => $label, 'fieldHtml' => $fieldHtml];
    }
    
    
    protected function addContactField(string $label, string $fieldHtml): void
    {
        $this->v_output_buffer['contact_fields'][] = ['label' => $label, 'fieldHtml' => $fieldHtml];
    }
    
    
    protected function addAdditionalField(string $label, string $fieldHtml): void
    {
        $this->v_output_buffer['additional_fields'][] = ['label' => $label, 'fieldHtml' => $fieldHtml];
    }
    
    
    protected function addExtraHtml(string $html): void
    {
        $this->v_output_buffer['extra_html'][] = $html;
    }
}
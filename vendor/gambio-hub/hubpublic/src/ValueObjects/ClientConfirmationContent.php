<?php
/* --------------------------------------------------------------
   ClientConfirmationContent.php 2017-02-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

/**
 * Class ClientConfirmationContent
 *
 * @package HubPublic\ValueObjects
 */
class ClientConfirmationContent
{
    /**
     * Order payment instructions.
     *
     * @var string
     */
    private $orderPaymentInstructions;
    /**
     * Additional module values.
     *
     * @var array
     */
    private $additionalModuleValues;
    
    
    /**
     * ClientConfirmationContent constructor.
     *
     * @param string $orderPaymentInstructions Order payment instructions.
     * @param array  $additionalModuleValues   Additional module values.
     */
    public function __construct(
        string $orderPaymentInstructions,
        array $additionalModuleValues
    ) {
        $this->orderPaymentInstructions = $orderPaymentInstructions;
        $this->additionalModuleValues   = $additionalModuleValues;
    }
    
    
    /**
     * If available, it returns the order payment instructions. Otherwise, an empty string is returned.
     *
     * @return string
     */
    public function getOrderPaymentInstructions(): string
    {
        return $this->orderPaymentInstructions;
    }
    
    
    /**
     * Returns additional module values.
     *
     * @return array
     */
    public function getAdditionalModuleValues(): array
    {
        return $this->additionalModuleValues;
    }
}

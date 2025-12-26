<?php
/* --------------------------------------------------------------
  HiddenInput.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output\HtmlOutput;

/**
 * Class HiddenInput
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters
 */
class HiddenInput implements HtmlOutput
{
    use HtmlUtilityTrait;

    /**
     * @param string $name
     * @param string $value
     * @param array  $attributes
     */
    public function __construct(
        private string $name,
        private string $value,
        private array  $attributes = []
    )
    {
    }


    /**
     * @inheritDoc
     */
    public function toHtml(): string
    {
        $attributes = $this->flattenInputAttributes($this->attributes);

        return sprintf(
            '<input type="hidden" name="%s" value="%s" %s/>',
            $this->name,
            $this->value,
            // @todo remove leading space once Gambio\Core\Verification\Service\VerificationService is not being used and data is being returned directly to consumers.
            $attributes ? " $attributes " : ''
        );
    }
}
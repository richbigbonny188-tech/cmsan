<?php
/* --------------------------------------------------------------
  ModelsMapper.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Content;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale;

/**
 * Class ModelsMapper
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class ModelsMapper
{
    use DataProcessorsTrait;

    /**
     * @param ModelsFactory $factory
     */
    public function __construct(
        private ModelsFactory $factory
    )
    {
    }


    /**
     * @param array $rawData
     *
     * @return Content
     */
    public function mapContent(array $rawData): Content
    {
        $id      = $this->process('content_id', $rawData, [$this, 'processInt']);
        $groupId = $this->process('content_group', $rawData, [$this, 'processInt']);
        $title   = $this->process('content_title', $rawData, [$this, 'processString']);

        return $this->factory->createContent($id, $groupId, $title);
    }


    /**
     * @param array $rawData
     *
     * @return Locale
     */
    public function mapLocaleSettings(array $rawData): Locale
    {
        return $this->factory->createLocaleSettings(
            $this->mapLocaleLanguageSettings($rawData),
            $this->mapLocaleCurrencySettings($rawData)
        );
    }


    /**
     * @param array $rawData
     *
     * @return Locale\Language
     */
    private function mapLocaleLanguageSettings(array $rawData): Locale\Language
    {
        $id        = $this->process('language_id', $rawData, [$this, 'processInt']);
        $code      = $this->process('language_code', $rawData, [$this, 'processString']);
        $directory = $this->process('language_directory', $rawData, [$this, 'processString']);

        return $this->factory->createLocaleLanguageSettings($id, $code, $directory);
    }


    /**
     * @param array $rawData
     *
     * @return Locale\Currency
     */
    private function mapLocaleCurrencySettings(array $rawData): Locale\Currency
    {
        $code               = $this->process('currency_code', $rawData, [$this, 'processString']);
        $decimalSeparator   = $this->process('decimal_separator', $rawData, [$this, 'processString']);
        $thousandsSeparator = $this->process('thousands_separator', $rawData, [$this, 'processString']);
        $decimals           = $this->process('decimals', $rawData, [$this, 'processInt']);

        return $this->factory->createLocaleCurrencySettings($code, $decimalSeparator, $thousandsSeparator, $decimals);
    }
}
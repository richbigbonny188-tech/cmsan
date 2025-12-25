<?php
/* --------------------------------------------------------------
 LanguageDependentConfiguration.php 2020-10-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Model;

use Gambio\Core\Configuration\Model\Interfaces\LanguageDependentConfiguration as LanguageConfiguration;

/**
 * Class LanguageDependentConfiguration
 * @package Gambio\Core\Configuration\Model
 */
class LanguageDependentConfiguration implements LanguageConfiguration
{
    /**
     * @var string
     */
    private $key;
    
    /**
     * @var string
     */
    private $languageCode;
    
    /**
     * @var string|null
     */
    private $value;
    
    /**
     * @var int|null
     */
    private $sortOrder;
    
    
    /**
     * LanguageDependentConfiguration constructor.
     *
     * @param string      $key
     * @param string      $languageCode
     * @param string|null $value
     * @param int|null    $sortOrder
     */
    public function __construct(string $key, string $languageCode, ?string $value, int $sortOrder = null)
    {
        $this->key          = $key;
        $this->languageCode = $languageCode;
        $this->value        = $value;
        $this->sortOrder    = $sortOrder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function key(): string
    {
        return $this->key;
    }
    
    
    /**
     * @inheritDoc
     */
    public function languageCode(): string
    {
        return $this->languageCode;
    }
    
    
    /**
     * @inheritDoc
     */
    public function value(): ?string
    {
        return $this->value;
    }
    
    
    /**
     * @inheritDoc
     */
    public function sortOrder(): ?int
    {
        return $this->sortOrder;
    }
}
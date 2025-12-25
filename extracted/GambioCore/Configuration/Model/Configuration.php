<?php
/* --------------------------------------------------------------
 Configuration.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\Model;

/**
 * Class Configuration
 *
 * @package Gambio\Core\Configuration\Model
 */
class Configuration implements Interfaces\Configuration
{
    /**
     * @var string
     */
    private $key;
    
    /**
     * @var string|null
     */
    private $value;
    
    /**
     * @var int|null
     */
    private $sortOrder;
    
    
    /**
     * Configuration constructor.
     *
     * @param string      $key
     * @param string|null $value
     * @param int|null    $sortOrder
     */
    public function __construct(string $key, string $value = null, int $sortOrder = null)
    {
        $this->key       = $key;
        $this->value     = $value;
        $this->sortOrder = $sortOrder;
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
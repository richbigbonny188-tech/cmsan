<?php
/* --------------------------------------------------------------
  LanguageId.php 2020-02-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Language\ValueObjects;

/**
 * Class LanguageId
 * @package Gambio\Shop\Language\ValueObjects
 */
class LanguageId
{
    /**
     * @var int
     */
    protected $value;
    
    
    /**
     * LanguageId constructor.
     *
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }
    
}
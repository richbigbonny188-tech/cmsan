<?php
/*--------------------------------------------------------------
   FieldName.php 2021-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects;

use MainFactory;

/**
 * Class FieldName
 * @package Gambio\MainComponents\Services\Core\AdditionalFields\Model\ValueObjects
 */
class FieldName
{
    /**
     * @var string
     */
    protected $languageCode;
    
    /**
     * @var string
     */
    protected $name;
    
    
    /**
     * FieldName constructor.
     *
     * @param string $languageCode
     * @param string $name
     */
    public function __construct(string $languageCode, string $name)
    {
        $this->languageCode = $languageCode;
        $this->name         = $name;
    }
    
    
    /**
     * @param string $languageCode
     * @param string $name
     *
     * @return FieldName
     */
    public static function create(string $languageCode, string $name): FieldName
    {
        return MainFactory::create(FieldName::class, $languageCode, $name);
    }
    
    
    /**
     * @param string $name
     *
     * @return FieldName
     */
    public function withName(string $name): FieldName
    {
        return MainFactory::create(FieldName::class, $this->languageCode, $name);
    }
    
    /**
     * @return string
     */
    public function languageCode(): string
    {
        return $this->languageCode;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
}
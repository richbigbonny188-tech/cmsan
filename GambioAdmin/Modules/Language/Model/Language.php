<?php
/* --------------------------------------------------------------
   Language.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\Model;

use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageId;

/**
 * Class Language
 *
 * @package Gambio\Admin\Modules\Language\Model
 */
class Language
{
    /**
     * @var LanguageId
     */
    private $id;
    
    /**
     * @var LanguageCode
     */
    private $code;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $charset;
    
    /**
     * @var string
     */
    private $directory;
    
    
    /**
     * Language constructor.
     *
     * @param LanguageId   $id
     * @param LanguageCode $code
     * @param string       $name
     * @param string       $charset
     * @param string       $directory
     */
    private function __construct(LanguageId $id, LanguageCode $code, string $name, string $charset, string $directory)
    {
        $this->id        = $id;
        $this->code      = $code;
        $this->name      = $name;
        $this->charset   = $charset;
        $this->directory = $directory;
    }
    
    
    /**
     * @param LanguageId   $id
     * @param LanguageCode $code
     * @param string       $name
     * @param string       $charset
     * @param string       $directory
     *
     * @return Language
     */
    public static function create(
        LanguageId $id,
        LanguageCode $code,
        string $name,
        string $charset,
        string $directory
    ): Language {
        return new self($id, $code, $name, $charset, $directory);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code->value();
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return string
     */
    public function charset(): string
    {
        return $this->charset;
    }
    
    
    /**
     * @return string
     */
    public function directory(): string
    {
        return $this->directory;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id->value(),
            'code'      => $this->code->value(),
            'name'      => $this->name,
            'charset'   => $this->charset,
            'directory' => $this->directory,
        ];
    }
}
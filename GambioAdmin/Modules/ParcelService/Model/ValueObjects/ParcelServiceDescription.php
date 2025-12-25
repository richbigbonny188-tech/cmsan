<?php
/* --------------------------------------------------------------
   ParcelServiceProvidersDescription.inc.inc.php 2020-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ParcelServiceDescription
 *
 * @package Gambio\Admin\Modules\ParcelService\Model\ValueObjects
 */
class ParcelServiceDescription
{
    /**
     * @var string
     */
    private $languageCode;
    
    /**
     * @var string
     */
    private $url;
    
    /**
     * @var string
     */
    private $comment;
    
    
    /**
     * ParcelServiceDescription constructor.
     *
     * @param string $languageCode
     * @param string $url
     * @param string $comment
     */
    private function __construct(string $languageCode, string $url, string $comment)
    {
        $this->languageCode = $languageCode;
        $this->url          = $url;
        $this->comment      = $comment;
    }
    
    
    /**
     * @param string $languageCode
     * @param string $url
     * @param string $comment
     *
     * @return ParcelServiceDescription
     */
    public static function create(string $languageCode, string $url, string $comment): ParcelServiceDescription
    {
        $languageCode = strtolower($languageCode);
        
        Assert::regex($languageCode, '/^[a-z]{2}$/', 'Given language code must be a two-digit ISO code.');
        
        return new self($languageCode, $url, $comment);
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
    public function url(): string
    {
        return $this->url;
    }
    
    
    /**
     * @return string
     */
    public function comment(): string
    {
        return $this->comment;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'languageCode' => $this->languageCode,
            'url'          => $this->url,
            'comment'      => $this->comment
        ];
    }
}
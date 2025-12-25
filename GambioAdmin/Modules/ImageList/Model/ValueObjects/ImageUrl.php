<?php
/*--------------------------------------------------------------
   ImageUrl.php 2021-06-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ImageUrl
 * @package Gambio\Admin\Modules\ImageList\Model\ValueObjects
 */
class ImageUrl
{
    /**
     * @var string
     */
    private $url;
    
    
    /**
     * ImageUrl constructor.
     *
     * @param string $url
     */
    private function __construct(string $url)
    {
        $this->url = $url;
    }
    
    
    /**
     * @param string $url
     *
     * @return ImageUrl
     */
    public static function create(string $url): ImageUrl
    {
        Assert::regex($url, '/^http/', self::class . ' must be a valid url');
        
        return new self($url);
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->url;
    }
}
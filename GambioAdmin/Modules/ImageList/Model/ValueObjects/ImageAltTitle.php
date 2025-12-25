<?php
/*--------------------------------------------------------------
   ImageAltTitle.php 2021-10-27
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
 * Class ImageAltTitle
 * @package Gambio\Admin\Modules\ImageList\Model\ValueObjects
 */
class ImageAltTitle extends AbstractImageText
{
    public const TYPE = 'alt_title';
    
    
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return self::TYPE;
    }
    
    
    /**
     * @param string $languageCode
     * @param string $text
     *
     * @return ImageAltTitle
     */
    public static function create(string $languageCode, string $text): ImageAltTitle
    {
        Assert::regex($languageCode,
                      '/^[a-zA-Z]{2}$/',
                      'Given language code does not match two digit ISO format. Got: %s');
        
        return new static($languageCode, $text);
    }
}
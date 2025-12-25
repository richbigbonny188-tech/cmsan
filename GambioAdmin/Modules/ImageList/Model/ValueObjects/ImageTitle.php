<?php
/*--------------------------------------------------------------
   ImageTitle.php 2023-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ImageTitle
 * @package Gambio\Admin\Modules\ImageList\Model\ValueObjects
 */
class ImageTitle extends AbstractImageText
{
    public const TYPE = 'title';
    
    
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
     * @return ImageTitle
     */
    public static function create(string $languageCode, string $text): ImageTitle
    {
        Assert::regex($languageCode,
                      '/^[a-zA-Z]{2}$/',
                      'Given language code does not match two digit ISO format. Got: %s');
    
        return new static($languageCode, $text);
    }
}
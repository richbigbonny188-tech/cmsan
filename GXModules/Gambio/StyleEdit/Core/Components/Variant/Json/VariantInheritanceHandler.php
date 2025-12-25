<?php
/*--------------------------------------------------------------------------------------------------
    VariantInheritanceHandler.php 2019-10-15
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Variant\Json;

use Exception;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\Json\InheritanceHandler;

/**
 * Class VariantInheritanceHandlerFactory
 * @package Gambio\StyleEdit\Core\Components\Variant\Json
 */
class VariantInheritanceHandler extends InheritanceHandler
{
    public const DEFAULT_THUMBNAIL_FILENAME = 'thumbnail.png';

    /**
     * VariantInheritanceHandler constructor.
     *
     * @param FileIO $fileIO
     * @param VariantInheritanceResolver $resolver
     *
     * @throws Exception
     */
    public function __construct(FileIO $fileIO, VariantInheritanceResolver $resolver)
    {
        parent::__construct('', $fileIO, $resolver);
    }


    /**
     * @param $filename
     *
     * @return void
     * @throws Exception
     */
    public function setFilename($filename): void
    {
        parent::setFilename($filename);
    }

    /**
     * @param string $settings
     *
     * @return mixed
     *
     * @throws \FileNotFoundException
     */
    protected function readParentObject($settings)
    {
        $result = parent::readParentObject($settings);
        $result->thumbnail = $this->getRelativeThumbnailPath($settings, $result->thumbnail ?? self::DEFAULT_THUMBNAIL_FILENAME);

        return $result;
    }


    public function __clone()
    {
        $this->filename = null;
        $this->basePath = null;
        $this->object = null;
    }

    protected function getRelativeThumbnailPath($settings, $thumbnail)
    {
        if (!$this->fileIO->exists($this->basePath . DIRECTORY_SEPARATOR . $thumbnail)) {
            return $this->resolver->getResourceRelativePath($settings, $this->basePath, $thumbnail);

        }

        return $thumbnail;
    }
}
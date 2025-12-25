<?php

/* --------------------------------------------------------------
   FileDownloadHttpControllerResponse.inc.php 2018-05-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FileDownloadHttpControllerResponse
 *
 * @category   System
 * @package    Http
 * @subpackage ValueObjects
 * @extends    HttpControllerResponse
 */
class FileDownloadHttpControllerResponse extends HttpControllerResponse
{
    /**
     * Initializes the file download dialog http controller response.
     *
     * @param NonEmptyStringType      $file File path
     * @param NonEmptyStringType|null $name Custom file name (optional)
     * @param NonEmptyStringType|null $type Content MIME type (optional)
     */
    public function __construct(
        NonEmptyStringType $file,
        NonEmptyStringType $name = null,
        NonEmptyStringType $type = null
    ) {
        $realPath = realpath($file->asString());
        
        $contentDispositionHeaderChunk = $name ? ' filename="' . $name->asString() . '"' : '';
        
        $this->httpHeadersArray[] = 'Content-Disposition: attachment;' . $contentDispositionHeaderChunk;
        
        if ($type) {
            $this->httpHeadersArray[] = 'Content-Type: ' . $type->asString();
        }
        
        if (!$realPath) {
            throw new InvalidArgumentException('Invalid file path' . $file->asString());
        }
        
        $this->httpBody = file_get_contents($realPath);
    }
}
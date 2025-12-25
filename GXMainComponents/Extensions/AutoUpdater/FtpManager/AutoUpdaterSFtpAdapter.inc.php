<?php
/* --------------------------------------------------------------
   AutoUpdaterSFtpAdapter.inc.php 2018-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use League\Flysystem\FilesystemException;
use League\Flysystem\PhpseclibV2\ConnectionProvider;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;

/**
 * Class AutoUpdaterSFtpAdapter
 */
class AutoUpdaterSFtpAdapter extends SftpAdapter
{
    private VisibilityConverter $visibilityConverter;

    /**
     * AutoUpdaterSFtpAdapter constructor.
     *
     * @param ConnectionProvider $connectionProvider
     * @param string $root
     * @param VisibilityConverter|null $visibilityConverter
     * @param MimeTypeDetector|null $mimeTypeDetector
     */
    public function __construct(
        ConnectionProvider  $connectionProvider,
        string              $root,
        VisibilityConverter $visibilityConverter = null,
        MimeTypeDetector    $mimeTypeDetector = null
    )
    {
        parent::__construct($connectionProvider, $root, $visibilityConverter, $mimeTypeDetector);
        $this->visibilityConverter = $visibilityConverter ?: new PortableVisibilityConverter();
    }

    /**
     * Change file permissions via sftp.
     *
     * @param string $path
     * @param int $mode
     *
     * @return bool
     */
    public function chmod(string $path, int $mode): bool
    {
        try {
            $this->setVisibility($path, $this->visibilityConverter->inverseForFile($mode));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }
}

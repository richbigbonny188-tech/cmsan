<?php
/* --------------------------------------------------------------
   AutoUpdaterFtpAdapter.inc.php 2018-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use League\Flysystem\FilesystemException;
use League\Flysystem\Ftp\ConnectivityChecker;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\Ftp\FtpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use League\MimeTypeDetection\MimeTypeDetector;

/**
 * Class AutoUpdaterFtpAdapter
 */
class AutoUpdaterFtpAdapter extends FtpAdapter
{
    private VisibilityConverter $visibilityConverter;

    /**
     * AutoUpdaterFtpAdapter constructor.
     *
     * @param FtpConnectionOptions $connectionOptions
     * @param FtpConnectionProvider|null $connectionProvider
     * @param ConnectivityChecker|null $connectivityChecker
     * @param VisibilityConverter|null $visibilityConverter
     * @param MimeTypeDetector|null $mimeTypeDetector
     */
    public function __construct(
        FtpConnectionOptions  $connectionOptions,
        FtpConnectionProvider $connectionProvider = null,
        ConnectivityChecker   $connectivityChecker = null,
        VisibilityConverter   $visibilityConverter = null,
        MimeTypeDetector      $mimeTypeDetector = null
    )
    {
        parent::__construct($connectionOptions, $connectionProvider, $connectivityChecker, $visibilityConverter, $mimeTypeDetector);
        $this->visibilityConverter = $visibilityConverter ?: new PortableVisibilityConverter();
    }

    /**
     * Change file permissions via ftp.
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

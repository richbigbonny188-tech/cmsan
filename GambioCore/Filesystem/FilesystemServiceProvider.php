<?php
/* --------------------------------------------------------------
 FilesystemServiceProvider.php 2022-04-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Filesystem;

use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Filesystem\Interfaces\Filesystem;
use League\Flysystem\Filesystem as LeagueFileSystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

/**
 * Class FilesystemServiceProvider
 * @package Gambio\Core\Filesystem
 */
class FilesystemServiceProvider extends AbstractServiceProvider
{
    /**
     * Cache file/dir permissions.
     *
     * @var array
     */
    private static $permissions = [
        'file' => [
            'public'  => 0777,
            'private' => 0777,
        ],
        'dir'  => [
            'public'  => 0777,
            'private' => 0777,
        ]
    ];
    
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            Filesystem::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        /** @var Path $path */
        $path     = $this->application->get(Path::class);
        $basePath = $path->base();
        
        $visibility = PortableVisibilityConverter::fromArray(static::$permissions);
        $this->application->register(LocalFilesystemAdapter::class)
            ->addArgument($basePath)
            ->addArgument($visibility)
            ->addArgument(LOCK_EX)
            ->addArgument(LocalFilesystemAdapter::DISALLOW_LINKS);
        $this->application->register(LeagueFileSystem::class)->addArgument(LocalFilesystemAdapter::class);
        
        $this->application->registerShared(Filesystem::class, FlysystemAdapter::class)
            ->addArgument(LeagueFileSystem::class)
            ->addArgument($basePath);
    }
}
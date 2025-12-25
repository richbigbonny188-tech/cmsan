<?php
/* --------------------------------------------------------------
  ExportHtmlService.php 2019-12-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services;

use Exception;
use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Configurations\ShopBaseUrl;
use Gambio\StyleEdit\Core\Mapper\ContentGroupToContentAliasMapper;
use Gambio\StyleEdit\Core\Mapper\Exceptions\AliasNotFoundException;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ExportHtmlService
 * @package Gambio\StyleEdit\Core\Services
 */
class ExportHtmlService
{
    /**
     * @var array
     */
    protected $changedFiles = [];
    
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    
    /**
     * @var ShopBaseUrl
     */
    protected $shopBaseUrl;
    
    /**
     * @var ContentGroupToContentAliasMapper
     */
    protected $mapper;
    
    
    /**
     * ExportHtmlService constructor.
     *
     * @param ContentGroupToContentAliasMapper $mapper
     *
     * @throws Exception
     */
    public function __construct(ContentGroupToContentAliasMapper $mapper)
    {
        $this->filesystem  = SingletonPrototype::instance()->get('FilesystemAdapterShopRoot');
        $this->shopBaseUrl = SingletonPrototype::instance()->get(ShopBaseUrl::class);
        $this->mapper      = $mapper;
    }
    
    
    /**
     * @param string $themeId
     *
     * @throws FileNotFoundException
     * @throws AliasNotFoundException
     * @throws Exception
     */
    public function updateContentZoneHtmlFiles(string $themeId): void
    {
        $contentZoneHtmlPaths = $this->contentZoneHtmlPaths($themeId);
        
        if (count($contentZoneHtmlPaths)) {
            
            foreach ($contentZoneHtmlPaths as $path) {
                
                $content = $this->filesystem->read($path);
                $search  = array_keys($this->changedFiles());
                $replace = $this->publicThemeUrls(array_values($this->changedFiles()));
                $content = str_replace($search, $replace, $content);
                $content = $this->removeProductWidgetHtml($content);
                $content = $this->updateContentManagerFunctionCalls($content);
                $this->filesystem->update($path, $content);
            }
        }
    }
    
    
    /**
     * @param array $values
     *
     * @return array
     */
    protected function publicThemeUrls(array $values): array
    {
        if (count($values)) {
            
            foreach ($values as &$value) {
                
                $pattern = '#themes/[^/]+/#';
                $value   = preg_replace($pattern, 'public/theme/', $value);
                $value   = str_replace($this->shopBaseUrl->value(), '', $value);
            }
            unset($value);
        }
        
        return $values;
    }
    
    
    /**
     * @param string $themeId
     *
     * @return array
     */
    protected function contentZoneHtmlPaths(string $themeId): array
    {
        $result  = [];
        $htmlDir = 'themes' . DIRECTORY_SEPARATOR . $themeId . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                   . 'system';
        
        foreach ($this->filesystem->listContents($htmlDir) as $file) {
            
            if ($file['extension'] === 'html' && 0 === strpos(basename($file['path']), 'content_zone')) {
                
                $result[] = $file['path'];
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param array $changedFiles
     */
    public function setChangedFiles(array $changedFiles): void
    {
        $this->changedFiles = $changedFiles;
    }
    
    
    /**
     * @return array
     */
    public function changedFiles(): array
    {
        return $this->changedFiles;
    }
    
    
    /**
     * @param string $content
     *
     * @return string
     * @throws Exception
     */
    protected function removeProductWidgetHtml(string $content): string
    {
        $pattern = '/\{product[^}]+}/';
        
        if (preg_match_all($pattern, $content, $matches) && count($matches[0])) {
    
            foreach ($matches as $match) {
                
                $content = str_replace($match, '', $content);
            }
        }
        
        
        return $content;
    }
    
    
    /**
     * @param string $content
     *
     * @return string
     * @throws AliasNotFoundException
     */
    protected function updateContentManagerFunctionCalls(string $content): string
    {
        $pattern = '/{content_manager\sgroup=("|\')([^}]+)("|\')}/m';
        
        if (!preg_match_all($pattern, $content, $matches)) {
            
            return $content;
        }
    
        $functionCalls = $matches[0];
        $arguments     = $matches[2];
    
        foreach ($arguments as $index => $argument) {
    
            $alias   = $this->mapper->getAlias((int)$argument);
            $search  = $functionCalls[$index];
            $replace = '{content_manager_group_alias alias="' . $alias . '"}';
            $content = str_replace($search, $replace, $content);
        }
        
        return $content;
    }
}
<?php
/* --------------------------------------------------------------
   BasicElementController.inc.php 2022-06-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Components;

use Exception;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\RequestedThemeInterface;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeConfiguration;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeConfigurationCollection;
use Gambio\StyleEdit\Core\Components\Theme\Exceptions\ThemeIdNotSuppliedException;
use Gambio\StyleEdit\Core\Components\Theme\Exceptions\InvalidThemeIdException;
use Gambio\StyleEdit\Core\Components\Theme\PreviewSettingsService;
use Gambio\StyleEdit\Core\Components\Theme\StyleEditThemeService;
use Gambio\StyleEdit\Core\SingletonPrototype;

/**
 * Class BasicElementController
 * @package Gambio\StyleEdit\Core\Elements
 */
abstract class BasicController
{

    /**
     * @var bool
     */
    protected $previewSettingsService;
    /**
     * @var null | ThemeConfigurationCollection
     */
    protected $themesList;
    /**
     * @var bool | ThemeConfiguration
     */
    private $theme = false;
    /**
     * @var null | StyleEditThemeService
     */
    private $themeService;
    /**
     * @var RequestedThemeInterface
     */
    private $requestedTheme;


    /**
     * @param array $uri
     *
     * @return mixed
     */
    abstract public function get(array $uri);


    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    abstract public function put(array $uri, $data);


    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    abstract public function post(array $uri, $data);


    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    abstract public function delete(array $uri, $data);


    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    abstract public function patch(array $uri, $data);


    /**
     * @return BasicController
     */
    abstract public function __clone();

    public function __construct(RequestedThemeInterface $requestedTheme = null)
    {
        $this->requestedTheme = $requestedTheme;
    }


    /**
     * @return bool
     */
    public function requiresAuthentication(): bool
    {
        return true;
    }


    protected function updateThemeList()
    {

        $this->themeService()->updateThemeList();
        $this->themesList = null;
    }


    /**
     * @return bool|StyleEditThemeService
     * @throws Exception
     */
    protected function themeService()
    {
        if ($this->themeService === null) {
            $this->themeService = SingletonPrototype::instance()->get(StyleEditThemeService::class);
            if (!$this->themeService) {
                throw new Exception('ThemeService not published');
            }
        }

        return $this->themeService;
    }


    /**
     * @return bool|PreviewSettingsService|mixed|null
     * @throws Exception
     */
    protected function previewSettingsService()
    {
        if ($this->previewSettingsService === null) {
            $this->previewSettingsService = SingletonPrototype::instance()->get(PreviewSettingsService::class);
            if (!$this->previewSettingsService) {
                throw new Exception('PreviewSettingsService not published');
            }
        }

        return $this->previewSettingsService;
    }


    /**
     * @param mixed $value
     * @return string
     */
    protected function outputJson($value): string
    {
//        header('Content-type: application/json');
        return json_encode($value);
    }


    /**
     * @return string
     * @throws Exception
     */
    protected function currentThemeId(): string
    {
        try {
            return $this->currentTheme()->id();
        } catch (\Exception $e) {
            if ($this->requestedTheme) {
                throw new InvalidThemeIdException([$this->requestedTheme->id()], $e);
            }

            throw new ThemeIdNotSuppliedException(['not supplied'], $e);
        }
    }


    /**
     * @return CurrentThemeInterface|null
     * @throws Exception
     */
    protected function currentTheme(): ?CurrentThemeInterface
    {
        if (!$this->theme) {
            $this->theme = SingletonPrototype::instance()->get(CurrentThemeInterface::class);
        }

        return $this->theme ?: null;
    }


    /**
     * @return ThemeConfigurationCollection
     * @throws Exception
     */
    protected function getThemesList(): ThemeConfigurationCollection
    {
        if ($this->themesList === null) {
            $this->themesList = SingletonPrototype::instance()->get(ThemeConfigurationCollection::class);
        }

        return $this->themesList;
    }

}
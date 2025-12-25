<?php
/* --------------------------------------------------------------
 TemplateVersion.php 2020-12-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 31 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Filter\Types;

use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Admin\Layout\Menu\Filter\FilterConditionArguments;
use Gambio\Admin\Layout\Menu\Filter\FilterInterface;
use InvalidArgumentException;
use function array_key_exists;
use function file_exists;
use function file_get_contents;
use function implode;
use function in_array;
use function preg_match;

/**
 * Class TemplateVersion
 * @package Gambio\Admin\Layout\Menu\Filter\Types
 *
 * @codeCoverageIgnore
 */
class TemplateVersion implements FilterInterface
{
    public const  FILTER_METHOD    = 'templateVersion';
    private const FALLBACK_VERSION = 1.0;
    
    private const OPERATOR_GREATER       = '>';
    private const OPERATOR_GREATER_EQUAL = '>=';
    private const OPERATOR_LOWER         = '<';
    private const OPERATOR_LOWER_EQUAL   = '<=';
    private const OPERATOR_EQUAL         = '=';
    private const OPERATORS              = [
        self::OPERATOR_GREATER,
        self::OPERATOR_GREATER_EQUAL,
        self::OPERATOR_LOWER,
        self::OPERATOR_LOWER_EQUAL,
        self::OPERATOR_EQUAL,
    ];
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    /**
     * @var Path
     */
    private $path;
    
    
    /**
     * TemplateVersion constructor.
     *
     * @param ConfigurationService $configurationService
     * @param Path                 $path
     */
    public function __construct(ConfigurationService $configurationService, Path $path)
    {
        $this->configurationService = $configurationService;
        $this->path                 = $path;
    }
    
    
    /**
     * @inheritDoc
     */
    public function check(FilterConditionArguments $condition): bool
    {
        $this->assertArgsValid($condition);
        
        $args            = $condition->args();
        
        [$operator, $expectedVersion] = $args;
        $version         = $this->presentationVersionDetection();
        
        switch ($operator) {
            case self::OPERATOR_GREATER:
                return $version > (float)$expectedVersion;
            case self::OPERATOR_GREATER_EQUAL:
                return $version >= (float)$expectedVersion;
            case self::OPERATOR_LOWER:
                return $version < (float)$expectedVersion;
            case self::OPERATOR_LOWER_EQUAL:
                return $version <= (float)$expectedVersion;
            case self::OPERATOR_EQUAL:
                return $version === (float)$expectedVersion;
            default:
                return false;
        }
    }
    
    
    /**
     * Presentation version detection.
     *
     * At first, this function search for a settings file in the public's theme path and tries to return the
     * presentation version. If it didnt work, the function tries to find template settings.
     * In case of nothing succeed, a fallback version will be returned.
     *
     * @return float
     */
    private function presentationVersionDetection(): float
    {
        // theme version detection
        $themeSettingsPath = "{$this->path->base()}/public/theme/config/theme_settings.php";
        if (file_exists($themeSettingsPath)) {
            // regex is used by intend, because an include cause issues with the main factory
            $pattern = '/\'THEME_PRESENTATION_VERSION\' => (\d+.\d+)/';
            preg_match($pattern, file_get_contents($themeSettingsPath), $matches);
            
            return array_key_exists(1, $matches) ? (float)$matches[1] : self::FALLBACK_VERSION;
        }
        
        return self::FALLBACK_VERSION;
    }
    
    
    /**
     * Validates the arguments, so they are save to use after validation.
     *
     * @param FilterConditionArguments $condition
     */
    private function assertArgsValid(FilterConditionArguments $condition): void
    {
        $args = $condition->args();
        if (!array_key_exists(0, $args)) {
            throw new InvalidArgumentException('First parameter for TemplateVersion filter (operator) not found');
        }
        
        if (!array_key_exists(1, $args)) {
            throw new InvalidArgumentException('Second parameter for TemplateVersion filter (version) not found');
        }
        
        if (!in_array($args[0], self::OPERATORS, true)) {
            $operators = implode('", "', self::OPERATORS);
            $operators = '"' . $operators . '"';
            throw new InvalidArgumentException("Operator param {$args[0]} invalid. Valid operators: {$operators}");
        }
    }
}
<?php
/* --------------------------------------------------------------
 MenuFactory.php 2020-04-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 30 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Factories;

use Gambio\Admin\Layout\Menu\Filter\Conditions;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Admin\Layout\Menu\Filter\Condition;
use Gambio\Admin\Layout\Menu\Models\Cache\Collections\MenuItems;
use Gambio\Admin\Layout\Menu\Models\Cache\MenuGroup;
use Gambio\Admin\Layout\Menu\Models\Cache\MenuItem;
use Gambio\Core\TextManager\Services\TextManager;
use InvalidArgumentException;
use Webmozart\Assert\Assert;
use function array_key_exists;

/**
 * Class MenuFactory
 * @package Gambio\Admin\Layout\Menu
 * @codeCoverageIgnore
 */
class CacheMenuFactory
{
    /**
     * @var TextManager
     */
    private $textManager;
    
    /**
     * @var int
     */
    private $languageId;
    
    
    /**
     * MenuFactory constructor.
     *
     * @param TextManager     $textManager
     * @param UserPreferences $userPreferences
     */
    public function __construct(TextManager $textManager, UserPreferences $userPreferences)
    {
        $this->textManager = $textManager;
        $this->languageId  = $userPreferences->languageId();
    }
    
    
    /**
     * Creates a condition if possible.
     *
     * This function ties to create a new condition model from the given data.
     * If anything goes wrong, just null will be returned.
     * This can be the case too, if the "if" key not exists in the data set, which means that no
     * filter should be applied.
     *
     * @param array $data
     *
     * @return Conditions|null
     */
    public function createConditions(array $data): ?Conditions
    {
        if (!array_key_exists('if', $data)) {
            return null;
        }
        $conditionData = $data['if'];
        
        if ($this->isNumericArray($conditionData)) {
            return $this->createMultipleConditions($conditionData);
        }
        
        return $this->createSingleCondition($conditionData);
    }
    
    
    /**
     * Creates a menu group.
     *
     * This function creates a menu group model from the given data.
     * In case of bad formed $data, an exception is thrown.
     *
     * @param array           $data
     * @param Conditions|null $conditions
     *
     * @return MenuGroup
     * @throws InvalidArgumentException
     */
    public function createMenuGroup(array $data, ?Conditions $conditions): MenuGroup
    {
        $data['title'] = $this->translate($data['title'] ?? null);
        
        return MenuGroup::fromArray($data, new MenuItems(), $conditions);
    }
    
    
    /**
     * Creates a menu item.
     *
     * This function creates a menu item from the given data.
     * In case of bad formed $data, an exception is thrown.
     *
     * @param array           $data
     * @param Conditions|null $conditions
     *
     * @return MenuItem
     * @throws InvalidArgumentException
     */
    public function createMenuItem(array $data, ?Conditions $conditions = null): MenuItem
    {
        $data['title'] = $this->translate($data['title']);
        
        return MenuItem::fromArray($data, $conditions);
    }
    
    
    /**
     * Creates a conditions object from the given data.
     * It is expected that the data contains information about multiple conditions.
     *
     * @param array $conditionData
     *
     * @return Conditions|null
     */
    private function createMultipleConditions(array $conditionData): ?Conditions
    {
        $conditions = [];
        foreach ($conditionData as $conditionDataSet) {
            $condition = $this->createCondition($conditionDataSet);
            if ($condition) {
                $conditions[] = $condition;
            }
        }
        if (empty($conditions)) {
            return null;
        }
        
        return new Conditions(...$conditions);
    }
    
    
    /**
     * Creates a conditions object from the given data.
     * It is expected that the data contains information for exactly one condition.
     *
     * @param array $conditionData
     *
     * @return Conditions|null
     */
    private function createSingleCondition(array $conditionData): ?Conditions
    {
        $condition = $this->createCondition($conditionData);
        if ($condition) {
            return new Conditions($condition);
        }
        
        return null;
    }
    
    
    /**
     * Tries to create a condition object from the given data.
     *
     * @param array $data
     *
     * @return Condition|null
     */
    private function createCondition(array $data): ?Condition
    {
        try {
            Assert::keyExists($data, 'filter');
            Assert::keyExists($data, 'args');
            
            Assert::string($data['filter']);
            Assert::isArray($data['args']);
            
            return Condition::create($data['filter'], $data['args']);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }
    
    
    /**
     * Translates the language key.
     *
     * @param string|null $langKey
     *
     * @return string|null
     */
    private function translate(?string $langKey): ?string
    {
        if (!$langKey) {
            return null;
        }
        
        $langData = explode('.', $langKey);
        if (count($langData) === 2) {
            [$section, $phrase] = $langData;
            
            $translated = $this->textManager->getPhraseText($phrase, $section, $this->languageId);
            
            return $translated !== $phrase ? $translated : $langKey;
        }
        
        return $langKey;
    }
    
    
    /**
     * Checks if given array is numeric.
     *
     * @param array $array
     *
     * @return bool
     */
    private function isNumericArray(array $array): bool
    {
        return array_keys($array) === range(0, count($array) - 1);
    }
}
<?php
/* --------------------------------------------------------------
  AbstractProductModifierDisplayType.php 2023-03-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class AbstractProductModifierDisplayType
 */
abstract class AbstractProductModifierDisplayType implements ProductModifierDisplayTypeInterface
{
    public const NAME = '';
    
    protected const LANGUAGE_PHRASE_SECTION = 'product_modifier_display_types';
    
    /**
     * @var string
     */
    protected $name;
    /**
     * @var LanguageTextManager
     */
    protected $textManager;
    
    
    /**
     * AbstractProductModifierDisplayType constructor.
     *
     * @param string              $name
     * @param LanguageTextManager $textManager
     */
    public function __construct(string $name, LanguageTextManager $textManager)
    {
        if (static::NAME !== $name && static::NAME !== self::NAME) {
            
            throw new InvalidArgumentException('Class constant NAME must be set and equal the DisplayType name');
        }
    
        $this->name        = $name;
        $this->textManager = $textManager;
    }
    
    
    /**
     * @param LanguageTextManager|null $textManager
     *
     * @return ProductModifierDisplayTypeInterface
     */
    public static function create(?LanguageTextManager $textManager = null): ProductModifierDisplayTypeInterface
    {
        return new static(static::NAME, $textManager ?? static::textManager());
    }
    
    
    /**
     * @return LanguageTextManager
     */
    protected static function textManager(): LanguageTextManager
    {
        return LanguageTextManager::get_instance(static::LANGUAGE_PHRASE_SECTION);
    }
    
    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }
    
    /**
     * @inheritDoc
     */
    public function localisation(): string
    {
        return $this->textManager->get_text(strtolower($this->name));
    }
}
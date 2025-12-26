<?php
/**
 * Repository.php 2020-3-17
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Properties\Representation\SelectionHtml\Repository;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\Properties\ProductModifiers\Database\ValueObjects\PropertyModifierIdentifier;
use Gambio\Shop\Properties\Representation\SelectionHtml\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\Properties\Representation\SelectionHtml\Generators\ModifierHtmlGenerator;
use Gambio\Shop\Properties\Representation\SelectionHtml\Repository\Readers\ReaderInterface;

/**
 * Class Repository
 * @package Gambio\Shop\Properties\Representation\SelectionHtml\Repository
 */
class Repository implements RepositoryInterface
{
    /**
     * @var ReaderInterface
     */
    protected $reader;
    
    
    /**
     * Repository constructor.
     *
     * @param ReaderInterface $reader
     */
    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * @inheritDoc
     */
    public function selectionHtmlGenerator(
        ModifierIdentifierCollectionInterface $identifiers,
        LanguageId $languageId
    ): ModifierHtmlGenerator {
        
        $dtos = [];
    
        foreach ($identifiers as $identifier) {
        
            if ($identifier instanceof PropertyModifierIdentifier) {
            
                $dtos[] = $this->reader->selectionData($identifier, $languageId);
            }
        }
    
        return $this->createGenerator($dtos);
    }
    
    //**
    
    protected function createGenerator(array $dtos): ModifierHtmlGenerator
    {
        return new ModifierHtmlGenerator($dtos);
    }
}
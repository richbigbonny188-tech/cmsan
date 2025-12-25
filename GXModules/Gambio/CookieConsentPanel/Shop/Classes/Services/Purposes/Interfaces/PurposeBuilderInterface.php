<?php
/* --------------------------------------------------------------
  PurposeBuilderInterface.php 2020-01-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

use Gambio\CookieConsentPanel\Services\Purposes\Exceptions\UnfinishedBuildException;
use Gambio\CookieConsentPanel\Services\Purposes\ValueObjects\Id;
use Gambio\CookieConsentPanel\Services\Purposes\ValueObjects\LanguageCode;

/**
 * Interface PurposeBuilderInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface PurposeBuilderInterface
{
    
    /**
     * @return PurposeInterface
     * @throws UnfinishedBuildException
     */
    public function build(): PurposeInterface;
    
    
    /**
     * @param int          $categoryId
     * @param LanguageCode $languageCode
     *
     * @return PurposeBuilderInterface
     */
    public function withCategoryId(int $categoryId, LanguageCode $languageCode): PurposeBuilderInterface;
    
    
    /**
     * @param CategoryInterface $category
     *
     * @return PurposeBuilderInterface
     */
    public function withCategory(CategoryInterface $category): PurposeBuilderInterface;
    
    
    /**
     *
     * @param string $alias
     *
     * @return PurposeBuilderInterface
     */
    public function withAlias(string $alias = null): PurposeBuilderInterface;
    
    
    /**
     * @param int $id
     *
     * @return PurposeBuilderInterface
     */
    public function withId(int $id): PurposeBuilderInterface;
    
    
    /**
     * @param bool $deletable
     *
     * @return PurposeBuilderInterface
     */
    public function withDeletable(bool $deletable): PurposeBuilderInterface;
    
    
    /**
     * @param array $descriptions
     *
     * @return PurposeBuilderInterface
     */
    public function withDescriptions(array $descriptions): PurposeBuilderInterface;
    
    
    /**
     * @param array $names
     *
     * @return PurposeBuilderInterface
     */
    public function withNames(array $names): PurposeBuilderInterface;
    
    
    /**
     * @param bool $status
     *
     * @return PurposeBuilderInterface
     */
    public function withStatus(bool $status): PurposeBuilderInterface;
    
}
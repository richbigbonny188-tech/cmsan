<?php
/* --------------------------------------------------------------
   ConfigurationFactory.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Services;

use Gambio\Admin\Modules\Configuration\Model\Collections\Categories;
use Gambio\Admin\Modules\Configuration\Model\Collections\Configurations;
use Gambio\Admin\Modules\Configuration\Model\Collections\Groups;
use Gambio\Admin\Modules\Configuration\Model\Collections\Links;
use Gambio\Admin\Modules\Configuration\Model\Collections\ListingCategories;
use Gambio\Admin\Modules\Configuration\Model\Collections\Tags;
use Gambio\Admin\Modules\Configuration\Model\Entities\Category;
use Gambio\Admin\Modules\Configuration\Model\Entities\Configuration;
use Gambio\Admin\Modules\Configuration\Model\Entities\Group;
use Gambio\Admin\Modules\Configuration\Model\Entities\Tag;
use Gambio\Admin\Modules\Configuration\Model\Entities\Type;
use Gambio\Admin\Modules\Configuration\Model\ValueObjects\LanguageConfigurationValues;
use Gambio\Admin\Modules\Configuration\Model\ValueObjects\Link;
use Gambio\Admin\Modules\Configuration\Model\ValueObjects\ListingCategory;
use InvalidArgumentException;

/**
 * Class ConfigurationFactory
 *
 * @package Gambio\Admin\Modules\Configuration\Services
 */
class ConfigurationFactory
{
    /**
     * @var TypeFactoryAggregation
     */
    private $typeFactory;
    
    
    /**
     * ConfigurationFactory constructor.
     *
     * @param TypeFactoryAggregation $typeFactory
     */
    public function __construct(TypeFactoryAggregation $typeFactory)
    {
        $this->typeFactory = $typeFactory;
    }
    
    
    /**
     * @param string $id
     * @param array  $params
     *
     * @return Type
     */
    public function createType(string $id, array $params): Type
    {
        return $this->typeFactory->createType($id, $params);
    }
    
    
    /**
     * @param string $label
     * @param string $link
     * @param string $buttonText
     * @param bool   $newWindow
     *
     * @return Link
     */
    public function createLink(string $label, string $link, string $buttonText, bool $newWindow): Link
    {
        return Link::create($label, $link, $buttonText, $newWindow);
    }
    
    
    /**
     * @param Link ...$links
     *
     * @return Links
     */
    public function createLinks(Link ...$links): Links
    {
        return Links::create(...$links);
    }
    
    
    /**
     * @param Category $details
     * @param Groups   $groups
     *
     * @return ListingCategory
     */
    public function createListingCategory(Category $details, Groups $groups): ListingCategory
    {
        return ListingCategory::create($details, $groups);
    }
    
    
    /**
     * @param ListingCategory ...$listingCategories
     *
     * @return ListingCategories
     */
    public function createListingCategories(ListingCategory ...$listingCategories): ListingCategories
    {
        return ListingCategories::create(...$listingCategories);
    }
    
    
    /**
     * @param string            $key
     * @param string|bool|array $value
     * @param string            $label
     * @param string            $tooltip
     * @param Type              $type
     * @param Tags              $tags
     *
     * @return Configuration
     */
    public function createConfiguration(
        string $key,
        $value,
        string $label,
        string $tooltip,
        Type $type,
        Tags $tags
    ): Configuration {
        if (is_bool($value)) {
            return Configuration::createWithBoolValue($key, $value, $label, $tooltip, $type, $tags);
        }
        
        if (is_string($value)) {
            return Configuration::createWithStringValue($key, $value, $label, $tooltip, $type, $tags);
        }
        if (is_array($value)) {
            $langValues = new LanguageConfigurationValues($value);
            
            return Configuration::createWithLanguageDependentValues($key, $langValues, $label, $tooltip, $type, $tags);
        }
        
        throw new InvalidArgumentException('Provided value needs to be of string or bool type.');
    }
    
    
    /**
     * @param Configuration ...$configurations
     *
     * @return Configurations
     */
    public function createConfigurations(Configuration ...$configurations): Configurations
    {
        return Configurations::create(...$configurations);
    }
    
    
    /**
     * @param string $id
     * @param string $label
     *
     * @return Category
     */
    public function createCategory(string $id, string $label): Category
    {
        return Category::create($id, $label);
    }
    
    
    /**
     * @param Category ...$categories
     *
     * @return Categories
     */
    public function createCategories(Category ...$categories): Categories
    {
        return Categories::create(...$categories);
    }
    
    
    /**
     * @param string $id
     * @param string $label
     *
     * @return Tag
     */
    public function createTag(string $id, string $label): Tag
    {
        return Tag::create($id, $label);
    }
    
    
    /**
     * @param Tag ...$tags
     *
     * @return Tags
     */
    public function createTags(Tag ...$tags): Tags
    {
        return Tags::create(...$tags);
    }
    
    
    /**
     * @param string         $id
     * @param string         $label
     * @param Configurations $configurations
     * @param Links          $links
     *
     * @return Group
     */
    public function createGroup(string $id, string $label, Configurations $configurations, Links $links): Group
    {
        return Group::create($id, $label, $configurations, $links);
    }
    
    
    /**
     * @param Group ...$groups
     *
     * @return Groups
     */
    public function createGroups(Group ...$groups): Groups
    {
        return Groups::create(...$groups);
    }
}
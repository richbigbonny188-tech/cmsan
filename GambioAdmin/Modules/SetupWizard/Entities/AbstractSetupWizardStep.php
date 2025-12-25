<?php
/* --------------------------------------------------------------
  AbstractSetupWizardStep.inc.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\SetupWizard\Entities;

use Gambio\Admin\Modules\SetupWizard\Interfaces\SetupWizardStep;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Icon;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Index;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Key;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Status;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Text;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Title;
use Gambio\Admin\Modules\SetupWizard\ValueObjects\Url;

/**
 * Class SetupWizardStep
 *
 * CONSTANT KEY must be set in children classes
 */
abstract class AbstractSetupWizardStep implements SetupWizardStep
{
    /**
     * @var Status
     */
    protected $status;
    
    /**
     * @var Icon
     */
    protected $icon;
    
    /**
     * @var Index
     */
    protected $index;
    
    /**
     * @var Url
     */
    protected $link;
    
    /**
     * @var Key
     */
    private $key;
    
    /**
     * @var Title
     */
    private $title;
    /**
     * @var Text
     */
    private $description;
    
    
    /**
     * SetupWizardStep constructor.
     *
     * @param Status $status
     * @param Icon   $icon
     * @param Index  $index
     * @param Url    $link
     * @param Title  $title
     * @param Text   $description
     * @param Key    $key
     */
    protected function __construct(
        Status $status,
        Icon $icon,
        Index $index,
        Url $link,
        Title $title,
        Text $description,
        Key $key
    ) {
        $this->status      = $status;
        $this->icon        = $icon;
        $this->index       = $index;
        $this->link        = $link;
        $this->key         = $key;
        $this->title       = $title;
        $this->description = $description;
    }
    
    
    /**
     * @return Status
     */
    public function status(): Status
    {
        return $this->status;
    }
    
    
    /**
     * @return Icon
     */
    public function icon(): Icon
    {
        return $this->icon;
    }
    
    
    /**
     * @return Index
     */
    public function index(): Index
    {
        return $this->index;
    }
    
    
    /**
     * @return Title
     */
    public function headline(): Title
    {
        return $this->title;
    }
    
    
    /**
     * @return Text
     */
    public function description(): Text
    {
        
        return $this->description;
    }
    
    
    /**
     * @return Url
     */
    public function link(): Url
    {
        return $this->link;
    }
    
    
    /**
     * @return Key
     */
    public function stepKey(): Key
    {
        return $this->key;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize () {
        $result = [
            'title'=>$this->headline()->value(),
            'text'=>$this->description()->value(),
            'icon'=>$this->icon()->value()->value(),
            'linkHref'=>$this->link()->value(),
            'status'=>$this->status()->value(),
            'key'=>$this->key->value()
        ];
        return $result;
    }
}
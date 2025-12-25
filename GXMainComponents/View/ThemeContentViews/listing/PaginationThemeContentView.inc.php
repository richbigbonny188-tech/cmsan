<?php

/* --------------------------------------------------------------
  PaginationThemeContentView.inc.php 2019-01-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class PaginationThemeContentView extends ThemeContentView
{
    /**
     * @var ExtendedInformationPager
     */
    protected $pager;
    
    /**
     * @var string
     */
    protected $urlTemplate;
    
    
    public function __construct()
    {
        parent::__construct();
        $this->set_content_template('pagination.html');
        $this->set_flat_assigns(true);
        $this->set_caching_enabled(false);
    }
    
    
    public function prepare_data()
    {
        $data = [
            'currentPage' => $this->pager->page(),
            'currentMin'  => $this->pager->lowestOnPage(),
            'currentMax'  => $this->pager->highestOnPage(),
            'isFirstPage' => $this->pager->isFirstPage(),
            'isLastPage'  => $this->pager->isLastPage(),
            'totalItems'  => $this->pager->totalItemCount(),
            'totalPages'  => $this->pager->totalPageCount(),
            'urlTemplate' => $this->urlTemplate
        ];
        
        foreach ($data as $key => $value) {
            $this->set_content_data($key, $value);
        }
    }
    
    
    public function setPager(ExtendedInformationPager $pager)
    {
        $this->pager = $pager;
    }
    
    
    public function setUrlTemplate(StringType $urlTemplate)
    {
        $this->urlTemplate = $urlTemplate->asString();
    }
}
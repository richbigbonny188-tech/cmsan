<?php
/* --------------------------------------------------------------
   ExtendedInformationPager.inc.php 2022-04-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * ExtendedInformationPager
 */
class ExtendedInformationPager extends Pager
{
    /**
     * @var int
     */
    protected $totalItemCount;
    
    /**
     * @var string
     */
    protected $pageParameter;
    
    
    /**
     * ExtendedInformationPager constructor.
     *
     * @param IntType    $page
     * @param IntType    $perPage
     * @param IntType    $totalItemCount
     * @param StringType $pageParameter
     */
    public function __construct(IntType $page, IntType $perPage, IntType $totalItemCount, StringType $pageParameter)
    {
        parent::__construct($page, $perPage);
        $this->totalItemCount = $totalItemCount->asInt();
        $this->pageParameter  = $pageParameter->asString();
        $this->page           = $page->asInt() < 1 ? 1 : min($page->asInt(), $this->totalPageCount());
        $this->offset         = $this->page <= 1 ? 0 : ($this->page - 1) * $this->perPage;
    }
    
    
    /**
     * Named constructor of extended information pager.
     *
     * @param int    $page           Current page.
     * @param int    $perPage        Items per page.
     * @param int    $totalItemCount Total number of items.
     * @param string $pageParameter  The name of the page parameter
     *
     * @return ExtendedInformationPager New instance.
     *
     * @throws InvalidArgumentException
     */
    public static function createExtendedInformationPager($page, $perPage, $totalItemCount, $pageParameter)
    {
        return MainFactory::create(static::class,
                                   new IntType($page),
                                   new IntType($perPage),
                                   new IntType($totalItemCount),
                                   new StringType($pageParameter));
    }
    
    
    /**
     * Gets the lowest item of the current page.
     *
     * @return int
     */
    public function lowestOnPage()
    {
        return ($this->page - 1) * $this->perPage + 1;
    }
    
    
    /**
     * Gets the highest item of the current page.
     *
     * @return int
     */
    public function highestOnPage()
    {
        return (int)min($this->page * $this->perPage, $this->totalItemCount);
    }
    
    
    /**
     * Gets the total number of pages.
     *
     * @return int
     */
    public function totalPageCount()
    {
        return $this->perPage > 0 ? (int)ceil($this->totalItemCount / $this->perPage) : 1;
    }
    
    
    /**
     * Gets the total number of items.
     *
     * @return int
     */
    public function totalItemCount()
    {
        return $this->totalItemCount;
    }
    
    
    /**
     * Returns true if the current page is the first page.
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->page <= 1;
    }
    
    
    /**
     * Returns true if the current page is the last page.
     *
     * @return bool
     */
    public function isLastPage()
    {
        return $this->page >= $this->totalPageCount();
    }
    
    
    /**
     * Gets the name of the page parameter.
     *
     * @return string
     */
    public function pageParameter()
    {
        return $this->pageParameter;
    }


    /**
     * Get the all pages as an array with the following information:
     * [
     *   [ 'page' => 1, 'text' => 1, 'title' => 1 ]
     * ]
     *
     * @return array
     */
    public function getPages(): array
    {
        $pages = [];

        $currentPage = $this->page();
        $maxPage = $this->totalPageCount();
        $maxPageNumber = $this->perPage();

        $checkSum   = 1 + (($maxPageNumber - 1) * 2) + 2;
        $countIndex = 0;

        if ($checkSum >= $maxPage) {
            for ($i = 1; $i <= $maxPage; $i++) {
                $pages[$i - 1]['page']  = $i;
                $pages[$i - 1]['text']  = $i;
                $pages[$i - 1]['title'] = $i;
            }

            return $pages;
        }

        $pagesBeforeAndAfter = $maxPageNumber - 1;
        if ($currentPage - $pagesBeforeAndAfter > 1) {
            $pages[$countIndex]['page']  = 1;
            $pages[$countIndex]['text']  = 1;
            $pages[$countIndex]['title'] = 1;
        }

        if ($currentPage - $pagesBeforeAndAfter > 2) {
            $countIndex                  = count($pages);
            $pages[$countIndex]['page']  = $currentPage - $pagesBeforeAndAfter - 1;
            $pages[$countIndex]['text']  = '...';
            $pages[$countIndex]['title'] = $maxPageNumber;
        }

        for ($i = 0; $i < $pagesBeforeAndAfter * 2 + 1; $i++) {
            if ($currentPage - $pagesBeforeAndAfter + $i < $maxPage &&
                $currentPage - $pagesBeforeAndAfter + $i > 0
            ) {
                $countIndex                  = count($pages);
                $pages[$countIndex]['page']  = $currentPage - $pagesBeforeAndAfter + $i;
                $pages[$countIndex]['text']  = $currentPage - $pagesBeforeAndAfter + $i;
                $pages[$countIndex]['title'] = $currentPage - $pagesBeforeAndAfter + $i;
            }
        }

        if ($pages[count($pages) - 1]['page'] === $maxPage - 2) {
            $countIndex                  = count($pages);
            $pages[$countIndex]['page']  = $maxPage - 1;
            $pages[$countIndex]['text']  = $maxPage - 1;
            $pages[$countIndex]['title'] = $maxPage - 1;
        } elseif ($pages[count($pages) - 1]['page'] < $maxPage - 1) {
            $countIndex                  = count($pages);
            $pages[$countIndex]['page']  = $currentPage + $pagesBeforeAndAfter + 1;
            $pages[$countIndex]['text']  = '...';
            $pages[$countIndex]['title'] = $maxPageNumber;
        }

        $countIndex                  = count($pages);
        $pages[$countIndex]['page']  = $maxPage;
        $pages[$countIndex]['text']  = $maxPage;
        $pages[$countIndex]['title'] = $maxPage;

        return $pages;
    }

}
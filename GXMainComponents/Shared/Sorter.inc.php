<?php
/* --------------------------------------------------------------
   Sorter.inc.php 2019-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Sorter
 */
class Sorter
{
    
    /**
     * @var int
     */
    protected $direction;
    
    /**
     * @var array
     */
    protected $dimensions;
    
    /**
     * @var string
     */
    protected $alias;
    
    /**
     * @var array
     */
    protected $validSortSignals = ['+', '-', ' '];
    
    
    /**
     * Sorter constructor.
     *
     * @param StringType $sortInstruction URL query parameter with sort information
     */
    public function __construct(StringType $sortInstruction)
    {
        
        if (empty(trim($sortInstruction->asString()))) {
            throw new InvalidArgumentException("Malformed sort instruction {$sortInstruction->asString()}");
        }
        
        $sortOrderSignal = substr($sortInstruction->asString(), 0, 1);
        
        if (!in_array($sortOrderSignal, $this->validSortSignals, true)) {
            $sortOrderSignal = '+';
            $this->alias     = $sortInstruction->asString();
        } else {
            $this->alias = substr($sortInstruction->asString(), 1);
        }
        
        $this->direction  = ($sortOrderSignal === '-') ? SORT_DESC : SORT_ASC;
        $this->dimensions = explode('.', $this->alias);
    }
    
    
    /**
     * Initialize an array of Sorter based on a query string.
     *
     * @param StringType $sortInstructions URL query parameter with sort information
     *
     * @return array
     */
    
    public static function initializeArrayFromQuery(StringType $sortInstructions)
    {
        $resultArray = [];
        if (!empty(trim($sortInstructions->asString()))) {
            
            $params = explode(',', $sortInstructions->asString());
            foreach ($params as $paramIndex => &$param) {
                $resultArray[] = new Sorter(new StringType($param));
            }
        }
        
        return $resultArray;
    }
    
    
    /**
     * @return int
     */
    public function direction()
    {
        return $this->direction;
    }
    
    
    /**
     * @return array
     */
    public function dimensions()
    {
        return $this->dimensions;
    }
    
    
    /**
     * @return string
     */
    public function alias()
    {
        return $this->alias;
    }
    
    
}
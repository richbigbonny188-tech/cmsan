<?php

/* --------------------------------------------------------------
   QuickEditDocuments.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditDocuments
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
class QuickEditDocuments implements QuickEditDocumentsInterface
{
    /**
     * @var string
     */
    const PDF_PAGE_ORIENTATION = 'P';
    
    /**
     * @var string
     */
    const PDF_UNIT = 'mm';
    
    /**
     * @var string
     */
    const PDF_PAGE_FORMAT = 'A4';
    
    /**
     * @var string
     */
    const PDF_HEADER_LOGO = '';
    
    /**
     * @var int
     */
    const PDF_HEADER_LOGO_WIDTH = 0;
    
    /**
     * @var string
     */
    const PDF_HEADER_TITLE = 'INVENTORY_CHECKLIST';
    
    /**
     * @var string
     */
    const PDF_HEADER_STRING = '';
    
    /**
     * @var int
     */
    const PDF_FONT_SIZE_MAIN = 8;
    
    /**
     * @var string
     */
    const PDF_FILENAME = 'InventoryChecklist.pdf';
    
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $title = 'INVENTORY_CHECKLIST';
    
    /**
     * @var string
     */
    protected $creator = 'fpdf.org/gambio.de';
    
    /**
     * @var array
     */
    protected $products = [];
    
    /**
     * @var array
     */
    protected $categories = [];
    
    /**
     * @var LanguageTextManager
     */
    protected $languageTextManager;
    
    /**
     * @var QuickEditOverviewColumns
     */
    protected $quickEditOverviewColumns;
    
    
    /**
     * QuickEditDocuments constructor.
     */
    public function __construct()
    {
        $this->db                       = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $this->quickEditOverviewColumns = MainFactory::create('QuickEditOverviewColumns');
    }
    
    
    /**
     * Creates a list of products that have been passed as parameters.
     *
     * @param array $products Returns the list of products that have been passed as parameters.
     *
     * @return bool Returns true if the create and save was successful - otherwise, false is returned.
     */
    public function getProductsById(array $products)
    {
        $result = $this->db->select([
                                        'products.products_id',
                                        'products_description.products_name',
                                        'products.products_model',
                                        'products.products_price',
                                        'products.products_weight',
                                        'products.products_quantity',
                                    ])
            ->join('languages', 'languages.languages_id = ' . $_SESSION['languages_id'])
            ->join('products_description',
                   'products_description.products_id = products.products_id')
            ->where('products_description.language_id = languages.languages_id')
            ->where_in('products.products_id',
                       $products)
            ->get('products')
            ->result_array();
        
        foreach ($result as $value) {
            $this->products[] = MainFactory::create('QuickEditProductListItem', $value);
        }
        
        return $this->_createPdfInventoryDocument();
    }
    
    
    /**
     * Creates a list of all products needed to create the inventory list. The list is then generated.
     *
     * @return bool Returns true if the create and save was successful - otherwise, false is returned.
     */
    public function getProducts()
    {
        $this->db->select([
                              'products.products_id',
                              'products_description.products_name',
                              'products.products_model',
                              'products.products_price',
                              'products.products_weight',
                              'products.products_quantity',
                          ]);
        $this->db->join('languages', 'languages.languages_id = ' . $_SESSION['languages_id']);
        $this->db->join('products_description', 'products_description.language_id = languages.languages_id');
        $this->db->where('products_description.products_id = products.products_id');
        
        $result = $this->db->get('products')->result_array();
        
        foreach ($result as $value) {
            $this->products[] = MainFactory::create('QuickEditProductListItem', $value);
        }
        
        return $this->_createPdfInventoryDocument();
    }
    
    
    /**
     * Returns the link to the last created inventory list.
     *
     * @return array Returns the link to the last created inventory list.
     */
    public function getLink()
    {
        if (file_exists(DIR_FS_CATALOG . '/export/inventory/' . self::PDF_FILENAME)) {
            $link = '/export/inventory/' . self::PDF_FILENAME;
            
            return ['success' => true, 'link' => $link];
        }
        
        return ['success' => false];
    }
    
    
    /**
     * Creates a PDF file and saves it.
     *
     * @return bool Returns true if the create and save was successful - otherwise, false is returned.
     */
    protected function _createPdfInventoryDocument()
    {
        try {
            $this->languageTextManager = MainFactory::create('LanguageTextManager',
                                                             'admin_quick_edit',
                                                             $_SESSION['languages_id']);
            
            if (file_exists(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php')) {
                require_once(DIR_FS_CATALOG . 'PdfCreator/tcpdf.php');
            }
            
            $pdf = new TCPDF(self::PDF_PAGE_ORIENTATION, self::PDF_UNIT, self::PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->setHeaderData(self::PDF_HEADER_LOGO,
                                self::PDF_HEADER_LOGO_WIDTH,
                                strtoupper($this->languageTextManager->get_text(self::PDF_HEADER_TITLE)),
                                lcfirst($this->languageTextManager->get_text('CREATED_AT')) . ' ' . date('d.m.Y'));
            $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', self::PDF_FONT_SIZE_MAIN]);
            $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
            $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->setFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
            $pdf->SetFont('', '', 8);
            $pdf->SetFillColor(195, 195, 195);
            $pdf->AddPage();
            
            // Column widths
            $w = [10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60];
            
            $columns = $this->_getColumns();
            
            // Data
            foreach ($this->products as $product) {
                if ($pdf->GetY() == 27 || $pdf->GetY() == 272) {
                    // Header
                    $pdf->Cell($w[0], 10, $columns['id'], 'LTB', 0, 'C', true);
                    $pdf->Cell($w[10], 10, $columns['name'], 'TB', 0, 'C', true);
                    $pdf->Cell($w[4], 10, $columns['model'], 'TB', 0, 'C', true);
                    $pdf->Cell($w[3], 10, $columns['price'] . ' (Netto)', 'TB', 0, 'C', true);
                    $pdf->Cell($w[1], 10, $columns['weight'], 'TB', 0, 'C', true);
                    $pdf->Cell($w[6], 5, $columns['quantity'], 'LTR', 2, 'C', true);
                    $pdf->Cell($w[2], 5, strtoupper($this->languageTextManager->get_text('IS')), 'LTB', 0, 'C', true);
                    $pdf->Cell($w[2],
                               5,
                               strtoupper($this->languageTextManager->get_text('SHOULD')),
                               'LTRB',
                               0,
                               'C',
                               true);
                    $pdf->Ln();
                }
                
                /** @var QuickEditProductListItem $product */
                $pdf->Cell($w[0], 5, $product->getId(), 'LTB', 0, 'C', false, '', 1);
                $pdf->Cell($w[10], 5, $product->getName(), 'LTB', 0, 'L', false, '', 1);
                $pdf->Cell($w[4], 5, $product->getModel(), 'LTB', 0, 'L', false, '', 1);
                $pdf->Cell($w[3], 5, $product->getPrice(), 'LTB', 0, 'R', false, '', 1);
                $pdf->Cell($w[1], 5, $product->getWeight(), 'LTB', 0, 'R', false, '', 1);
                $pdf->Cell($w[2], 5, $product->getQuantity(), 'LTRB', 0, 'R', false, '', 1);
                $pdf->Cell($w[2], 5, ' ', 'TRB', 0);
                $pdf->Ln();
            }
            
            $pdf->Ln();
            
            $pdf->lastPage();
            
            $pdf->SetCreator($this->creator);
            $pdf->SetTitle(strtoupper($this->languageTextManager->get_text($this->title)));
            $pdf->SetSubject('');
            $pdf->SetKeywords('');
            $pdf->Output(DIR_FS_CATALOG . '/export/inventory/' . self::PDF_FILENAME, 'F');
            
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    
    /**
     * Returns the required column names and their names.
     *
     * @return array Returns the required column names and their names.
     */
    protected function _getColumns()
    {
        $columns = [];
        
        foreach ($this->quickEditOverviewColumns->getColumns() as $dataTableColumn) {
            $columns[$dataTableColumn->getName()] = $this->languageTextManager->get_text(strtoupper($dataTableColumn->getName()));
        }
        
        return $columns;
    }
}
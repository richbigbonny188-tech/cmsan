<?php
/* --------------------------------------------------------------
   InvoiceActions.inc.php 2016-10-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceActions
 *
 * @category   System
 * @package    Extensions
 * @subpackage Invoices
 */
class InvoiceActions
{
    /**
     * Outputs the concatenated invoice PDFs.
     *
     * @param array $invoiceIds The invoice IDs to be included in the concatenated file.
     *
     * @throws \Mpdf\MpdfException
     */
    public function bulkPdfInvoices(array $invoiceIds)
    {
        $this->_createBulkPdf($invoiceIds);
    }
    
    
    /**
     * Outputs a bulk PDF file through the use of mPDF.
     *
     * This method will parse the selected PDFs of the provided invoices and will concatenate them into
     * a single file. This file will be then outputted directly so that browsers download it immediately.
     *
     * @param array $invoiceIds The selected invoice IDs.
     *
     * @throws \Mpdf\MpdfException
     * @see mPDF
     */
    protected function _createBulkPdf(array $invoiceIds)
    {
        require_once DIR_FS_ADMIN . 'includes/functions/mpdf_csprng_polyfill.inc.php';
        $mPDF = new \Mpdf\Mpdf();
        
        $countFiles = 0;
        $basePath   = DIR_FS_CATALOG . 'export/invoice/';
        
        /** @var InvoiceArchiveReadService $invoiceArchiveReadService */
        $invoiceArchiveReadService = StaticGXCoreLoader::getService('InvoiceArchiveRead');
        
        foreach ($invoiceIds as $index => $invoiceId) {
            $invoiceListItem = $invoiceArchiveReadService->getInvoiceListItemById(new IdType($invoiceId));
            
            $filePath  = $basePath . $invoiceListItem->getInvoiceFilename();
            $pageCount = $mPDF->SetSourceFile($filePath);
            $countFiles++;
            
            for ($i = 1; $i <= $pageCount; $i++) {
                $currentPageNumber = $mPDF->importPage($i);
                $mPDF->UseTemplate($currentPageNumber);
                
                $onLastPage = ($index + 1) === count($invoiceIds);
                
                if (($onLastPage && $i < $pageCount) || (!$onLastPage && $i <= $pageCount)) {
                    $mPDF->AddPage();
                }
            }
        }
        
        // Set PDF permissions depending the database settings. 
        $permissions = $this->_getPdfPermissions();
        $mPDF->setProtection($permissions);
        
        // Output the PDF file for browser download.
        if ($countFiles > 1) {
            $mPDF->Output('Invoices-' . date('Y_m_d') . '.pdf', 'D');
        } else {
            $invoices = $invoiceArchiveReadService->getInvoiceListByConditions(['invoice_id' => reset($invoiceIds)],
                                                                               null,
                                                                               null,
                                                                               new StringType('invoice_date DESC'));
            /** @var InvoiceListItem $invoice */
            $invoice = $invoices->getItem(0);
            
            $mPDF->Output('Invoice-' . $invoice->getInvoiceNumber() . '-' . $invoice->getInvoiceDate()->format('d_m_Y')
                          . '.pdf',
                          'D');
        }
    }
    
    
    /**
     * Generate the PDF permissions depending the shop configuration.
     *
     * @link https://mpdf.github.io/reference/mpdf-functions/setprotection.html
     *
     * @return array
     */
    protected function _getPdfPermissions()
    {
        $permissions = [
            'print',
            'fill-forms',
            'extract',
            'assemble',
            'print-highres'
        ];
        
        if (filter_var(gm_get_conf('GM_PDF_ALLOW_COPYING'), FILTER_VALIDATE_BOOLEAN)) {
            $permissions[] = 'copy';
        }
        
        if (filter_var(gm_get_conf('GM_PDF_ALLOW_NOTIFYING'), FILTER_VALIDATE_BOOLEAN)) {
            $permissions[] = 'annot-forms';
        }
        
        if (filter_var(gm_get_conf('GM_PDF_ALLOW_MODIFYING'), FILTER_VALIDATE_BOOLEAN)) {
            $permissions[] = 'modify';
        }
        
        return $permissions;
    }
}

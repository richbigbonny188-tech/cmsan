<?php
/* --------------------------------------------------------------
   InvoicesModalsAjaxController.inc.php 2022-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoicesModalsAjaxController
 *
 * AJAX controller for the invoices modals.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class InvoicesModalsAjaxController extends AdminHttpViewController
{
    /**
     * @var InvoiceArchiveReadService
     */
    protected $invoiceArchiveReadService;
    
    /**
     * @var InvoiceArchiveWriteService
     */
    protected $invoiceArchiveWriteService;
    
    /**
     * @var OrderReadService
     */
    protected $orderReadService;
    
    
    /**
     * Initialize Controller
     *
     * @throws Exception
     */
    public function init()
    {
        $this->_validatePageToken();
        
        $this->invoiceArchiveReadService  = StaticGXCoreLoader::getService('InvoiceArchiveRead');
        $this->invoiceArchiveWriteService = StaticGXCoreLoader::getService('InvoiceArchiveWrite');
        $this->orderReadService           = StaticGXCoreLoader::getService('OrderRead');
    }
    
    
    /**
     * Delete Invoices Callback
     *
     * This method expects a "selectedInvoices" POST value which must contain the IDs of the invoices to be deleted.
     *
     * @return JsonHttpControllerResponse
     *
     * @throws InvalidArgumentException
     */
    public function actionDeleteInvoice()
    {
        $invoiceIds  = $this->_getPostData('selectedInvoices') ? : [];
        $orderFormat = MainFactory::create('GMOrderFormat');
        
        foreach ($invoiceIds as $invoiceId) {
            $invoiceListItem = $this->invoiceArchiveReadService->getInvoiceListItemById(new IdType($invoiceId));
            $orderFormat->delete_id('invoice', $invoiceListItem->getInvoiceNumber());
            $this->invoiceArchiveWriteService->deleteInvoiceById(new IdType($invoiceId));
        }
        
        return MainFactory::create('JsonHttpControllerResponse', []);
    }
    
    
    /**
     * Download Bulk Invoices PDF.
     *
     * This method will provide a concatenated file of invoice PDFs. Provide a GET parameter "i" that contain
     * the selected invoice IDs.
     *
     * Notice: The "i" is used instead of "invoiceIds" because the final URL must be as small as possible (some
     * browsers do not work with GET URL of 100 invoices).
     *
     * @see InvoiceActions
     */
    public function actionBulkPdfInvoices()
    {
        $invoiceActions = MainFactory::create('InvoiceActions');
        $invoiceIds     = $this->_getQueryParameter('i');
        $invoiceActions->bulkPdfInvoices($invoiceIds);
    }
    
    
    /**
     * Get Email Invoice Information
     *
     * This method will provide the required email-invoice information to the modal JS controller. The subject
     * will have to be parsed in frontend with JavaScript.
     *
     * Provide a GET "o" parameter with the selected order numbers.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionGetEmailInvoiceInformation()
    {
        $response = [
            'emails' => [],
        ];
    
        $orderIds = $this->_getQueryParameter('o') ? : [];
        $orderIds = array_map('intval', $orderIds);
    
        $dateFormats = [];
        $languageIds = [];
        if (!empty($orderIds)) {
            $result = xtc_db_query('SELECT
                                            o.orders_id,
                                            l.languages_id,
                                            l.date_format,
                                            o.language
                                        FROM
                                            languages l,
                                            orders o
                                        WHERE
                                            o.orders_id IN (' . implode(',', $orderIds) . ') AND
                                            l.directory = o.language');
            while ($row = xtc_db_fetch_array($result)) {
                $dateFormats[$row['orders_id']]    = $row['date_format'];
                $languageIds[$row['languages_id']] = $row['languages_id'];
            }
        }
    
        foreach ($orderIds as $orderId) {
            $orders = $this->orderReadService->filterOrderList(['number' => $orderId]);
            $email  = $orders->count() ? $orders->getItem(0)->getCustomerEmail() : '';
        
            if ($email) {
                $response['emails'][$orderId] = $email;
                $response['date_formats'][$orderId] = str_replace(['d', 'm', 'Y'],
                                                                  ['DD', 'MM', 'YYYY'],
                                                                  $dateFormats[$orderId]);
            }
        }
    
        $languageId          = count($languageIds) === 1 ? (int)current($languageIds) : (int)($_SESSION['languages_id'] ?? null);
        $response['subject'] = gm_get_content('GM_PDF_EMAIL_SUBJECT', $languageId);
    
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
}
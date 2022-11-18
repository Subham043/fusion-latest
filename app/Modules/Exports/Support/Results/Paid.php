<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Support\Results;

use FI\Modules\Invoices\Models\Invoice;
use FI\Support\Statuses\InvoiceStatuses;

class Paid implements SourceInterface
{
    public function getResults($params = [])
    {		
	$invoice = Invoice::select('invoices.number AS Invoice_Number','clients.name AS Client_Name', 'invoices.event_date AS Event_Date', 'invoices_custom.column_12 AS Event_Name', 'invoices_custom.column_2 AS Location', 'invoice_amounts.total AS Total', 'invoice_amounts.paid AS Paid', 'invoice_amounts.balance AS Balance')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
	    ->whereIn('invoices.invoice_status_id', [InvoiceStatuses::getStatusId('paid_full'), InvoiceStatuses::getStatusId('paid_partial')])
	    ->orderBy('invoices.id','DESC');

        return $invoice->get()->toArray();
    }
}
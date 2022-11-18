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
use Carbon\Carbon;
use Illuminate\Database\Query\Expression;


class Aging implements SourceInterface
{
    public function getResults($params = [])
    {		
	$invoice = Invoice::select('invoices.number AS Invoice_Number','clients.name AS Client_Name', 'clients.email AS Client_Email', 'invoices.invoice_date AS Invoice_Date', new Expression('DATEDIFF(NOW(), invoices.due_at)  AS Aging'), 'invoice_amounts.balance AS Balance')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
	    ->whereDate('invoices.due_at', '<', Carbon::now())
	    ->whereIn('invoices.invoice_status_id', [InvoiceStatuses::getStatusId('paid_partial'),InvoiceStatuses::getStatusId('overdue')])
	    ->orderBy('invoices.id','DESC');

        return $invoice->get()->toArray();
    }
}
<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Reports\Reports;

use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\Invoices\Models\Invoice;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;
use FI\Support\Statuses\InvoiceStatuses;

class SalesReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null)
    {
        $results = [
            'from_date' => DateFormatter::format($fromDate),
            'to_date'   => DateFormatter::format($toDate),
            'records'   => [],
        ];

	$invoices = Invoice::select('invoices.*')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
            ->with(['client', 'activities', 'amount.invoice.currency'])
	    ->where('invoices.invoice_status_id', '<>', InvoiceStatuses::getStatusId('canceled'))
	    ->orderBy('invoices.id','DESC');


        if ($companyProfileId)
        {
            $invoices->where('invoices.company_profile_id', $companyProfileId);
        }

        $items = $invoices->get();

        foreach ($items as $item)
        {
            $results['records'][] = [
                'client_name'    => $item->client->unique_name,
                'invoice_number' => $item->number,
                'date'           => $item->getFormattedInvoiceEventDateAttribute(),
		'event_name'           => !empty($item->custom) ? $item->custom->column_12 : '',
		'location'           => !empty($item->custom) ? $item->custom->column_2 : '',
                'subtotal'       => $item->amount->formatted_subtotal,
                'tax'            => $item->amount->formatted_tax,
                'total'          => $item->amount->formatted_total,
		'paid'          => $item->amount->formatted_paid,
		'balance'          => $item->amount->formatted_balance,
            ];

        }

        return $results;
    }
}
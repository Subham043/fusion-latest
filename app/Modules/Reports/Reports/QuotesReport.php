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

use FI\Modules\Quotes\Models\QuoteItem;
use FI\Modules\Quotes\Models\Quote;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;
use FI\Support\Statuses\QuoteStatuses;

class QuotesReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null)
    {
        $results = [
            'from_date' => DateFormatter::format($fromDate),
            'to_date'   => DateFormatter::format($toDate),
            'records'   => [],
        ];

	$invoices = Quote::select('quotes.*')
            ->join('clients', 'clients.id', '=', 'quotes.client_id')
            ->join('quote_amounts', 'quote_amounts.quote_id', '=', 'quotes.id')
            ->join('quotes_custom', 'quotes_custom.quote_id', '=', 'quotes.id')
            ->with(['client', 'activities', 'amount.quote.currency'])
	    ->whereNotIn('quotes.quote_status_id', [QuoteStatuses::getStatusId('rejected'),QuoteStatuses::getStatusId('canceled')])
	    ->orderBy('quotes.id','DESC');


        if ($companyProfileId)
        {
            $invoices->where('quotes.company_profile_id', $companyProfileId);
        }

        $items = $invoices->get();

        foreach ($items as $item)
        {
            $results['records'][] = [
                'client_name'    => $item->client->unique_name,
                'quote_number' => $item->number,
                'date'           => $item->formatted_event_date,
		'event_name'           => !empty($item->custom) ? $item->custom->column_8 : '',
		'location'           => !empty($item->custom) ? $item->custom->column_1 : '',
                'subtotal'       => $item->amount->formatted_subtotal,
                'tax'            => $item->amount->formatted_tax,
                'total'          => $item->amount->formatted_total,            ];

        }

        return $results;
    }
}
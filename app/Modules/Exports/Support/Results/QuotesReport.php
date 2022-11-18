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

use FI\Modules\Quotes\Models\Quote;
use FI\Support\Statuses\QuoteStatuses;

class QuotesReport implements SourceInterface
{
    public function getResults($params = [])
    {		
	$invoice = Quote::select('quotes.number AS Quote_Number','clients.name AS Client_Name', 'quotes.event_date AS Event_Date', 'quotes_custom.column_8 AS Event_Name', 'quotes_custom.column_1 AS Location', 'quote_amounts.subtotal AS Net_Amount', 'quote_amounts.tax AS Tax', 'quote_amounts.total AS Total')
            ->join('clients', 'clients.id', '=', 'quotes.client_id')
            ->join('quote_amounts', 'quote_amounts.quote_id', '=', 'quotes.id')
            ->join('quotes_custom', 'quotes_custom.quote_id', '=', 'quotes.id')
	    ->whereNotIn('quotes.quote_status_id', [QuoteStatuses::getStatusId('rejected'),QuoteStatuses::getStatusId('canceled')])
	    ->orderBy('quotes.id','DESC');

        return $invoice->get()->toArray();
    }
}
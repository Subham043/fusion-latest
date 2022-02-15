<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Quotes\Support;

use FI\Events\InvoiceModified;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\Groups\Models\Group;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Support\Statuses\InvoiceStatuses;
use FI\Support\Statuses\QuoteStatuses;
use Addons\Scheduler\Models\Schedule;

class QuoteToInvoice
{
    public function convert($quote, $invoiceDate, $dueAt, $groupId)
    {
        $record = [
            'client_id'          => $quote->client_id,
            'invoice_date'       => $invoiceDate,
            'event_date'       => $quote->event_date,
            'due_at'             => $dueAt,
            'group_id'           => $groupId,
            // 'number'             => Group::generateNumber($groupId),
            'number'             => str_replace("QUO", "INV", $quote->number),
            'user_id'            => $quote->user_id,
            'invoice_status_id'  => InvoiceStatuses::getStatusId('draft'),
            'terms'              => ((config('fi.convertQuoteTerms') == 'quote') ? $quote->terms : config('fi.invoiceTerms')),
            'footer'             => $quote->footer,
            'currency_code'      => $quote->currency_code,
            'exchange_rate'      => $quote->exchange_rate,
            'summary'            => $quote->summary,
            'discount'           => $quote->discount,
            'company_profile_id' => $quote->company_profile_id,
        ];

        $toInvoice = Invoice::create($record);

        CustomField::copyCustomFieldValues($quote, $toInvoice);

        $quote->invoice_id      = $toInvoice->id;
        $quote->quote_status_id = QuoteStatuses::getStatusId('approved');
        $quote->save();
        
        $event =  Schedule::select('*')
            ->where('quotes_id', $quote->id)
            ->first();
        $event->url   = route('invoices.edit', [$toInvoice->id]);
		$event->category_id = 5;
		$event->invoices_id = $toInvoice->id;
		$event->save();
        
        

        foreach ($quote->quoteItems as $item)
        {
            $itemRecord = [
                'invoice_id'    => $toInvoice->id,
                'name'          => $item->name,
                'description'   => $item->description,
                'quantity'      => $item->quantity,
                'price'         => $item->price,
                'tax_rate_id'   => $item->tax_rate_id,
                'tax_rate_2_id' => $item->tax_rate_2_id,
                'display_order' => $item->display_order,
                'inventory_id' => $item->inventory_id,
            ];

            InvoiceItem::create($itemRecord);
        }

        event(new InvoiceModified($toInvoice));

        return $toInvoice;
    }
}
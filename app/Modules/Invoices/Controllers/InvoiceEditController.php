<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Currencies\Models\Currency;
use FI\Modules\Inventory\Models\Inventory;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Invoices\Models\InvoiceItem;
use FI\Modules\Invoices\Support\InvoiceTemplates;
use FI\Modules\Invoices\Requests\InvoiceUpdateRequest;
use FI\Modules\ItemLookups\Models\ItemLookup;
use FI\Modules\TaxRates\Models\TaxRate;
use FI\Support\DateFormatter;
use FI\Support\Statuses\InvoiceStatuses;
use FI\Traits\ReturnUrl;
use Addons\Scheduler\Models\Schedule;
use FI\Modules\Quotes\Models\Quote;
use Addons\Scheduler\Models\ScheduleOccurrence;

class InvoiceEditController extends Controller
{
    use ReturnUrl;

    public function edit($id)
    {
        $inventoryList = Inventory::getList();
        $inventoryList["null"] = "Select a product";
        $invoice = Invoice::with(['items.amount.item.invoice.currency'])->find($id);
//echo "<pre>"; print_r($invoice);exit;
        return view('invoices.edit')
            ->with('invoice', $invoice)
            ->with('statuses', InvoiceStatuses::lists())
            ->with('currencies', Currency::getList())
            ->with('inventory', $inventoryList)
            ->with('taxRates', TaxRate::getList())
            ->with('customFields', CustomField::forTable('invoices')->get())
            ->with('returnUrl', $this->getReturnUrl())
            ->with('templates', InvoiceTemplates::lists())
            ->with('itemCount', count($invoice->invoiceItems));
    }

    public function update(InvoiceUpdateRequest $request, $id)
    {
        
        // Unformat the invoice dates.
        $invoiceInput                 = $request->except(['event_date', 'items', 'custom', 'apply_exchange_rate']);
        $invoiceInput['invoice_date'] = DateFormatter::unformat($invoiceInput['invoice_date']);
        $invoiceInput['due_at']       = DateFormatter::unformat($invoiceInput['due_at']);

        // Save the invoice.
        $invoice = Invoice::find($id);
        $invoice->fill($invoiceInput);
        $invoice->invoice_status_id = $invoiceInput['invoice_status_id'];
        $invoice->event_date = DateFormatter::unformat($request->input('event_date'));
        
        if($invoice->quote()->count()){
            //check event date 
            $quote = Quote::select('*')
                ->where('invoice_id', $invoice->id)
                ->first();
            $exist_quote = Quote::where('client_id',$quote->client_id)->where('event_date',DateFormatter::unformat($request->input('event_date')))->where('id', '<>', $quote->id)->get();
            if(count($exist_quote)>0){
                 return response()->json(['message' => 'Another event exists on the same date by the same client'], 422);
            }
            
            $invoice->save();
            
             // save the event color
            $quote->event_date = DateFormatter::unformat($request->input('event_date'));
            $quote->save();
            
            $event =  Schedule::select('*')
                ->where('quotes_id', $quote->id)
                ->first();
    		$event->category_id = number_format($invoiceInput['invoice_status_id'])+4;
    		$event->save();
    		
    		// save the event date
    		$occurrence = ScheduleOccurrence::select('*')
                ->where('schedule_id', $event->id)
                ->first();
    		$occurrence->start_date = DateFormatter::unformat($request->input('event_date'));
    		$occurrence->end_date   = DateFormatter::unformat($request->input('event_date'));
    		$occurrence->save();
        }else{
            
            $exist_quote = Invoice::where('client_id',$invoice->client_id)->where('event_date',DateFormatter::unformat($request->input('event_date')))->where('id', '<>', $invoice->id)->get();
            if(count($exist_quote)>0){
                 return response()->json(['message' => 'Another event exists on the same date by the same client'], 422);
            }
            
            $invoice->save();
            
            $event =  Schedule::select('*')
                ->where('invoices_id', $invoice->id)
                ->first();
    		$event->category_id = number_format($invoiceInput['invoice_status_id'])+4;
    		$event->save();
    		
    		// save the event date
    		$occurrence = ScheduleOccurrence::select('*')
                ->where('schedule_id', $event->id)
                ->first();
    		$occurrence->start_date = DateFormatter::unformat($request->input('event_date'));
    		$occurrence->end_date   = DateFormatter::unformat($request->input('event_date'));
    		$occurrence->save();
        }
        

        // Save the custom fields.
        $invoice->custom->update(request('custom', []));
        
        // Save the items.
        if($request->input('items')==null){
            return response()->json(['message' => 'Please add an item to the invoice'], 422);
        }
        foreach ($request->input('items') as $item)
        {
            $item['apply_exchange_rate'] = request('apply_exchange_rate');
            
            $availableQuan = $item['availableQuan'];
            unset($item['availableQuan']);
            
            $inventory = Inventory::find($item['inventory_id']);

            if (!isset($item['id']) or (!$item['id']))
            {
                if((int)$item['quantity']>(int)$availableQuan){
                    return response()->json(['message' => $item['name']."'s quantity is more than the available quantity"], 400);
                }
                
                $saveItemAsLookup = $item['save_item_as_lookup'];
                unset($item['save_item_as_lookup']);

                InvoiceItem::create($item);

                if ($saveItemAsLookup)
                {
                    ItemLookup::create([
                        'name'          => $item['name'],
                        'description'   => $item['description'],
                        'price'         => $item['price'],
                        'tax_rate_id'   => $item['tax_rate_id'],
                        'tax_rate_2_id' => $item['tax_rate_2_id'],
                        'inventory_id' => $item['inventory_id'],
                    ]);
                }
            }
            else
            {
                if((int)$item['quantity']>(int)$availableQuan){
                    return response()->json(['message' => $item['name']."'s quantity is more than the available quantity"], 400);
                }
                
                $invoiceItem = InvoiceItem::find($item['id']);
                
                $invoiceItem->fill($item);
                $invoiceItem->save();
            }
        }
    }

    public function refreshEdit($id)
    {
	$inventoryList = Inventory::getList();
        $inventoryList["null"] = "Select a product";
        $invoice = Invoice::with(['items.amount.item.invoice.currency'])->find($id);

        return view('invoices._edit')
            ->with('invoice', $invoice)
            ->with('statuses', InvoiceStatuses::lists())
            ->with('currencies', Currency::getList())
            ->with('inventory', $inventoryList)
            ->with('taxRates', TaxRate::getList())
            ->with('customFields', CustomField::forTable('invoices')->get())
            ->with('returnUrl', $this->getReturnUrl())
            ->with('templates', InvoiceTemplates::lists())
            ->with('itemCount', count($invoice->invoiceItems));
            
    }

    public function refreshTotals()
    {
        return view('invoices._edit_totals')
            ->with('invoice', Invoice::with(['items.amount.item.invoice.currency'])->find(request('id')));
    }

    public function refreshTo()
    {
        return view('invoices._edit_to')
            ->with('invoice', Invoice::find(request('id')));
    }

    public function refreshFrom()
    {
        return view('invoices._edit_from')
            ->with('invoice', Invoice::find(request('id')));
    }

    public function updateClient()
    {
        Invoice::where('id', request('id'))->update(['client_id' => request('client_id')]);
    }

    public function updateCompanyProfile()
    {
        Invoice::where('id', request('id'))->update(['company_profile_id' => request('company_profile_id')]);
    }
}

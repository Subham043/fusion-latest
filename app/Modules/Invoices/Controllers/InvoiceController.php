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
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Invoices\Models\Invoice;
use FI\Support\FileNames;
use FI\Support\PDF\PDFFactory;
use FI\Support\Statuses\InvoiceStatuses;
use FI\Traits\ReturnUrl;
use Addons\Scheduler\Models\Schedule;
use FI\Modules\CustomFields\Models\CustomField;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Auth;

class InvoiceController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $status = request('status', 'all_statuses');

        $invoices = Invoice::select('invoices.*')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
            ->with(['client', 'activities', 'amount.invoice.currency'])
            ->status($status)
            ->keywords(request('search'))
            ->clientId(request('client'))
            ->companyProfileId(request('company_profile'))
            ->sortable(['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
            ->paginate(config('fi.resultsPerPage'));

        return view('invoices.index')
            ->with('invoices', $invoices)
            ->with('status', $status)
            ->with('statuses', InvoiceStatuses::listsAllFlat() + ['overdue' => trans('fi.overdue')])
            ->with('keyedStatuses', collect(InvoiceStatuses::lists()))
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList())
            ->with('displaySearch', true);
    }

    public function delete($id)
    {
	$invoice = Invoice::find($id);
        
       	if($invoice->quote()->count()){
            
            $event =  Schedule::select('*')
                ->where('quotes_id', $invoice->quote->id)
                ->first();
            if(!empty($event)){
    		$event->url   = route('quotes.edit', [$invoice->quote->id]);
		                $event->category_id = $invoice->quote->quote_status_id;
		                $event->invoices_id = 0;
		                $event->save();
            }
        }else{
   
            $event =  Schedule::select('*')
                ->where('invoices_id', $invoice->id)
                ->first();
            if(!empty($event)){
    		$event->delete();
            }
        }

        Invoice::destroy($id);

        return redirect()->route('invoices.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
	foreach(request('ids') as $id){
            $invoice = Invoice::find($id);
            
        
            if($invoice->quote()->count()){
                
                $event =  Schedule::select('*')
                    ->where('quotes_id', $invoice->quote->id)
                    ->first();
                    if(!empty($event)){
        		        $event->url   = route('quotes.edit', [$invoice->quote->id]);
		                $event->category_id = $invoice->quote->quote_status_id;
		                $event->invoices_id = 0;
		                $event->save();
                    }
            }else{
       
                $event =  Schedule::select('*')
                    ->where('invoices_id', $invoice->id)
                    ->first();
                    if(!empty($event)){
        		$event->delete();
                    }
            }
            
        }
        Invoice::destroy(request('ids'));
    }

    public function bulkStatus()
    {
        Invoice::whereIn('id', request('ids'))
            ->where('invoice_status_id', '<>', InvoiceStatuses::getStatusId('paid'))
            ->update(['invoice_status_id' => request('status')]);
	foreach(request('ids') as $id){
            $invoice = Invoice::find($id);
            if($invoice->quote()->count()){
                $quote = Quote::select('*')
                    ->where('invoice_id', $id)
                    ->first();
                $event =  Schedule::select('*')
                    ->where('quotes_id', $quote->id)
                    ->first();
			if(!empty($event)){
        		$event->category_id = number_format(request('status'))+4;
        		$event->save();
			}
            }else{
                $event =  Schedule::select('*')
                    ->where('invoices_id', $id)
                    ->first();
			if(!empty($event)){
        		$event->category_id = number_format(request('status'))+4;
        		$event->save();
			}
            }
        }
    }

    public function pdf($id)
    {
        $invoice = Invoice::find($id);

        $pdf = PDFFactory::create();

        $pdf->download($invoice->html, FileNames::invoice($invoice));
    }
    
    public function itemChecklist($id)
    {
        $invoice = Invoice::find($id);
        
        $file = view('templates.checklist.invoiceChecklist')
            ->with('invoice', $invoice)
	   ->with('customFields', CustomField::forTable('invoices')->get())
            ->with('logo', $invoice->companyProfile->logo())->render();

        $pdf = PDFFactory::create();

        $pdf->download($file,'item-checklist.pdf');
    }
    
    public function print($id)
    {
        $invoice = Invoice::find($id);

        
        return view('invoices.print')
            ->with('invoice', $invoice);
    }

	public function itemChecklistPrint($id)
	{
		$invoice = Invoice::find($id);
//		return $invoice->custom->column_9;
		return view('invoices.itemChecklistPrint')
			->with('invoice', $invoice)
			->with('customFields', CustomField::forTable('invoices')->get())
			->with('logo', $invoice->companyProfile->logo())->render();
	}

	public function barcodePrinter(Request $req)
    {
        $this->setReturnUrl();

        $status = request('status', 'all_statuses');

        $invoices = Invoice::select('invoices.*')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
            ->with(['client', 'activities', 'amount.invoice.currency'])
	    ->orderBy('id','DESC')
            ->keywords(request('search'));
	    if($req->has('item')){
			if($req->input('item')=="all"){

				$invoices= $invoices->paginate(10000);
			}else{
			  $invoices= $invoices->paginate($req->input('item'));
			}
		}
		else{
            $invoices= $invoices->paginate(config('fi.resultsPerPage'));
		}

            

        return view('invoices.barcodePrinter')
	    ->with('displaySearch', true)
            ->with('invoices', $invoices);
    }

	public function barcodePrinterSingle($id)
	{
		$invoice = Invoice::findOrFail($id);
		return view('invoices.barcodePrinterSingle')
			->with('invoices', $invoice);
	}


    public function displayReminder()
    {
        $this->setReturnUrl();

        $status = request('status', 'all_statuses');

        $invoices = Invoice::select('invoices.*')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
            ->with(['client', 'activities', 'amount.invoice.currency'])
	    ->whereDate('invoices.due_at', '<', Carbon::now())
	    ->where('clients.type',1)
	    ->whereIn('invoices.invoice_status_id', array(InvoiceStatuses::getStatusId('paid_partial'), InvoiceStatuses::getStatusId('overdue')))
            ->sortable(['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
            ->paginate(config('fi.resultsPerPage'));

        return view('invoices.displayReminder')
            ->with('invoices', $invoices)
            ->with('displaySearch', true);
    }

	public function displayReminderCorporate()
    {
        $this->setReturnUrl();

        $status = request('status', 'all_statuses');

        $invoices = Invoice::select('invoices.*')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
            ->with(['client', 'activities', 'amount.invoice.currency'])
	    ->whereDate('invoices.due_at', '<', Carbon::now())
	    ->where('clients.type',2)
	    ->whereIn('invoices.invoice_status_id', array(InvoiceStatuses::getStatusId('paid_partial'), InvoiceStatuses::getStatusId('overdue')))
            ->sortable(['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
            ->paginate(config('fi.resultsPerPage'));


        return view('invoices.displayReminder')
            ->with('invoices', $invoices)
            ->with('displaySearch', true);
    }

    public function sendAllReminder(){

	$invoices = Invoice::select('invoices.*')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
            ->with(['client', 'activities', 'amount.invoice.currency'])
	    ->whereDate('invoices.due_at', '<', Carbon::now())
	    ->whereIn('invoices.invoice_status_id', array(InvoiceStatuses::getStatusId('paid_partial'), InvoiceStatuses::getStatusId('overdue')))
            ->sortable(['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
            ->get();

	foreach($invoices as $invoices){
		$msg = "<p>Hello ".$invoices->client->name."<br/> We would like to follow up on payment for the attached invoice.<br/> We are looking forward to being a part of your event!<br/> Hope you have a wonderful and blessed day!<br/> Sincerely,<br/> Your Team at Millennium <br/> Click the link below to view the invoice:</p> <p><a href='".$invoices->public_url."'>".$invoices->public_url."</a></p>";
		$body = ['body'=>$msg];
		Mail::send('templates.emails.html', $body, function($message) use($invoices) {
         		$message->to($invoices->client->email, $invoices->client->name)->subject('Payment Reminder Email: Invoice #'.$invoices->number );
         		$message->from('subham.s@jurysoft.com', Auth::user()->name);
        	});
	}
	return redirect()->route('invoices.displayReminder')
            ->with('alert', 'Reminder sent successfully');
    }

	public function sendMultipleReminder(){
	
	$data = request('data');
	if(empty($data)){
		return redirect()->route('invoices.displayReminder')
            		->with('alert', 'Please select atleast one invoice');
	}
	$data = explode (",", $data);

	$invoices = Invoice::select('invoices.*')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->join('invoices_custom', 'invoices_custom.invoice_id', '=', 'invoices.id')
            ->with(['client', 'activities', 'amount.invoice.currency'])
	    ->whereDate('invoices.due_at', '<', Carbon::now())
	    ->whereIn('invoices.id', $data)
	    ->whereIn('invoices.invoice_status_id', array(InvoiceStatuses::getStatusId('paid_partial'), InvoiceStatuses::getStatusId('overdue')))
            ->sortable(['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
            ->get();

	foreach($invoices as $invoices){
		$msg = "<p>Hello ".$invoices->client->name."<br/> We would like to follow up on payment for the attached invoice.<br/> We are looking forward to being a part of your event!<br/> Hope you have a wonderful and blessed day!<br/> Sincerely,<br/> Your Team at Millennium <br/> Click the link below to view the invoice:</p> <p><a href='".$invoices->public_url."'>".$invoices->public_url."</a></p>";
		$body = ['body'=>$msg];
		Mail::send('templates.emails.html', $body, function($message) use($invoices) {
         		$message->to($invoices->client->email, $invoices->client->name)->subject('Payment Reminder Email: Invoice #'.$invoices->number );
         		$message->from('subham.s@jurysoft.com', Auth::user()->name);
        	});
	}
	return redirect()->route('invoices.displayReminder')
            ->with('alert', 'Reminder sent successfully');
    }

    

}

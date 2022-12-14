@include('invoices._js_edit')

<!-- CSS for searching -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- JS for searching -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// .js-example-basic-single declare this class into your select box
$(document).ready(function() {
    $('.js-example-basic-single').select2();
});

function deleteUnsavedGroupItem(){
	if (!confirm('{!! trans('fi.delete_record_warning') !!}')) return false;
	//console.log(this.event.target.parentNode.parentNode)
	this.event.target.parentNode.parentNode.remove();
}


</script>


<section class="content-header">
    <h1 class="pull-left">{{ trans('fi.invoice') }} #{{ $invoice->number }}</h1>

    @if ($invoice->viewed)
        <span style="margin-left: 10px;" class="label label-success">{{ trans('fi.viewed') }}</span>
    @else
        <span style="margin-left: 10px;" class="label label-default">{{ trans('fi.not_viewed') }}</span>
    @endif

    @if ($invoice->quote()->count())
        <span class="label label-info"><a href="{{ route('quotes.edit', [$invoice->quote->id]) }}" style="color: inherit;">{{ trans('fi.converted_from_quote') }} #{{ $invoice->quote->number }}</a></span>
    @endif

    <div class="pull-right">
        <a href="{{ route('invoices.print', [$invoice->id]) }}" target="_blank" id="btn-print-invoice"
           class="btn btn-default"><i class="fa fa-print"></i> {{ trans('fi.print') }}</a>
        <!--<a href="{{ route('invoices.pdf', [$invoice->id]) }}" target="_blank" id="btn-pdf-invoice"
           class="btn btn-default"><i class="fa fa-file-pdf-o"></i> {{ trans('fi.pdf') }}</a>-->
        @if (config('fi.mailConfigured'))
            <a href="javascript:void(0)" id="btn-email-invoice" class="btn btn-default email-invoice"
               data-invoice-id="{{ $invoice->id }}" data-redirect-to="{{ route('invoices.edit', [$invoice->id]) }}"><i
                    class="fa fa-envelope"></i> {{ trans('fi.email') }}</a>
        @endif

        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                {{ trans('fi.other') }} <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                @if ($invoice->isPayable or config('fi.allowPaymentsWithoutBalance'))
                    <li><a href="javascript:void(0)" id="btn-enter-payment" class="enter-payment"
                           data-invoice-id="{{ $invoice->id }}"
                           data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                           data-redirect-to="{{ route('invoices.edit', [$invoice->id]) }}"><i
                                class="fa fa-credit-card"></i> {{ trans('fi.enter_payment') }}</a></li>
                @endif
                <li><a href="javascript:void(0)" id="btn-copy-invoice"><i
                            class="fa fa-copy"></i> {{ trans('fi.copy') }}</a></li>
                <li><a href="{{ route('clientCenter.public.invoice.show', [$invoice->url_key]) }}" target="_blank"><i
                            class="fa fa-globe"></i> {{ trans('fi.public') }}</a></li>
                <li class="divider"></li>
               <!-- <li><a href="{{ route('invoices.delete', [$invoice->id]) }}"
                       onclick="return confirm('{{ trans('fi.delete_record_warning') }}');"><i
                            class="fa fa-trash-o"></i> {{ trans('fi.delete') }}</a></li> -->
            </ul>
        </div>

        <div class="btn-group">
            @if ($returnUrl)
                <a href="{{ $returnUrl }}" class="btn btn-default"><i
                        class="fa fa-backward"></i> {{ trans('fi.back') }}</a>
            @endif
        </div>

        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-save-invoice"><i
                    class="fa fa-save"></i> {{ trans('fi.save') }}</button>
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <li><a href="#" class="btn-save-invoice"
                       data-apply-exchange-rate="1">{{ trans('fi.save_and_apply_exchange_rate') }}</a></li>
            </ul>
        </div>

    </div>

    <div class="clearfix"></div>
</section>

<section class="content">

    <div class="row">

        <div class="col-lg-10">

            <div id="form-status-placeholder"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">{{ trans('fi.event_name') }}</h3>
                        </div>
                        <div class="box-body">
                            {!! Form::text('summary', $invoice->summary, ['id' => 'summary', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-6" id="col-from">

                    @include('invoices._edit_from')

                </div>

                <div class="col-sm-6" id="col-to">

                    @include('invoices._edit_to')

                </div>

            </div>

            

		<div class="row">

                <div class="col-lg-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab-product-items" data-toggle="tab">Product Items</a></li>
                            <li><a href="#tab-group-items" data-toggle="tab">Group Items </a></li>
                        </ul>
                        <div class="tab-content">

                            <div class="tab-pane active" id="tab-product-items">
				
				<div class="row">

                <div class="col-sm-12 table-responsive" style="overflow-x: visible;">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">{{ trans('fi.items') }}</h3>

                            <div class="box-tools pull-right">
                                <a href="{{ route('invoices.itemChecklistPrint', [$invoice->id]) }}" target="_blank" id="btn-pdf-invoice"
           class="btn btn-default"><i class="fa fa-file-pdf-o"></i> Item Checklist</a>
                                
				<label><a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm" ><i
                                        class="fa fa-plus"></i> Add new item in inventory</a></label>

                            </div>
                        </div>

                        <div class="box-body">
                            <table id="item-table" class="table table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 20%;">{{ trans('fi.product') }} <span style="color:red;">*</span></th>
                                    <th style="width: 25%;">{{ trans('fi.description') }}</th>
                                    <th style="width: 10%;">{{ trans('fi.qty') }} <span style="color:red;">*</span></th>
                                    <th style="width: 10%;">{{ trans('fi.price') }} <span style="color:red;">*</span></th>
                                    <th style="width: 10%;">{{ trans('fi.tax_1') }}</th>
                                   <!-- <th style="width: 10%;">{{ trans('fi.tax_2') }}</th>-->
                                    <th style="width: 10%; text-align: right; padding-right: 25px;">{{ trans('fi.total') }}</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr id="new-item" style="display: none;">
                                    <td>
                                        {!! Form::hidden('invoice_id', $invoice->id) !!}
                                        {!! Form::hidden('', $invoice->event_date, ['class'=> 'eventDate']) !!}
                                        {!! Form::hidden('', $invoice->id, ['class'=> 'invoiceId']) !!}
                                        @if ($invoice->quote()->count())
                                        {!! Form::hidden('', $invoice->quote->id, ['class'=> 'quoteId']) !!}
                                        @else
                                        {!! Form::hidden('', '', ['class'=> 'quoteId']) !!}
                                        @endif
                                        {!! Form::hidden('id', '') !!}
                                        {!! Form::hidden('availableQuan', '') !!}
                                        {!! Form::hidden('inventory_id', '', ['class'=> 'inventory_id']) !!}
                                        <!--{!! Form::text('name', null, ['class' => 'form-control']) !!}-->
                                        {!! Form::select('name', $inventory, "null", ['class' => 'form-control','style'=>'width:100%;']) !!}
                                        <br>
                                        <input type="hidden" name="save_item_as_lookup" tabindex="999">
                                    </td>
                                    <td>{!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 1]) !!}</td>
                                    <td class="info-td ">
                                        {!! Form::text('quantity', null, ['class' => 'form-control']) !!}

                                        
                                        <div class="tooltip-custom"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                          <span class="tooltiptext">
                                              <table style="width:100%;">
                                                  <thead>
                                                      <tr style="width:100%">
                                                          <td style="text-align:left">Total Quantity :</td>
                                                          <td class="total-quan"></td>
                                                      </tr>
                                                      <tr style="width:100%">
                                                          <td style="text-align:left">Reserved Quantity :</td>
                                                          <td class="reserved-quan"></td>
                                                      </tr>
                                                      <tr style="width:100%">
                                                          <td style="text-align:left">Alloted Quantity :</td>
                                                          <td class="alloted-quan"></td>
                                                      </tr>
                                                      <tr style="width:100%">
                                                          <td style="text-align:left">Available Quantity :</td>
                                                          <td class="available-quan"></td>
                                                      </tr>
                                                  </thead>
                                              </table>
                                          </span>
                                        </div>
                                    </td>
                                    <td>{!! Form::text('price', null, ['class' => 'form-control']) !!}</td>
                                    <td>{!! Form::select('tax_rate_id', $taxRates, config('fi.itemTaxRate'), ['class' => 'form-control']) !!}</td>
				    <td style="text-align: right; padding-right: 25px;" class="changeTotal"></td>
                                   <!-- <td>{!! Form::select('tax_rate_2_id', $taxRates, config('fi.itemTax2Rate'), ['class' => 'form-control']) !!}</td>-->
                                    
                                    <td>
						<a class="btn btn-xs btn-default btn-delete-invoice-group-item-unsaved" onclick="deleteUnsavedGroupItem()" href="javascript:void(0);"
                                               title="{{ trans('fi.delete') }}">
                                                <i class="fa fa-times" style="pointer-events:none;"></i>
                                            </a>

					</td>
                                </tr>
                                @foreach ($invoice->items as $item)
                                    <tr class="item" id="tr-item-{{ $item->id }}">
                                        <td>
                                            {!! Form::hidden('invoice_id', $invoice->id) !!}
                                            {!! Form::hidden('', $invoice->event_date, ['class'=> 'eventDate']) !!}
                                            {!! Form::hidden('', $invoice->id, ['class'=> 'invoiceId']) !!}
                                            @if ($invoice->quote()->count())
                                            {!! Form::hidden('', $invoice->quote->id, ['class'=> 'quoteId']) !!}
                                            @else
                                            {!! Form::hidden('', '', ['class'=> 'quoteId']) !!}
                                            @endif
                                            {!! Form::hidden('id', $item->id) !!}
                                            {!! Form::hidden('availableQuan', $item->getAvailableQuantity()) !!}
                                            {!! Form::hidden('inventory_id', $item->inventory_id, ['class'=> 'inventory_id']) !!}
                                            <!--{!! Form::text('name', $item->name, ['class' => 'form-control item-lookup']) !!}-->
                                            {!! Form::select('name', $inventory, $item->name, ['class' => 'form-control js-example-basic-single','style'=>'width:100%;']) !!}

                                        </td>
                                        <td>{!! Form::textarea('description', $item->description, ['class' => 'form-control', 'rows' => 1]) !!}</td>
                                        <td class="info-td">
                                            {!! Form::text('quantity', $item->formatted_quantity, ['class' => 'form-control']) !!}

                                                <!--<input type="text" name="quantity"  class="form-control changeQty" value="{!! $item->formatted_quantity !!}" onkeyup="changeQtyValue({!! $item->formatted_numeric_price !!})">-->

                                            <div class="tooltip-custom"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                          <span class="tooltiptext">
                                              <table style="width:100%;">
                                                  <thead>
                                                      <tr style="width:100%">
                                                          <td style="text-align:left">Total Quantity :</td>
                                                         @if ($item->inventory()->count() > 0)
                                                                <td class="total-quan">{{$item->inventory->total}}</td>
                                                            @else
                                                                <td class="total-quan">0</td>
                                                            @endif
                                                      </tr>
                                                      <tr style="width:100%">
                                                          <td style="text-align:left">Reserved Quantity :</td>
                                                          <td class="reserved-quan">{{$item->getReservedQuantity()}}</td>
                                                      </tr>
                                                      <tr style="width:100%">
                                                          <td style="text-align:left">Alloted Quantity :</td>
                                                          <td class="alloted-quan">{{$item->getAllocatedQuantity()}}</td>
                                                      </tr>
                                                      <tr style="width:100%">
                                                          <td style="text-align:left">Available Quantity :</td>
                                                          <td class="available-quan">{{$item->getAvailableQuantity()}}</td>
                                                      </tr>
                                                  </thead>
                                              </table>
                                          </span>
                                        </div>
                                        </td>
                                        <td>{!! Form::text('price', $item->formatted_numeric_price, ['class' => 'form-control']) !!}</td>
                                        <td>{!! Form::select('tax_rate_id', $taxRates, $item->tax_rate_id, ['class' => 'form-control']) !!}</td>
                                       <!-- <td>{!! Form::select('tax_rate_2_id', $taxRates, $item->tax_rate_2_id, ['class' => 'form-control']) !!}</td>-->
                                        <td style="text-align: right; padding-right: 25px;" class="changeTotal">{{ $item->amount->formatted_subtotal }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-default btn-delete-invoice-item" href="javascript:void(0);"
                                               title="{{ trans('fi.delete') }}" data-item-id="{{ $item->id }}">
                                                <i class="fa fa-times"></i>
                                            </a>
                                            
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="box-body">
                           <!--- <label><a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm" ><i
                                        class="fa fa-plus"></i> Add new item in inventory</a></label>-->
				<button class="btn btn-primary btn-sm" id="btn-add-item"><i
                                        class="fa fa-plus"></i> {{ trans('fi.add_item') }}</button>
                        </div>
                    </div>
                </div>

            </div>
                              
                            </div>

                            <div class="tab-pane" id="tab-group-items">
                                
				<div class="row">

                <div class="col-sm-12 table-responsive" style="overflow-x: visible;">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Groups</h3>

                            <div class="box-tools pull-right">
                                <a href="{{ route('invoices.itemChecklistPrint', [$invoice->id]) }}" target="_blank" id="btn-pdf-invoice"
           class="btn btn-default"><i class="fa fa-file-pdf-o"></i> Item Checklist</a>

                                <label><a href="{{ route('inventorygrouplist.create') }}" class="btn btn-primary btn-sm" ><i
                                        class="fa fa-plus"></i> Add new item in inventory group list</a></label>
                            </div>
                        </div>

                        <div class="box-body">
                            <table id="item-group-table" class="table table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 20%;">Group <span style="color:red;">*</span></th>
                                    <th style="width: 55%;">{{ trans('fi.description') }}</th>
                                    <th style="width: 10%;">{{ trans('fi.qty') }} <span style="color:red;">*</span></th>
				    <th style="width: 10%;">Price <span style="color:red;">*</span></th>
                                    <th style="width: 10%; text-align: right; padding-right: 25px;">{{ trans('fi.total') }}</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr id="new-group-item" style="display: none;">
                                    <td>
                                        {!! Form::hidden('invoice_id', $invoice->id) !!}
                                        {!! Form::hidden('', $invoice->event_date, ['class'=> 'eventDate']) !!}
                                        {!! Form::hidden('', $invoice->id, ['class'=> 'invoiceId']) !!}
                                        @if ($invoice->quote()->count())
                                        {!! Form::hidden('', $invoice->quote->id, ['class'=> 'quoteId']) !!}
                                        @else
                                        {!! Form::hidden('', '', ['class'=> 'quoteId']) !!}
                                        @endif
                                        {!! Form::hidden('id', '') !!}
                                        {!! Form::hidden('inventory_group_list_id', '', ['class'=> 'inventory_group_list_id']) !!}
                                        <!--{!! Form::text('name', null, ['class' => 'form-control']) !!}-->
                                        {!! Form::select('group_name', $inventory_group_list, "null", ['class' => 'form-control','style'=>'width:100%']) !!}
                                        <br>
                                    </td>
                                    <td>{!! Form::textarea('group_description', null, ['class' => 'form-control', 'rows' => 1]) !!}</td>
                                    <td class="info-td">
                                        {!! Form::text('group_quantity', null, ['class' => 'form-control']) !!}                                    
                                    </td>
                                    <td>{!! Form::text('group_price', null, ['class' => 'form-control']) !!}</td>
                                    <td style="text-align: right; padding-right: 25px;" class="changeGroupTotal"></td>
                                    <td>
					<a class="btn btn-xs btn-default btn-delete-invoice-group-item-unsaved" onclick="deleteUnsavedGroupItem()" href="javascript:void(0);"
                                               title="{{ trans('fi.delete') }}">
                                                <i class="fa fa-times" style="pointer-events:none;"></i>
                                            </a>
				    </td>
                                </tr>
                                @foreach ($invoice->groupitems as $item)
                                    <tr class="item_group" id="tr-group-item-{{ $item->id }}">
                                        <td>
                                            {!! Form::hidden('invoice_id', $invoice->id) !!}
                                            {!! Form::hidden('', $invoice->event_date, ['class'=> 'eventDate']) !!}
                                            {!! Form::hidden('', $invoice->id, ['class'=> 'invoiceId']) !!}
                                            @if ($invoice->quote()->count())
                                            {!! Form::hidden('', $invoice->quote->id, ['class'=> 'quoteId']) !!}
                                            @else
                                            {!! Form::hidden('', '', ['class'=> 'quoteId']) !!}
                                            @endif
                                            {!! Form::hidden('id', $item->id) !!}
                                            {!! Form::hidden('inventory_group_list_id', $item->inventory_id, ['class'=> 'inventory_group_list_id']) !!}
                                            <!--{!! Form::text('name', $item->name, ['class' => 'form-control item-lookup']) !!}-->
                                            {!! Form::select('group_name', $inventory_group_list, $item->name, ['class' => 'js-example-basic-single form-control','style'=>'width:100%;']) !!}
                                        </td>
                                        <td>{!! Form::textarea('group_description', $item->description, ['class' => 'form-control', 'rows' => 1]) !!}</td>
                                        <td class="info-td">
                                            {!! Form::text('group_quantity', $item->formatted_quantity, ['class' => 'form-control']) !!}
                                        </td>
                                        <td>{!! Form::text('group_price', number_format((float)$item->price, 2, '.', ''), ['class' => 'form-control']) !!}</td>
                                        <td style="text-align: right; padding-right: 25px;" class="changeGroupTotal">${{ number_format((float)$item->total, 2, '.', '') }}</td>
                                        <td>
                                            <a class="btn btn-xs btn-default btn-delete-invoice-group-item" href="javascript:void(0);"
                                               title="{{ trans('fi.delete') }}" data-item-id="{{ $item->id }}">
                                                <i class="fa fa-times"></i>
                                            </a>
                                            
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="box-body">
                            <!---<label><a href="{{ route('inventorygrouplist.create') }}" class="btn btn-primary btn-sm" ><i
                                        class="fa fa-plus"></i> Add new item in inventory group list</a></label>--->
				<button class="btn btn-primary btn-sm" id="btn-add-group-item"><i
                                        class="fa fa-plus"></i> Add Group Item</button>
                        </div>
                    </div>
                </div>

            </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        


            <div class="row">

                <div class="col-lg-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab-additional" data-toggle="tab">{{ trans('fi.additional') }}</a></li>
                            <li><a href="#tab-notes" data-toggle="tab">{{ trans('fi.notes') }}</a></li>
                            <li><a href="#tab-attachments" data-toggle="tab">{{ trans('fi.attachments') }}</a></li>
                            <li><a href="#tab-payments" data-toggle="tab">{{ trans('fi.payments') }}</a></li>
                        </ul>
                        <div class="tab-content">

                            <div class="tab-pane active" id="tab-additional">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ trans('fi.terms_and_conditions') }}</label>
                                            {!! Form::textarea('terms', $invoice->terms, ['id' => 'terms', 'class' => 'form-control', 'rows' => 5]) !!}
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>{{ trans('fi.footer') }}</label>
                                            {!! Form::textarea('footer', $invoice->footer, ['id' => 'footer', 'class' => 'form-control', 'rows' => 5]) !!}
                                        </div>
                                    </div>
                                </div>

                                @if ($customFields->count())
                                    <div class="row">
                                        <div class="col-md-12">
                                            @include('custom_fields._custom_fields_unbound', ['object' => $invoice])
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane" id="tab-notes">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @include('notes._notes', ['object' => $invoice, 'model' => 'FI\Modules\Invoices\Models\Invoice', 'showPrivateCheckbox' => true, 'hideHeader' => true])
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab-attachments">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @include('attachments._table', ['object' => $invoice, 'model' => 'FI\Modules\Invoices\Models\Invoice'])
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab-payments">
                                <table class="table table-hover">

                                    <thead>
                                    <tr>
                                        <th>{{ trans('fi.payment_date') }}</th>
                                        <th>{{ trans('fi.amount') }}</th>
                                        <th>{{ trans('fi.payment_method') }}</th>
                                        <th>{{ trans('fi.note') }}</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach ($invoice->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->formatted_paid_at }}</td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>@if ($payment->paymentMethod) {{ $payment->paymentMethod->name }} @endif</td>
                                            <td>{{ $payment->note }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2">

            <div id="div-totals">
                @include('invoices._edit_totals')
            </div>

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">{{ trans('fi.options') }}</h3>
                </div>

                <div class="box-body">

                    <div class="form-group">
                        <label>{{ trans('fi.invoice') }} #</label>
                        {!! Form::text('number', $invoice->number, ['id' => 'number', 'class' =>
                        'form-control
                        input-sm', 'readonly'=>'true']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.event_date') }} <span style="color:red;">*</span></label>
                        @if ($invoice->quote()->count())
                        {!! Form::text('event_date', $invoice->quote->formatted_event_date, ['id' =>
                        'event_date', 'class' => 'form-control input-sm']) !!}
                        @else
                        {!! Form::text('event_date', $invoice->getFormattedInvoiceEventDateAttribute(), ['id' =>
                        'event_date', 'class' => 'form-control input-sm']) !!}
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label>{{ trans('fi.Invoice_date') }} <span style="color:red;">*</span></label>
                        {!! Form::text('invoice_date', $invoice->formatted_invoice_date, ['id' =>
                        'invoice_date', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>Payment Due Date <span style="color:red;">*</span></label>
                        {!! Form::text('due_at', $invoice->formatted_due_at, ['id' => 'due_at', 'class'
                        => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.discount') }}</label>
                        <div class="input-group">
                            {!! Form::text('discount', $invoice->formatted_numeric_discount, ['id' =>
                            'discount', 'class' => 'form-control input-sm']) !!}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.currency') }} <span style="color:red;">*</span></label>
                        {!! Form::select('currency_code', $currencies, $invoice->currency_code, ['id' =>
                        'currency_code', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.exchange_rate') }} <span style="color:red;">*</span></label>
                        <div class="input-group">
                            {!! Form::text('exchange_rate', $invoice->exchange_rate, ['id' =>
                            'exchange_rate', 'class' => 'form-control input-sm']) !!}
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-sm" id="btn-update-exchange-rate" type="button"
                                        data-toggle="tooltip" data-placement="left"
                                        title="{{ trans('fi.update_exchange_rate') }}"><i class="fa fa-refresh"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.status') }} <span style="color:red;">*</span></label>
                        {!! Form::select('invoice_status_id', $statuses, $invoice->invoice_status_id,
                        ['id' => 'invoice_status_id', 'class' => 'form-control input-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.template') }} <span style="color:red;">*</span></label>
                        {!! Form::select('template', $templates, $invoice->template,
                        ['id' => 'template', 'class' => 'form-control input-sm']) !!}
                    </div>

                </div>
            </div>
        </div>

    </div>

</section>

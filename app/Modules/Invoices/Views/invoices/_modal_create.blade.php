@include('layouts._datepicker')
@include('layouts._typeahead')
@include('clients._js_lookup')
@include('invoices._js_create')

<!-- CSS for searching -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- JS for searching -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<div class="modal fade" id="create-invoice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ trans('fi.create_invoice') }}</h4>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" id="user_id">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Master {{ trans('fi.client') }}</label>

                        <div class="col-sm-9">
			    {!! Form::select('MasterClient', $MasterClient, null, ['placeholder' => '--Select Master client--','id' => 'create_master_client_name', 'class' => 'js-example-basic-single form-control','style'=>'width:100%;']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.client') }}</label>

                        <!--<div class="col-sm-9">
                            {!! Form::text('client_name', null, ['id' => 'create_client_name', 'class' =>
                            'form-control client-lookup', 'autocomplete' => 'off']) !!}
                        </div>-->
			<div class="col-sm-9">
			    {!! Form::select('client_name', $Client, null, ['placeholder' => '--Select client--','id' => 'create_client_name_new', 'class' => 'js-example-basic-single form-control','style'=>'width:100%;']) !!}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.client_type') }}</label>

                        <div class="col-sm-9">
                            {!! Form::select('type', ['1' => trans('fi.individual'), '2' => trans('fi.corporate')], config('fi.defaultCompanyProfile'),
                            ['id' => 'type', 'class' => 'form-control']) !!}
                        </div>
                    </div>

		    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.event_date') }}</label>

                        <div class="col-sm-9">
                            {!! Form::text('event_date', date(config('fi.dateFormat')), ['id' => 'create_event_date', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.Invoice_date') }}</label>

                        <div class="col-sm-9">
                            {!! Form::text('invoice_date', date(config('fi.dateFormat')), ['id' =>
                            'create_invoice_date', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.company_profile') }}</label>

                        <div class="col-sm-9">
                            {!! Form::select('company_profile_id', $companyProfiles, config('fi.defaultCompanyProfile'),
                            ['id' => 'company_profile_id', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{ trans('fi.group') }}</label>

                        <div class="col-sm-9">
                            {!! Form::select('group_id', $groups, config('fi.invoiceGroup'),
                            ['id' => 'create_group_id', 'class' => 'form-control', 'disabled'=>'true']) !!}
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="invoice-create-confirm" class="btn btn-primary">{{ trans('fi.submit') }}
                </button>
            </div>
        </div>
    </div>
</div>

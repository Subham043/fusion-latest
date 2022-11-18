@include('clients._js_unique_name')

<script type="text/javascript">
    $(function() {
        $('#name').focus();
    });
</script>

<div class="row">
    <div class="col-md-4" id="col-client-name">
        <div class="form-group">
            <label>* Master {{ trans('fi.client_name') }}:</label>
            {!! Form::text('client_name', null, ['id' => 'client_name', 'class' => 'form-control']) !!}
            <p class="help-block">
                <small>{{ trans('fi.help_text_client_name') }}
                    <a href="javascript:void(0)" id="btn-show-unique-name"
                       tabindex="-1">{{ trans('fi.view_unique_name') }}</a>
                </small>
            </p>
        </div>
    </div>
    <div class="col-md-3" id="col-client-unique-name" style="display: none;">
        <div class="form-group">
            <label>* {{ trans('fi.unique_name') }}:</label>
            {!! Form::text('unique_name', null, ['id' => 'unique_name', 'class' => 'form-control']) !!}
            <p class="help-block">
                <small>{{ trans('fi.help_text_client_unique_name') }}</small>
            </p>
        </div>
    </div>
    
    <div class="col-md-4" id="col-client-email">
        
        <div class="form-group">
            <label>* {{ trans('fi.email_address') }}: </label>
            {!! Form::text('client_email', null, ['id' => 'client_email', 'class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-4" id="col-client-active">
        <div class="form-group">
            <label>{{ trans('fi.mobile_number') }}: </label>
            {!! Form::text('mobile', null, ['id' => 'mobile', 'class' => 'form-control']) !!}
        </div>
    </div>
</div>



<link href="{{ asset('assets/addons/Scheduler/Assets/jquery-datetimepicker/build/jquery.datetimepicker.min.css') }}" rel="stylesheet" type="text/css"/>
<script src='{{ asset('assets/addons/Scheduler/Assets/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js') }}'></script>

<script type="text/javascript">
    $(function () {
        $('textarea.custom-form-field').autosize();
    });
 $(function () {
        
 });

</script>

@foreach ($customFields as $customField)
    <div class="form-group">
        
        @if ($customField->field_type == 'dropdown')
		<label>{{ $customField->field_label }}</label>
            	{!! Form::select('custom[' . $customField->column_name . ']', array_combine(array_merge([''], explode(',', $customField->field_meta)), array_merge([''], explode(',', $customField->field_meta))), (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]) !!}
         @elseif ($customField->field_type == 'text' && $customField->field_label != 'Event Name' && ($customField->tbl_name == 'quotes') && ($customField->column_name == 'column_8' || $customField->column_name == 'column_9' || $customField->column_name == 'column_5' || $customField->column_name == 'column_6'))
		@if($customField->column_name == 'column_5' || $customField->column_name == 'column_6')
		<label>{{ $customField->field_label }} <span style="color:red;">*</span></label>
		<input type="datetime-local" min="{{date('Y-m-d H:i:s')}}" class="custom-form-field form-control from" value="{{ (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : date('Y-m-d H:i:s',strtotime($object->event_date))) }}" {!!''.'data-' . $customField->tbl_name . '-field-name'.'="'.$customField->column_name.'"'!!} >

		<!---{!! call_user_func_array('Form::' . $customField->field_type, ['custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : $object->event_date), ['class' => 'custom-form-field form-control from', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]]) !!}--->
		@else
		<label>{{ $customField->field_label }} <span style="color:red;">*</span></label>
	        <input type="datetime-local" min="{{date('Y-m-d H:i:s')}}" class="custom-form-field form-control from" value="{{ (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : date('Y-m-d H:i:s',strtotime($object->event_date))) }}" {!!''.'data-' . $customField->tbl_name . '-field-name'.'="'.$customField->column_name.'"'!!} >
		<!---{!! call_user_func_array('Form::' . $customField->field_type, ['custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control from', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]]) !!}--->
		@endif
	@elseif ($customField->field_type == 'text' && ($customField->tbl_name == 'invoices') && ($customField->column_name == 'column_8' || $customField->column_name == 'column_9' || $customField->column_name == 'column_5' || $customField->column_name == 'column_6'))
		@if($customField->column_name == 'column_5' || $customField->column_name == 'column_6')
		<label>{{ $customField->field_label }} <span style="color:red;">*</span></label>
			<input type="datetime-local" min="{{date('Y-m-d H:i:s')}}" {!!''.'data-' . $customField->tbl_name . '-field-name'.'="'.$customField->column_name.'"'!!} class="custom-form-field form-control from" value="{{ (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : date('Y-m-d H:i:s',strtotime($object->event_date))) }}" >
		<!--{!! call_user_func_array('Form::' . $customField->field_type, ['custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : $object->event_date), ['class' => 'custom-form-field form-control from', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]]) !!}--->
		@else
		<label>{{ $customField->field_label }} <span style="color:red;">*</span></label>
		<input type="datetime-local" min="{{date('Y-m-d H:i:s')}}" {!!''.'data-' . $customField->tbl_name . '-field-name'.'="'.$customField->column_name.'"'!!} class="custom-form-field form-control from" value="{{ (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : date('Y-m-d H:i:s',strtotime($object->event_date))) }}" >

		<!--{!! call_user_func_array('Form::' . $customField->field_type, ['custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control from', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]]) !!}-->
		
		@endif
	@else
	    <label>{{ $customField->field_label }}</label>
            {!! call_user_func_array('Form::' . $customField->field_type, ['custom[' . $customField->column_name . ']', (isset($object->custom->{$customField->column_name}) ? $object->custom->{$customField->column_name} : null), ['class' => 'custom-form-field form-control', 'data-' . $customField->tbl_name . '-field-name' => $customField->column_name]]) !!}
        @endif
    </div>
@endforeach
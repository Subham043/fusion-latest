@extends('layouts.master')

@section('javascript')
    @include('invoices._js_index')
@stop

@section('content')

    <section class="content-header">
        <h1 class="pull-left">{{ trans('fi.invoices') }} - Payment Due</h1>

        <div class="pull-right">


          <!--  <a href="javascript:void(0)" class="btn btn-default bulk-actions" id="btn-bulk-delete"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a> -->

            <a href="javascript:void(0)" class="btn btn-default bulk-actions" id="btn-bulk-reminder" >Send Reminder To Multiple</a>
            <a href="{{route('invoices.sendAllReminder')}}" class="btn btn-primary"> Send Reminder To All</a>
        </div>

        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body no-padding">
                        @include('invoices._table2')
                    </div>

                </div>

                <div class="pull-right">
                    {!! $invoices->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

<script>
$(function () {
	$("#btn-bulk-reminder").click(function(){
		var bulkData = [];
		var checkBoxData = document.getElementsByClassName('bulk-record');
		for(i=0;i<checkBoxData.length;i++){
			if(checkBoxData[i].type=="checkbox" && checkBoxData[i].checked==true ){
				bulkData.push(checkBoxData[i].value)
			}
		}
		if(bulkData.length==0){
			alert("Please select atleast one invoice");
		}else{
			var data = bulkData.join(',');
			window.location.replace("{{URL::to('/')}}/invoices/payment-due/send-multiple?data="+data);
		}
	});
});
</script>

@stop

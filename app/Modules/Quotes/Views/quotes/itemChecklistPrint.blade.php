<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('fi.quote') }} #{{ $quote->number }}</title>
    <link rel="stylesheet" media="print" href="{{ asset('assets/plugins/chosen/print.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">

    <style>
        @page {
            size:A4;
            margin: 25px;
        }
        
        .wrapper {
        	overflow: visible !important;
        }
        
        section.content-header {
        	padding: 13px;
        	background: #17abe6 !important;
        	margin-bottom:30px;
        }
        
        .box-header {
        	background: white;
        	position: sticky;
        	top:95px;
        	z-index: 100;
        	border-bottom: 1px solid #888;
        }


        body {
            color: #001028;
            background: #FFFFFF;
            font-family : DejaVu Sans, Helvetica, sans-serif;
            font-size: 12px;
            margin-bottom: 50px;
        }

        a {
            color: #5D6975;
            border-bottom: 1px solid currentColor;
            text-decoration: none;
        }

        h1 {
            color: #5D6975;
            font-size: 2.8em;
            line-height: 1.4em;
            font-weight: bold;
            margin: 0;
        }

        table {
            width: 100%;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        th, .section-header {
            padding: 5px 10px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
            text-align: left;
        }

        td {
            padding: 10px;
        }

        table.alternate tr:nth-child(odd) td {
            background: #F5F5F5;
        }

        th.amount, td.amount {
            text-align: right;
        }

        .info {
            color: #5D6975;
            font-weight: bold;
        }

        .terms {
            padding: 10px;
        }

        .footer {
            position: fixed;
            height: 50px;
            width: 100%;
            bottom: 0;
            text-align: center;
        }
        
        #cp-logo{
            width: 30%;
            object-fit: contain;
        }

    </style>
</head>
<body>
    
    <section class="content-header" id="print-section">

    <div class="pull-right">
<a href="{{ route('quotes.itemChecklist', [$quote->id]) }}" target="_blank" id="btn-pdf-quote"
           class="btn btn-default"><i class="fa fa-print"></i> PDF</a>
        <button  onclick="window.print();"
           class="btn btn-default"><i class="fa fa-print"></i> {{ trans('fi.print') }}</button>

    </div>

    <div class="clearfix"></div>
</section>

<table>
    <tr>
        <td style="width: 50%;" valign="top">
            <h1>ITEM CHECKLIST</h1>
            <span class="info">{{ mb_strtoupper(trans('fi.quote')) }} #</span>{{ $quote->number }}<br>
            <span class="info">{{ mb_strtoupper(trans('fi.issued')) }}</span> {{ $quote->formatted_created_at }}<br>
            <span class="info">{{ mb_strtoupper(trans('fi.event_date')) }}</span> {{ $quote->formatted_event_date }}<br>
            <span class="info">{{ mb_strtoupper(trans('fi.expires')) }}</span> {{ $quote->formatted_expires_at }}<br><br>
            <span class="info">{{ mb_strtoupper(trans('fi.bill_to')) }}</span><br>{{ $quote->client->name }}<br>
            @if ($quote->client->address) {!! $quote->client->formatted_address !!}<br>@endif
		<br>
			@if ($customFields->count())
				@foreach ($customFields as $customField)
					@if ($customField->field_type == 'textarea' && $customField->column_name == 'column_9')
						<span class="info">VENUE ADDRESS</span><br>{{$quote->custom->column_9}}<br>
					 @endif
				@endforeach
			@endif
        </td>
        <td style="width: 50%; text-align: right;" valign="top">
            {!! $quote->companyProfile->logo(150) !!}<br>
            {{ $quote->companyProfile->company }}<br>
            {!! $quote->companyProfile->formatted_address !!}<br>
            @if ($quote->companyProfile->phone) {{ $quote->companyProfile->phone }}<br>@endif
            @if ($quote->user->email) <a href="mailto:{{ $quote->user->email }}">{{ $quote->user->email }}</a>@endif
        </td>
    </tr>
</table>

<table>
    <tr>
        <th style="text-align:left;">Notes:</th>
    </tr>
    <tr>
        <td style="width:100%;height:50px;border:1px solid #818181"></td>
    </tr>
</table>

<table class="alternate">
    <thead>
    <tr>
	<th></th>
        <th>{{ mb_strtoupper(trans('fi.product')) }}</th>
        <th>{{ mb_strtoupper(trans('fi.description')) }}</th>
        <th class="amount">{{ mb_strtoupper(trans('fi.quantity')) }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($quote->items as $item)
        <tr>
	<td><input type="checkbox" /></td>
            <td>{!! $item->name !!}</td>
            <td>{!! $item->formatted_description !!}</td>
            <td nowrap class="amount">{{ $item->formatted_quantity }}</td>
        </tr>
    @endforeach

    <!--<tr>-->
    <!--    <td colspan="4" class="amount">{{ mb_strtoupper(trans('fi.subtotal')) }}</td>-->
    <!--    <td class="amount">{{ $quote->amount->formatted_subtotal }}</td>-->
    <!--</tr>-->

    <!--@if ($quote->discount > 0)-->
    <!--    <tr>-->
    <!--        <td colspan="4" class="amount">{{ mb_strtoupper(trans('fi.discount')) }}</td>-->
    <!--        <td class="amount">{{ $quote->amount->formatted_discount }}</td>-->
    <!--    </tr>-->
    <!--@endif-->

    <!--@foreach ($quote->summarized_taxes as $tax)-->
    <!--    <tr>-->
    <!--        <td colspan="4" class="amount">{{ mb_strtoupper($tax->name) }} ({{ $tax->percent }})</td>-->
    <!--        <td class="amount">{{ $tax->total }}</td>-->
    <!--    </tr>-->
    <!--@endforeach-->

    <!--<tr>-->
    <!--    <td colspan="4" class="amount">{{ mb_strtoupper(trans('fi.total')) }}</td>-->
    <!--    <td class="amount">{{ $quote->amount->formatted_total }}</td>-->
    <!--</tr>-->
    </tbody>
</table>

<table>
    <tr>
        <td style="width:50%;text-align:left;font-weight:bold;">Signature</td>
        <td style="width:50%;text-align:right;font-weight:bold;">Date</td>
    </tr>
</table>

@if ($quote->terms)
    <div class="section-header">{{ mb_strtoupper(trans('fi.terms_and_conditions')) }}</div>
    <div class="terms">{!! $quote->formatted_terms !!}</div>
@endif

<div class="footer">{!! $quote->formatted_footer !!}</div>



</body>
</html>

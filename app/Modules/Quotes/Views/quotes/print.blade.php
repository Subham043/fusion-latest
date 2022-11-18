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
	<a href="{{ route('quotes.pdf', [$quote->id]) }}" target="_blank" id="btn-pdf-quote"
           class="btn btn-default"><i class="fa fa-print"></i> {{ trans('fi.pdf') }} </a>
        <button  onclick="window.print();"
           class="btn btn-default"><i class="fa fa-print"></i> {{ trans('fi.print') }}</button>

    </div>

    <div class="clearfix"></div>
</section>

<table>
    <tr>
        <td style="width: 33%;" valign="top">
            <h1>{{ mb_strtoupper(trans('fi.quote')) }}</h1>
            <span class="info">{{ mb_strtoupper(trans('fi.invoice')) }} #</span>{{ $quote->number }}<br>
            <span class="info">{{ mb_strtoupper(trans('fi.quote')) }} #</span>{{ str_replace("INV", "QUO", $quote->number) }}<br>
            <span class="info">{{ mb_strtoupper(trans('fi.issued')) }}</span> {{ $quote->formatted_created_at }}<br>
            <span class="info">{{ mb_strtoupper(trans('fi.due_date')) }}</span> {{ $quote->formatted_due_at }}<br><br>
            <span class="info">{{ mb_strtoupper(trans('fi.bill_to')) }}</span><br>{{ $quote->client->name }}<br>
            @if ($quote->client->address) {!! $quote->client->formatted_address !!}<br>@endif
        </td>
        <td style="width: 33%;"><h4 style="display:block;margin:auto;text-align: center;">{{ $quote->summary }}</h4></td>
        <td style="width: 33%; text-align: right;" valign="top">
            {!! $quote->companyProfile->logo() !!}<br>
            {{ $quote->companyProfile->company }}<br>
            {!! $quote->companyProfile->formatted_address !!}<br>
            @if ($quote->companyProfile->phone) {{ $quote->companyProfile->phone }}<br>@endif
            @if ($quote->user->email) {{ $quote->user->email }}@endif
        </td>
    </tr>
</table>

<table class="alternate">
    <thead>
    <tr>
        <th>{{ mb_strtoupper(trans('fi.product')) }}</th>
        <th>{{ mb_strtoupper(trans('fi.description')) }}</th>
        <th class="amount">{{ mb_strtoupper(trans('fi.quantity')) }}</th>
        <th class="amount">{{ mb_strtoupper(trans('fi.price')) }}</th>
        <th class="amount">{{ mb_strtoupper(trans('fi.total')) }}</th>
    </tr>
    </thead>
    <tbody>
	
    @foreach ($quote->items as $item)
	
        <tr>
            <td>{!! $item->name !!}</td>
            <td>{!! $item->formatted_description !!}</td>
            <td nowrap class="amount">{{ $item->formatted_quantity }}</td>
            <td nowrap class="amount">{{ $item->formatted_price }}</td>
            <td nowrap class="amount">{{ $item->amount->formatted_subtotal }}</td>
        </tr>
    @endforeach

@if($quote->groupitems()->count()>0)
@foreach ($quote->groupitems as $item)
        <tr>
            <td>{!! $item->name !!}</td>
            <td>{!! $item->formatted_description !!}</td>
            <td nowrap class="amount">{{ $item->formatted_quantity }}</td>
	    <td nowrap class="amount">${{ number_format((float)$item->total, 2, '.', '') }}</td>
            <td nowrap class="amount">${{ number_format((float)$item->total, 2, '.', '') }}</td>
        </tr>
@endforeach
@endif


    <tr>
        <td colspan="4" class="amount">{{ mb_strtoupper(trans('fi.subtotal')) }}</td>
        <td class="amount">{{ $quote->amount->formatted_subtotal }}</td>
    </tr>

    @if ($quote->discount > 0)
        <tr>
            <td colspan="4" class="amount">{{ mb_strtoupper(trans('fi.discount')) }}</td>
            <td class="amount">{{ $quote->amount->formatted_discount }}</td>
        </tr>
    @endif

    @foreach ($quote->summarized_taxes as $tax)
        <tr>
            <td colspan="4" class="amount">{{ mb_strtoupper($tax->name) }} ({{ $tax->percent }})</td>
            <td class="amount">{{ $tax->total }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="4" class="amount">{{ mb_strtoupper(trans('fi.total')) }}</td>
        <td class="amount">{{ $quote->amount->formatted_total }}</td>
    </tr>
    <tr>
        <td colspan="4" class="amount">{{ mb_strtoupper(trans('fi.paid')) }}</td>
        <td class="amount">{{ $quote->amount->formatted_paid }}</td>
    </tr>
    <tr>
        <td colspan="4" class="amount">{{ mb_strtoupper(trans('fi.balance')) }}</td>
        <td class="amount">{{ $quote->amount->formatted_balance }}</td>
    </tr>
    </tbody>
</table>

@if ($quote->terms)
    <div class="section-header">{{ mb_strtoupper(trans('fi.terms_and_conditions')) }}</div>
    <div class="terms">{!! $quote->formatted_terms !!}</div>
@endif

<div class="footer">{!! $quote->formatted_footer !!}</div>

</body>
</html>
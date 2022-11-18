@extends('reports.layouts.master')

@section('content')

    <h1 style="margin-bottom: 0;">Aging</h1>
    <!--<h3 style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>-->

        <table class="alternate">
            <thead>
            <tr>
		<th style="width: 10%; text-align: left;">Invoice Number</th>
                <th style="width: 10%; text-align: left;">Client Name</th>
		<th style="width: 10%; text-align: left;">Client Email</th>
                <th style="width: 10%; text-align: left;">Invoice Date</th>
		<th style="width: 10%; text-align: left;">Aging</th>
                <th class="amount" style="width: 10%;">Balance</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($results['records'] as $item)
                <tr>
                    <td>{{ $item['invoice_number'] }}</td>
                    <td>{{ $item['client_name'] }}</td>
		    <td>{{ $item['client_email'] }}</td>
                    <td>{{ $item['date'] }}</td>
		    <td>{{ $item['aging'] }}</td>
                    <td class="amount">{{ $item['balance'] }}</td>
                </tr>
            @endforeach
            </tbody>

        </table>

@stop
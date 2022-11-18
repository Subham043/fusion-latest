@extends('reports.layouts.master')

@section('content')

    <h1 style="margin-bottom: 0;">Paid</h1>
    <!--<h3 style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>-->

        <table class="alternate">
            <thead>
            <tr>
		<th style="width: 10%; text-align: left;">Invoice Number</th>
                <th style="width: 10%; text-align: left;">Client Name</th>
                <th style="width: 10%; text-align: left;">Event Date</th>
		<th style="width: 10%; text-align: left;">Event Name</th>
		<th style="width: 10%; text-align: left;">Location</th>
                <th class="amount" style="width: 10%;">Total</th>
                <th class="amount" style="width: 10%;">Amount Paid</th>
                <th class="amount" style="width: 10%;">Balance</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($results['records'] as $item)
                <tr>
                    <td>{{ $item['invoice_number'] }}</td>
                    <td>{{ $item['client_name'] }}</td>
                    <td>{{ $item['date'] }}</td>
		    <td>{{ $item['event_name'] }}</td>
		    <td>{{ $item['location'] }}</td>
                    <td class="amount">{{ $item['total'] }}</td>
                    <td class="amount">{{ $item['paid'] }}</td>
                    <td class="amount">{{ $item['balance'] }}</td>
                </tr>
            @endforeach
            </tbody>

        </table>

@stop
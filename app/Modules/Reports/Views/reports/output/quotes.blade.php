@extends('reports.layouts.master')

@section('content')

    <h1 style="margin-bottom: 0;">Quotes</h1>
    <!--<h3 style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>-->

        <table class="alternate">
            <thead>
            <tr>
		<th style="width: 10%; text-align: left;">Quote Number</th>
                <th style="width: 10%; text-align: left;">Client Name</th>
                <th style="width: 10%; text-align: left;">Event Date</th>
		<th style="width: 10%; text-align: left;">Event Name</th>
		<th style="width: 10%; text-align: left;">Location</th>
                <th class="amount" style="width: 10%;">Net Amount</th>
                <th class="amount" style="width: 10%;">Sales Tax</th>
                <th class="amount" style="width: 10%;">Total</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($results['records'] as $item)
                <tr>
                    <td>{{ $item['quote_number'] }}</td>
                    <td>{{ $item['client_name'] }}</td>
                    <td>{{ $item['date'] }}</td>
		    <td>{{ $item['event_name'] }}</td>
		    <td>{{ $item['location'] }}</td>
                    <td class="amount">{{ $item['subtotal'] }}</td>
                    <td class="amount">{{ $item['tax'] }}</td>
                    <td class="amount">{{ $item['total'] }}</td>
                </tr>
            @endforeach
            </tbody>

        </table>

@stop
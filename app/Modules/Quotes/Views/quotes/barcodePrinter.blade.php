<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Barcode Printer</title>
    <link rel="stylesheet" media="print" href="{{ asset('assets/plugins/chosen/print.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">

<script>
	function getUpdateList(){
		var searchKey=document.getElementById("searchChange").value;
		if(searchKey==''){
			console.log(searchKey)	
			document.getElementById("searchSubmit").submit();	
		}
		
	}
	
	function chk(){
		document.getElementById("searchSubmit").submit();
	}
</script>



    <style>
        @page {
            size:A4;
            margin: 25px;
        }

	@media print {
               .noprint {
                  visibility: hidden;
               }
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
	.width-50{
		width:50%;
	}
	.pt-10{
	    	padding-top:10px;
	}
	.txt{
		position: absolute;
		top: 8%;
    		width: 10%;
    		right: 1%;
	}



    </style>
</head>
<body>
    
    <section class="content-header" id="print-section">
<form action="{{ url()->current() }}" method="get" id="searchSubmit" class="sidebar-form">

	<div class="pull-left width-50">
	<div class="row ">
			
		<div class="col-md-6">
				@if (isset($displaySearch) and $displaySearch == true)
            
                
                    <input type="hidden" name="status" value="{{ request('status') }}"/>
                    <div class="input-group">
                        <input type="text" name="search" id="searchChange" onkeyup="getUpdateList()" class="form-control" placeholder="{{ trans('fi.search') }}..."/>
                                            </div>
                
			</div>

		</div>


                
            @endif

		</div>


    <div class="pull-right">
        	<div class="input-group pt-10 txt">
					
                        		<select class="form-control" name="item" onChange="this.form.submit()">
						<option value="" {{ empty(request()->get('item')) ? 'selected':'' }}>Select no. rows</option>
			<option value="50" {{ (request()->get('item')==50) ? 'selected':'' }}>50</option>
			<option value="100" {{ (request()->get('item')==100) ? 'selected':'' }}>100</option>
			<option value="200" {{ (request()->get('item')==200) ? 'selected':'' }}>200</option>
			<option value="400" {{ (request()->get('item')==400) ? 'selected':'' }}>400</option>
			<option value="all" {{ (request()->get('item')=="all") ? 'selected':'' }}>All</option>
					</select>
					
                                </div>


    </div>
</form>
<button  onclick="window.print();" style="position:absolute;right:20px;"
           class="btn btn-default"><i class="fa fa-print"></i> {{ trans('fi.print') }}</button>


    <div class="clearfix"></div>
</section>

<br>
<table class="alternate">
	<thead>
	    <tr>
		<th>Barcode</th>
        	<th>Quote Number</th>
        <th>Event Date</th>
        <th>Quote Date</th>
        <th>Expires At</th>
        <th>Client</th>
	<th>Total Amount</th>
	<th class="noprint">Action</th>
    	    </tr>
    	</thead>

    <tbody>
    @foreach ($quotes as $item)
	@if(file_exists(public_path('assets/barcode/').('quote-item-checklist-'.$item->id.'-barcode.png')))
        <tr>
            <td><label style="text-align:center;">
	   <img src="{{asset('assets/barcode/quote-item-checklist-'.$item->id.'-barcode.png')}}" />
	   <br>
	   CHK-{{$item->id}}</label></td>
		
		<td>{{$item->number}}</td>
		<td>{{ $item->formatted_event_date }}</td>
            	<td>{{ $item->formatted_quote_date }}</td>
            	<td>{{ $item->formatted_expires_at }}</td>
		<td>{{$item->client->name}}</td>
		<td>{{ $item->amount->formatted_total }}</td>
		<td class="noprint">
			<a href="{{route('quotes.barcodePrinterSingle', $item->id)}}" target="_blank"
           		class="btn btn-default"><i class="fa fa-print"></i> {{ trans('fi.print') }} Single</button>
		</td>
        </tr>
	@else
	<tr>
            <td><label style="text-align:center;">
		Please save the quote to regenerate the barcode for this item.
	   
	   <br>
	   CHK-{{$item->id}}</label></td>

		<td>{{$item->number}}</td>
		<td>{{ $item->formatted_event_date }}</td>
            	<td>{{ $item->formatted_quote_date }}</td>
            	<td>{{ $item->formatted_expires_at }}</td>
		<td>{{$item->client->name}}</td>
		<td>{{ $item->amount->formatted_total }}</td>
        </tr>
	@endif
    @endforeach

    </tbody>
</table>

<div class="pull-right" id="pagination-nav">
     {!! $quotes->appends(request()->except('page'))->render() !!}
</div>





</body>
</html>

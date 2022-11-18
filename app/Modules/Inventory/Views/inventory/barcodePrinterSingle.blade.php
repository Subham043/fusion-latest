<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Barcode Printer</title>
    <link rel="stylesheet" media="print" href="{{ asset('assets/plugins/chosen/print.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">

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
	.pt-10{
	    	padding-top:10px;
	}

	.txt{
		position: absolute;
		top: 11%;
    		width: 10%;
    		right: 1%;
	}


    </style>
</head>
<body>
    
    <section class="content-header" id="print-section">

	<div class="pull-right">
		<button  onclick="window.print();"
           class="btn btn-default"><i class="fa fa-print"></i> {{ trans('fi.print') }}</button>

	</div>

    <div class="clearfix"></div>
</section>

	

<br>

<div style="display:flex; justify-content:center; align-items:center; width:100%;">
	<div style="max-width: 400px; width:400px; display: grid; place-items:center; border: 4px solid grey; border-radius: 10px; padding: 20px 30px;">
		<div>
			<img style="width:200px" src="{{asset('assets/barcode/inventory'.$itemLookup->id.'-barcode.png')}}" />
	   		<br>
		</div>
		<div style="text-align:center">
			<h3>PRD-{{$itemLookup->id}}</h3>
			<h4>{{$itemLookup->name}}</h4>
			<h5>{{$itemLookup->location}}</h5>
		</div>
	</div>
</div>





</body>
</html>

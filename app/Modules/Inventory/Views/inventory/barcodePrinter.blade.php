<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barcode Printer</title>
    <link rel="stylesheet" media="print" href="{{ asset('assets/plugins/chosen/print.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    
    <script src="{{ asset('assets/plugins/jQuery/jQuery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    
    
<script type="text/javascript">

    $(function () {
        $('#bulk-select-all').click(function() {
            if ($(this).prop('checked')) {
                $('.bulk-record').prop('checked', true);
            }
            else {
                $('.bulk-record').prop('checked', false);
                //$('.bulk-actions').hide();
            }
        });
       
       $('.bulk-print').click(function() {
           
           var ids = [];

            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });
           
          // var selectedIds= $('input[name="selectedIds"]:checked');  // get the value of the checkbox

            /*Now make the ajax call*/
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/inventory/barcode-print-multiple',
                datatType : 'json',
                type: 'get',
                data: {
                    selectedIds: ids
                },
                success:function(response) {
                    
                   // window.location.href = "{{ route('inventory.barcodePrinterMultiple')}}";
                   $('#showHtml').hide();
                   
                   $('#customHtml').html(response);
                    
                    //$('#bulk-select-all').load('/'');
                   // window.open("/", "_blank");
                   // alert(response);
                }
            });
        });

    });
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
    <div id="customHtml"></div>
    
    <div id="showHtml">
        <section class="content-header" id="print-section">
        <form action="{{ url()->current() }}" method="get" id="searchSubmit" class="sidebar-form">
        	<div class="pull-left" style="width:600px">
            	<div class="row ">
            		<div class="col-md-6">
            			@if (isset($displaySearch) and $displaySearch == true)
                            <input type="hidden" name="status" value="{{ request('status') }}"/>
                            <div class="input-group">
                                <input type="text" name="search" id="searchChange" value="{{ request()->get('search') }}" onkeyup="getUpdateList()" class="form-control" placeholder="{{ trans('fi.search') }}..."/>
                            </div>
                        @endif
            		</div>
            	</div>
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
    
        <div class="row">
    		<div class="col-md-6">
    		    <button  onclick="window.print();" style="position:absolute;right:20px;"
               class="btn btn-default"><i class="fa fa-print"></i> {{ trans('fi.print') }}</button>
    		</div>
    		<div class="col-md-6">
    		    <a href="javascript:void(0)" class="btn btn-default bulk-print"><i class="fa fa-print"></i> {{ trans('fi.print') }} Multiple</a>
    		</div>
    	</div>
    
        <div class="clearfix"></div>
    </section>
    
    	
    
    <br>
    
    
    <table class="alternate">
    	<thead>
                   <tr>
                       <th><div class="btn-group"><input type="checkbox" id="bulk-select-all"></div></th>
    		   <th>Barcode</th>	
                       <th>Name</th>
                       <th>Category</th>
    	           <th>Price</th>
                       <th>Quantity</th>
    		   <th class="noprint">Action</th>
                       
                   </tr>
             </thead>
    
        <tbody>
        @foreach ($itemLookups as $item)
    	@if(file_exists(public_path('assets/barcode/').('inventory'.$item->id.'-barcode.png')))
            <tr>
                <td><input type="checkbox" class="bulk-record"  data-id="{{ $item->id }}" value="{{ $item->id }}"></td>
                <td>
    		<label style="text-align:center;">
    	   		<img  src="{{asset('assets/barcode/inventory'.$item->id.'-barcode.png')}}" />
    	   		<br>
    	   		PRD-{{$item->id}}
    		</label>
    	     </td>
    	     <td>{{ $item->name }}</td>
                 <td>{{ $item->category }}</td>
    	     <td>{{ $item->formatted_price }}</td>
    	     <td>{{ $item->total }}</td>
    	     <td class="noprint">
    
    		<a href="{{route('inventory.barcodePrinterSingle', $item->id)}}" target="_blank"
               class="btn btn-default"><i class="fa fa-print"></i> {{ trans('fi.print') }} Single</a>
               
    
    	     </td>
            </tr>
    	@else
    	<tr>
    	    <td><input type="checkbox" class="no-bulk-record" data-id="{{ $item->id }}" disabled></td>
                <td>
    		<label style="text-align:center;">
    	   		Please save the inventory to regenerate the barcode for this item.
    
    	   		<br>
    	   		PRD-{{$item->id}}
    		</label>
    	     </td>
    	     <td>{{ $item->name }}</td>
                 <td>{{ $item->category }}</td>
    	     <td>{{ $item->formatted_price }}</td>
    	     <td>{{ $item->total }}</td>
    
    
        
            </tr>
    	@endif
        @endforeach
    
        </tbody>
    </table>
    
    <div class="pull-right" id="pagination-nav">
         {!! $itemLookups->appends(request()->except('page'))->render() !!}
    </div>

    </div>
    
    
</body>
</html>

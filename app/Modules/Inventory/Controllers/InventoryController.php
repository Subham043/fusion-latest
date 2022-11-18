<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Inventory\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Inventory\Models\Inventory;
use FI\Modules\Inventory\Models\InventoryImage;
use FI\Modules\InventoryCategory\Models\InventoryCategory;
use FI\Modules\InventoryColor\Models\InventoryColor;
use FI\Modules\InventoryStyle\Models\InventoryStyle;
use FI\Modules\InventorySubCategory\Models\InventorySubCategory;
use FI\Modules\InventoryItemLocation\Models\InventoryItemLocation;
use FI\Modules\InventoryLocation\Models\InventoryLocation;
use FI\Modules\Inventory\Requests\InventoryRequest;
use FI\Modules\Inventory\Requests\InventoryImageRequest;
use FI\Modules\TaxRates\Models\TaxRate;
use FI\Support\NumberFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $itemLookups = Inventory::sortable(['name' => 'asc'])->with(['taxRate', 'taxRate2'])->keywords(request('search'))->paginate(config('fi.resultsPerPage'));
	
        return view('inventory.index')
            ->with('itemLookups', $itemLookups)
            ->with('displaySearch', true);
    }

	public function barcodePrinter(Request $req)
    {
        $itemLookups = Inventory::sortable(['name' => 'asc'])->with(['taxRate', 'taxRate2'])->keywords(request('search'));

      if($req->has('item')){
			if($req->input('item')=="all"){

				$itemLookups = $itemLookups ->paginate(10000);
			}else{
			  $itemLookups = $itemLookups ->paginate($req->input('item'));
			}
		}
		else{
            $itemLookups = $itemLookups ->paginate(config('fi.resultsPerPage'));
		}

	
        return view('inventory.barcodePrinter')
            ->with('itemLookups', $itemLookups)
            ->with('displaySearch', true);
    }

	public function barcodePrinterSingle($id)
    {
        $itemLookup = Inventory::findOrFail($id);
            return view('inventory.barcodePrinterSingle')
            ->with('itemLookup', $itemLookup);
	   
    }
    
    public function barcodePrinterMultiple(Request $request)
    {
        
       $selectedIds = $request->input('selectedIds');
        
         $itemLookup = Inventory::findOrFail($selectedIds);
         
         //return $itemLookup;
        
         return view('inventory.barcodePrinterMultiple')
           ->with('itemLookup', $itemLookup);
    }

    public function create()
    {

	$firstInventoryCategory = InventoryCategory::first();
	if(!empty($firstInventoryCategory)){
		$firstInventorySubCategory = InventorySubCategory::where('inventory_category_id',$firstInventoryCategory->id)->pluck( 'name', 'name' );
	}else{
		$firstInventorySubCategory = array();
	}
	$firstInventoryLocation = InventoryLocation::first();
	if(!empty($firstInventoryLocation)){
		$firstInventoryItemLocation = InventoryItemLocation::where('inventory_location_id',$firstInventoryLocation->id)->pluck( 'name', 'name' );
	}else{
		$firstInventoryItemLocation = array();
	}

	
	
        return view('inventory.form')
            ->with('editMode', false)
	    ->with('InventoryCategory',InventoryCategory::pluck( 'name', 'name' ))
	    ->with('InventoryColor',InventoryColor::pluck( 'name', 'name' ))
	    ->with('InventoryStyle',InventoryStyle::pluck( 'name', 'name' ))
	    ->with('InventorySubCategory',$firstInventorySubCategory)
	    ->with('subCategorySelector',InventoryCategory::with('InventorySubCategory')->get())
	    ->with('InventoryLocation',InventoryLocation::pluck( 'name', 'name' ))
	    ->with('InventoryItemLocation',$firstInventoryItemLocation)
	    ->with('itemLocationSelector',InventoryLocation::with('InventoryItemLocation')->get())
            ->with('taxRates', TaxRate::getList());
    }

    public function store(InventoryRequest $request)
    {
        $inventory = Inventory::create($request->all());
        $inventory->available = $request->input('total');
        $inventory->save();

	

        return redirect()->route('inventory.index')
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        $itemLookup = Inventory::find($id);
	$firstInventoryCategory = InventoryCategory::where('name',$itemLookup->category)->first();
	if(!empty($firstInventoryCategory)){
		$firstInventorySubCategory = InventorySubCategory::where('inventory_category_id',$firstInventoryCategory->id)->pluck( 'name', 'name' );
	}else{
		$firstInventoryCategory = InventoryCategory::first();
		$firstInventorySubCategory = InventorySubCategory::where('inventory_category_id',$firstInventoryCategory->id)->pluck( 'name', 'name' );
	}
	$firstInventoryLocation = InventoryLocation::where('name',$itemLookup->location)->first();
	if(!empty($firstInventoryLocation)){
		$firstInventoryItemLocation = InventoryItemLocation::where('inventory_location_id',$firstInventoryLocation->id)->pluck( 'name', 'name' );
	}else{
		$firstInventoryLocation = InventoryLocation::first();
		$firstInventoryItemLocation = InventoryItemLocation::where('inventory_location_id',$firstInventoryLocation->id)->pluck( 'name', 'name' );
	}
            return view('inventory.form')
            ->with('editMode', true)
            ->with('itemLookup', $itemLookup)
	    ->with('InventoryCategory',InventoryCategory::pluck( 'name', 'name' ))
	    ->with('InventoryColor',InventoryColor::pluck( 'name', 'name' ))
	    ->with('InventoryStyle',InventoryStyle::pluck( 'name', 'name' ))
	    ->with('InventorySubCategory',$firstInventorySubCategory)
	    ->with('subCategorySelector',InventoryCategory::with('InventorySubCategory')->get())
	    ->with('InventoryLocation',InventoryLocation::pluck( 'name', 'name' ))
	    ->with('InventoryItemLocation',$firstInventoryItemLocation)
	    ->with('itemLocationSelector',InventoryLocation::with('InventoryItemLocation')->get())
            ->with('taxRates', TaxRate::getList());
	   
    }

    public function update(InventoryRequest $request, $id)
    {
        $itemLookup = Inventory::find($id);

        $itemLookup->fill($request->all());

        $itemLookup->save();

	$url = "PRD-".$itemLookup->id;

	$barcode = new \FI\Modules\Inventory\Barcode\Barcode();
	$bobj = $barcode->getBarcodeObj('C128', $url, 0, -30, 'black', array(0, 0, 0, 0));
  	Storage::put('public/barcode/inventory'.$itemLookup->id.'-barcode.png', $bobj->getPngData());
	File::move(storage_path('app/public/barcode/inventory'.$itemLookup->id.'-barcode.png'), public_path('assets/barcode/inventory'.$itemLookup->id.'-barcode.png'));

        return redirect()->route('inventory.index')
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        Inventory::destroy($id);

        return redirect()->route('inventory.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function ajaxInventoryLookup()
    {
        $items = Inventory::orderBy('name')->where('name', 'like', '%' . request('query') . '%')->get();
        

        $list = [];

        foreach ($items as $item)
        {
            $list[] = [
                'id'          => $item->id,
                'name'          => $item->name,
                'description'   => $item->description,
                'price'         => NumberFormatter::format($item->price),
                'tax_rate_id'   => $item->tax_rate_id,
                'tax_rate_2_id' => $item->tax_rate_2_id,
                'total' => $item->total,
                'reserved' => $this->getReservedQuantity(request('eventDate'),request('query'),request('invoiceId'),request('quoteId')),
                'allocated' => $this->getAllocatedQuantity(request('eventDate'),request('query'),request('invoiceId')),
                'available' => $this->getAvailableQuantity(request('eventDate'),request('query'),request('invoiceId')),
            ];
        }

        return json_encode($list);
    }
    
    public function getReservedQuantity($eventDate,$itemName,$invoiceId,$quoteId)
    {
	if($itemName=="null"){
            return 0;
        }
        $query = DB::table('invoices')
            ->select('invoice_items.quantity','invoice_items.name', DB::raw("sum(invoice_items.quantity) as sum"))
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.event_date',$eventDate)
            ->where('invoice_items.name',$itemName)
            ->whereIn('invoices.invoice_status_id', array(1,2))
            ->get();
            
        $invoice = $query[0]->sum==null ? 0 : $query[0]->sum;
        
        $query = DB::table('quotes')
            ->select('quote_items.quantity','quote_items.name', 'quotes.id', 'quotes.event_date', DB::raw("sum(quote_items.quantity) as sum"))
            ->join('quote_items', 'quote_items.quote_id', '=', 'quotes.id')
            ->where('quotes.event_date',$eventDate)
            ->where('quote_items.name',$itemName)
            ->whereIn('quotes.quote_status_id', array(2,3))
            ->get();
            
        $quote = $query[0]->sum==null ? 0 : $query[0]->sum;
        
        return (int)$invoice + (int)$quote;
    }
    
    public function getAllocatedQuantity($eventDate,$itemName,$invoiceId)
    {
        if($itemName=="null"){
            return 0;
        }
        $query = DB::table('invoices')
            ->select('invoice_items.quantity','invoice_items.name', DB::raw("sum(invoice_items.quantity) as sum"))
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.event_date',$eventDate)
            ->where('invoice_items.name',$itemName)
            ->whereIn('invoices.invoice_status_id', array(3,4))
            ->get();

        if($query[0]->sum==null){
            return 0;
        }
        return (int)$query[0]->sum;
    }
    
    public function getAvailableQuantity($eventDate,$itemName,$invoiceId)
    {
	if($itemName=="null"){
            return 0;
        }
        $inventory = Inventory::where('name',$itemName)->first();

        $query = DB::table('invoices')
            ->select('invoice_items.quantity','invoice_items.name', DB::raw("sum(invoice_items.quantity) as sum"))
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.event_date',$eventDate)
            ->where('invoice_items.name',$itemName)
            ->whereIn('invoices.invoice_status_id', array(3,4))
            ->get();
        if($query[0]->sum==null){
            return (int)$inventory->total;
        }
        return (int)$inventory->total-(int)$query[0]->sum;
    }

	public function add_image(Request $req, $id){
		$itemLookup = InventoryImage::where('item_lookup_id',$id)->get();
                //echo "<pre>";print_r($itemLookup);exit;
		return view('inventory.image')->with('id',$id)->with('itemLookupImage', $itemLookup);
	}

	public function store_image(InventoryImageRequest $req, $id){
		$itemLookup = InventoryImage::where('item_lookup_id',$id)->get();
		$uploadedFileCount=count($itemLookup);
		$currentFileCount=count($req->file('image'));
		$totalCount=$uploadedFileCount+$currentFileCount;
		if($totalCount>5){
			return redirect()->route('inventory.add_image', $id)
            		->with('alert', "You can not upload more then 5 images");
		}else{

		//echo count($req->file('image'));exit;
		//echo count($itemLookup)."<pre>";print_r($itemLookup);exit;
		//InventoryImageRequest 
		//echo $id."----test --><pre>";print_r($req->image);exit;
		
		$image_names = [];
		//$image = new InventoryImage;

        // loop through images and save to /uploads directory
	$i=0;
        foreach ($req->file('image') as $image_data) {
	    $image = new InventoryImage;

            $name = time().'-'.$image_data->getClientOriginalName();
            $req->image[$i]->storeAs('public/upload/inventory',$name);
            $image_names[] = $name;
	     $image->image = $name ;

	    $image->item_lookup_id = $id;

	    $image->save();
	    File::move(storage_path('app/public/upload/inventory/'.$image->image), public_path('assets/upload/inventory/'.$image->image));
            $i++;		
        }		
	return redirect()->route('inventory.add_image', $id)
            ->with('alert', "Data Saved Successfully");

     }








	//echo '<pre>';print_r($image_names);exit;


		/*$validator = $req->validate([
            'image' => ['required','image','mimes:jpeg,png,jpg,webp'],        ],
        [
            'image.image' => 'Please enter a valid image !',
            'image.mimes' => 'Please enter a valid image !',
        ]);
	$image = new InventoryImage;
	if($req->hasFile('image')){

            $newImage = time().'-'.$req->image->getClientOriginalName();

            $req->image->storeAs('public/upload/inventory',$newImage);
            $image->image = $newImage;
        }
	$image->item_lookup_id = $id;
	$image->save();
	File::move(storage_path('app/public/upload/inventory/'.$image->image), public_path('assets/upload/inventory/'.$image->image));

	return redirect()->route('inventory.add_image', $id)
            ->with('alert', "Data Saved Successfully");*/
		
	}

	public function delete_image($id){
		$itemLookup = InventoryImage::where('id',$id)->firstOrFail();
		if($itemLookup->image!=null && file_exists(public_path('assets/upload/inventory').'/'.$itemLookup->image)){
                	unlink(public_path('assets/upload/inventory/'.$itemLookup->image)); 
            	}
		$itemLookup->delete();
		return redirect()->route('inventory.add_image', $itemLookup->item_lookup_id)
            	->with('alert', "Data Deleted Successfully");
	}

}

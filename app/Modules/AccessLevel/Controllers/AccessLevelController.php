<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\AccessLevel\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\AccessLevel\Models\AccessLevel;
use FI\Modules\AccessLevel\Requests\AccessLevelRequest;
use FI\Modules\TaxRates\Models\TaxRate;
use FI\Support\NumberFormatter;
use Illuminate\Support\Facades\DB;
use FI\Support\Statuses\AccessStatuses;


class AccessLevelController extends Controller
{
    public function index()
    {
        $itemLookups = AccessLevel::sortable(['name' => 'asc'])->paginate(config('fi.resultsPerPage'));
	$accessList = array(
        '0' => 'Clients',
        '1' => 'Quotes',
        '2' => 'Invoices',
        '3' => 'Load In Load Out Calendar',
        '4' => 'Event Calendar',
	'5' => 'Payments',
	'6' => 'Inventory',
	'7' => 'Barcode Printer',
	'8' => 'Expenses',
	'9' => 'Reports',
	'10' => 'Schedule',
	'11' => 'Settings',
    );

        return view('accesslevel.index')
	    ->with('editMode', false)
            ->with('itemLookups', $itemLookups)
	    ->with('accessList', $accessList)
            ->with('displaySearch', false);
    }

    public function create()
    {
	$accessList = AccessStatuses::lists();
        return view('accesslevel.form')
	    ->with('accessList', $accessList)
            ->with('editMode', false);
    }

    public function store(AccessLevelRequest $request)
    {
        $inventory = AccessLevel::create(array('name'=>$request->name, 'access'=>json_encode($request->access)));
        $inventory->save();

        return redirect()->route('access_level.index')
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
	$accessList = array(
        '0' => 'Clients',
        '1' => 'Quotes',
        '2' => 'Invoices',
        '3' => 'Load In Load Out Calendar',
        '4' => 'Event Calendar',
	'5' => 'Payments',
	'6' => 'Inventory',
	'7' => 'Barcode Printer',
	'8' => 'Expenses',
	'9' => 'Reports',
	'10' => 'Schedule',
	'11' => 'Settings',
    );
        $itemLookup = AccessLevel::find($id);
	$itemLookups = AccessLevel::sortable(['name' => 'asc'])->paginate(config('fi.resultsPerPage'));

        return view('accesslevel.index')
            ->with('editMode', true)
            ->with('itemLookup', $itemLookup)
	    ->with('itemLookups', $itemLookups)
	    ->with('accessList', $accessList)
            ->with('displaySearch', true);
    }

    public function update(AccessLevelRequest $request, $id)
    {
        $itemLookup = AccessLevel::find($id);

        $itemLookup->fill(array('name'=>$request->name, 'access'=>json_encode($request->access)));

        $itemLookup->save();

        return redirect()->route('access_level.index')
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        AccessLevel::destroy($id);

        return redirect()->route('access_level.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

        
}

<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\MasterClients\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\MasterClients\Models\MasterClient;
use FI\Modules\MasterClients\Requests\MasterClientStoreRequest;
use FI\Modules\MasterClients\Requests\MasterClientUpdateRequest;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\Payments\Models\Payment;
use FI\Support\Frequency;
use FI\Traits\ReturnUrl;

class MasterClientController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $status = (request('status')) ?: 'all';

        $clients = MasterClient::
            sortable(['name' => 'asc'])
            //->status($status)
            ->keywords(request('search'))
            ->paginate(config('fi.resultsPerPage'));

        return view('master_clients.index')
            ->with('clients', $clients)
            ->with('status', $status)
            ->with('displaySearch', true);
    }

    public function create()
    {
        return view('master_clients.form')
            ->with('editMode', false);
    }

    public function store(MasterClientStoreRequest $request)
    {
	// print_r($request); exit;
	
        $client = MasterClient::create($request->except('custom'));

        return redirect()->route('master_clients.edit', [$client->id])
            ->with('alertInfo', trans('fi.record_successfully_created'));
    }

    public function show($clientId)
    {
        $this->setReturnUrl();

        $client = MasterClient::find($clientId);

        return view('master_clients.view')
            ->with('client', $client)
            ->with('frequencies', Frequency::lists());
    }

    public function edit($clientId)
    {
        $client = MasterClient::find($clientId);

        return view('master_clients.form')
            ->with('editMode', true)
            ->with('client', $client)
            ->with('returnUrl', $this->getReturnUrl());
    }

    public function update(MasterClientUpdateRequest $request, $id)
    {
        $client = MasterClient::find($id);
        $client->fill($request->except('custom'));
        $client->save();

        return redirect()->route('master_clients.edit', [$id])
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($clientId)
    {
        MasterClient::destroy($clientId);

        return redirect()->route('master_clients.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        Client::destroy(request('ids'));
    }

    public function ajaxLookup()
    {
        $clients = Client::select('unique_name')
            ->where('active', 1)
            ->where('unique_name', 'like', '%' . request('query') . '%')
            ->orderBy('unique_name')
            ->get();

        $list = [];

        foreach ($clients as $client)
        {
            $list[]['value'] = $client->unique_name;
        }

        return json_encode($list);
    }
    
    public function ajaxUserLookup()
    {
        $clients = Client::select('type')
            ->where('active', 1)
            ->where('unique_name', request('query') )
	    ->orWhere('name', request('query') )
            // ->where('unique_name', 'like', '%' . request('query') . '%')
            ->first();
        return json_encode($clients);
    }

    public function ajaxModalEdit()
    {
        return view('clients._modal_edit')
            ->with('editMode', true)
            ->with('client', Client::getSelect()->with(['custom'])->find(request('client_id')))
            ->with('refreshToRoute', request('refresh_to_route'))
            ->with('id', request('id'))
            ->with('customFields', CustomField::forTable('clients')->get());
    }

    public function ajaxModalUpdate(ClientUpdateRequest $request, $id)
    {
        $client = Client::find($id);
        $client->fill($request->except('custom'));
        $client->save();

        $client->custom->update($request->get('custom', []));

        return response()->json(['success' => true], 200);
    }

    public function ajaxModalLookup()
    {
        return view('clients._modal_lookup')
            ->with('updateClientIdRoute', request('update_client_id_route'))
            ->with('refreshToRoute', request('refresh_to_route'))
            ->with('id', request('id'));
    }

    public function ajaxCheckName()
    {
        $client = Client::select('id')->where('unique_name', request('client_name'))->first();

        if ($client)
        {
            return response()->json(['success' => true, 'client_id' => $client->id], 200);
        }

        return response()->json([
            'success' => false,
            'errors'  => ['messages' => [trans('fi.client_not_found')]],
        ], 400);
    }

    public function ajaxCheckDuplicateName()
    {
        if (Client::where(function ($query)
            {
                $query->where('name', request('client_name'));
                $query->orWhere('unique_name', request('unique_name'));
            })->where('id', '<>', request('client_id'))->count() > 0
        )
        {
            return response()->json(['is_duplicate' => 1]);
        }

        return response()->json(['is_duplicate' => 0]);
    }
}
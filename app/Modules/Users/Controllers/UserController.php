<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Users\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\AccessLevel\Models\AccessLevel;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\Users\Models\User;
use FI\Modules\Users\Requests\UserStoreRequest;
use FI\Modules\Users\Requests\UserUpdateRequest;
use FI\Traits\ReturnUrl;

class UserController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $users = User::sortable(['name' => 'asc'])->userType(request('userType'))->paginate(config('fi.resultsPerPage'));

        return view('users.index')
            ->with('users', $users)
            ->with('userTypes', ['' => trans('fi.all_accounts'), 'admin' => trans('fi.admin_accounts'), 'client' => trans('fi.client_accounts')]);
    }

    public function create($userType)
    {

	$access_level = AccessLevel::all()->pluck('name', 'id');

        $view = view('users.' . $userType . '_form')
            ->with('editMode', false)
	    ->with('access_level', $access_level)
            ->with('customFields', CustomField::forTable('users')->get());

        if ($userType == 'client')
        {
            $view->with('clients', Client::whereDoesntHave('user')
                ->where('email', '<>', '')
                ->whereNotNull('email')
                ->where('active', 1)
                ->orderBy('unique_name')
                ->pluck('unique_name', 'id')
                ->toArray()
            );
        }

        return $view;
    }

    public function store(UserStoreRequest $request, $userType)
    {
        $user = new User($request->except('custom'));

        $user->password = $request->input('password');

        $user->save();

        $user->custom->update($request->input('custom', []));

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id, $userType)
    {
        $user = User::find($id);
	$access_level = AccessLevel::all()->pluck('name', 'id');

        return view('users.' . $userType . '_form')
            ->with(['editMode' => true, 'user' => $user])
	    ->with('access_level', $access_level)
            ->with('customFields', CustomField::forTable('users')->get());
    }

    public function update(UserUpdateRequest $request, $id, $userType)
    {
        $user = User::find($id);

        $user->fill($request->except('custom'));

        $user->save();
	
	if($request->input('custom')){
		$user->custom->update($request->input('custom', []));
	}
        

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        User::destroy($id);

        return redirect()->route('users.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function getClientInfo()
    {
        return Client::find(request('id'));
    }
}
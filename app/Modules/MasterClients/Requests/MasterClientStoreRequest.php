<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\MasterClients\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasterClientStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'        => trans('fi.name'),
            'unique_name' => trans('fi.unique_name'),
            'email'       => trans('fi.email'),
            'mobile'       => trans('fi.mobile_number'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

	//print_r($request['name']);exit;

        $request['email'] = $this->input('client_email', $this->input('email', ''));
	$request['name'] = $this->input('client_name', $this->input('name', ''));
        unset($request['client_email']);
	unset($request['client_name']);

        

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'name'        => 'required',
            'unique_name' => 'required|unique:master_clients',
            'email'       => 'required|email',
            'mobile'       => 'regex:/^[0-9\s\+\-]*$/',
        ];
    }
}